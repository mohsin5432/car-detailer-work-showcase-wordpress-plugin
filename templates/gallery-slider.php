<?php
/**
 * Template: Slider / Carousel Layout
 *
 * CSS Scroll Snap horizontal carousel with navigation and auto-play.
 * Clicking any image opens the universal lightbox.
 *
 * @package Car_Detailers_Showcase
 * @var array $car_data     Array of car entries.
 * @var array $all_services All unique service types.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$slider_id = 'rcg-slider-' . wp_rand( 1000, 9999 );
?>

<div class="rcg-slider-wrapper" id="<?php echo esc_attr( $slider_id ); ?>" data-autoplay="true">
	<div class="rcg-slider-track" role="list" aria-label="<?php esc_attr_e( 'Car gallery carousel', 'car-detailers-showcase' ); ?>">
		<?php foreach ( $car_data as $car_index => $car ) : ?>
			<div class="rcg-slider-slide" role="listitem" data-index="<?php echo intval( $car_index ); ?>">
				<div class="rcg-slider-image-container rcg-open-lightbox" data-car-index="<?php echo intval( $car_index ); ?>" data-img-index="0" role="button" tabindex="0" aria-label="<?php printf( esc_attr__( 'View gallery for %s', 'car-detailers-showcase' ), $car['name'] ); ?>">
					<img
						src="<?php echo esc_url( $car['images'][0]['full'] ); ?>"
						alt="<?php echo esc_attr( $car['images'][0]['alt'] ); ?>"
						loading="<?php echo 0 === $car_index ? 'eager' : 'lazy'; ?>"
						decoding="async"
					/>
					<div class="rcg-slider-gradient"></div>
				</div>
				<div class="rcg-slider-content">
					<h3 class="rcg-slider-title"><?php echo esc_html( $car['name'] ); ?></h3>
					<div class="rcg-slider-meta">
						<?php if ( ! empty( $car['work_done'] ) ) : ?>
							<span class="rcg-badge"><?php echo esc_html( $car['work_done'] ); ?></span>
						<?php endif; ?>
						<span class="rcg-photo-badge">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
							<?php echo intval( count( $car['images'] ) ); ?>
						</span>
					</div>

					<?php if ( count( $car['images'] ) > 1 ) : ?>
						<div class="rcg-slider-thumbstrip">
							<?php foreach ( array_slice( $car['images'], 0, 5 ) as $tindex => $img ) : ?>
								<img
									src="<?php echo esc_url( $img['thumb'] ); ?>"
									alt="<?php echo esc_attr( $img['alt'] ); ?>"
									class="<?php echo 0 === $tindex ? 'rcg-thumb-active' : ''; ?> rcg-open-lightbox"
									data-car-index="<?php echo intval( $car_index ); ?>"
									data-img-index="<?php echo intval( $tindex ); ?>"
									loading="lazy"
									decoding="async"
									role="button"
									tabindex="0"
								/>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<!-- Navigation Arrows -->
	<button class="rcg-slider-nav rcg-slider-prev" aria-label="<?php esc_attr_e( 'Previous slide', 'car-detailers-showcase' ); ?>">
		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
	</button>
	<button class="rcg-slider-nav rcg-slider-next" aria-label="<?php esc_attr_e( 'Next slide', 'car-detailers-showcase' ); ?>">
		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
	</button>

	<!-- Dot Indicators -->
	<div class="rcg-slider-dots" role="tablist" aria-label="<?php esc_attr_e( 'Slide navigation', 'car-detailers-showcase' ); ?>">
		<?php foreach ( $car_data as $index => $car ) : ?>
			<button
				class="rcg-slider-dot <?php echo 0 === $index ? 'rcg-dot-active' : ''; ?>"
				role="tab"
				aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>"
				aria-label="<?php printf( esc_attr__( 'Go to slide %d', 'car-detailers-showcase' ), $index + 1 ); ?>"
				data-index="<?php echo intval( $index ); ?>"
			></button>
		<?php endforeach; ?>
	</div>
</div>
