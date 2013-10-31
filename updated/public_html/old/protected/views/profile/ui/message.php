<?php 

if(!CHelperProfile::getPaymentLinkWithAction('sendmessage', $profile->getId(), $emaillink)){
	$emaillink = "submitMessagesend();";
}
?>
<div id="send-message-profile" class="fleft">
    <div class="box-contain">
        <div class="box-header">
            Send a message to <?php echo $profile->getDataValue('username'); ?><span></span>
        </div>
        <div class="box-content round">
            <form id="message-send" method="post" action="/thread/send">
            <input id="profile_id" name="profile_id" value="<?php echo Yii::app()->secur->encryptID( $profile->getId()) ?>" type="hidden">
            <label class="subject" for="subject">Message Subject:&nbsp;<span></span></label>
			<input id="subject" placeholder="No Subject" name="subject" value="" type="text">
			<textarea id="message" placeholder="No Message" name="message"></textarea>
            <div style="height: 12px !important;" class="clear">
            </div>
            <?php $this->widget('application.components.common.FlirtButtonWidget', array('type'=>'EmailSent', 'action'=>" $emaillink; return false;", 'profileid'=>$profile->getId())); ?>
            <div class="clear"></div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){ 
	//attachWatermark('#subject','No Subject');
});
function submitMessagesend(){
    if ($('#subject').val() == '' || $('#subject').val() == 'No Subject') {
        showMessagePopup('Please type your subject');
        return;
    }
    if ($('#message').val() == '') {
        showMessagePopup('Please type your mesasge');
        return;
    }
    var url = '/thread/send/' + $('#profile_id').val();
	var data = {
                'id': $('#profile_id').val(),
				'subject': $('#subject').val(),
				'content': $('#message').val()
    };
    $.post(url, data, function () {
    })
	.success(function (res) {
	    try {
	        data = $.parseJSON(res);
			if(data.threadlink){
				window.location = data.threadlink;
				return;
			}
	        if (data.content && data.fromLink) {
				window.location = "/thread/" + $('#profile_id').val(); // + '#message';;
	        }
			else if(data.alert){
				showAlert(data.alert);
			}
			else if(data.title && data.desc){
				showAlert(data);
			}
	    }
	    catch (ex) {
	    }
	});
}
</script>