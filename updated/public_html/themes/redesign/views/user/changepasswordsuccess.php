<?php
include dirname(__FILE__).'/css/temporary.php';
?>
<div id="bx-content">
    <div id="subpage-content">
        <div id="login-box2">
		
	<h2>
	Your password has been successfully changed.</h2>
	<p>
	We emailed new password to you.</p>	
		<?php if($data['homelink'] != '') { ?>
			<p>Please <a href="<?php echo $data['homelink']; ?>">click here</a> to proceed to your homepage</p>
		<?php } ?>
	</div>
        <div class="clear">
        </div>
    </div>
</div>