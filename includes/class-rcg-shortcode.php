<?php
/**
 * Shortcode registration and rendering.
 *
 * @package Car_Detailers_Showcase
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class RCG_Shortcode
 */
class RCG_Shortcode {

	/**
	 * Initialize shortcode.
	 */
	public function init() {
		add_shortcode( 'car_gallery', array( $this, 'render_shortcode' ) );
	}

	/**
	 * Render the car gallery shortcode.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Enclosed content (unused).
	 * @return string HTML output.
	 */
	public function render_shortcode( $atts = array(), $content = '' ) {
		$defaults = array(
			'layout'  => get_option( 'rcg_default_layout', 'grid' ),
			'count'   => get_option( 'rcg_items_per_page', 12 ),
			'service' => '',
			'columns' => 3,
		);

		$atts = shortcode_atts( $defaults, $atts, 'car_gallery' );

		// Sanitize attributes.
		$valid_layouts = array( 'grid', 'masonry', 'slider', 'lightbox', 'cardflip' );
		$layout        = in_array( $atts['layout'], $valid_layouts, true ) ? $atts['layout'] : 'grid';
		$count         = absint( $atts['count'] );
		$columns       = absint( $atts['columns'] );
		$service       = sanitize_text_field( $atts['service'] );

		if ( $count < 1 ) {
			$count = 12;
		}
		if ( $columns < 1 || $columns > 6 ) {
			$columns = 3;
		}

		// Enqueue frontend assets.
		$this->enqueue_frontend_assets();

		// Build query.
		$query_args = array(
			'post_type'      => 'rcg_car',
			'posts_per_page' => $count,
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		if ( ! empty( $service ) ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'rcg_work_done',
					'field'    => 'slug',
					'terms'    => $service,
				),
			);
		}

		$cars = new WP_Query( $query_args );

		if ( ! $cars->have_posts() ) {
			return '<div class="rcg-no-results">' . esc_html__( 'No car gallery entries found.', 'car-detailers-showcase' ) . '</div>';
		}

		// Prepare car data.
		$car_data = array();
		while ( $cars->have_posts() ) {
			$cars->the_post();
			$post_id   = get_the_ID();
			$image_ids = get_post_meta( $post_id, '_rcg_gallery_images', true );
			$image_ids = ! empty( $image_ids ) ? array_filter( array_map( 'absint', (array) $image_ids ) ) : array();

			$terms      = wp_get_post_terms( $post_id, 'rcg_work_done', array( 'fields' => 'all' ) );
			$work_done  = ! empty( $terms ) && ! is_wp_error( $terms ) ? $terms[0]->name : '';
			$work_slug  = ! empty( $terms ) && ! is_wp_error( $terms ) ? $terms[0]->slug : '';

			$images = array();
			foreach ( $image_ids as $img_id ) {
				$full  = wp_get_attachment_image_url( $img_id, 'large' );
				$card  = wp_get_attachment_image_url( $img_id, 'medium_large' );
				$thumb = wp_get_attachment_image_url( $img_id, 'medium' );
				$alt   = get_post_meta( $img_id, '_wp_attachment_image_alt', true );
				if ( $full ) {
					$images[] = array(
						'id'    => $img_id,
						'full'  => $full,
						'card'  => $card ? $card : $full,
						'thumb' => $thumb ? $thumb : $full,
						'alt'   => $alt ? $alt : get_the_title(),
					);
				}
			}

			if ( ! empty( $images ) ) {
				$car_data[] = array(
					'id'        => $post_id,
					'name'      => get_the_title(),
					'work_done' => $work_done,
					'work_slug' => $work_slug,
					'images'    => $images,
				);
			}
		}
		wp_reset_postdata();

		if ( empty( $car_data ) ) {
			return '<div class="rcg-no-results">' . esc_html__( 'No car gallery entries with photos found.', 'car-detailers-showcase' ) . '</div>';
		}

