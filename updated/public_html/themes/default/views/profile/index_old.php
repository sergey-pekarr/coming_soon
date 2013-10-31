<?php

//FB::info($profile->getDataValue('id'), 'PROFILE ID');


//$videoList = $profile->getInfoValue('video');
$imgPrimary = $profile->getInfoValue('img_primary');
$images = $profile->getInfoValue('imgs');
$n = $images[$imgPrimary];
//FB::error($n);

$streamName = '/video/'.Yii::app()->secur->encryptID($profile->getDataValue('id')).'/'.$n.'.mp4';

//FB::error($streamName);

$isOwner = ( $profile->getDataValue('id') == Yii::app()->user->id ) ? true : false;
$imgPreview = $profile->imgUrl('original');


$profileData = $profile->getData();


$imgUrl = $imgPreview;
$videoUrl = SITE_URL.$streamName;
$autoplay = 'false';
?>


<div class="top profile-view">
    
    <div class="short-info">
        <div class="color2 name info-username">
            <?php echo $profile->getDataValue('username') ?>

        </div>
        
        <div class="otherInfo">
            <?php echo $profile->getDataValue('age') ?>
            year old
            <?php echo CHelperProfile::textGender( $profile->getDataValue('gender') ); ?>
        </div>        
        
        <?php $this->widget('application.components.userprofile.UserReportAbuseFormWidget', array('id'=>$profile->getDataValue('id'))); ?>

        <div class="clear"></div>
    </div>
    
    <div class="full-info">
        <div>
        
        </div>
    </div>
        

</div>








<div class="middle">

    <div class="left">
        <div 
            class="videoProfileBox <?php if (!$profileData['pics']) { ?>noVideo<?php } ?>" 
            style="width: <?php echo CHelperPlayer::getPlayerWidth($profileData['video']['ratio']) ?>px; height: <?php echo CHelperPlayer::getPlayerHeight($profileData['video']['ratio']) ?>px;"
        >
            
            <?php if (!$profileData['pics']) { ?>
                <div class="videoProfileNoVideo">
                    <?php echo $profileData['username'] ?> have no video
                </div>
            <?php } elseif ( 1==1/*Yii::app()->user->checkAccess('approved') || $isOwner*/ ) { ?>
                
                <?php if ( !CHelperPlayer::playerSublime() ) { ?>

 
                    <video 
                        id="videoBox" 
                        width ="<?php echo CHelperPlayer::getPlayerWidth($profileData['video']['ratio']) ?>" 
                        height="<?php echo CHelperPlayer::getPlayerHeight($profileData['video']['ratio']) ?>" 
                        poster="<?php echo $profile->imgUrl('original') ?>" 
                        preload="none"
                    >
                      <source src="<?php echo $profile->videoUrl(0,true,'mp4') ?>" />
                    </video>
                
                    <script type="text/javascript">
                        $(document).ready(function() 
                        {
                            jwplayer("videoBox").setup(//jwplayer("videoBox_"+k).setup(//
                            {
                                'skin': "<?php echo SITE_URL ?>/js/jwplayer/skins/glow/glow.zip",
                                'controlbar': 'over',
                                'modes': [
                                    { type: 'html5' },
                                    { type: 'flash', src: '<?php echo SITE_URL ?>/js/players/jwplayer/player.swf' } 
                                ],
                                events: {
                                    onPlay: function() {
                                        
                                        metricVideoPlay('<?php echo Yii::app()->secur->encryptID($profileData['id']) ?>');
                                        
                                        <?php if (METRICS_ID) { ?>
                                        _springMetq.push(['setdata', {'Video view (<?php echo $profile->getDataValue('username') ?>)': 'YES'}]);
                                        <?php } ?>
                                    }
                                }
                                

                            });
                        });                            
                    </script>
                
                <?php } else { ?>
                    <video 
                        class="sublime" 
                        width ="<?php echo CHelperPlayer::getPlayerWidth($profileData['video']['ratio']) ?>" 
                        height="<?php echo CHelperPlayer::getPlayerHeight($profileData['video']['ratio']) ?>" 
                        poster="<?php echo $profile->imgUrl('original') ?>" 
                        preload="none"
                    >
                      <source src="<?php echo $profile->videoUrl(0,true,'mp4') ?>" />
                      <source src="<?php echo $profile->videoUrl(0,true,'ogv') ?>" />
                    </video>
                    
                    <script type="text/javascript">
                        sublimevideo.ready(function() {
                            sublimevideo.onStart(function(sv) {
                                    
                                metricVideoPlay('<?php echo Yii::app()->secur->encryptID($profileData['id']) ?>');
                                
                                <?php if (METRICS_ID) { ?>
                                _springMetq.push(['setdata', {'Video view (<?php echo $profile->getDataValue('username') ?>)': 'YES'}]);
                                <?php } ?>                            
                            });
                        }); 
                    </script>                      
                    
                                    
                <?php } ?>
                
            <?php } else { ?>

                <video 
                    class="sublime" 
                    width ="<?php echo CHelperPlayer::getPlayerWidth($profileData['video']['ratio']) ?>" 
                    height="<?php echo CHelperPlayer::getPlayerHeight($profileData['video']['ratio']) ?>"                     
                    poster="<?php echo $profile->imgUrl('original') ?>" 
                    preload="none"
                >
                    <source src="" />
                </video>
                
                <?php /*
                <script type="text/javascript">
                    $(document).ready(function() 
                    {
                        $("#noAccess").modal({backdrop:'static'}).modal('show');
                    })
                </script>
                */ ?>
            <?php } ?>
        </div>        

    </div>

    <div class="personal-info2 right" style="height: <?php echo CHelperPlayer::getPlayerHeight($profileData['video']['ratio']) ?>px;">
        <?php $this->widget('application.components.userprofile.UserPersonalShowWidget', array('profile'=>$profile)); ?>
        
        <div class="profileActions">
            <?php
                //ACTIONS 
                include "_indexActions.php"; 
            ?>
        </div>
        
    </div>    

    
    <div class="cls"></div> 
    
