<div id="bx-content">
	<div id="subpage-content">
		<?php if($data['confirmed']) { ?>
		<h1>
            Congratulations</h1>
        <p>
            Your account has now been fully activated and you have been credited with 5 free winks.</p>
		<?php } else { ?>
		<h2>
            Your request could not be completed.</h2>
        <p>
            We apologize but we are unable to verify the link you are using to access this page.</p>	
		<?php } ?>
	</div>
		<?php if($data['homelink'] != '') { ?>
			<p>Please <a href="<?php echo $data['homelink'] ?>">click here</a> to proceed to your homepage</p>
		<?php } ?>
    <div class="clear">
    </div>
</div>
