<?php
$userprofile = new Profile(Yii::app()->user->id);
$userImg = $userprofile->imgUrl('small');


if(!CHelperProfile::getPaymentLinkWithAction('viewfavourite', Yii::app()->user->id, $viewFavLink)){
	$viewFavLink = "/favourite";
} else{
	$viewFavLink = 'javascript: '.$viewFavLink;
}

//Note: sidebar should be a widget. Do later when we have time!



?>

<?php $this->widget('application.components.common.UserStatusWidget', array('profile'=>$userprofile, 'sidebar'=>true )); ?>

<div id="profile">
    <a href="/profile">
        <img alt="" src="<?php echo $userImg; ?>" width="32" height="32"></a>
    <div id="username">
        <a style="font-weight: bold !important;" href="/profile">
			<?php 
			$shortname = $userprofile->getDataValue('username');
			if(strlen($shortname)>16){
				$shortname = substr($shortname,0,13).'...';
			}
			echo $shortname; 
			?></a>
        <br>
        <a id="edit-my-profile" href="/profile">Edit My Profile</a>
    </div>
</div>

<div class="clear">
</div>


<ul id="links">
    <li>
        <img class="iconAccount" alt="Account icon" src="/images/img/blank.gif"><a id="my-account"
            href="/account">My Account</a><a href="#"><span></span></a></li>

    <li class="spacer"></li>

    <p class="activityselect">
        <strong>Activity</strong> <a onclick="sidebar_show_received(this); return false;"
            href="#"><span class="activityselect msg">Received</span></a> | <a onclick="sidebar_show_Sent(this); return false;"
                href="#"><span class="activityselect ">Sent</span></a></p>
    <div id="received-activity">
        <li id="sidebar_inbox_link">
            <img class="iconEmail" alt="Email icon" src="/images/img/blank.gif">
            <a href="/msg/inbox">Email</a> <a id="EmailCount" class="active" href="#" style="display:none;">
                </a></li>
        <li>
            <img class="iconWinks" alt="Winks icon" src="/images/img/blank.gif"><a href="/winks">Winks</a>
            <a id="WinksCount" class="active" href="#" style="display:none;"></a></li>
        <li>
            <img class="iconViews" alt="Views icon" src="/images/img/blank.gif"><a href="/view">Views</a>
            <a id="ViewsCount" class="active" href="#" style="display:none;"></a></li>
        <li>
            <img class="iconFavourited" alt="Favourited icon" src="/images/img/blank.gif"><a href="<?php echo $viewFavLink; ?>">Who
                Favours Me</a> <a id="FavoriteCount" class="active" href="#" style="display:none;"></a></li>
        <li>
            <img class="iconCamera" alt="Camera icon" src="/images/img/blank.gif"><a href="/photorequest">Photo
                Requests</a> <a id="PhotoRequestCount" class="active" href="#" style="display:none;"></a></li>
        <li>
            <img class="iconLiked" alt="Liked icon" src="/images/img/blank.gif"><a href="/like">Who
                Likes Me</a> <a id="LikeCount" class="active" href="#" style="display:none;"></a></li>
    </div>
    <div style="display: none;" id="sent-activity">
        <li>
            <img class="iconWinksSent" alt="WinksSent icon" src="/images/img/blank.gif"><a href="/winks/sent">Winks
                Sent</a></li>
        <li>
            <img class="iconViews" alt="Views icon" src="/images/img/blank.gif"><a href="/view/sent">Viewed
                Profiles</a></li>
        <li>
            <img class="iconHeart" alt="Heart icon" src="/images/img/blank.gif"><a href="/myfavourite">My
                Favorites</a></li>
        <li>
            <img class="iconNo" alt="No icon" src="/images/img/blank.gif"><a href="/blacklisted">Blocked
                Profiles</a> </li>
    </div>
	<script>
		$(document).ready(function(){
			var url = document.URL;
			if(url.indexOf('/winks/sent') >= 0 || url.indexOf('/view/sent') >= 0 || url.indexOf('/myfavourite') >= 0 || url.indexOf('/blacklisted') >= 0){
				sidebar_show_Sent($('a[onclick="sidebar_show_Sent(this); return false;"]').get(0));
			}
		});
	</script>

    <span id="sidebar-lower">
        <li class="spacer"></li>
		<?php //$this->widget('application.components.Fan.FanMenuItemWidget', array('userprofile'=>$userprofile, 'group' => false)); ?>
		
		<?php /* if($userprofile->getDataValue('role') == 'gold') { ?>
		<li>
			<img class="iconunlimitedporn" alt="Unlimited porn" src="/images/ulp.gif"><a title="Unlimited porn"
				href="javascript:void()" onclick="showUlp();"><strong>Unlimited porn</strong></a><span></span></li>
		<?php } */ ?>
        <li>
            <img class="iconVip" alt="Vip icon" src="/images/img/blank.gif"><a href="/profile/levelup">Prestige
                Membership</a></li>
        <li style="display: none;">
            <img class="iconHotNot" alt="HotNot icon" src="/images/img/blank.gif"><a href=""><strong>Hot
                Meter</strong></a></li>
        <li>
            <img class="iconSnoopy" alt="Snoopy icon" src="/images/img/snoopy-icon.png"><a title="Snoopy's Top Tips"
                href="/site/snoopy">Snoopy's Top Tips</a><span></span></li>
        <li>
            <img class="iconHelp" alt="Help icon" src="/images/img/blank.gif"><a title="Need help?"
                href="/site/support"><strong>Contact Support</strong></a><span></span></li>
				
		<?php $curController = Yii::app()->controller->id;
		if($curController != 'quizz' && false) { ?>
				<li>
			<img class="iconSnoopy" alt="Quizz icon" src="/images/img/quizz_icon.png"><a title="Quizz"
				href="/quizz"><strong>Quizz | Beta version</strong></a><span></span></li>
		<?php } ?>
        <li class="spacer"></li>
    </span>
	
	<?php //$this->widget('application.components.Fan.FanMenuItemWidget', array('userprofile'=>$userprofile, 'group' => true)); ?>
	
	<?php $this->widget('application.components.quizz.QuizzMenuItemWidget', array()); ?>	
	
</ul>

<div style="display: none;" id="viewing-my-profile">
    <h3 style=" font-size:12px;">
        Viewing Your Profile:</h3>
</div>
<div style="display: none;" id="viewed-my-profile">
    <h3 style=" font-size:12px;">
        Viewed Your Profile:</h3>
</div>
<div style="display: none;" id="you-viewed">
    <h3 style=" font-size:12px;">
        You Recently Viewed:</h3>
</div>
<div class="clear"></div>

<?php $this->widget('application.components.common.tubegaloreWidget', array('userprofile'=>$userprofile)); ?>
		
<script>
var IM_INTERVAL = 45000; //In realtime
var ALERT_INTERVAL = 5000;
var imdata = {};
<?php
//Data should be ready after document loaded
$imdata = CHelperAction::buidImData();
echo 'imdata = '.json_encode($imdata).';';
?>


$(document).ready(function () {
    buildIm(imdata);
    window.setTimeout(updateIm, IM_INTERVAL);
});

</script>

