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
	// Exit early if the stripe_elements gateway or class already exist
	if ( isset( $gateways['stripe_elements'] ) ) {
		return $gateways;
	}

	// Define the stripe_elements gateway
	$gateways['stripe_elements'] = array(
		'label'        => 'Stripe Elements',
		'admin_label'  => 'Stripe Elements',
		'class'        => 'RCP_Payment_Gateway_Stripe_Elements'
	);

	// Include RCP Stripe Elements Gateway
	if ( ! class_exists( 'RCP_Payment_Gateway_Stripe_Elements' ) ) {
		$path = trailingslashit( plugin_dir_path( __FILE__ ) );
		require_once $path . 'class-rcp-payment-gateway-stripe-elements.php';
	}

	return $gateways;
}
add_filter( 'rcp_payment_gateways', 'rcp_elements_register_stripe_elements_gateway' );

/**
 * Custom path for RCP template overrides
 *
 * @param array  $template_stack
 * @param string $template_names
 *
 * @return array $template_stack
 */
function rcp_elements_custom_template_path( $template_stack, $template_names ) {
	$template_stack[] = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'templates/';

	return $template_stack;
}
add_filter( 'rcp_template_stack', 'rcp_elements_custom_template_path', 10, 2 );

/**
 * Conditionally loads Stripe Elements JS for the `update card` page
 *
 * @return void
 */
function rcp_elements_load_scripts() {

	// Bail early if scripts shouldn't be loaded
	if ( rcp_elements_is_update_card_page() === false ) {
		return;
	}

	$gateway = new RCP_Payment_Gateway_Stripe_Elements();
	$gateway->scripts();
}
add_action( 'wp_enqueue_scripts', 'rcp_elements_load_scripts', 10, 0 );

/**
 * Checks if the current page is the RCP update card page
 *
 * @return bool
 */
function rcp_elements_is_update_card_page() {
	global $rcp_options, $post;

	if ( isset( $rcp_options['update_card'] ) ) {
		return is_page( $rcp_options['update_card'] );
	}

	if ( ! empty( $post ) && has_shortcode( $post->post_content, 'rcp_update_card' ) ) {
		return true;
	}

	return false;
}