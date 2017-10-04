<?php
/**
 * Payment Gateway For Stripe Elements
 *
 * @package     Restrict Content Pro Stripe Elements Gateway
 * @subpackage  Classes/Gateways/Stripe Elements
 * @author      Chris Van Patten <chris@vanpattenmedia.com>
 * @author      Ian McFarlan <ian@vanpattenmedia.com>
 * @copyright   Copyright (c) 2017, Van Patten Media
 * @license     https://github.com/TomodomoCo/rcp-stripe-elements/blob/master/LICENSE GNU General Public License v2.0
 * @since       1.0.0
 */

class RCP_Payment_Gateway_Stripe_Elements extends RCP_Payment_Gateway_Stripe {

	/**
	 * Print fields for this gateway
	 *
	 * @return string
	 */
	public function fields() {

		/**
		 * Pass custom styles using `rcp_stripe_elements_form_data`
		 *
		 * @see style options: https://stripe.com/docs/stripe.js#element-options
		 */
		$data = apply_filters( 'rcp_stripe_elements_form_data', array(
			'style' => array(
				'base' => array(
					'fontFamily' => 'Arial, sans-serif',
					'fontSize'   => '16px',
					'color'      => 'black',
				),
				'invalid' => array(
					'color' => 'red',
				),
			),
		) );

		ob_start(); ?>

		<div class="form-row">
			<label for="card-element"><?php _e( 'Credit or debit card', 'rcp' ); ?></label>
			<div id="card-element" class="rcp-stripe-elements-input"></div>
			<div id="card-errors" role="alert"></div>
		</div>
		<br>

		<script>
			var stripe, elements, card, elementsArgs;

			// Load Stripe Elements
			stripe       = Stripe('<?php echo $this->publishable_key; ?>');
			elements     = stripe.elements();
			elementsArgs = <?php echo json_encode( $data ); ?>;

			// Create and mount the card
			card = elements.create( 'card', elementsArgs );
			card.mount( '#card-element' );

			// Listen for errors during type in Stripe Elements
			card.addEventListener( 'change', function ( event ) {
				var displayError = document.getElementById( 'card-errors' );

				// Errors present
				if ( event.error ) {
					displayError.textContent = event.error.message;

					var submission_form = jQuery( '#rcp_registration_form' );
					submission_form.unblock();

				// No errors, pass blank string
				} else {
					displayError.textContent = '';
				}
			});

			// Map token from Stripe Elements to hidden element
			function stripeTokenHandler( token ) {
				var form, hiddenInput;

				// Insert the token ID into the form so it gets submitted to the server
				form        = document.getElementById( 'rcp_registration_form' );
				hiddenInput = document.createElement( 'input' );

				// Assign attributes to hidden field
				hiddenInput.setAttribute( 'type', 'hidden' );
				hiddenInput.setAttribute( 'name', 'stripeToken' );
				hiddenInput.setAttribute( 'value', token.id );
				hiddenInput.setAttribute( 'id', 'stripeToken' );

				form.appendChild( hiddenInput );
				form.submit();
			}

			/**
			 * 'rcp_register_form_submission' is triggered in register.js
			 * if the form data is successfully validated.
			 */
			jQuery( 'body' ).off( 'rcp_register_form_submission' ).on( 'rcp_register_form_submission', function( e, response, form_id ) {

				// Bail early if not stripe elements
				if ( response.gateway.slug !== 'stripe_elements' ) {
					return;
				}

				// Discounted at 100%, pass without token/charge to avoid JS error
				if ( jQuery( '.rcp_gateway_fields' ).hasClass( 'rcp_discounted_100' ) ) {
					return true;
				}

				// Create a Stripe token
				stripe.createToken( card ).then( function( result ) {

					// Errors present
					if ( result.error ) {
						var errorElement = document.getElementById( 'card-errors' );
						errorElement.textContent = result.error.message;

					// Token created, ready to send to server
					} else {
						stripeTokenHandler( result.token );
					}
				});

			} );
		</script>

		<?php
		return ob_get_clean();
	}

	/**
	 * Load Stripe Elements JS; dequeue Stripe Checkout JS
	 *
	 * @return void
	 */
	public function scripts() {
		wp_enqueue_script( 'stripe-elements', 'https://js.stripe.com/v3/' );
	}

	/**
	 * Validate fields - empty as gateway does validation
	 *
	 * @return void
	 */
	public function validate_fields() {}
}
