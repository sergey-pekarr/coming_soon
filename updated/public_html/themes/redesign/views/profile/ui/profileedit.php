<div class="content_tabs bround">
    <ul>
        <li class="active"><a title="aboutme" onclick="changeEditTab(this); return false;"
            href="javascript:void(0);">About Me</a><span></span></li>
        <li><a title="quickprofile" onclick="changeEditTab(this); return false;"
            href="javascript:void(0);">Basic Infor</a><span></span></li>
        <li><a title="myprofile" onclick="changeEditTab(this); return false;"
            href="javascript:void(0);">My Profile</a><span></span></li>
        <li><a title="mylifestyle" onclick="changeEditTab(this); return false;"
            href="javascript:void(0);">My Lifestyle</a><span></span></li>
        <li class=""><a title="mydate" onclick="changeEditTab(this); return false;"
            href="javascript:void(0);">My Date</a><span></span></li>
    </ul>
    <div class="clear">
    </div>
    <div style="min-height: 100px; display: block;" class="content_tabs_wrap">
        <div style="display: block;" id="aboutme" class="content_tabs_box">
            <form id="profile-words-form" class="profile_aboutme_form">
            <table border="0" cellspacing="5" cellpadding="5">
                <tbody>
                    <tr>
                        <td class="desc">
                            Your Profile headline
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input id="headline" name="headline" value="<?php echo $profile->getPersonalValue('headline'); ?>" type="text">
                        </td>
                    </tr>
                    <tr>
                        <td class="desc">
                            Tell us a little about yourself
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <textarea id="character" rows="15" name="character"><?php echo $profile->getPersonalValue('character'); ?></textarea>
                        </td>
                    </tr>
                </tbody>
            </table>
            <hr>
            <a class="content_button content_button_Save" onclick="saveaboutme();" href="javascript:void(0);">
                Save Changes <span>
                    <img class="iconSave" alt="" src="/images/img/blank.gif"></span> </a>
            </form>
			<div class="clear"></div>
			<script>
				$(document).ready(function(){
					$('.profile_aboutme_form input:text, .profile_aboutme_form textarea').change(function(){
						myprofile.change['aboutme'] = true;
					});
				});
			</script>
        </div>
        <div style="display: none;" id="quickprofile" class="content_tabs_box">
        </div>
        <div style="display: none;" id="myprofile" class="content_tabs_box">
        </div>
        <div style="display: none;" id="mylifestyle" class="content_tabs_box">
        </div>
        <div style="display: none;" id="mydate" class="content_tabs_box">
        </div>
    </div>
</div>
