<?php
/**
 * Template: Masonry Layout
 *
 * CSS multi-column masonry with progressive enhancement for native grid masonry.
 * Clicking any image opens the universal lightbox.
 *
 * @package Car_Detailers_Showcase
 * @var array $car_data     Array of car entries.
 * @var array $all_services All unique service types.
 * @var int   $columns      Number of columns.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php if ( ! empty( $all_services ) ) : ?>
<div class="rcg-filter-bar" role="navigation" aria-label="<?php esc_attr_e( 'Filter by service', 'car-detailers-showcase' ); ?>">
	<button class="rcg-filter-pill rcg-filter-active" data-filter="all">
		<?php esc_html_e( 'All', 'car-detailers-showcase' ); ?>
	</button>
	<?php foreach ( $all_services as $slug => $name ) : ?>
		<button class="rcg-filter-pill" data-filter="<?php echo esc_attr( $slug ); ?>">
			<?php echo esc_html( $name ); ?>
		</button>
	<?php endforeach; ?>
</div>
<?php endif; ?>

<div class="rcg-masonry" role="list">
	<?php foreach ( $car_data as $car_index => $car ) : ?>
		<article class="rcg-masonry-item rcg-reveal" data-service="<?php echo esc_attr( $car['work_slug'] ); ?>" role="listitem">
			<div class="rcg-masonry-hero rcg-open-lightbox" data-car-index="<?php echo intval( $car_index ); ?>" data-img-index="0" role="button" tabindex="0" aria-label="<?php printf( esc_attr__( 'View gallery for %s', 'car-detailers-showcase' ), $car['name'] ); ?>">
				<img
					src="<?php echo esc_url( $car['images'][0]['full'] ); ?>"
					alt="<?php echo esc_attr( $car['images'][0]['alt'] ); ?>"
					loading="lazy"
					decoding="async"
				/>
				<div class="rcg-masonry-info">
					<h3 class="rcg-masonry-title"><?php echo esc_html( $car['name'] ); ?></h3>
					<?php if ( ! empty( $car['work_done'] ) ) : ?>
						<span class="rcg-badge"><?php echo esc_html( $car['work_done'] ); ?></span>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( count( $car['images'] ) > 1 ) : ?>
				<div class="rcg-masonry-extra">
					<?php foreach ( array_slice( $car['images'], 1, 3 ) as $extra_idx => $img ) : ?>
						<img
							src="<?php echo esc_url( $img['thumb'] ); ?>"
							alt="<?php echo esc_attr( $img['alt'] ); ?>"
							loading="lazy"
							decoding="async"
							class="rcg-open-lightbox"
							data-car-index="<?php echo intval( $car_index ); ?>"
							data-img-index="<?php echo intval( $extra_idx + 1 ); ?>"
							role="button"
							tabindex="0"
						/>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</article>
	<?php endforeach; ?>
</div>
