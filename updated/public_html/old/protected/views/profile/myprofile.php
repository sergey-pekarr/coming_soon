<?php
//$justjoined = !($profile->getSettingsValue('email_activated') || $profile->getSettingsValue('email_activated_at') != '0000-00-00 00:00:00');
//<div class="<?php echo ($justjoined?'myunverified':'verified'); ? >">This Profile is Verified!</div>

$levelInst = new LevelUp($profile);
$levelInf = $levelInst->checkLevel();
$level = $levelInf['level'];
$isCurrentUser = true;
?>
<div id="profile-left" class="span-8">
	
	<?php $this->widget('application.components.common.UserStatusWidget', array('profile'=>$profile, 'sidebar'=>false )); ?>
	
	<?php include dirname(__FILE__).'/ui/image.php'; ?>
    <div class="clear"></div>
    <div style="padding-right: 30px; margin-top:20px;">
        <?php $this->widget('application.components.common.FlirtButtonWidget', array('type'=>'Request-Photos', 'text' => 'Upload Photo', 'action' => 'uploadPhoto();', 'profileid'=>$profile->getId())); ?>
    </div>	
    <div style="padding-right: 30px;">
        <?php $this->widget('application.components.common.FlirtButtonWidget', array('type'=>'Edit', 'action'=>'window.location = \'/account\'', 'text' => 'Edit Username', 'profileid'=>$profile->getId())); ?>
    </div>
    <div class="clear"></div>
    <h3></h3>
	<div style="">
		<div onclick="showLevelup();" style="margin:20px auto 30px auto;" class="award-status award-icon-<?php echo ($level); ?>"></div>
	</div>
    <?php include dirname(__FILE__).'/ui/favouriteform.php'; ?>		
</div>

<div id="content_profile_data" class="span-16 last">
	<div id="profile-quicksummary">
	</div>
	<div style="display: block;" id="saveAlert">
        
	</div>
    <?php include dirname(__FILE__).'/ui/profileedit.php'; ?>
    <div class="clear"></div>
    <?php //include dirname(__FILE__).'/ui/gift.php'; ?>
	<?php $this->widget('application.components.common.GiftReceivedWidget', array('profile'=>$profile));   ?>    
    <div class="clear"></div>
    <?php include dirname(__FILE__).'/ui/levelup.php'; ?>
    <div class="clear"></div>
</div>
<?php include dirname(__FILE__).'/ui/uploadimage.php'; ?>
<?php include dirname(__FILE__).'/ui/leveluppopup.php'; ?>



    <div class="clear"></div>

<script>
myprofile = {
    'loaded':{},
    'change':{}
};
myprofile.loaded['aboutme'] = true;
<?php if($showeditphoto) { ?>
	$(document).ready(function(){
		window.setTimeout(function(){
			uploadPhoto();
		}, 1000);
	});
<?php } 
else if($showlevelup) {?>
	$(document).ready(function(){
		window.setTimeout(function(){
			showLevelup();
		}, 1000);
	});	
<?php } ?>
</script>

