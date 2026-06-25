<?php
/**
 * Template: Card Flip Layout
 *
 * 3D CSS Transform flip cards — front shows hero image, back shows gallery details.
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

<div class="rcg-cardflip-grid" role="list">
	<?php foreach ( $car_data as $car_index => $car ) : ?>
		<div class="rcg-cardflip-item rcg-reveal" data-service="<?php echo esc_attr( $car['work_slug'] ); ?>" role="listitem">
			<div class="rcg-cardflip-inner">
				<!-- Front Face -->
				<div class="rcg-cardflip-front">
					<img
						src="<?php echo esc_url( $car['images'][0]['full'] ); ?>"
						alt="<?php echo esc_attr( $car['images'][0]['alt'] ); ?>"
						loading="lazy"
						decoding="async"
					/>
					<div class="rcg-cardflip-front-overlay">
						<h3 class="rcg-cardflip-name"><?php echo esc_html( $car['name'] ); ?></h3>
						<?php if ( ! empty( $car['work_done'] ) ) : ?>
							<span class="rcg-badge"><?php echo esc_html( $car['work_done'] ); ?></span>
						<?php endif; ?>
					</div>
					<div class="rcg-cardflip-hint">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/></svg>
						<span><?php esc_html_e( 'Hover to flip', 'car-detailers-showcase' ); ?></span>
					</div>
				</div>

				<!-- Back Face -->
				<div class="rcg-cardflip-back">
					<div class="rcg-cardflip-back-header">
						<h3><?php echo esc_html( $car['name'] ); ?></h3>
						<?php if ( ! empty( $car['work_done'] ) ) : ?>
							<span class="rcg-badge"><?php echo esc_html( $car['work_done'] ); ?></span>
						<?php endif; ?>
					</div>

					<div class="rcg-cardflip-back-gallery">
						<?php foreach ( array_slice( $car['images'], 0, 6 ) as $img_idx => $img ) : ?>
							<img
								src="<?php echo esc_url( $img['thumb'] ); ?>"
								alt="<?php echo esc_attr( $img['alt'] ); ?>"
								loading="lazy"
								decoding="async"
								class="rcg-open-lightbox"
								data-car-index="<?php echo intval( $car_index ); ?>"
								data-img-index="<?php echo intval( $img_idx ); ?>"
								role="button"
								tabindex="0"
								aria-label="<?php printf( esc_attr__( 'View photo %d of %s', 'car-detailers-showcase' ), $img_idx + 1, $car['name'] ); ?>"
							/>
						<?php endforeach; ?>
					</div>

					<div class="rcg-cardflip-back-footer">
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
			</div>
		</div>
	<?php endforeach; ?>
</div>