		// Gather all unique services for filter bar.
		$all_services = array();
		foreach ( $car_data as $car ) {
			if ( ! empty( $car['work_done'] ) && ! isset( $all_services[ $car['work_slug'] ] ) ) {
				$all_services[ $car['work_slug'] ] = $car['work_done'];
			}
		}

		// Get accent color and animation speed.
		$accent_color    = get_option( 'rcg_accent_color', '#3b82f6' );
		$animation_speed = get_option( 'rcg_animation_speed', '0.3' );

		// Start output buffering.
		ob_start();

		// Output CSS custom properties.
		printf(
			'<div class="rcg-gallery-wrapper" data-layout="%s" style="--rcg-accent: %s; --rcg-speed: %ss; --rcg-columns: %d;">',
			esc_attr( $layout ),
			esc_attr( $accent_color ),
			esc_attr( $animation_speed ),
			intval( $columns )
		);

		// Load the template.
		$template_file = RCG_PLUGIN_DIR . 'templates/gallery-' . $layout . '.php';
		if ( file_exists( $template_file ) ) {
			include $template_file;
		}

		// Always output universal lightbox dialog + data (available to all layouts).
		$this->render_universal_lightbox( $car_data );

		echo '</div>';

		return ob_get_clean();
	}

	/**
	 * Enqueue frontend CSS and JS.
	 */
	private function enqueue_frontend_assets() {
		wp_enqueue_style(
			'rcg-public-css',
			RCG_PLUGIN_URL . 'public/css/rcg-public.css',
			array(),
			RCG_VERSION
		);

		// Enqueue Google Fonts (Inter).
		wp_enqueue_style(
			'rcg-google-fonts',
			'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap',
			array(),
			null
		);

		wp_enqueue_script(
			'rcg-public-js',
			RCG_PLUGIN_URL . 'public/js/rcg-public.js',
			array(),
			RCG_VERSION,
			true
			);
	}

	/**
	 * Render the universal lightbox dialog and JSON data.
	 *
	 * This is shared across ALL layouts so clicking any image opens the lightbox.
	 *
	 * @param array $car_data Array of car entries.
	 */
	private function render_universal_lightbox( $car_data ) {
		?>
		<!-- Universal Lightbox Dialog -->
		<dialog class="rcg-lightbox-dialog" id="rcg-lightbox-dialog" aria-label="<?php esc_attr_e( 'Image lightbox', 'car-detailers-showcase' ); ?>">
			<div class="rcg-lightbox-inner">
				<div class="rcg-lightbox-image-wrap">
					<img class="rcg-lightbox-image" id="rcg-lightbox-image" src="" alt="" />

					<button class="rcg-lightbox-nav rcg-lightbox-prev" id="rcg-lightbox-prev" aria-label="<?php esc_attr_e( 'Previous image', 'car-detailers-showcase' ); ?>">
						<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
					</button>
					<button class="rcg-lightbox-nav rcg-lightbox-next" id="rcg-lightbox-next" aria-label="<?php esc_attr_e( 'Next image', 'car-detailers-showcase' ); ?>">
						<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
					</button>
				</div>

				<div class="rcg-lightbox-sidebar">
					<button class="rcg-lightbox-close" id="rcg-lightbox-close" aria-label="<?php esc_attr_e( 'Close lightbox', 'car-detailers-showcase' ); ?>">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
					</button>

					<div class="rcg-lightbox-details">
						<h3 class="rcg-lightbox-car-name" id="rcg-lightbox-car-name"></h3>
						<span class="rcg-badge rcg-lightbox-service" id="rcg-lightbox-service"></span>
						<span class="rcg-lightbox-counter" id="rcg-lightbox-counter"></span>
					</div>

					<div class="rcg-lightbox-thumbs" id="rcg-lightbox-thumbs"></div>
				</div>
			</div>
		</dialog>

		<!-- Lightbox data passed to JS -->
		<script type="application/json" id="rcg-lightbox-data">
		<?php echo wp_json_encode( $car_data ); ?>
		</script>
		<?php
	}
}
