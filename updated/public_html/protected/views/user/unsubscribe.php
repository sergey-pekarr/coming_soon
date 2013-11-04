<div id="bx-content">
	<div id="subpage-content">
		<h1><?php echo $data['message']; ?></h1>
	</div>
		<?php if($data['homelink'] != '') { ?>
			<p>Please <a href="<?php echo $data['homelink']; ?>">click here</a> to proceed to your homepage</p>
		<?php } ?>
    <div class="clear">
    </div>
</div>