<?php /*       
    <div class="middle2">
        <div class="images">
            <?php if (VIDEO_COUNT_MAX>1) { ?>
                <ul>
                    <?php for ($i=0; $i<VIDEO_COUNT_MAX; $i++) { ?>
                        <li <?php if ($i==$profile->getInfoValue('img_primary')) : ?> class="primary-img" <?php endif; ?>  >
                            
                            <?php 
                                if ($n = $images[$i])
                                {
                                    //$streamName = $videoList[$n];
                                    $streamName = '/video/'.Yii::app()->secur->encryptID($profile->getDataValue('id')).'/'.$n;
                                    
                                    $imgPreview = $profile->imgUrl('original', $i, false);                                    
                                }

                            ?>
                            
                            <a 
                                class="active" 
                                href="javascript:void(0)"  
                                <?php if ($n) { ?>
                                
                                    <?php if (Yii::app()->user->checkAccess('approved') || $isOwner) { ?>
                                        onclick='javascript:playerSetup("<?php echo $streamName ?>", "<?php echo $imgPreview ?>", true);'
                                    <?php } else { ?>
                                        onclick='javascript:playerNoAccess();'
                                    <?php } ?>                                 
                                <?php } ?>
                            >
                                <?php $this->widget( 'application.components.img.ImgAnimWidget', array('userId'=>$profile->getDataValue('id'), 'i'=>$i)); ?>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>
            
        </div>
        
        <div class="right">

            <?php 
            if (!$isOwner && Yii::app()->user->checkAccess('limited')) { ?>
            <a href="#" data-controls-modal="sendMessageBox" data-backdrop="static" >
                <span class="messAct">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Send Message
            </a>
            <br />
            
            <?php 
            $modelWinks = new Winks;
            //$winksAlreadySent = $modelWinks->getWinksFromTo($profile->getDataValue('id'), Yii::app()->user->id);
            ?>
            <a 
                id="sendWink" 
                <?php if ($winksAlreadySent) { ?>
                    class="winkSent"
                    title='Already sent'
                <?php } else { 
                    $id_to = Yii::app()->secur->encryptID( $profile->getDataValue('id') );
                    ?> 
                    href="javascript:void(0)" onclick="javascript:sendWink('<?php echo $id_to ?>')" 
                <?php } ?> 
            >
                <span class="winkAct">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Send Wink
            </a>
            <br />
            
            <a href="javascript:void(0)">
                <span class="hotAct">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Add to Hotlist
            </a>
            <?php } ?>

        </div> 
       
    
        <div class="cls"></div> 
    </div>
    <div class="cls"></div> 
*/ ?>     
    <?php $this->widget('application.components.userprofile.ProfilesInterestWidget', array('countNeed'=>5, 'excludeIdForce'=>$profile->getDataValue('id'))); ?>

</div>











<?php 
    
    //$this->widget('application.components.event.AccessDeniedWidget');

    /*
    if ( !Yii::app()->user->checkAccess('approved') ) { ?>
    <div id="noAccess" class="modal hide fade viewVideo">
                    <br />
                    <p style="font-weight: bold"> 
                        <span style="color:red">ACCESS DENIED</span> 
                        <br /><br /> 
                        Your account currently has limited access to profiles. 
                        <br /> 
                        To get full access you are required to have a profile video. 
                        <br /> 
                        Create your profile video now. 
                    </p>
                    <br />
                    <p>
                        <a href="javascript:void(0)" onclick="javascript:window.location.href = '/profile/myVideos'" class="btn" style="margin-right: 20px;">Record my video</a>
                        <a href="javascript:void(0)" onclick="javascript:$('#noAccess').modal('hide');" style="color:grey">Skip for now &raquo;</a>
                    </p>                                       
    </div>
<?php } */ ?>





<?php if ( Yii::app()->user->checkAccess('limited') ) $this->widget('application.components.message.SendFormWidget', array('profile'=>$profile, 'bubble'=>true)); ?>