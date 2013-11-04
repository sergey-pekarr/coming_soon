<script>
    function resendVerification() {
        $.get('/profile/resendverify', function (res) {
        })
        .success(function (res) {
            alert('Verification code is sent to your email!');
        });
    }
    function changeEmail() {
        $('#divEmail').slideDown();
    }
    function cancelEmail() {
        $('#divEmail .message').html('').removeClass('message_err');
        $('#divEmail').slideUp();
    }
    function saveEmail() {
        data = { 'newemail': $('#newemail').val(), 'confirmnewemail': $('#confirmnewemail').val() };
        $.getJSON('/profile/changeandverifyemail', data, function (res) {
        })
        .success(function (res) {
            if (res && res.error) {
                $('#divEmail .message').html(res.error.toString()).addClass('message_err');
            } else {
                $('#newemail').val('');
                $('#confirmnewemail').val('');
                alert('Verification code is sent to your new email!');
                $('#divEmail').slideUp();
                $('#currentemail').html(data.newemail);
            }
        })
        .fail(function (res) {
            $('#divEmail .message').html('Can\'t change email').addClass('message_err');
        });
    }
</script>
<div id="verify-intro">
    <div class="email-tick">
        &nbsp;</div>
    <h2>
        <?php echo Yii::app()->user->Profile->getDataValue('username'); ?>, You must verify your email address!</h2>
    <p>
        In order to receive messages from users you must verify your email address.</p>
    <p>
        <strong>How do I verify my email address?</strong><br>
        Go to your <b></b>email account, open your welcome email and click the "Activate
        Your Account" link in the email.</p>
    <p>
        <strong>I can't find my welcome email</strong><br>
        If you can't find your welcome email click the "Resend My Verification Email" button
        below. If you do not receive an email within 10 minutes, check your spam/junk folder.
        Help for this can be found below</p>
    <p>
            <strong><span id="currentemail"><?php echo Yii::app()->user->Profile->getDataValue('email'); ?></span> is not my email address</strong><br>
            You can change your email address by clicking the "Change email and resend" button
            below.</p>
    <p>
        <strong>Didn't get your verification email?</strong></p>
    <p class="center">
        <a id="resend-verification" onclick="resendVerification(); return false;" href="#" title="Resend verficaition">
            <img src="/images/img/resend-verify-email.png"></a></p>
	<p class="center">
            <a id="editEmail" onclick="changeEmail(); return false;" href="#">
                <img src="/images/img/change-resend-verify-email.png"></a></p>
	<div>
        <div id="divEmail">
            <div>
                New Email:<br>
                <input id="newemail" class="forminput" name="newemail" value="" type="text">
            </div>
            <div>
                Confirm Email:<br>
                <input id="confirmnewemail" class="forminput" name="confirmnewemail" value="" type="text">
            </div>
            <div>
                <a id="cancelEmail" class="content_button " style="width:auto; float:left;" onclick="cancelEmail(); return false;" href="#">Cancel<span>
                    <img class="iconX" alt="" src="/images/img/blank.gif"></span></a> 
                <a  style="width:auto;  float:left; margin-left: 60px;" id="saveEmail" class="content_button " onclick="saveEmail(); return false;" href="#">Save &amp; Send<span>
                    <img class="iconDisc" alt="" src="/images/img/blank.gif"></span></a>
                <div class="clear"></div>
                <div class="message">
                    <div class="message_wrap">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <p>
        <strong>If you have are still having trouble finding the email, make sure you have checked
            your spam folder.</strong><br></p>
</div>
