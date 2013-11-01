<div class="userInfoShot">
<?php
if (isset($profile)) { ?>
	
	<a target="_blank" href="<?php echo $profile->getUrlProfile() ?>">	
		<img class="img-<?php echo $this->size ?>" alt="" src="<?php echo $profile->imgUrl($this->size, $i, false) ?>" />
	</a>

	
<?php } else { ?>

	<span class="smaller">error...</span>

<?php } ?>

</div>
