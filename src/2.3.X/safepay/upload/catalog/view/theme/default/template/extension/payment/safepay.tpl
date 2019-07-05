<?php if ($payment_safepay_mode == 'sandbox') { ?>
<div class="alert alert-danger">
	<?php echo $text_sandbox_mode_enable; ?>
</div>
<?php } ?>
<div class="buttons">
  <div class="pull-right">
  	<div id="open-cart-payment-option-safepay"></div>
  </div>
</div>
<script type="text/javascript"><!--
	$( document ).ready(function() {
		safepay.Button.render({
		  env: '<?php echo $payment_safepay_mode; ?>',
		  amount: <?php echo $total; ?>,
		  currency: '<?php echo $currency; ?>',
		  client: {
		    "sandbox": "<?php echo $payment_safepay_sandbox_apikey; ?>",
		    "production": "<?php echo $payment_safepay_apikey; ?>"
		  },
			customer: {
				"first_name": "<?php echo $order_info['firstname']; ?>",
				"last_name": "<?php echo $order_info['lastname']; ?>",
				"phone": "<?php echo $order_info['telephone']; ?>",
				"email": "<?php echo $order_info['email']; ?>",
			},
			billing: {
				"name": "<?php echo $order_info['payment_firstname']; ?> - Billing Address",
				"address_1":"<?php echo $order_info['payment_address_1']; ?>",
				"address_2": "<?php echo $order_info['payment_address_2']; ?>",
				"city": "<?php echo $order_info['payment_city']; ?>",
				"province": "<?php echo $province; ?>",
				"postal": "<?php echo $order_info['payment_postcode']; ?>",
				"country": "<?php echo $country; ?>",
			},
		  payment: function (data, actions) {
		    return actions.payment.create({
		      transaction: {
		        amount: <?php echo $total; ?>,
		        currency: '<?php echo $currency; ?>'
		      }
		    })
		  },
		  onCancel: function (data, actions) {
		    // Do nothing.
		  },
		  onCheckout: function(data, actions) {
		  	// Log the results.
			var data = {
				'amount': data.amount, 
				'client': data.client, 
				'created_at': data.created_at, 
				'currency': data.currency, 
				'fees': data.fees, 
				'net': data.net, 
				'reference': data.reference, 
				'token': data.token, 
				'tracker': data.tracker, 
				'updated_at': data.updated_at, 
				'user': data.user
			};
			$.ajax({
				url: 'index.php?route=extension/payment/safepay/confirm',
				dataType: 'json',
				data: data,
				type: 'post',
				success: function(json) {
					if (json['redirect']) {
						location = json['redirect'];	
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		  }
		}, '#open-cart-payment-option-safepay');
	});
//--></script>