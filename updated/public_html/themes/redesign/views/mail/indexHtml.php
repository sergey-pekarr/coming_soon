
<div>
    <table style="margin: 0px auto; width: 552px;" border="0" cellspacing="0" cellpadding="0">
        <tbody>
            <tr>
                <td>
                    <a href="<?php echo (isset($row['data']['autoLoginUrl'])) ? $row['data']['autoLoginUrl'] : SITE_URL ?>" rel="nofollow" target="_blank">
                        <img style="margin: 0px 0px 10px; border: 0px currentColor;" alt="Meetsi" src="<?php echo SITE_URL ?>/images/img/meetsi/logo1.png">
					</a>
                </td>
                <td style="text-align: right;">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    &nbsp;
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <img src="<?php echo SITE_URL ?>/images/img/header-blank.png">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                	<?php echo $row['body'] ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div style="margin: 0 auto; font-family: tahoma; background: #f2f2f2; color: #727272;
                        font-size: 11px; line-height: 20px; text-align: center; width: 550px; border: 1px solid #dddad2;
                        border-top: 0px">
                        To stop receiving emails from us please click <strong><a href="<?php echo (isset($row['data']['unsubscribeUrl'])) ? $row['data']['unsubscribeUrl'] : SITE_URL.'/account' ?>"
                            target="_blank">unsubscribe</a></strong><br>
                        <a href="<?php echo (isset($row['data']['autoLoginUrl'])) ? $row['data']['autoLoginUrl'] : SITE_URL ?>" target="_blank">
                            Meetsi.com</a><br>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

                	
