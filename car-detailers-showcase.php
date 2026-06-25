<?php
/**
 * Plugin Name:       Car Detailers Showcase Gallery
 * Plugin URI:        https://cardetailersshowcase.com/
 * Description:       A premium car detailing gallery plugin with 5 stunning layout designs. Manage your car gallery entries and display them via shortcode.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Car Detailers Showcase
 * Author URI:        https://cardetailersshowcase.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       car-detailers-showcase
 * Domain Path:       /languages
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin constants.
 */
define( 'RCG_VERSION', '1.0.0' );
define( 'RCG_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'RCG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'RCG_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Include required class files.
 */
require_once RCG_PLUGIN_DIR . 'includes/class-rcg-post-type.php';
require_once RCG_PLUGIN_DIR . 'includes/class-rcg-admin.php';
require_once RCG_PLUGIN_DIR . 'includes/class-rcg-ajax.php';
require_once RCG_PLUGIN_DIR . 'includes/class-rcg-shortcode.php';

/**
 * Initialize the plugin.
 */
function rcg_init() {
	// Register Custom Post Type and Taxonomy.
	$post_type = new RCG_Post_Type();
	$post_type->register();

	// Initialize Admin.
	if ( is_admin() ) {
		$admin = new RCG_Admin();
		$admin->init();

		$ajax = new RCG_Ajax();
		$ajax->init();
	}

	// Initialize Shortcode.
	$shortcode = new RCG_Shortcode();
	$shortcode->init();
}
add_action( 'plugins_loaded', 'rcg_init' );

/**
 * Activation hook — seed default taxonomy terms.
 */
function rcg_activate() {
	// Register CPT & taxonomy first so terms can be inserted.
	$post_type = new RCG_Post_Type();
	$post_type->register();

	// Flush rewrite rules.
	flush_rewrite_rules();

	// Insert default "Work Done" terms.
	$defaults = array(
		'Full Detail',
		'Paint Correction',
		'Ceramic Coating',
		'Interior Cleaning',
		'Exterior Wash',
		'Window Tinting',
	);

	foreach ( $defaults as $term_name ) {
		if ( ! term_exists( $term_name, 'rcg_work_done' ) ) {
			wp_insert_term( $term_name, 'rcg_work_done' );
		}
	}

	// Set default options.
	if ( false === get_option( 'rcg_default_layout' ) ) {
		update_option( 'rcg_default_layout', 'grid' );
	}
	if ( false === get_option( 'rcg_items_per_page' ) ) {
		update_option( 'rcg_items_per_page', 12 );
	}
	if ( false === get_option( 'rcg_accent_color' ) ) {
		update_option( 'rcg_accent_color', '#3b82f6' );
	}
	if ( false === get_option( 'rcg_animation_speed' ) ) {
		update_option( 'rcg_animation_speed', '0.3' );
	}
}
register_activation_hook( __FILE__, 'rcg_activate' );

/**
 * Deactivation hook.
 */
function rcg_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'rcg_deactivate' );

/**
 * Enqueue admin assets.
 *
 * @param string $hook_suffix The current admin page.
 */
function rcg_admin_enqueue_scripts( $hook_suffix ) {
	$screen = get_current_screen();

	if ( ! $screen ) {
		return;
	}

	// Only load on our CPT screens and settings page.
	$allowed_screens = array( 'rcg_car', 'edit-rcg_car', 'toplevel_page_rcg-settings' );
	if ( ! in_array( $screen->id, $allowed_screens, true ) && 'rcg_car' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_script( 'jquery-ui-sortable' );

	wp_enqueue_style(
		'rcg-admin-css',
		RCG_PLUGIN_URL . 'admin/css/rcg-admin.css',
		array(),
		RCG_VERSION
	);

	wp_enqueue_script(
		'rcg-admin-js',
		RCG_PLUGIN_URL . 'admin/js/rcg-admin.js',
		array( 'jquery', 'jquery-ui-sortable' ),
		RCG_VERSION,
		true
	);

	wp_localize_script( 'rcg-admin-js', 'rcgAdmin', array(
		'ajaxUrl'          => admin_url( 'admin-ajax.php' ),
		'nonce'            => wp_create_nonce( 'rcg_admin_nonce' ),
		'mediaTitle'       => __( 'Select Car Photos', 'car-detailers-showcase' ),
		'mediaButton'      => __( 'Add to Gallery', 'car-detailers-showcase' ),
		'confirmRemove'    => __( 'Remove this photo?', 'car-detailers-showcase' ),
		'addServicePrompt' => __( 'Enter the new service type name:', 'car-detailers-showcase' ),
	) );
}
add_action( 'admin_enqueue_scripts', 'rcg_admin_enqueue_scripts' );

/**
 * Add settings link on the Plugins page.
 *
 * @param array $links Plugin action links.
 * @return array Modified action links.
 */
function rcg_plugin_action_links( $links ) {
	$settings_link = sprintf(
		'<a href="%s">%s</a>',
		esc_url( admin_url( 'admin.php?page=rcg-settings' ) ),
		esc_html__( 'Settings', 'car-detailers-showcase' )
	);
	array_unshift( $links, $settings_link );
	return $links;
}
add_filter( 'plugin_action_links_' . RCG_PLUGIN_BASENAME, 'rcg_plugin_action_links' );
