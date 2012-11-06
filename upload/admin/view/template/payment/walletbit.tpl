<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/payment.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td><?php echo $entry_test; ?></td>
            <td><?php if ($walletbit_test) { ?>
              <input type="radio" name="walletbit_test" value="1" checked="checked" />
              <?php echo $text_yes; ?>
              <input type="radio" name="walletbit_test" value="0" />
              <?php echo $text_no; ?>
              <?php } else { ?>
              <input type="radio" name="walletbit_test" value="1" />
              <?php echo $text_yes; ?>
              <input type="radio" name="walletbit_test" value="0" checked="checked" />
              <?php echo $text_no; ?>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_email; ?></td>
            <td><input type="text" name="walletbit_email" value="<?php echo $walletbit_email; ?>" />
              <?php if ($error_email) { ?>
              <span class="error"><?php echo $error_email; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_token; ?></td>
            <td><input type="text" name="walletbit_token" value="<?php echo $walletbit_token; ?>" size="40" />
              <?php if ($error_token) { ?>
              <span class="error"><?php echo $error_token; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_securityword; ?></td>
            <td><input type="password" name="walletbit_securityword" value="<?php echo $walletbit_securityword; ?>" />
              <?php if ($error_securityword) { ?>
              <span class="error"><?php echo $error_securityword; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_exchangerate; ?></td>
            <td>
				<?php echo $entry_exchangerate_text; ?>
				<br />
				$<input type="text" name="walletbit_exchangerate" value="<?php echo $walletbit_exchangerate; ?>" size="1" /> Current exchange rate: $<?php echo $current_exchange_rate; ?>
			</td>
          </tr>
          <tr>
            <td><?php echo $entry_status; ?></td>
            <td><select name="walletbit_status">
                <?php if ($walletbit_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_sort_order; ?></td>
            <td><input type="text" name="walletbit_sort_order" value="<?php echo $walletbit_sort_order; ?>" size="1" /></td>
          </tr>
        </table>
		<b><?php echo $entry_ipn_text; ?></b>
		<br /><br />
		<?php echo $entry_ipn_url; ?>
      </form>
    </div>
  </div>
</div>
<?php echo $footer; ?> 