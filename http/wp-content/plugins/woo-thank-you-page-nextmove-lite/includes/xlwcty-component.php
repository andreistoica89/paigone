<?php
defined( 'ABSPATH' ) || exit;

abstract class XLWCTY_Component {

	public static $order_meta_data = array();
	public static $component_css = array();
	private static $instance = null;
	public $viewpath = "";
	public $campaign_data = array();
	public $instance_campaign_data;
	public $data = "";
	public $is_multiple = false;
	public $fields = array();
	public $current_index = false;
	protected $slug = "";

	public function __construct( $order = false ) {
		$this->data = new stdClass();
		$this->initiate_fields();
		add_action( 'xlwcty_page_meta_setup_completed', array( $this, 'prepare_out_put_data' ) );

		add_action( "xlwcty_after_components_loaded", array( $this, "setup_fields" ) );
	}

	public function initiate_fields() {
		$fields = $this->fields;

		foreach ( $fields as $key => $val ) {
			$this->data->{$key} = "";
		}
	}

	public static function push_css( $component, $component_css ) {
		if ( $component != "" && $component_css != "" ) {
			if ( ! isset( self::$component_css[ $component ] ) ) {
				self::$component_css[ $component ] = array();
			}
			self::$component_css[ $component ] = $component_css;
		}
	}

	public static function get_css( $component = "" ) {
		if ( array_key_exists( $component, self::$component_css ) ) {
			return self::$component_css[ $component ];
		}

		return self::$component_css;
	}

	final public function wc_version() {
		return WC()->version;
	}

	public function component_script() {

	}

	public function setup_fields() {

	}

	public function is_enable( $index = 0 ) {
		if ( ! $this->has_multiple_fields() ) {
			if ( XLWCTY_Core()->data->get_meta( $this->get_slug() . "_enable", "raw" ) == "1" ) {
				return true;
			}
		} else {
			if ( XLWCTY_Core()->data->get_meta( $this->get_slug() . "_enable_" . $index, "raw" ) == "1" ) {
				return true;
			}
		}

		return false;
	}

	public function has_multiple_fields() {
		return false;
		if ( isset( $this->_component['fields']['is_multiple'] ) && $this->_component['fields']['is_multiple'] === true && isset( $this->_component['fields']['count'] ) && $this->_component['fields']['count'] > 0 ) {
			return true;
		}

		return false;
	}

	public function get_slug() {
		return $this->slug;
	}

	public function set_slug( $slug = "" ) {
		$this->slug = $slug;
	}

	public function prepare_out_put_data() {
		$get_values = XLWCTY_Core()->data->get_meta( $this->get_slug() );
		if ( is_array( $get_values ) && count( $get_values ) > 0 ) {
			if ( $this->has_multiple_fields() ) {
				foreach ( $get_values as $key => $values ) {
					$this->data->{$key} = new stdClass();
					foreach ( $values as $key_meta => $val ) {
						$this->data->{$key}->{$key_meta} = maybe_unserialize( $val );
					}
					do_action( "xlwcty_after_component_data_setup" . $this->get_slug(), $this->get_slug(), $key );
				}
			} else {
				foreach ( $get_values as $key => $val ) {
					$this->data->{$key} = maybe_unserialize( $val );
				}
				do_action( "xlwcty_after_component_data_setup" . $this->get_slug(), $this->get_slug() );
			}
		}
	}

	public function get_highest_order_product() {
		$max_product = array();
		$maxs        = array();
		$product     = array();
		$order_data  = $order = XLWCTY_Core()->data->get_order();
		if ( $order instanceof WC_Order ) {
			foreach ( $order_data->get_items() as $key => $val ) {
				$pro                 = XLWCTY_Compatibility::get_product_from_item( $order_data, $val );
				$pid                 = $pro->get_id();
				$product[ $pid ]     = $pro;
				$max_product[ $pid ] = XLWCTY_Compatibility::get_item_subtotal( $order_data, $val );
			}
			if ( is_array( $max_product ) && count( $max_product ) > 0 ) {
				$maxs = array_keys( $max_product, max( $max_product ) );
			}
		}

		return $maxs;
	}

	public function get_wp_date_format() {
		return get_option( "date_format", "Y-m-d" );
	}

	public function render_view( $slug ) {
		if ( $slug !== $this->get_slug() && $this->has_multiple_fields() ) {
			$this->current_index = str_replace( $this->get_slug() . "_", "", $slug );
		}
		$this->get_view();
	}

	public function get_view() {
		$order_data = $this->get_view_data();
		extract( $order_data );
		if ( file_exists( $this->viewpath ) ) {
			include $this->viewpath;
		}
	}

	public function get_view_data( $key = 'order' ) {
		$order = XLWCTY_Core()->data->get_order();
		if ( $order instanceof WC_Order ) {
			return array( "campaign_data" => $this->instance_campaign_data, "order_data" => $order );
		} else {
			return array( "order_id" => 0 );
		}
	}

	public function get_data( $key = 'order' ) {
		if ( $key != "" ) {
			return self::$order_meta_data[ $key ];
		}

		return self::$order_meta_data;
	}

	public function get_defaults() {
		if ( $this->_component === false ) {
			return array();
		}

		return ( isset( $this->_component['default'] ) ? $this->_component['default'] : array() );
	}

	public function set_component( $component ) {
		$this->_component = $component;
	}

	public function get_component() {
		return $this->_component;
	}

	public function get_component_property( $property ) {
		$component = $this->get_component();

		return isset( $component[ $property ] ) ? $component[ $property ] : "";
	}

	public static function save_original_content( $original_value, $args, $cmb2_field ) {
		return $original_value; // Unsanitized value.
	}

	public function get_title() {
		return $this->get_component_property( 'title' );
	}

}
