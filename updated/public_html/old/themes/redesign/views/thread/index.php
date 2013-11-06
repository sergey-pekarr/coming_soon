<?php
$targetLink = "/profile/{$this->encid}";


if(!CHelperProfile::getPaymentLinkWithAction('sendfavourite', $this->id, $favlink)){
	$favlink = "doAction('favourite', '{$this->encid}', this)";
}
if(!CHelperProfile::getPaymentLinkWithAction('sendmessage', $this->id, $emaillink)){
	$emaillink = "sendMesage();";
}
?>
<div class="content_tabs bround">
    <ul>
        <li><a onclick="" href="/msg/inbox">Message <span></span></a></li>
        <li><a onclick="" href="/msg/sent">Sent <span></span></a></li>
        <li><a onclick="" href="/msg/archive">Archive <span></span></a></li>
    </ul>
    <div class="clear">
    </div>
    <div class="content_tabs_wrap">
    </div>
</div>
<h1 class="title">
    Interaction between <?php echo $target->getDataValue('username'); ?> and yourself</h1>
<div id="userpanel">
    <div id="leftuser">
        <img width="82" height="82" src="<?php echo $user->imgUrl(); ?>" />
        <div>
            <a href="javascript:void()"><strong><?php echo $user->getDataValue('username'); ?></strong></a><br>
            <span class="strong"><?php echo CHelperProfile::showProfileInfoSimple($user, 4); ?><br>
                <?php echo CHelperProfile::showProfileInfoSimple($user,2,40); ?></span>
        </div>
    </div>
    <div id="rightuser">
        <a href="<?php echo $targetLink; ?>">
            <img width="82" height="82" src="<?php echo $target->imgUrl(); ?>" />
        </a>
        <div>
            <a href="<?php echo $targetLink; ?>"><strong><?php echo $target->getDataValue('username'); ?></strong></a><br>
            <span class="strong"><?php echo CHelperProfile::showProfileInfoSimple($target, 4); ?><br>
                <?php echo CHelperProfile::showProfileInfoSimple($target,2,40); ?></span><br>
            <?php echo round(Yii::app()->location->calculateMileage($user->getLocationValue('latitude'),$target->getLocationValue('latitude'),
            $user->getLocationValue('longitude'),$target->getLocationValue('longitude') ), 0);?> miles away.
        </div>
    </div>
    <div class="clear">
    </div>
</div>
<div class="spacer">
</div>
<div style="margin: 4px 0px 4px 0px;">
    <div style="margin-top: 3px; margin-right: -5px; margin-bottom: -3px;" class="right">
        <!--        <a class="content_button " onclick="; return false;"
            href="#">Report User / Spam<span><img class="iconAtt" alt="" src="/images/img/blank.gif"></span></a>-->
        <a class="content_button " onclick="doAction('winks','<?php echo $this->encid; ?>',this); return false;" href="#">Send A Wink<span><img
            class="iconWinks" alt="" src="/images/img/blank.gif"></span></a> <a class="content_button "
                onclick="<?php echo $favlink; ?>; return false;" href="#">Add to Favourites<span><img class="iconHeart"
                    alt="" src="/images/img/blank.gif"></span></a>
    </div>
    <div>
        <a style="margin-top: 5px; margin-left: 0px; position: absolute;" class="content_nav_prev"
            href="/msg/inbox">Back to Inbox<span></span></a>
    </div>
    <div class="clear">
    </div>
</div>
<div class="spacer">
</div>
<div id="messagespanel">
<?php 
$currentname = '';
$goldAccess = Yii::app()->user->checkAccess('gold');
foreach($data as $item) 
	{
	$from = array();
    $fromLink = "javascript:void();";
	if($item['id_from'] == $user->getId()){
        $from = $user;
    }
	else{
        $from = $target;
        $fromLink = $targetLink;
    }
    $name = $from->getDataValue('username');
    if($name != $currentname){
        $currentname = $name;
        $name .= ': ';
    }
    else{
         $name = '';
    }
?>
	<table width="100%" class="tblmessage">
        <tr>
            <td width="60px" style="vertical-align:top; padding-top:5px;">
                <a style="margin: 0px; padding: 0px;" href="<?php echo $fromLink; ?>">
                    <img class="profile-small left" alt="" src="<?php echo $from->imgUrl(); ?>" width="50" height="50">
                </a>
            </td>
            <td width="20px">
            </td>
<?php if($goldAccess) { ?>
            <td>
                <h2>
                <span class="name" style="color:red;"><?php echo $name; ?></span>
                <span class="subject">
                    <?php
                          echo $item['subject'];  
                      ?></span>
                </h2>
                <div class="content">
                    <?php echo $item['text']; ?>
                </div>
            </td>
            <td width="60px">
                <?php echo CHelperDate::date_distanceOfTimeInWords(time(), strtotime($item['added']), true); ?>
            </td>
<?php }  else {
CHelperProfile::getPaymentLinkWithAction('viewemail', '', $link, $nav);	
	?>
			<td colspan="2">
				<a href="/payment/<?php echo $nav; ?>">
					<img src="/images/img/msgwaiting.png"></a>
			</td>	
<?php }?>
        </tr>
    </table>
<?php } ?>
</div>
<div class="spacer">
</div>
<script>

