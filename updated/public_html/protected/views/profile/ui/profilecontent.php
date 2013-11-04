
<div class="content_tabs bround">
    <ul>
        <li class="active"><a title="aboutme" onclick="changeTab(this); return false;" href="javascript:void(0);">About Me</a><span></span></li>
        <li><a title="myprofile" onclick="changeTab(this); return false;" href="javascript:void(0);">My Profile</a><span></span></li>
        <li><a title="mylifestyle" onclick="changeTab(this); return false;" href="javascript:void(0);">My Lifestyle</a><span></span></li>
        <li class=""><a title="mydate" onclick="changeTab(this); return false;" href="javascript:void(0);">
            My Date</a><span></span></li>
    </ul>
    <div class="clear">
    </div>
    <div style="min-height:100px; display: block;" class="content_tabs_wrap">
        <div style="display: block;" id="aboutme" class="content_tabs_box">
            <table border="0" cellspacing="5" cellpadding="5">
                <tbody>
                    <tr>
                        <td>
                            <h3>
                            <?php echo  $profile->getPersonalValue('character'); ?></h3>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="clear">
            </div>
        </div>
        <div style="display: none;" id="myprofile" class="content_tabs_box">
            <table border="0" cellspacing="5" cellpadding="5">
                <tbody>
				<?php renderPersonalByType($myprofile); ?>
                </tbody>
            </table>
            <div class="clear">
            </div>
        </div>
        <div style="display: none;" id="mylifestyle" class="content_tabs_box">
            <table border="0" cellspacing="5" cellpadding="5">                <tbody>
				<?php renderPersonalByType($mylifestyle); ?>
                </tbody>
            </table>
            <div class="clear">
            </div>
        </div>
        <div style="display: none;" id="mydate" class="content_tabs_box">
            <table border="0" cellspacing="5" cellpadding="5">                <tbody>
				<?php renderPersonalByType($dating); ?>
                </tbody>
            </table>
            <div class="clear">
            </div>
        </div>
    </div>
</div>
