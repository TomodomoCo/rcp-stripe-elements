<?php
/**
 * Card Update Form
 *
 * This form is displayed with the [rcp_update_card] shortcode.
 * @link http://docs.restrictcontentpro.com/article/1608-rcpupdatecard
 */
?>

<?php $member = new RCP_Member( get_current_user_id() ); ?>
<form id="rcp_update_card_form" class="rcp_form" action="" method="POST">

	<?php $cards = $member->get_card_details(); ?>

	<?php if( ! empty( $cards ) ) : ?>
		<h3><?php _e( 'Your Cards', 'rcp' ); ?></h3>
		<?php foreach( $cards as $card ) : ?>
			<fieldset class="rcp_current_cards_fieldset">
				<p>
					<span class="rcp_card_details_name"><?php _e( 'Name:', 'rcp' ); ?> <?php echo $card['name']; ?></span>
					<span class="rcp_card_details_type"><?php _e( 'Type:', 'rcp' ); ?> <?php echo $card['type']; ?></span>
					<span class="rcp_card_details_last4"><?php _e( 'Last 4:', 'rcp' ); ?> <?php echo $card['last4']; ?></span>
					<span class="rcp_card_details_exp"><?php _e( 'Exp:', 'rcp' ); ?> <?php echo $card['exp_month'] . ' / ' . $card['exp_year']; ?></span>
				</p>
			</fieldset>
		<?php endforeach; ?>
	<?php endif; ?>

</form>