var msgtemplate = 
'   <table width="100%" class="tblmessage">' +
        '<tr>' +
            '<td width="60px" style="vertical-align:top; padding-top:5px;">' +
                '<a style="margin: 0px; padding: 0px;" href="javascript:void();">' +
                    '<img class="profile-small left" alt="" src="" width="50" height="50">' +
                '</a>' +
            '</td>' +
            '<td width="20px">' +
            '</td>' +
            '<td>' +
                '<h2>' +
                    '<span class="name" style="color:red;"></span>' +
                    '<span class="subject"></span>' +
                '</h2>' +
                '<div class="content">' +
                '</div>' +
            '</td>' +
            '<td width="60px" class="time">' +                
            '</td>' +
        '</tr>' +
    '</table>'

function sendMesage() {
    if ($('#subject').length > 0 && $('#subject').css('display') != 'none' && $('#subject').val() == '') {
        showMessagePopup('Please type your subject', 'Notification');
        return;
    }
    if ($('#message').val() == '') {
        showMessagePopup('Please type your mesasge', 'Notification');
        return;
    }
    var url = '/thread/send/' + $('#profile_id').val();
	var data = {
                'id': $('#profile_id').val(),
                'parentmsgid': $('#thread_id').val(),
				'subject': $('#subject').val(),
				'content': $('#message').val()
    };
    $.post(url, data, function () {
    })
	.success(function (res) {
	    try {
	        data = $.parseJSON(res);

	        //Fist message
	        if (data.threadlink && $('#messagespanel>table').length == 0) {
	            window.location = data.threadlink;
	            return;
	        }

	        if (data.content && data.fromLink) {
	            var jq = $(msgtemplate).appendTo($('#messagespanel'));
	            jq.find('.profile-small').prop('src', data.fromImgUrl);
	            jq.find('.subject').html(data.subject);
	            jq.find('.content').html(data.content);
	            jq.find('.time').html(data.time);
	            $('#message').val('');

	            if (jq.prev().find('.profile-small').attr('src') != data.fromImgUrl) {
	                jq.find('.name').html(data.fromName + ": ");
	            }

	            $('#title_subject, #subject').css('display', 'none');
	            $('#subject').val('');
	            $('#title_reply').css('display', 'block');

	        }
	        else if (data.alert) {
	            showAlert(data.alert);
	        }
	    }
	    catch (ex) {
	    }
	});
}
</script>
<style>
<?php if(!$data || count($data)== 0) { ?>
	#title_reply{display: none;}
<?php } else { ?>
	#title_subject, #subject {display: none;}	
<?php } ?>
</style>
<div class="reply-message">
    <form method="post" action="/thread/send">
    <input id="profile_id" name="profile_id" value="<?php echo $this->encid; ?>" type="hidden">
    <input id="thread_id" name="thread_id" value="<?php echo $this->parentmsgid; ?>" type="hidden">
    <!--<input hidefocus="true" style="margin: 0px; padding: 0px; border: 0px currentColor;
        width: 0px; height: 0px; float: left;" id="submit-button" tabindex="-1" name="submit"
        value="Submit" type="submit">-->
<?php if(!$data || count($data)== 0) { ?>
	<h1 id="title_subject" class="title" for="subject" style="display:inline;">Message Subject:&nbsp;<span></span></h1>
	<input id="subject" name="subject" value="" type="text" style="width:455px;">
<?php } else { ?>
    <h1 id="title_reply" class="title">
        Write your message to <?php echo $target->getDataValue('username'); ?></h1>
<?php } ?>
    <textarea id="message" name="message"></textarea>
    <div style="height: 12px !important;" class="clear">
    </div>
    <a class="content_button send-button content_button_EmailSent" onclick="<?php echo $emaillink; ?>; return false"
        href="#" style="width: 120px;">
        <div style="text-align: center;">
            Send Message
        </div>
        <span>
            <img class="iconEmailSent" alt="" src="/images/img/blank.gif"></span></a>
    </form>
</div>
<style type="text/css">
    #content .content_button
    {
        margin-right: 30px;
        display: block;
        float: right;
        width: auto;
    }
</style>
