<div id="usr-desc-container" style="display: none;">
    <div id="usr-desc-body" style="height: 420px; width: 470px; background-color:White;">
        <form id="usr-desc-words-form" class="profile_aboutme_form">
        <table border="0" cellspacing="5" cellpadding="5">
            <tbody>
                 <tr>
                    <td style="height:18px;line-height:18px;padding: 4px 0px;color: #000;font-weight:bold;">
                        Please introduce yourself:
                </td>
                </tr>
                <tr>
                    <td class="desc">
                        Please add your Profile headline
                    </td>
                </tr>
                <tr>
                    <td>
                        <input id="usr-desc-headline" name="headline" value="<?php echo $userprofile->getPersonalValue('headline'); ?>" type="text">
                    </td>
                </tr>
                <tr>
                    <td class="desc">
                        Please add at least 20 words to your Description
                    </td>
                </tr>
                <tr>
                    <td>
                        <textarea id="usr-desc-character" rows="15" name="character"><?php echo $userprofile->getPersonalValue('character'); ?></textarea>
                    </td>
                </tr>
            </tbody>
        </table>
        <hr>
        <a class="content_button content_button_Save" onclick="usrdescsaveaboutme();" href="javascript:void(0);">
            Save Changes <span>
                <img class="iconSave" alt="" src="/images/img/blank.gif"></span> </a>
        </form>
        <div class="clear">
        </div>
        <style>
            #usr-desc-container>div.ui-dialog-system>a
            {
                display:none;
            }
            #usr-desc-words-form
            {
                padding: 10px;
            }
            #usr-desc-body table
            {
                width: 100%;
            }
            #usr-desc-body textarea, #usr-desc-body input[type='text']
            {
                width: 440px;
                font-size: 13px;
                height:18px;
                line-height:18px;
                padding: 4px;
                margin-bottom: 9px;
            }
            #usr-desc-body textarea
            {
                height:224px;
            }
            #usr-desc-body td.desc
            {
                height:18px;
                line-height:18px;
                font-weight:bold;
                padding: 4px 0px;
                color: #666;
            }
            #usr-desc-body a.content_button_Save
            {
                width: 90px;
            }
        </style>
        <script>
            function usrdescsaveaboutme() {
                var text = $('#usr-desc-character').val();
                if (text.match(/\w([^\w]|$)/igm).length < 30) {
                    alert("Please add at least 30 words to your Description");
                    return;
                }

                var url = ' /profile/saveaboutme';
                $.post(url, { headline: $('#usr-desc-headline').val(), character: $('#usr-desc-character').val() }, function () {
                    
                })
                .success(function () {
                    $('#usr-desc-container').dialog('close');
                })
                .fail(function () {
                    
                });
            }

            function showUsrDesc() {                
                showPopup("usr-desc-container", $('#usr-desc-body').width() + 20, $('#usr-desc-body').height() + 20);
            }

            $(document).ready(function () {
                window.setTimeout(showUsrDesc, 500);
            });
			</script>
    </div>
</div>
