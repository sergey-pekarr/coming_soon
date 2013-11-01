<?php
//$justjoined = !($profile->getSettingsValue('email_activated') || $profile->getSettingsValue('email_activated_at') != '0000-00-00 00:00:00');
//	<div class="<?php echo ($justjoined?'unverified':'verified'); ? >">This Profile is Verified!</div>

$targetenc = Yii::app()->secur->encryptID($profile->getId());
if(!CHelperProfile::getPaymentLinkWithAction('sendfavourite', $profile->getId(), $favlink)){
	$favlink = "doAction('favourite', '$targetenc', this)";
}

$imgs = $profile->getDataValue('image');
if(count($imgs)>0) $phototext = "Request More Photos";
else $phototext = 'Request Photos';

$levelInst = new LevelUp($profile);
$levelInf = $levelInst->checkLevel();
$level = $levelInf['level'];
$isCurrentUser = false;

?>

<?php
function renderPersonalByType($items){ 
	foreach($items as $field =>$item){
	if($item['selected'] != '' && $item['selected'] != 0 && count($item['selected']) > 0) {
?>
		<tr>
		<td class="desc">
            <?php echo $item['text'] ?>
            </td>
            <td>
            <?php echo $item['selectedText'] ?>
            </td>
            </tr>
           <?php }
          } 
          }?>

<div id="profile-left" class="span-8">
	<?php $this->widget('application.components.common.UserStatusWidget', array('profile'=>$profile, 'sidebar'=>false )); ?>
	
	<?php include dirname(__FILE__).'/ui/image.php'; ?>
    <div class="clear"></div>
    <div style="padding-right: 30px; margin-top:20px;">
        <?php $this->widget('application.components.common.FlirtButtonWidget', array('type'=>'Request-Photos', 'text' => $phototext, 'profileid'=>$profile->getId())); ?>
    </div>	
    <div style="padding-right: 30px;">
        <?php $this->widget('application.components.common.FlirtButtonWidget', array('type'=>'EmailSent', 'action'=>'doToBookmark(\'#message\');', 'profileid'=>$profile->getId())); ?>
    </div>
   <div style="float: left;">
        <?php $this->widget('application.components.common.FlirtButtonWidget', array('type'=>'Wink', 'profileid'=>$profile->getId())); ?>
    </div>
    <div style="margin-left: 30px; float: left;">
        <?php $this->widget('application.components.common.FlirtButtonWidget', array('type'=>'Gift', 'action' => 'showGiftPopup();', 'profileid'=>$profile->getId())); ?>
    </div>
    <div class="clear"></div>
	
	<div style="">
		<div onclick="showLevelup();"  style="margin:20px auto 30px auto;" class="award-status award-icon-<?php echo ($level); ?>"></div>
	</div>
    
	<?php include dirname(__FILE__).'/ui/activities.php'; ?>	
		
	<?php include dirname(__FILE__).'/ui/favourite.php'; ?>
	
</div>

<div id="content_profile_data" class="span-16 last">
    <div style="" class="content_bar">
        <div style="" class="profile-headline">
            <?php echo CHelperProfile::getProfileLink($profile).': '.CHelperProfile::showProfileInfoSimple($profile,5).', '.CHelperProfile::showProfileInfoSimple($profile,2); ?>
        </div>
        <div style="" class="rightExlong">
            <?php $this->widget('application.components.common.FlirtIconWidget', array('types'=>array(
            'Block'/*,'Report'*/), 'profileid'=>$profile->getId()));   ?>
        </div>
        <div style="" class="tooltips">
            <?php $this->widget('application.components.common.FlirtIconWidget', array('types'=>array(
            array('type'=>'Email','action'=>'doToBookmark(\'#message\');'), 'Wink', array('type'=>'Favourite','action'=>"$favlink")), 'profileid'=>$profile->getId()));   ?>
        </div>
        <div class="clear"></div>
    </div>
    <div id="profile-quicksummary">
        <h3>
			<?php echo  $profile->getPersonalValue('headline'); ?>
        </h3>
        <p>
			<?php 
			$userprofile = new Profile(Yii::app()->user->id);
			$miles = Yii::app()->location->calculateMileage($profile->getLocationValue('latitude'),$userprofile->getLocationValue('latitude'),
					$profile->getLocationValue('longitude'),$userprofile->getLocationValue('longitude') );
			
			$miles = number_format($miles, 2);
			
			echo  $profile->getDataValue('age').', I live in '
				.CHelperProfile::showProfileInfoSimple($profile,2,100,false).
				' ('. $miles . ' miles away from you)'; ?></p>
        <p>
            I'm looking for a <?php echo CHelperProfile::textLookGender($profile->getDataValue('looking_for_gender')).' aged between '. 
            	$profile->getSettingsValue('ageMin').' to '.
				$profile->getSettingsValue('ageMax'); ?></p>
        <table border="0" cellspacing="5" cellpadding="5">
			<tbody>
			<?php renderPersonalByType($mybasic); ?>
			<?php 
				$turnon = $profile->getPersonalValue('turn_on');
				$turnoff = $profile->getPersonalValue('turn_off');
				if($turnon && $turnon != '') { ?>			
				<tr><td class="desc">Turn Ons</td><td><?php echo $turnon; ?></td></tr>
			<?php }
				if($turnoff && $turnoff != '') { ?>
				<tr><td class="desc">Turn Offs</td><td><?php echo $turnoff; ?></td></tr>
			<?php } ?>
			</tbody>
        </table>
    </div>
    <?php include dirname(__FILE__).'/ui/profilecontent.php'; ?>
    <br>        
    <?php include dirname(__FILE__).'/ui/message.php'; ?>
    <div class="clear">&nbsp;</div>
    <?php //include dirname(__FILE__).'/ui/gift.php'; ?>
	
	<?php $this->widget('application.components.common.GiftReceivedWidget', array('profile'=>$profile));   ?>
	
    <?php include dirname(__FILE__).'/ui/giftsend.php'; ?>
    
    <div class="clear"></div>
	
	<?php include dirname(__FILE__).'/ui/leveluppopup.php'; ?>
</div>
    <div class="clear"></div>

