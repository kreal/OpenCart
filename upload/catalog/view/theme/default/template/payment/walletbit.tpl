<?php if ($testmode) { ?>
<div class="warning"><?php echo $text_testmode; ?></div>
<?php } ?>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script type="text/javascript">
  (function() {
	var wb = document.createElement('script'); wb.type = 'text/javascript'; wb.async = true;
	wb.src = 'https://walletbit.com/pay/<?php echo $token; ?>?url=' + escape(parent.location.href);
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(wb, s);
  })();
</script>

<div class="buttons">
	<div class="right"><a rel="<?php echo $total; ?>" target="<?php echo $custom; ?>" test="<?php echo $testmode; ?>" href="<?php echo $return; ?>" class="WalletBitButton"><?php echo $item_name; ?></a></div>
</div>