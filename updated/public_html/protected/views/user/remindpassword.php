<?php
include dirname(__FILE__).'/css/temporary.php';
?>
<div id="bx-content">
    <div id="subpage-content">
        <div id="login-box2">
            <h2>
                Forgotten Login Details</h2>
				<?php if($data == 1) { ?>
					<p class="message_ok">
					We have emailed you instructions on how to reset your password.</p>
				<?php } ?>
            <form id="login-form-forgot" method="post" action="/user/remindpassword">
            <p>
                Enter the email address you used to signup below</p>
				<?php if($data == 2) { ?>
					<p class="message_err">This email address could not be found. If you believe this to be in error, please contact support.</p>
				<?php } ?>
            <div class="smallform">
                <fieldset id="fields">
                    <input id="f_email"
                        placeholder="Your Email Address"
                        name="f_email"
                        value="" type="text">
                    <input id="reset-password-button" src="/images/img/rst.png" type="image">
                    <p class="forgotten-pass-link">
                        <a href="<?php echo TICKET_URL ?>" target="_blank">Need Help?</a></p>
                </fieldset>
            </div>
            </form>
        </div>
    </div>
    <div class="clear">
    </div>
</div>
<script type="text/javascript">
$(function() {
   $('#login-form-forgot').submit(function() {
       if ($('#f_email').val() == '') {
           $('#f_email').focus();
           return false;
       }
       return true;
   })
});
</script>
