<div class="userInfoShot">
<?php
if (isset($profile)) { ?>
	
	<a target="_blank" class="bold" href="<?php echo $profile->getUrlProfile() ?>"><?php echo $profile->getDataValue('username'); ?></a>
	&nbsp;&nbsp;
	<?php echo CHelperProfile::showProfileInfoSimple($profile, 4) ?>
	
	    
	<?php if ($profile->getDataValue('promo')) { ?>
    	<br />
    	<span title="level" class="label user-promo smaller">promo</span>
    <?php } else { ?>
<?php  /*     
	    Affid: <?php echo $profile->getDataValue('affid') ?>,
	    Form:  <?php echo $profile->getDataValue('form') ?>
*/ ?>	    
    <?php } ?>

<?php /*
    <br />
    <?php echo CHelperProfile::showProfileInfoSimple($profile, 4) ?>
*/ ?>
	
	<br />
	<?php 
	$location = $profile->getDataValue('location');        
	
	
	$loc = $location['city'].", "; 
	$loc.= ($location['stateName']) ? $location['stateName'].", " : "";
	$loc.= $location['country'];
	$loc.= '&nbsp;<img class="flag" src="'.Yii::app()->location->flagUrl($location['country']).'" />';
	echo $loc; 
	?>
	
    <?php if ($profile->getDataValue('pics')>1) { ?>
    	<br />
		pics:<?php echo $profile->getDataValue('pics') ?>
	<?php } ?>

	
	<?php 
	$fb = $profile->getDataValue('ext', 'facebook');
	if ($fb) { ?>
		<a class="ext" target="_blank" href="http://facebook.com/<?php echo ($fb['fb_name']) ? $fb['fb_name'] : "profile.php?id=".$fb['fb_id']; ?>">
			<img src="/images/design/facebook.16x16.png" />
			<?php //echo ($fb['fb_name']) ? $fb['fb_name'] : "profile id ".$fb['fb_id']; ?>
		</a>		
	<?php } ?>

	<div class="smallerInfo">
		Joined: <?php echo $profile->getDataValue('activity', 'joined') ?>
	</div>	
	
	<span title="level" class="label smaller role_user-<?php echo $profile->getDataValue('role') ?>">
    	<?php echo $profile->getDataValue('role') ?>
	</span> 
	
	&nbsp;&nbsp;
	<a target="_blank" title="AUTOLOGIN LINK" href="<?php echo CHelperProfile::getAutoLoginUrl($profile->getDataValue('id'))//$profile->getUrlProfile(); ?>">
		autologin
	</a>

	&nbsp;&nbsp;

	<a target="_blank" title="View/Edit Link" href="/admin/users/edit?id=<?php echo $profile->getDataValue('id')//$profile->getUrlProfile(); ?>">
		edit
	</a>	

	
<?php } else { ?>

	<span class="smaller">Error</span>

<?php } ?>

</div>
