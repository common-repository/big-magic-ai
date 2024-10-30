<?php

class BigMagicAI_REST_API {
	/**
	 * Register the REST API routes.
	 */
	public static function bigmagicai_init() {
		if ( ! function_exists( 'register_rest_route' ) ) {
			// The REST API wasn't integrated into core until 4.4, and we support 4.0+ (for now).
			return false;
		}
        
        register_rest_route( 'openai/v1', '/key', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( 'BigMagicAI_REST_API', 'get_key' ),
			'permission_callback' => function() {
				return current_user_can('edit_others_posts');
			}
        ) );
	}

	/**
	 * Get the current Open AI API key.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function get_key( $request = null ) {
		return rest_ensure_response( BigMagicAI::get_api_key() );
	}
}
