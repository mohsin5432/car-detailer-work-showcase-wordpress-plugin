<?php
/**
 * AJAX handler for admin operations.
 *
 * @package Car_Detailers_Showcase
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class RCG_Ajax
 */
class RCG_Ajax {

	/**
	 * Initialize AJAX hooks.
	 */
	public function init() {
		add_action( 'wp_ajax_rcg_add_service_type', array( $this, 'add_service_type' ) );
	}

	/**
	 * AJAX: Add a new service type (taxonomy term).
	 */
	public function add_service_type() {
		// Verify nonce.
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'rcg_admin_nonce' ) ) {
			wp_send_json_error( array(
				'message' => __( 'Security check failed.', 'car-detailers-showcase' ),
			) );
		}

		// Check capabilities.
		if ( ! current_user_can( 'manage_categories' ) ) {
			wp_send_json_error( array(
				'message' => __( 'You do not have permission to add service types.', 'car-detailers-showcase' ),
			) );
		}

		// Validate input.
		if ( empty( $_POST['term_name'] ) ) {
			wp_send_json_error( array(
				'message' => __( 'Please enter a service name.', 'car-detailers-showcase' ),
			) );
		}

		$term_name = sanitize_text_field( wp_unslash( $_POST['term_name'] ) );

		// Check if term already exists.
		$existing = term_exists( $term_name, 'rcg_work_done' );
		if ( $existing ) {
			wp_send_json_error( array(
				'message' => __( 'This service type already exists.', 'car-detailers-showcase' ),
			) );
		}

		// Insert the term.
		$result = wp_insert_term( $term_name, 'rcg_work_done' );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array(
				'message' => $result->get_error_message(),
			) );
		}

		wp_send_json_success( array(
			'term_id'   => $result['term_id'],
			'term_name' => $term_name,
			'message'   => sprintf(
				/* translators: %s: service type name */
				__( '"%s" has been added successfully.', 'car-detailers-showcase' ),
				$term_name
			),
		) );
	}
}
