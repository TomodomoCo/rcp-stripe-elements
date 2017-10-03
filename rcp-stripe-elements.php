<?php
/*
Plugin Name: Restrict Content Pro Stripe Elements Gateway
Plugin URI: http://www.vanpattenmedia.com/
Description: Accept payments using Stripe Elements
Version: 1.0.0
Text Domain: rcp-stripe-elements
Domain Path: /languages
Author: Van Patten Media Inc.
Author URI: https://www.vanpattenmedia.com/
Contributors: chrisvanpatten, mcfarlan
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Stripe Elements with RCP
 *
 * @param array $gateways
 *
 * @return array $gateways
 */
function rcp_elements_register_stripe_elements_gateway( $gateways ) {
	$gateways['stripe_elements'] = array(
		'label'        => 'Stripe Elements',
		'admin_label'  => 'Stripe Elements',
		'class'        => 'RCP_Payment_Gateway_Stripe_Elements'
	);

	// Include RCP Stripe Elements Gateway
	$path = trailingslashit( plugin_dir_path( __FILE__ ) );
	require_once $path . 'class-rcp-payment-gateway-stripe-elements.php';

	return $gateways;
}
add_filter( 'rcp_payment_gateways', 'rcp_elements_register_stripe_elements_gateway' );