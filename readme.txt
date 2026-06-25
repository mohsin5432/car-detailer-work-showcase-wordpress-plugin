=== Car Detailers Showcase Gallery ===
Contributors: cardetailersshowcase
Tags: car gallery, auto detailing, car detailing, gallery, masonry, slider, lightbox, card flip
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A premium car detailing gallery plugin with 5 stunning layout designs — Grid, Masonry, Slider, Lightbox, and Card Flip.

== Description ==

**Car Detailers Showcase Gallery** is a lightweight, beautifully designed WordPress plugin built specifically for car detailing businesses. Showcase your work with 5 premium gallery layouts that will impress your clients and elevate your online presence.

= Features =

* **5 Gallery Layouts**: Grid, Masonry, Slider/Carousel, Lightbox, and Card Flip
* **Easy Car Management**: Add cars with name, service type, and multiple photos from the Media Library
* **Dynamic Service Types**: Add custom service categories on the fly (Full Detail, Paint Correction, Ceramic Coating, etc.)
* **Drag & Drop Reorder**: Arrange your gallery photos in any order
* **Filter by Service**: Let visitors filter your gallery by service type
* **Fully Responsive**: Looks great on desktops, tablets, and phones
* **Dark Theme Design**: Premium dark aesthetic with glassmorphism effects
* **Zero Dependencies**: No jQuery or external libraries on the frontend
* **Customizable**: Accent color, animation speed, and items per page
* **Keyboard Accessible**: Full keyboard navigation for lightbox and slider

= Shortcode Usage =

`[car_gallery]` — Uses default layout from settings

`[car_gallery layout="grid"]` — Grid layout
`[car_gallery layout="masonry"]` — Masonry layout
`[car_gallery layout="slider"]` — Slider/Carousel
`[car_gallery layout="lightbox"]` — Lightbox with thumbnails
`[car_gallery layout="cardflip"]` — 3D Card Flip

= Shortcode Parameters =

* `layout` — grid, masonry, slider, lightbox, or cardflip
* `count` — Number of cars to show (default: 12)
* `service` — Filter by service slug (e.g., "ceramic-coating")
* `columns` — Number of columns for grid layouts (default: 3)

== Installation ==

1. Upload the `car-detailers-showcase` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to "Showcase Gallery" in the admin menu
4. Add your car entries with photos
5. Add the `[car_gallery]` shortcode to any page or post

== Frequently Asked Questions ==

= How do I change the gallery layout? =

You can set the default layout in Showcase Gallery → Settings, or override it per shortcode using the `layout` parameter.

= Can I add custom service types? =

Yes! When editing a car entry, click the "Add New Service" button below the Work Done dropdown to create new service types on the fly.

= Does this plugin slow down my site? =

No. The plugin uses no external JavaScript libraries on the frontend. All animations are CSS-based, and images are lazy-loaded for optimal performance.

== Screenshots ==

1. Admin — Add New Car screen with photo gallery
2. Admin — Settings page
3. Frontend — Grid layout
4. Frontend — Masonry layout
5. Frontend — Slider/Carousel
6. Frontend — Lightbox
7. Frontend — Card Flip

== Changelog ==

= 1.0.0 =
* Initial release
* 5 gallery layouts: Grid, Masonry, Slider, Lightbox, Card Flip
* Custom Post Type for car entries
* Custom Taxonomy for service types
* Dynamic service type creation via AJAX
* Media Library integration for photo management
* Shortcode with layout, count, service, and columns parameters
* Settings page with accent color and animation speed options
* Responsive design with dark theme
* Keyboard accessibility for lightbox and slider
