<?php /* <!--<img src="http://pinkmeets.com/images/img/header-password.png">--> */ ?>
<div style="border-width: 0px 1px 1px; border-style: none solid solid; border-color: currentColor rgb(221, 218, 210) rgb(221, 218, 210);
    padding: 10px; width: 530px; text-align: left; color: rgb(114, 114, 114); line-height: 18px;
    font-family: tahoma; font-size: 13px; border-collapse: collapse;" id="ecxmessage-content">
    <p>
        Hi <?php echo $data['username'] ?>,</p>
    <p>
        We received notification <?php  /* on {datetime} */ ?> that you wish to reset your pinkmeets
        password.</p>
    <p>
        Please <a href="<?php echo $data['passwordResetUrl'] ?>"
            target="_blank">click here to set your new password</a></p>
<?php  /*
    <p>
        Please note that as a security precaution this link is only valid for today.</p>
*/ ?>       
    <p>
        The pinkmeets Team</p>
</div>
