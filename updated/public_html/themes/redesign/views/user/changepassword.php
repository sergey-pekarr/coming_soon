<?php
include dirname(__FILE__).'/css/temporary.php';
?>
<div id="bx-content">
    <div id="subpage-content">
        <div id="login-box2">
		

		

	<h2>Forgotten Login Details</h2>

	
	

	
	    <form id="new-password" method="post" action="">
	        <p style="width: 425px;">Please enter a new password below and then click<br>
	        	<strong>'Set My New Password'</strong>. We will email you a copy<br>
	        	of your new password for your records.</p>

	        <div class="smallform">
	            <fieldset id="fields">
	                <div style="display: none;"><input hideFocus="true" style="margin: 0px; padding: 0px; border: 0px currentColor; width: 0px; height: 0px; float: left;" id="submit-button" tabIndex="-1" name="submit" value="Submit" type="submit"></div>

	                <label style="padding: 0px !important; font-weight: bold;" for="f_password">New Password</label>
	                <input id="f_password" name="f_password" type="password">

	                <label style="padding: 0px !important; font-weight: bold;" for="f_confirmpassword">Confirm New Password</label>
	                <input id="f_confirmpassword" name="f_confirmpassword" type="password">
					
    <div class="clear">
    </div>
					<?php if(isset($data['error'])){ ?>
					<p class="message_err"><?php echo $data['error']; ?></p>
					<?php } ?>
	                
	                <p><a id="setnewpassword" class="content_button_right " onclick="$('#submit-button').click();return false;" href="#">Set My New Password<span><img class="iconMark" alt="" src="/images/img/blank.gif" heigh="40" width="10"></span></a></p>
	            </fieldset>
	        </div>
	    </form>

	

		
		
		</div>
    </div>
    <div class="clear">
    </div>
</div>