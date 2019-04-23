<?php
defined( 'ABSPATH' ) || exit;

class XLWCTY_Components {

	protected static $components_dir;
	protected static $components = array();
	protected static $components_fields = array();
	private static $instance = null;
	public $order_data;
	public $view_data;

	public function __construct() {
		add_action( "plugins_loaded", array( $this, 'load_components' ), 1 );
	}

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public static function retrieve_components_fields() {
		return apply_filters( "XLWCTY_Component_fields", self::$components_fields );
	}

	/**
	 *
	 * @param type $component
	 *
	 * @return XLWCTY_Component
	 */
	public static function get_components( $component = false ) {
		$components = self::retrieve_components();
		if ( $component != false && array_key_exists( $component, $components ) ) {
			return $components[ $component ];
		}

		return new self;
	}

	/**
	 * @return XLWCTY_Component[]
	 */
	public static function retrieve_components() {
		return apply_filters( 'XLWCTY_Component', self::$components );
	}

	public function load_components() {
		self::$components_dir = XLWCTY_PLUGIN_DIR . '/components';
		$all_components       = array(
			'additional-information',
			'coupon-code',
			'crosssell-product',
			'customer-info',
			'html',
			'image-content',
			'join-us',
			'map',
			'order-acknowledge',
			'order-details',
			'recently-viewed-product',
			'related-product',
			'simple-text',
			'smart-bribe',
			'social-share',
			'specific-product',
			'upsell-product',
			'video',
		);
		foreach ( $all_components as $entry ) {
			$needed_file    = self::$components_dir . '/' . $entry . '/data.php';
			$component_data = include_once $needed_file;
			if ( is_array( $component_data ) && isset( $component_data['instance'] ) && is_object( $component_data['instance'] ) ) {
				$slug                             = $component_data['slug'];
				self::$components_fields[ $slug ] = $component_data['fields'];
				$component_data['instance']->set_slug( $slug );
				$component_data['instance']->set_component( $component_data );
				self::$components[ $slug ] = $component_data['instance'];
			}
		}

		do_action( 'xlwcty_after_components_loaded' );

		return self::$components_fields;
	}

	public function __call( $name, $arguments ) {

	}

}

XLWCTY_Components::get_instance();
