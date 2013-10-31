<?php
$levelup = new LevelUp($profile);
    $level = 1;
    $photo = 0;
    $profilecount = 0;
    $email = 0;
    $wink=0;
    $favourite = 0;
    $like = 0;
?>
<div id="award-container" style="display: block;">
    <div style="width: 740px; background-color: White;" id="award">
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
                Hey <span class="award-name">you</span>, your progress to
                <!--<img src="/images/img/levelup/txt.diva.png">-->
                <span class="award-text-4"></span>status!
            </h2>
            <div id="award-progress-content">
                <div id="award-progress">
                    <img id="award-progress-pbImage" title=" 0%" alt=" 0%" src="/images/img/levelup/progressbar.bg.png"
                        width="450" height="28"><span id="award-progress-pbText"> 0%</span></div>
            </div>
            <div id="levelup-todo">
                <h3>
                    To achieve <span class="award-text-<?php echo ($level +1); ?>"></span>you need to...</h3>
                <table>
                    <tr class="level-1-1">
                        <td>
                            <span class="photo_count"></span>
                        </td>
                        <td>Upload <strong>1</strong> new photo
                        </td>
                        <td>
                            <a href="/profile/editphotos"></a>
                        </td>
                    </tr>                    
                    <tr class="level-1-2">
                        <td>
                            <span class="word_count"></span>
                        </td>
                        <td>Add <strong>20</strong> words to your description
                        </td>
                        <td>
                            <a href="/profile"></a>
                        </td>
                    </tr>                    
                    <tr class="level-1-3">
                        <td>
                            <span class="email_verify"></span>
                        </td>
                        <td>Verify email address
                        </td>
                        <td>
                            <a href="/profile/verifyemail"></a>
                        </td>
                    </tr>

                    
                    <tr class="level-2-1">
                        <td>
                            <span class="photo_count"></span>
                        </td>
                        <td>Upload <strong><?php echo max(2-$photo,0); ?></strong> new photo
                        </td>
                        <td>
                            <a href="/profile/editphotos"></a>
                        </td>
                    </tr>
                    <tr class="level-2-2">
                        <td>
                            <span class="profile"></span>
                        </td>
                        <td>Update <strong><?php echo max(5-$profilecount,0); ?></strong> more options on profiles
                        </td>
                        <td>
                            <a href="/profile"></a>
                        </td>
                    </tr>
                    <tr class="level-2-3">
                        <td>
                            <span class="paid"></span>
                        </td>
                        <td>Become a paid member
                        </td>
                        <td>
                            <a href="/payment"></a>
                        </td>
                    </tr>
                    <tr class="level-2-4">
                        <td>
                            <span class="msg_sent"></span>
                        </td>
                        <td>Send <strong><?php echo max(5-$email,0); ?></strong> more messages
                        </td>
                        <td>
                            <a href="/online"></a>
                        </td>
                    </tr>
                    <tr class="level-2-5">
                        <td>
                            <span class="wink_sent"></span>
                        </td>
                        <td>Send <strong><?php echo max(5-$wink,0); ?></strong> more winks
                        </td>
                        <td>
                            <a href="/online"></a>
                        </td>
                    </tr>
                    <tr class="level-2-6">
                        <td>
                            <span class="add_favs"></span>
                        </td>
                        <td>Favourite <strong><?php echo max(5-$favourite,0); ?></strong> more profiles
                        </td>
                        <td>
                            <a href="/online"></a>
                        </td>
                    </tr>
                    <tr class="level-2-7">
                        <td>
                            <span class="like_photos"></span>
                        </td>
                        <td>Like <strong><?php echo max(5-$like,0); ?></strong> more photos
                        </td>
                        <td>
                            <a href="/online"></a>
                        </td>
                    </tr>

                    
                    <tr class="level-3-1">
                        <td>
                            <span class="photo_count"></span>
                        </td>
                        <td>Upload <strong><?php echo min(max(4-$photo,0),2); ?></strong> new photo
                        </td>
                        <td>
                            <a href="/profile/editphotos"></a>
                        </td>
                    </tr>
                    <tr class="level-3-2">
                        <td>
                            <span class="msg_sent"></span>
                        </td>
                        <td>Send <strong><?php echo min(max(10-$email,0),5); ?></strong> more messages
                        </td>
                        <td>
                            <a href="/online"></a>
                        </td>
                    </tr>
                    <tr class="level-3-3">
                        <td>
                            <span class="add_favs"></span>
                        </td>
                        <td>Favourite <strong><?php echo min(max(10-$favourite,0),5); ?></strong> more profiles
                        </td>
                        <td>
                            <a href="/online"></a>
                        </td>
                    </tr>
                    <tr class="level-3-4">
                        <td>
                            <span class="like_photos"></span>
                        </td>
                        <td>Like <strong><?php echo min(max(10-$like,0),5); ?></strong> more photos
                        </td>
                        <td>
                            <a href="/online"></a>
                        </td>
                    </tr>

                </table>
            </div>
            <div class="clear">
                &nbsp;</div>
            <div id="levelup-about">
                <h3>
                    What's this about?</h3>
                <p>
                    Level Up is the latest way to track &amp; improve the status of your pinkmeets profile.
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
    function showLevelup() {
        showPopup("award-container", null, null, '', 'award');
    }
    
    function changeLevelProgress(r){
        if(r<0) r=0;
        if(r>1) r = 1;
        var rtext = Math.floor(r*100) + '%';
        $('#award-progress-pbText').html(rtext);
        $('#award-progress-pbImage').css('background-position-x', (450*r - 450) + 'px');
    }
</script>

<div id="award-current" class="award-header award-header-<?php echo ($level); ?>">
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
            </tr>
        </table>
        <div class="clear">
        </div>
    </div>
</div>

<div class="award-status award-icon-<?php echo ($level); ?>"></div>
