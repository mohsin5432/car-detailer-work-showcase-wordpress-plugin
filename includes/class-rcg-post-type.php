<?php
/**
 * Custom Post Type and Taxonomy registration.
 *
 * @package Car_Detailers_Showcase
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class RCG_Post_Type
 *
 * Registers the `rcg_car` custom post type and `rcg_work_done` taxonomy.
 */
class RCG_Post_Type {

	/**
	 * Register hooks for CPT and taxonomy.
	 */
	public function register() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_taxonomy' ) );
	}

	/**
	 * Register the rcg_car custom post type.
	 */
	public function register_post_type() {
		$labels = array(
			'name'                  => _x( 'Cars', 'Post type general name', 'car-detailers-showcase' ),
			'singular_name'         => _x( 'Car', 'Post type singular name', 'car-detailers-showcase' ),
			'menu_name'             => _x( 'Showcase Gallery', 'Admin menu text', 'car-detailers-showcase' ),
			'name_admin_bar'        => _x( 'Car', 'Admin bar text', 'car-detailers-showcase' ),
			'add_new'               => __( 'Add New Car', 'car-detailers-showcase' ),
			'add_new_item'          => __( 'Add New Car', 'car-detailers-showcase' ),
			'new_item'              => __( 'New Car', 'car-detailers-showcase' ),
			'edit_item'             => __( 'Edit Car', 'car-detailers-showcase' ),
			'view_item'             => __( 'View Car', 'car-detailers-showcase' ),
			'all_items'             => __( 'All Cars', 'car-detailers-showcase' ),
			'search_items'          => __( 'Search Cars', 'car-detailers-showcase' ),
			'not_found'             => __( 'No cars found.', 'car-detailers-showcase' ),
			'not_found_in_trash'    => __( 'No cars found in Trash.', 'car-detailers-showcase' ),
			'featured_image'        => _x( 'Car Cover Photo', 'Overrides the "Featured Image" label', 'car-detailers-showcase' ),
			'set_featured_image'    => _x( 'Set cover photo', 'Overrides the "Set featured image" label', 'car-detailers-showcase' ),
			'remove_featured_image' => _x( 'Remove cover photo', 'Overrides the "Remove featured image" label', 'car-detailers-showcase' ),
			'use_featured_image'    => _x( 'Use as cover photo', 'Overrides the "Use as featured image" label', 'car-detailers-showcase' ),
		);

		$args = array(
			'labels'              => $labels,
			'public'              => false,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'query_var'           => false,
			'rewrite'             => false,
			'capability_type'     => 'post',
			'has_archive'         => false,
			'hierarchical'        => false,
			'menu_position'       => 25,
			'menu_icon'           => 'dashicons-car',
			'supports'            => array( 'title', 'thumbnail' ),
			'show_in_rest'        => false,
		);

		register_post_type( 'rcg_car', $args );
	}

	/**
	 * Register the rcg_work_done taxonomy.
	 */
	public function register_taxonomy() {
		$labels = array(
			'name'                       => _x( 'Services', 'Taxonomy general name', 'car-detailers-showcase' ),
			'singular_name'              => _x( 'Service', 'Taxonomy singular name', 'car-detailers-showcase' ),
			'search_items'               => __( 'Search Services', 'car-detailers-showcase' ),
			'all_items'                  => __( 'All Services', 'car-detailers-showcase' ),
			'parent_item'                => __( 'Parent Service', 'car-detailers-showcase' ),
			'parent_item_colon'          => __( 'Parent Service:', 'car-detailers-showcase' ),
			'edit_item'                  => __( 'Edit Service', 'car-detailers-showcase' ),
			'update_item'                => __( 'Update Service', 'car-detailers-showcase' ),
			'add_new_item'               => __( 'Add New Service', 'car-detailers-showcase' ),
			'new_item_name'              => __( 'New Service Name', 'car-detailers-showcase' ),
			'menu_name'                  => __( 'Services', 'car-detailers-showcase' ),
			'not_found'                  => __( 'No services found.', 'car-detailers-showcase' ),
			'no_terms'                   => __( 'No services', 'car-detailers-showcase' ),
			'items_list_navigation'      => __( 'Services list navigation', 'car-detailers-showcase' ),
			'items_list'                 => __( 'Services list', 'car-detailers-showcase' ),
			'back_to_items'              => __( '&larr; Go to Services', 'car-detailers-showcase' ),
		);

		$args = array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'public'            => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => false,
			'show_tagcloud'     => false,
			'show_in_rest'      => false,
			'rewrite'           => false,
		);

		register_taxonomy( 'rcg_work_done', array( 'rcg_car' ), $args );
	}
}
