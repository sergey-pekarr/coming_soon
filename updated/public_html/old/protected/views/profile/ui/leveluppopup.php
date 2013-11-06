<?php
$incomplete = $levelInf['incomplete'];
if($isCurrentUser){
    $lvtext1 = 'Hey <span class="award-name">'.$profile->getDataValue('username').'</span>, your progress to' ;
    $lvtext2 = 'you need to...';
    $lvtext3 = 'your';
}
else{
    $lvtext1 = $profile->getDataValue('username').'\'s progress to' ;
    $lvtext2 = $profile->getDataValue('username').' need to...';
    $lvtext3 = $profile->getDataValue('username').'\'s';
}

$photo = $levelInf['photo'];
$personal = $levelInf['personal'];
$messages = $levelInf['messages'];
$winks = $levelInf['winks'];
$favourite = $levelInf['favourite'];
$like = $levelInf['like'];

//echo '<!--';
//print_r($levelInf);
//echo '-->';
?>
<div id="award-container" style="display: none;">
    <div style="width: 740px; height: auto; background-color: White;" id="award">
        <div class="award-header award-header-<?php echo ($level); ?>">
            <div class="arrow-position">
            </div>
            <div class="award-icon-box">
                <table>
                    <tr>
                        <td>
                            <div class="award-icon award-icon-1">
                            </div>
                        </td>
                        <td>
                            <div class="award-icon award-icon-2">
                            </div>
                        </td>
                        <td>
                            <div class="award-icon award-icon-3">
                            </div>
                        </td>
                        <td>
                            <div class="award-icon award-icon-4">
                            </div>
                        </td>
                        <!--                        <td>
                            <div class="award-icon award-icon-5">
                            </div>
                        </td>-->
                    </tr>
                </table>
                <div class="clear">
                </div>
            </div>
        </div>
        <div id="award-content">
            <h2>
                <?php echo $lvtext1; ?>
                <span class="award-text-4"></span>status!
            </h2>
            <div id="award-progress-content">
                <div id="award-progress">
                    <img id="award-progress-pbImage" title=" 0%" alt=" 0%" src="/images/img/levelup/progressbar.bg.png"
                        width="450" height="28"><span id="award-progress-pbText"> 0%</span></div>
            </div>
            <div id="levelup-todo">
                <h3>
					<?php if($level<4) { ?>
                    To achieve <span class="award-text-<?php echo ($level +1); ?>"></span><?php echo $lvtext2; ?>
					<?php } ?>
				</h3>
                <table>
                <?php if($level == 1) { ?>
                    <?php if(in_array('1.1', $incomplete)) { ?>
                    <tr class="level-1-1">
                        <td>
                            <span class="photo_count"></span>
                        </td>
                        <td>Upload <strong>1</strong> new photo
                        </td>
                        <td>
                            <a href="profile/editphotos"></a>
                        </td>
                    </tr>   
                    <?php } ?>
                    <?php if(in_array('1.2', $incomplete)) { ?>       
                    <tr class="level-1-2">
                        <td>
                            <span class="word_count"></span>
                        </td>
                        <td>Add <strong>20</strong> words to <?php echo $lvtext3; ?> description
                        </td>
                        <td>
                            <a href="profile"></a>
                        </td>
                    </tr>   
                    <?php } ?>
                    <?php if(in_array('1.3', $incomplete)) { ?>                   
                    <tr class="level-1-3">
                        <td>
                            <span class="email_verify"></span>
                        </td>
                        <td>Verify email address
                        </td>
                        <td>
                            <a href="profile/verifyemail"></a>
                        </td>
                    </tr>                    
                    <?php } ?>
                <?php } ?>
                
                <?php if($level == 2) { ?>
                    <?php if(in_array('2.1', $incomplete)) { ?>    
                    <tr class="level-2-1">
                        <td>
                            <span class="photo_count"></span>
                        </td>
                        <td>Upload <strong><?php echo max(2-$photo,0); ?></strong> new photo
                        </td>
                        <td>
                            <a href="profile/editphotos"></a>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php if(in_array('2.2', $incomplete)) { ?>    
                    <tr class="level-2-2">
                        <td>
                            <span class="profile"></span>
                        </td>
                        <td>Update <strong><?php echo max(5-$personal,0); ?></strong> more options on profiles
                        </td>
                        <td>
                            <a href="profile"></a>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php if(in_array('2.3', $incomplete)) { ?>    
                    <tr class="level-2-3">
                        <td>
                            <span class="paid"></span>
                        </td>
                        <td>Become a paid member
                        </td>
                        <td>
                            <a href="payment"></a>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php if(in_array('2.4', $incomplete)) { ?>    
                    <tr class="level-2-4">
                        <td>
                            <span class="msg_sent"></span>
                        </td>
                        <td>Send <strong><?php echo max(5-$messages,0); ?></strong> more messages
                        </td>
                        <td>
                            <a href="profiles/online"></a>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php if(in_array('2.5', $incomplete)) { ?>    
                    <tr class="level-2-5">
                        <td>
                            <span class="wink_sent"></span>
                        </td>
                        <td>Send <strong><?php echo max(5-$winks,0); ?></strong> more winks
                        </td>
                        <td>
                            <a href="profiles/online"></a>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php if(in_array('2.6', $incomplete)) { ?>    
                    <tr class="level-2-6">
                        <td>
                            <span class="add_favs"></span>
                        </td>
                        <td>Favourite <strong><?php echo max(5-$favourite,0); ?></strong> more profiles
                        </td>
                        <td>
                            <a href="profiles/online"></a>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php if(in_array('2.7', $incomplete)) { ?>    
                    <tr class="level-2-7">
                        <td>
                            <span class="like_photos"></span>
                        </td>
                        <td>Like <strong><?php echo max(5-$like,0); ?></strong> more photos
                        </td>
                        <td>
                            <a href="profiles/online"></a>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>

                <?php if($level == 3) { ?>    
                    <?php if(in_array('3.1', $incomplete)) { ?>    
                    <tr class="level-3-1">
                        <td>
                            <span class="photo_count"></span>
                        </td>
                        <td>Upload <strong><?php echo min(max(4-$photo,0),2); ?></strong> new photo
                        </td>
                        <td>
                            <a href="profile/editphotos"></a>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php if(in_array('3.2', $incomplete)) { ?>    
                    <tr class="level-3-2">
                        <td>
                            <span class="msg_sent"></span>
                        </td>
                        <td>Send <strong><?php echo min(max(10-$messages,0),5); ?></strong> more messages
                        </td>
                        <td>
                            <a href="profiles/online"></a>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php if(in_array('3.3', $incomplete)) { ?>    
                    <tr class="level-3-3">
                        <td>
                            <span class="add_favs"></span>
                        </td>
                        <td>Favourite <strong><?php echo min(max(10-$favourite,0),5); ?></strong> more profiles
                        </td>
                        <td>
                            <a href="profiles/online"></a>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php if(in_array('3.4', $incomplete)) { ?>    
                    <tr class="level-3-4">
                        <td>
                            <span class="like_photos"></span>
                        </td>
                        <td>Like <strong><?php echo min(max(10-$like,0),5); ?></strong> more photos
                        </td>
                        <td>
                            <a href="profiles/online"></a>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>

                </table>
            </div>
            <div class="clear">
                &nbsp;</div>
            <div id="levelup-about">
                <h3>
                    What's this about?</h3>
                <p>
                    Level Up is the latest way to track &amp; improve the status of <?php echo $lvtext3; ?> pinkmeets profile.
                    By completing the simple tasks you can become a prestige member and increase the
                    visibility of your account to other members. Get started now and stand out from
                    the crowd!
                </p>
            </div>
        </div>
        <div class="clear">
            &nbsp;</div>
    </div>
</div>
<script>
    var requireRefreshLevelup = false;
    function showLevelup() {
        if(requireRefreshLevelup){
            window.location = '/profile/levelup';
        }
        var h = 153 + 10 + 51 + 81 + 57 + $('#levelup-todo table tr').length * 36 + 57 + 54 + 10;
        showPopup("award-container", null, h + 20, '', 'award');
    }
    
	$(document).ready(function(){	
		changeLevelProgress(<?php echo $levelInf['percent']/100; ?>);
		$('#levelup-todo table td:last-child a').each(function(index, ele){
			var elejq = $(ele);
			var href = elejq.attr('href');
			if(href == '/profile' || href == '/profile/'){
				elejq.attr('href', 'javascript:void();');
				elejq.click(function(evt){
					$('#award-container .ui-dialog-system a').click();
					evt.preventDefault();
                    requireRefreshLevelup = true;
				});
			}
			if(href == '/profile/editphotos'){
				elejq.attr('href', 'javascript:void();');
				elejq.click(function(evt){
					$('#award-container .ui-dialog-system a').click();
					evt.preventDefault();
					uploadPhoto();
                    requireRefreshLevelup = true;
				});
			}
		});	
		
	});
	
    function changeLevelProgress(r){
        if(r<0) r=0;
        if(r>1) r = 1;
        var rtext = ' ' + Math.floor(r*100) + '%';
        $('#award-progress-pbText').html(rtext);
        var jqimg = $('#award-progress-pbImage');
        jqimg.css('background-position-x', (450 * r - 450) + 'px');
        jqimg.attr('alt', rtext);
        jqimg.attr('title', rtext);
    }
</script>