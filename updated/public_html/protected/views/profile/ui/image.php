<?php

$primaryimgurl = $profile->imgUrl('big', 0, true);
$imgs = $profile->getDataValue('image');

/* Improvement: #profile-img-container -> show big imaage
"small profile_img" -> show small image
changeImage() -> replace /small/ -> /medium/
showlargeimage(), shownextimage() -> replace /small/ or /medium/ -> /original/
*/
$allowlarge = !CHelperProfile::getPaymentLinkWithAction('viewlargephoto', null, $imgClick);
if($allowlarge){
	$imgClick = "showlargeimage();";
}

if(stristr($primaryimgurl, '/images/design/nophoto')) $imgClick = 'return false;';

?>

<div id="content_profile_images">
    <a id="profile-img-container" onlick="return false;" href="#">
        <img style="display: block;" class="big" alt="" src="<?php echo $primaryimgurl; ?>" onclick="<?php echo $imgClick; ?>"
            width="200" rel="0" />
    </a>
    <div class="clear">
    </div>
	<?php 
	if ($imgs)
	foreach($imgs as $i=>$img) { ?>
		<a class="small profile_img" onclick="changeImage(this); return false;" href="#">
				<img class="selected" alt="" src="<?php echo $profile->imgUrl('small', $i, false); ?>" 
				bigUrl="<?php echo $profile->imgUrl('big', $i, false); ?>"
				picId="<?php echo $img['n']; ?>" width="32" height="32" rel="0">
			</a>
		<?php } ?>
</div>

<?php if($allowlarge) { ?>
<div id="imgviewer-container" style="display: none;">
	<div id="imageviewer-body" style="width:100%;height:100%; background-color:#ffffff">
		<div id="imgviewer">
			<div class="imgback" onclick="shownextimage('back')">
				<img src="/images/img/prevlabel.gif">
			</div>
			<div class="imgnext" onclick="shownextimage('next')">
				<img src="/images/img/nextlabel.gif">
			</div>
			<?php if($profile->getId() != Yii::app()->user->id) { 
				$encid = Yii::app()->secur->encryptID($profile->getId());
			?>
			<div class="imglike" onclick="doActionLike('<?php echo $encid; ?>', this);">
				<a>&nbsp;&nbsp;</a>Like
			</div>
			<?php } ?>
			<img src="/images/img/blank.gif" onload="onImageLoad(this);">
			<div class="clear">
			</div>
		</div>
	</div>
</div>
<?php } ?>



