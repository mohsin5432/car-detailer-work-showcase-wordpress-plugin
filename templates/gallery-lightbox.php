<?php
/**
 * Template: Lightbox Layout
 *
 * Thumbnail grid — clicking opens the universal lightbox.
 * The lightbox dialog itself is rendered by the shortcode class.
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

<div class="rcg-lightbox-grid" role="list">
	<?php foreach ( $car_data as $car_index => $car ) : ?>
		<article
			class="rcg-lightbox-card rcg-reveal"
			data-service="<?php echo esc_attr( $car['work_slug'] ); ?>"
			data-car-index="<?php echo intval( $car_index ); ?>"
			role="listitem"
		>
			<?php
			$card_srcset = wp_get_attachment_image_srcset( $car['images'][0]['id'], 'medium_large' );
			$card_sizes  = '(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw';
			?>
			<div
				class="rcg-lightbox-trigger rcg-open-lightbox"
				role="button"
				tabindex="0"
				aria-label="<?php printf( esc_attr__( 'View gallery for %s', 'car-detailers-showcase' ), $car['name'] ); ?>"
				data-car-index="<?php echo intval( $car_index ); ?>"
				data-img-index="0"
			>
				<img
					src="<?php echo esc_url( ! empty( $car['images'][0]['card'] ) ? $car['images'][0]['card'] : $car['images'][0]['full'] ); ?>"
					alt="<?php echo esc_attr( $car['images'][0]['alt'] ); ?>"
					<?php if ( ! empty( $card_srcset ) ) : ?>srcset="<?php echo esc_attr( $card_srcset ); ?>" sizes="<?php echo esc_attr( $card_sizes ); ?>"<?php endif; ?>
					loading="lazy"
					decoding="async"
				/>
				<div class="rcg-lightbox-card-overlay">
					<div class="rcg-lightbox-card-icon">
						<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
							<line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/>
						</svg>
					</div>
					<h3 class="rcg-lightbox-card-title"><?php echo esc_html( $car['name'] ); ?></h3>
					<?php if ( ! empty( $car['work_done'] ) ) : ?>
						<span class="rcg-badge"><?php echo esc_html( $car['work_done'] ); ?></span>
					<?php endif; ?>
				</div>
			</div>
		</article>
	<?php endforeach; ?>
</div>
