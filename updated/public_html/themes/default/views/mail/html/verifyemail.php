<div style="border: 1px solid #dddad2; border-top: 0px; border-collapse: collapse;
    text-align: left; color: #727272; font-family: tahoma; font-size: 13px; line-height: 18px;
    width: 530px; padding: 10px;">
    <p>
        Hey <?php echo $data['username'] ?>,</p>
    <p>
        Welcome to Meetsi!</p>
    <p>
        To complete your account setup and gain access to the members area we need you to
        verify your email address.</p>
    <p style="text-align: center;">
        <a href="<?php echo $data['confirmEmailUrl'] ?>"
            target="_blank">ACTIVATE MY ACCOUNT</a></p>
    <p style="word-wrap: break-word;">
        If you can't click to the link above. Copy the following url, and then paste to
        browser's address bar: <?php echo $data['confirmEmailUrl'] ?>
    </p>
</div>
