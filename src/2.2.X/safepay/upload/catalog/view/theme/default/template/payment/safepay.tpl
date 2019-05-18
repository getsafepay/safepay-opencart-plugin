<?php if ($safepay_mode == 'sandbox') { ?>
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
		  env: '<?php echo $safepay_mode; ?>',
		  amount: <?php echo $total; ?>,
		  currency: '<?php echo $currency; ?>',
		  client: {
		    "sandbox": "<?php echo $safepay_sandbox_apikey; ?>",
		    "production": "<?php echo $safepay_apikey; ?>"
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
				url: 'index.php?route=payment/safepay/confirm',
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