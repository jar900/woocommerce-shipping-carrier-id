<?php
/**
 * Free Shipping Method.
 * 
 * @version 2.6.0
 * @package WooCommerce/Classes/Shipping
 */

defined( 'ABSPATH' ) || exit;

/**
 *
 * WC_Shipping_Carrier_Free_Shipping class.
 *
 */
 
class WC_Shipping_Carrier_Free_Shipping extends WC_Shipping_Free_Shipping {
	
	protected $carrier_id;
	
	public function __construct( $instance_id = 0 ) {
		parent::__construct($instance_id);
	}
	
	public function init() {
		parent::init();
		$this->instance_form_fields['carrier_id'] = array(
			'title'             => __( 'Carrier ID', 'woocommerce' ),
			'type'              => 'text',
			'placeholder'       => __( 'Carrier ID', 'woocommerce' ),
			'description'       => '',
			'default'           => '',
			'desc_tip'          => true,
			'sanitize_callback' => array( $this, 'sanitize_cost' ),
		);
		
		$this->carrier_id = $this->get_option( 'carrier_id' );
	}
	
	public function get_carrier_id(){
		return $this->carrier_id;
	}
}
