/**
 * Car Detailers Showcase Gallery — Admin JavaScript
 *
 * Handles media library integration, sortable gallery, and dynamic service dropdown.
 *
 * @package Car_Detailers_Showcase
 */

(function ($) {
	'use strict';

	/**
	 * Media Library — multi-select frame for adding photos.
	 */
	function initMediaUploader() {
		var $addBtn    = $('#rcg-add-photos-btn');
		var $preview   = $('#rcg-gallery-preview');
		var $idsInput  = $('#rcg-gallery-ids');
		var mediaFrame;

		if (!$addBtn.length) {
			return;
		}

		$addBtn.on('click', function (e) {
			e.preventDefault();

			if (mediaFrame) {
				mediaFrame.open();
				return;
			}

			mediaFrame = wp.media({
				title: rcgAdmin.mediaTitle,
				button: { text: rcgAdmin.mediaButton },
				multiple: true,
				library: { type: 'image' },
			});

			mediaFrame.on('select', function () {
				var attachments = mediaFrame.state().get('selection').toJSON();
				var currentIds  = getImageIds();

				attachments.forEach(function (attachment) {
					// Skip duplicates.
					if (currentIds.indexOf(attachment.id) !== -1) {
						return;
					}

					currentIds.push(attachment.id);

					var thumbUrl = attachment.sizes && attachment.sizes.thumbnail
						? attachment.sizes.thumbnail.url
						: attachment.url;

					var $item = $(
						'<div class="rcg-gallery-item" data-id="' + attachment.id + '">' +
							'<img src="' + thumbUrl + '" alt="" />' +
							'<button type="button" class="rcg-remove-img" title="Remove">' +
								'<span class="dashicons dashicons-no-alt"></span>' +
							'</button>' +
						'</div>'
					);

					$preview.append($item);
				});

				updateImageIds();
			});

			mediaFrame.open();
		});

		// Remove image.
		$preview.on('click', '.rcg-remove-img', function (e) {
			e.preventDefault();
			$(this).closest('.rcg-gallery-item').fadeOut(200, function () {
				$(this).remove();
				updateImageIds();
			});
		});

		// Sortable.
		$preview.sortable({
			items: '.rcg-gallery-item',
			cursor: 'grabbing',
			opacity: 0.7,
			tolerance: 'pointer',
			placeholder: 'ui-sortable-placeholder',
			update: function () {
				updateImageIds();
			},
		});

		/**
		 * Collect all image IDs in order.
		 */
		function getImageIds() {
			var ids = [];
			$preview.find('.rcg-gallery-item').each(function () {
				ids.push(parseInt($(this).data('id'), 10));
			});
			return ids;
		}

		/**
		 * Update hidden input with current image IDs.
		 */
		function updateImageIds() {
			$idsInput.val(getImageIds().join(','));
		}
	}

	/**
	 * Dynamic "Add New Service" functionality.
	 */
	function initAddService() {
		var $btn    = $('#rcg-add-service-btn');
		var $select = $('#rcg_work_done_select');

		if (!$btn.length || !$select.length) {
			return;
		}

		$btn.on('click', function (e) {
			e.preventDefault();

			var termName = prompt(rcgAdmin.addServicePrompt);

			if (!termName || !termName.trim()) {
				return;
			}

			termName = termName.trim();

			$btn.prop('disabled', true).text('Adding...');

			$.ajax({
				url: rcgAdmin.ajaxUrl,
				type: 'POST',
				data: {
					action: 'rcg_add_service_type',
					nonce: rcgAdmin.nonce,
					term_name: termName,
				},
				success: function (response) {
					if (response.success) {
						// Add new option and select it.
						var $option = $('<option></option>')
							.val(response.data.term_id)
							.text(response.data.term_name);

						$select.append($option);
						$select.val(response.data.term_id);

						// Brief success feedback.
						$btn.text('✓ Added!');
						setTimeout(function () {
							resetBtn();
						}, 1500);
					} else {
						alert(response.data.message || 'Error adding service type.');
						resetBtn();
					}
				},
				error: function () {
					alert('Network error. Please try again.');
					resetBtn();
				},
			});

			function resetBtn() {
				$btn.prop('disabled', false).html(
					'<span class="dashicons dashicons-plus-alt2"></span> Add New Service'
				);
			}
		});
	}

	/**
	 * Initialize on DOM ready.
	 */
	$(document).ready(function () {
		initMediaUploader();
		initAddService();
	});

})(jQuery);
