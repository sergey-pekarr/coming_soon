<?php
if (isset($profile)) { ?>
	
	<div>
    <?php if ($profile->getDataValue('info', 'ref_url')) { ?>
		Ref:
		<a href="javascript:void(0)" onclick="javascript:slideUpDownBox('user_ref_box_<?php echo $profile->getId() ?>')" title="<?php echo $profile->getDataValue('info', 'ref_url') ?>" >
	    	<?php echo $profile->getDataValue('info', 'ref_domain') ?>
		</a>
	    <div class="user_ref_box" id="user_ref_box_<?php echo $profile->getId() ?>">
	    	<textarea rows="6" cols=""><?php echo $profile->getDataValue('info', 'ref_url') ?></textarea>
		</div>
	<?php } ?>
	</div>
	
<?php } ?>
