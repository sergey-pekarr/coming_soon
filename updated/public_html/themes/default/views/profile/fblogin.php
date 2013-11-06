<style>
    #fbcontent .box-content p, #fbcontent .box-content p strong
    {
        font-size: 12px;
        padding: 10px 0px 8px 0px;
        line-height: 18px;
    }   
    .pluginFaviconButton
    {
        color: rgb(255, 255, 255);
        vertical-align: top;
        display: inline-block;
        cursor: pointer;
        background-color: rgb(95, 120, 171);
    }
    .pluginFaviconButton:active
    {
        background-color: rgb(79, 106, 163);
    }
    .pluginFaviconButtonIcon
    {
        vertical-align: top;
    }
    .pluginFaviconButtonIconActive
    {
        vertical-align: top;
    }
    .pluginFaviconButton:active .pluginFaviconButtonIcon
    {
        display: none;
    }
    .pluginFaviconButton .pluginFaviconButtonIconActive
    {
        display: none;
    }
    .pluginFaviconButton:active .pluginFaviconButtonIconActive
    {
        display: inline-block;
    }
    .pluginFaviconButtonBorder
    {
        border-top-color: rgb(41, 68, 126);
        border-right-color: rgb(41, 68, 126);
        border-bottom-color: rgb(26, 53, 110);
        border-top-width: 1px;
        border-right-width: 1px;
        border-bottom-width: 1px;
        border-top-style: solid;
        border-right-style: solid;
        border-bottom-style: solid;
        display: inline-block;
    }
    .pluginFaviconButtonBorder:active
    {
        border-color: rgb(52, 67, 125);
    }
    .pluginFaviconButtonText
    {
        border-top-color: rgb(135, 154, 192);
        border-top-width: 1px;
        border-top-style: solid;
        display: inline-block;
        white-space: nowrap;
    }
    .pluginFaviconButton:active .pluginFaviconButtonText
    {
        border-top-color: rgb(80, 96, 156);
    }
    .pluginFaviconButtonSmall .pluginFaviconButtonText
    {
        padding: 2px 6px 3px;
        line-height: 10px;
        font-size: 10px;
    }
    .pluginFaviconButtonMedium .pluginFaviconButtonText
    {
        padding: 2px 6px 3px;
        line-height: 14px;
        font-size: 11px;
    }
    .pluginFaviconButtonLarge .pluginFaviconButtonText
    {
        padding: 3px 6px;
        line-height: 16px;
        font-size: 13px;
    }
    .pluginFaviconButtonXlarge .pluginFaviconButtonText
    {
        padding: 3px 8px;
        line-height: 30px;
        font-size: 24px;
    }
    
    .uiGrid
    {
        border: 0px currentColor;
        border-collapse: collapse;
        border-spacing: 0;
    }
    .uiGridFixed
    {
        width: 100%;
        table-layout: fixed;
    }
    .uiGrid .vTop
    {
        vertical-align: top;
    }
    .uiGrid .vMid
    {
        vertical-align: middle;
    }
    .uiGrid .vBot
    {
        vertical-align: bottom;
    }
    .uiGrid .hLeft
    {
        text-align: left;
    }
    .uiGrid .hCent
    {
        text-align: center;
    }
    .uiGrid .hRght
    {
        text-align: right;
    }
    i.img u
    {
        top: -9999999px;
        position: absolute;
    }
    .sp_login-button
    {
        width: 18px;
        height: 18px;
        display: inline-block;
        background-image: url("http://static.ak.fbcdn.net/rsrc.php/v2/yf/r/S-DbSHszr4D.png");
        background-repeat: no-repeat;
    }
    .sx_login-button_small
    {
        background-position: 0px -178px;
    }
    .sx_login-button_smalla
    {
        background-position: -19px -178px;
    }
    .sx_login-button_medium
    {
        background-position: 0px -132px;
        width: 22px;
        height: 22px;
    }
    .sx_login-button_mediuma
    {
        background-position: 0px -155px;
        width: 22px;
        height: 22px;
    }
    .sx_login-button_large
    {
        background-position: 0px -80px;
        width: 25px;
        height: 25px;
    }
    .sx_login-button_largea
    {
        background-position: 0px -106px;
        width: 25px;
        height: 25px;
    }
    .sx_login-button_xlarge
    {
        width: 39px;
        height: 39px;
    }
    .sx_login-button_xlargea
    {
        background-position: 0px -40px;
        width: 39px;
        height: 39px;
    }
    .fwn
    {
        font-weight: normal;
    }
    .fwb
    {
        font-weight: bold;
    }
</style>
<div class="box-contain" id="fbcontent">
    <div class="box-header" style="text-transform:none; height:32px;">
        Please login to facebook<span></span>
    </div>
    <div class="box-content round">
        <p class="center">
            Please click the Facebook login button below to login to your Facebook account and
            access your images to import.</p>
        <div style="width: 280px; margin-left: auto; margin-right: auto;">
            <div class="pluginFaviconButton pluginFaviconButtonXlarge" onclick="window.location = '<?php echo $loginUrl; ?>';">
                <table class="uiGrid" cellspacing="0" cellpadding="0">
                    <tbody>
                        <tr>
                            <td style="vertical-align: top">
                                <i class="pluginFaviconButtonIcon img sp_login-button sx_login-button_xlarge"></i>
                                <i class="pluginFaviconButtonIconActive img sp_login-button sx_login-button_xlargea">
                                </i>
                            </td>
                            <td style="vertical-align: top">
                                <span class="pluginFaviconButtonBorder"><span class="pluginFaviconButtonText fwb">Login
                                    to Facebook</span></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div style="height: 15px !important;" class="clear">
            &nbsp;</div>
        <p class="center">
            <strong style="color: black;">Please note, we highly respect your privacy - no one will
                know you are using <?php echo SITE_URL; ?></strong>
        </p>
    </div>
</div>
<!--
<?php print_r($res); ?>
-->