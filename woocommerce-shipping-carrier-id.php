<?php

/**
 * Plugin Name: Woocommerce Shipping Carrier ID
 * Plugin URI: https://localhost
 * Description: This plugin expand the existing fields of  Flat Rate, Free Shipping in Shipping method(s)
 * Version: 0.1
 * Author: Jarmit Goswami
 * Author URI: https://localhost
 * Tested with: 5.2.2
 * Text Domain: woo-shipping-carrier-id
 * Domain Path: /languages/
 *
 * @author   Jarmit Goswami
 */



if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! class_exists( 'WC_Shipping_Carrier_ID' ) ) {
	class WC_Shipping_Carrier_ID {
		
		/**
		 * Construct Method.
		 * @return void
		 */
		function __construct() {
			$this->init_hooks();
		}
		
		/**
		 * init hooks.
		 * @return void
		 */
		protected function init_hooks(){
			add_action( 'admin_init', array( $this, 'init' ) );
			add_action( 'woocommerce_shipping_init', array( $this, 'setup_shipping_methods' ) );
			add_action('woocommerce_order_status_processing', array( $this, 'update_order_carrier_id') );
		}
		
		/**
		 * init.
		 * @return void
		 */
		function init(){
			if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				add_action( 'admin_notices', array($this, 'woo_requirements_error' ));
				deactivate_plugins( plugin_basename( __FILE__ ) ); 
				if ( isset( $_GET['activate'] ) ) {
					unset( $_GET['activate'] );
				}
			}
		}
		
		function woo_requirements_error(){
			echo '<div id="message" class="error notice is-dismissible"> <p>Woocommerce Shipping Carrier ID : <b>Woocommerce is not active!</b></p></div>';
		}
		
		/**
		 * Update Order Meta Key _carrier_id.
		 * @return void
		 */
		 
		function update_order_carrier_id($order_id){
			$carrier_id = 0;
			
			$order = wc_get_order( $order_id );
			
			foreach( $order->get_items( 'shipping' ) as $item_id => $shipping_method ){
				if($shipping_method->get_method_id() == 'flat_rate'){
					$flat_rate = new WC_Shipping_Carrier_Flat_Rate($shipping_method->get_instance_id());
					$carrier_id = $flat_rate->get_carrier_id();
					break;
				}elseif($shipping_method->get_method_id() == 'free_shipping'){
					$flat_rate = new WC_Shipping_Carrier_Flat_Rate($shipping_method->get_instance_id());
					$carrier_id = $flat_rate->get_carrier_id();
					break;
				}
			}
			
			if($carrier_id){
				$order->update_meta_data( '_carrier_id', $carrier_id);
				$order->save();
			}
		}
		
		/**
		 * Setup Shipping Methods.
		 * @return void
		 */
		function setup_shipping_methods(){
			
			// load the classes
			require_once( plugin_basename( 'shipping/class-wc-shipping-flat-rate.php' ) );
			require_once( plugin_basename( 'shipping/class-wc-shipping-free-shipping.php' ) );
			
			// shipping_methods init
			add_filter( 'woocommerce_shipping_methods',  array( $this, 'add_carrier_id_to_shipping_method' ));

		}
		
		/**
		 * Add Carrier ID to Shipping Mmethod.
		 * @return void
		 */
		function add_carrier_id_to_shipping_method( $methods ){
			
			// check if flat_rate method is present
			if(isset($methods['flat_rate'])){
				$methods['flat_rate'] = 'WC_Shipping_Carrier_Flat_Rate';
			}
			
			// check if free_shipping is present
			if(isset($methods['free_shipping'])){
				$methods['free_shipping'] = 'WC_Shipping_Carrier_Free_Shipping';
			}
			
			return $methods;
		}
		
	}
}

$WC_Shipping_Carrier_ID = new WC_Shipping_Carrier_ID();