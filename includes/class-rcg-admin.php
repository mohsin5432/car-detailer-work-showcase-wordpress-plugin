<?php
/**
 * Admin UI — meta boxes, settings page, and custom columns.
 *
 * @package Car_Detailers_Showcase
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class RCG_Admin
 */
class RCG_Admin {

	/**
	 * Initialize admin hooks.
	 */
	public function init() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post_rcg_car', array( $this, 'save_meta_boxes' ), 10, 2 );
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// Custom admin columns.
		add_filter( 'manage_rcg_car_posts_columns', array( $this, 'set_custom_columns' ) );
		add_action( 'manage_rcg_car_posts_custom_column', array( $this, 'render_custom_columns' ), 10, 2 );
	}

	/**
	 * Register meta boxes.
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'rcg_work_done_meta',
			__( 'Work Done', 'car-detailers-showcase' ),
			array( $this, 'render_work_done_meta_box' ),
			'rcg_car',
			'side',
			'high'
		);

		add_meta_box(
			'rcg_gallery_meta',
			__( 'Photo Gallery', 'car-detailers-showcase' ),
			array( $this, 'render_gallery_meta_box' ),
			'rcg_car',
			'normal',
			'high'
		);

		// Remove default taxonomy meta box — we replace with our custom dropdown.
		remove_meta_box( 'rcg_work_donediv', 'rcg_car', 'side' );
	}

	/**
	 * Render Work Done dropdown meta box.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function render_work_done_meta_box( $post ) {
		wp_nonce_field( 'rcg_save_meta', 'rcg_meta_nonce' );

		$terms         = get_terms( array(
			'taxonomy'   => 'rcg_work_done',
			'hide_empty' => false,
		) );
		$current_terms = wp_get_post_terms( $post->ID, 'rcg_work_done', array( 'fields' => 'ids' ) );
		$selected_id   = ! empty( $current_terms ) ? $current_terms[0] : 0;
		?>
		<div class="rcg-work-done-wrap">
			<label for="rcg_work_done_select" class="screen-reader-text">
				<?php esc_html_e( 'Select service type', 'car-detailers-showcase' ); ?>
			</label>
			<select name="rcg_work_done" id="rcg_work_done_select" class="rcg-select">
				<option value=""><?php esc_html_e( '— Select Service —', 'car-detailers-showcase' ); ?></option>
				<?php foreach ( $terms as $term ) : ?>
					<option value="<?php echo esc_attr( $term->term_id ); ?>" <?php selected( $selected_id, $term->term_id ); ?>>
						<?php echo esc_html( $term->name ); ?>
					</option>
				<?php endforeach; ?>
			</select>

			<button type="button" class="button rcg-add-service-btn" id="rcg-add-service-btn">
				<span class="dashicons dashicons-plus-alt2"></span>
				<?php esc_html_e( 'Add New Service', 'car-detailers-showcase' ); ?>
			</button>
		</div>
		<?php
	}

	/**
	 * Render Photo Gallery meta box.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function render_gallery_meta_box( $post ) {
		$image_ids = get_post_meta( $post->ID, '_rcg_gallery_images', true );
		$image_ids = ! empty( $image_ids ) ? array_filter( array_map( 'absint', (array) $image_ids ) ) : array();
		?>
		<div class="rcg-gallery-wrap">
			<input type="hidden" name="rcg_gallery_images" id="rcg-gallery-ids" value="<?php echo esc_attr( implode( ',', $image_ids ) ); ?>" />

			<div class="rcg-gallery-preview" id="rcg-gallery-preview">
				<?php if ( ! empty( $image_ids ) ) : ?>
					<?php foreach ( $image_ids as $img_id ) : ?>
						<?php
						$thumb_url = wp_get_attachment_image_url( $img_id, 'thumbnail' );
						if ( ! $thumb_url ) {
							continue;
						}
						?>
						<div class="rcg-gallery-item" data-id="<?php echo esc_attr( $img_id ); ?>">
							<img src="<?php echo esc_url( $thumb_url ); ?>" alt="" />
							<button type="button" class="rcg-remove-img" title="<?php esc_attr_e( 'Remove', 'car-detailers-showcase' ); ?>">
								<span class="dashicons dashicons-no-alt"></span>
							</button>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>

			<div class="rcg-gallery-actions">
				<button type="button" class="button button-primary" id="rcg-add-photos-btn">
					<span class="dashicons dashicons-format-gallery"></span>
					<?php esc_html_e( 'Add Photos', 'car-detailers-showcase' ); ?>
				</button>
				<p class="description">
					<?php esc_html_e( 'Select multiple photos from the Media Library. Drag to reorder.', 'car-detailers-showcase' ); ?>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Save meta box data.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 */
	public function save_meta_boxes( $post_id, $post ) {
		// Verify nonce.
		if ( ! isset( $_POST['rcg_meta_nonce'] ) || ! wp_verify_nonce( $_POST['rcg_meta_nonce'], 'rcg_save_meta' ) ) {
			return;
		}

		// Check autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Save Work Done taxonomy.
		if ( isset( $_POST['rcg_work_done'] ) ) {
			$term_id = absint( $_POST['rcg_work_done'] );
			if ( $term_id > 0 ) {
				wp_set_post_terms( $post_id, array( $term_id ), 'rcg_work_done' );
			} else {
				wp_set_post_terms( $post_id, array(), 'rcg_work_done' );
			}
		}

		// Save gallery images.
		if ( isset( $_POST['rcg_gallery_images'] ) ) {
			$image_ids_raw = sanitize_text_field( wp_unslash( $_POST['rcg_gallery_images'] ) );
			if ( ! empty( $image_ids_raw ) ) {
				$image_ids = array_filter( array_map( 'absint', explode( ',', $image_ids_raw ) ) );
				update_post_meta( $post_id, '_rcg_gallery_images', $image_ids );
			} else {
				delete_post_meta( $post_id, '_rcg_gallery_images' );
			}
		}
	}

	/**
	 * Add settings page under Car Detailers Showcase menu.
	 */
	public function add_settings_page() {
		add_submenu_page(
			'edit.php?post_type=rcg_car',
			__( 'Gallery Settings', 'car-detailers-showcase' ),
			__( 'Settings', 'car-detailers-showcase' ),
			'manage_options',
			'rcg-settings',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register settings.
	 */
	public function register_settings() {
		register_setting( 'rcg_settings_group', 'rcg_default_layout', array(
			'type'              => 'string',
			'sanitize_callback' => array( $this, 'sanitize_layout' ),
			'default'           => 'grid',
		) );

		register_setting( 'rcg_settings_group', 'rcg_items_per_page', array(
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'default'           => 12,
		) );

		register_setting( 'rcg_settings_group', 'rcg_accent_color', array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_hex_color',
			'default'           => '#3b82f6',
		) );

		register_setting( 'rcg_settings_group', 'rcg_animation_speed', array(
			'type'              => 'string',
			'sanitize_callback' => array( $this, 'sanitize_animation_speed' ),
			'default'           => '0.3',
		) );
	}

	/**
	 * Sanitize layout option.
	 *
	 * @param string $value Raw value.
	 * @return string Sanitized layout.
	 */
	public function sanitize_layout( $value ) {
		$valid = array( 'grid', 'masonry', 'slider', 'lightbox', 'cardflip' );
		return in_array( $value, $valid, true ) ? $value : 'grid';
	}

	/**
	 * Sanitize animation speed.
	 *
	 * @param string $value Raw value.
	 * @return string Sanitized speed.
	 */
	public function sanitize_animation_speed( $value ) {
		$speed = floatval( $value );
		if ( $speed < 0 || $speed > 2 ) {
			return '0.3';
		}
		return (string) $speed;
	}

	/**
	 * Render settings page.
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap rcg-settings-wrap">
			<div class="rcg-settings-header">
				<h1>
					<span class="dashicons dashicons-car"></span>
					<?php esc_html_e( 'Showcase Gallery Settings', 'car-detailers-showcase' ); ?>
				</h1>
				<p class="rcg-settings-subtitle"><?php esc_html_e( 'Configure your car gallery display preferences.', 'car-detailers-showcase' ); ?></p>
			</div>

			<form method="post" action="options.php" class="rcg-settings-form">
				<?php settings_fields( 'rcg_settings_group' ); ?>

				<div class="rcg-settings-card">
					<h2><?php esc_html_e( 'Layout Settings', 'car-detailers-showcase' ); ?></h2>

					<table class="form-table" role="presentation">
						<tr>
							<th scope="row">
								<label for="rcg_default_layout"><?php esc_html_e( 'Default Layout', 'car-detailers-showcase' ); ?></label>
							</th>
							<td>
								<select name="rcg_default_layout" id="rcg_default_layout" class="rcg-select">
									<option value="grid" <?php selected( get_option( 'rcg_default_layout' ), 'grid' ); ?>>
										<?php esc_html_e( 'Grid', 'car-detailers-showcase' ); ?>
									</option>
									<option value="masonry" <?php selected( get_option( 'rcg_default_layout' ), 'masonry' ); ?>>
										<?php esc_html_e( 'Masonry', 'car-detailers-showcase' ); ?>
									</option>
									<option value="slider" <?php selected( get_option( 'rcg_default_layout' ), 'slider' ); ?>>
										<?php esc_html_e( 'Slider / Carousel', 'car-detailers-showcase' ); ?>
									</option>
									<option value="lightbox" <?php selected( get_option( 'rcg_default_layout' ), 'lightbox' ); ?>>
										<?php esc_html_e( 'Lightbox', 'car-detailers-showcase' ); ?>
									</option>
									<option value="cardflip" <?php selected( get_option( 'rcg_default_layout' ), 'cardflip' ); ?>>
										<?php esc_html_e( 'Card Flip', 'car-detailers-showcase' ); ?>
									</option>
								</select>
								<p class="description">
									<?php esc_html_e( 'Choose the default gallery layout. Override per shortcode: [car_gallery layout="masonry"]', 'car-detailers-showcase' ); ?>
								</p>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="rcg_items_per_page"><?php esc_html_e( 'Items Per Page', 'car-detailers-showcase' ); ?></label>
							</th>
							<td>
								<input type="number" name="rcg_items_per_page" id="rcg_items_per_page"
									value="<?php echo esc_attr( get_option( 'rcg_items_per_page', 12 ) ); ?>"
									min="1" max="100" class="small-text" />
								<p class="description">
									<?php esc_html_e( 'Number of car entries to display per page.', 'car-detailers-showcase' ); ?>
								</p>
							</td>
						</tr>
					</table>
				</div>

				<div class="rcg-settings-card">
					<h2><?php esc_html_e( 'Appearance', 'car-detailers-showcase' ); ?></h2>

					<table class="form-table" role="presentation">
						<tr>
							<th scope="row">
								<label for="rcg_accent_color"><?php esc_html_e( 'Accent Color', 'car-detailers-showcase' ); ?></label>
							</th>
							<td>
								<input type="color" name="rcg_accent_color" id="rcg_accent_color"
									value="<?php echo esc_attr( get_option( 'rcg_accent_color', '#3b82f6' ) ); ?>" />
								<p class="description">
									<?php esc_html_e( 'Primary accent color for badges, buttons, and highlights.', 'car-detailers-showcase' ); ?>
								</p>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="rcg_animation_speed"><?php esc_html_e( 'Animation Speed (seconds)', 'car-detailers-showcase' ); ?></label>
							</th>
							<td>
								<input type="number" name="rcg_animation_speed" id="rcg_animation_speed"
									value="<?php echo esc_attr( get_option( 'rcg_animation_speed', '0.3' ) ); ?>"
									min="0" max="2" step="0.1" class="small-text" />
								<p class="description">
									<?php esc_html_e( 'Speed of hover and transition animations (0 = no animation).', 'car-detailers-showcase' ); ?>
								</p>
							</td>
						</tr>
					</table>
				</div>

				<div class="rcg-settings-card rcg-shortcode-reference">
					<h2><?php esc_html_e( 'Shortcode Reference', 'car-detailers-showcase' ); ?></h2>
					<div class="rcg-shortcode-examples">
						<div class="rcg-shortcode-example">
							<code>[car_gallery]</code>
							<span><?php esc_html_e( 'Default layout', 'car-detailers-showcase' ); ?></span>
						</div>
						<div class="rcg-shortcode-example">
							<code>[car_gallery layout="masonry"]</code>
							<span><?php esc_html_e( 'Masonry layout', 'car-detailers-showcase' ); ?></span>
						</div>
						<div class="rcg-shortcode-example">
							<code>[car_gallery layout="slider" count="6"]</code>
							<span><?php esc_html_e( 'Slider with 6 items', 'car-detailers-showcase' ); ?></span>
						</div>
						<div class="rcg-shortcode-example">
							<code>[car_gallery layout="lightbox" service="ceramic-coating"]</code>
							<span><?php esc_html_e( 'Lightbox filtered by service', 'car-detailers-showcase' ); ?></span>
						</div>
						<div class="rcg-shortcode-example">
							<code>[car_gallery layout="cardflip" columns="4"]</code>
							<span><?php esc_html_e( 'Card Flip with 4 columns', 'car-detailers-showcase' ); ?></span>
						</div>
					</div>
				</div>

				<?php submit_button( __( 'Save Settings', 'car-detailers-showcase' ) ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Define custom columns for car list table.
	 *
	 * @param array $columns Default columns.
	 * @return array Modified columns.
	 */
	public function set_custom_columns( $columns ) {
		$new_columns = array();
		$new_columns['cb']              = $columns['cb'];
		$new_columns['rcg_thumbnail']   = __( 'Photo', 'car-detailers-showcase' );
		$new_columns['title']           = __( 'Car Name', 'car-detailers-showcase' );
		$new_columns['rcg_work_done']   = __( 'Work Done', 'car-detailers-showcase' );
		$new_columns['rcg_photo_count'] = __( 'Photos', 'car-detailers-showcase' );
		$new_columns['date']            = $columns['date'];
		return $new_columns;
	}

	/**
	 * Render custom column content.
	 *
	 * @param string $column  Column name.
	 * @param int    $post_id Post ID.
	 */
	public function render_custom_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'rcg_thumbnail':
				$image_ids = get_post_meta( $post_id, '_rcg_gallery_images', true );
				if ( ! empty( $image_ids ) && is_array( $image_ids ) ) {
					$thumb_url = wp_get_attachment_image_url( $image_ids[0], 'thumbnail' );
					if ( $thumb_url ) {
						printf(
							'<img src="%s" alt="%s" style="width:50px;height:50px;object-fit:cover;border-radius:6px;" />',
							esc_url( $thumb_url ),
							esc_attr( get_the_title( $post_id ) )
						);
					}
				} else {
					echo '<span class="dashicons dashicons-format-image" style="color:#ccc;font-size:30px;"></span>';
				}
				break;

			case 'rcg_work_done':
				$terms = wp_get_post_terms( $post_id, 'rcg_work_done', array( 'fields' => 'names' ) );
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
					echo '<span class="rcg-admin-badge">' . esc_html( implode( ', ', $terms ) ) . '</span>';
				} else {
					echo '<span style="color:#999;">—</span>';
				}
				break;

			case 'rcg_photo_count':
				$image_ids = get_post_meta( $post_id, '_rcg_gallery_images', true );
				$count     = is_array( $image_ids ) ? count( $image_ids ) : 0;
				printf(
					'<span class="rcg-photo-count">%s</span>',
					esc_html( $count )
				);
				break;
		}
	}
}
