<?php
/**
 * Template: Grid Layout
 *
 * Responsive CSS Grid with hover overlay revealing car details.
 * Clicking any image opens the universal lightbox.
 *
 * @package Car_Detailers_Showcase
 * @var array $car_data     Array of car entries.
 * @var array $all_services All unique service types.
 * @var int   $columns      Number of grid columns.
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

<div class="rcg-grid" role="list">
	<?php foreach ( $car_data as $car_index => $car ) : ?>
		<article class="rcg-grid-card rcg-reveal" data-service="<?php echo esc_attr( $car['work_slug'] ); ?>" role="listitem">
			<div class="rcg-grid-card-image rcg-open-lightbox" data-car-index="<?php echo intval( $car_index ); ?>" data-img-index="0" role="button" tabindex="0" aria-label="<?php printf( esc_attr__( 'View gallery for %s', 'car-detailers-showcase' ), $car['name'] ); ?>">
				<?php
				$hero_srcset = wp_get_attachment_image_srcset( $car['images'][0]['id'], 'medium_large' );
				$hero_sizes  = '(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw';
				?>
				<img
					src="<?php echo esc_url( ! empty( $car['images'][0]['card'] ) ? $car['images'][0]['card'] : $car['images'][0]['full'] ); ?>"
					alt="<?php echo esc_attr( $car['images'][0]['alt'] ); ?>"
					<?php if ( ! empty( $hero_srcset ) ) : ?>srcset="<?php echo esc_attr( $hero_srcset ); ?>" sizes="<?php echo esc_attr( $hero_sizes ); ?>"<?php endif; ?>
					loading="lazy"
					decoding="async"
				/>
				<div class="rcg-grid-card-overlay">
					<h3 class="rcg-grid-card-title"><?php echo esc_html( $car['name'] ); ?></h3>
					<?php if ( ! empty( $car['work_done'] ) ) : ?>
						<span class="rcg-badge"><?php echo esc_html( $car['work_done'] ); ?></span>
					<?php endif; ?>
					<span class="rcg-photo-badge">
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
						<?php
						printf(
							/* translators: %d: number of photos */
							esc_html( _n( '%d Photo', '%d Photos', count( $car['images'] ), 'car-detailers-showcase' ) ),
							count( $car['images'] )
						);
						?>
					</span>
				</div>
			</div>

			<?php if ( count( $car['images'] ) > 1 ) : ?>
				<div class="rcg-grid-card-thumbs">
					<?php foreach ( array_slice( $car['images'], 1, 4 ) as $thumb_idx => $img ) : ?>
						<img
							src="<?php echo esc_url( $img['thumb'] ); ?>"
							alt="<?php echo esc_attr( $img['alt'] ); ?>"
							loading="lazy"
							decoding="async"
							class="rcg-open-lightbox"
							data-car-index="<?php echo intval( $car_index ); ?>"
							data-img-index="<?php echo intval( $thumb_idx + 1 ); ?>"
							role="button"
							tabindex="0"
						/>
					<?php endforeach; ?>
					<?php if ( count( $car['images'] ) > 5 ) : ?>
						<span class="rcg-more-count rcg-open-lightbox" data-car-index="<?php echo intval( $car_index ); ?>" data-img-index="5" role="button" tabindex="0">+<?php echo intval( count( $car['images'] ) - 5 ); ?></span>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</article>
	<?php endforeach; ?>
</div>
