<?php

class BigMagicAI { 
    private static $initiated = false;

    public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
	}
 
    /**
	 * Initializes WordPress hooks
	 */
	private static function init_hooks() {
		self::$initiated = true;

        // @TODO unsure why this isn't working
        // add_action( 'init', 'create_bigmagicai_block' );
	}

    public static function get_api_key() {
		return apply_filters( 'bigmagicai_get_api_key', get_option('bigmagicai_plugin_options')['openai_api_key'] );
	}

    /**
	 * Registers a block type:
     * The recommended way is to register a block type using the metadata stored in the block.json file
	 * https://developer.wordpress.org/reference/functions/register_block_type/
	 */
	public static function create_bigmagicai_block() {
		register_block_type( plugin_dir_path( __FILE__ ) . 'build/correct-english-grammar/' );
		register_block_type( plugin_dir_path( __FILE__ ) . 'build/generate-content/' );
		register_block_type( plugin_dir_path( __FILE__ ) . 'build/generate-image/' );
		register_block_type( plugin_dir_path( __FILE__ ) . 'build/translate/' );
	}

}