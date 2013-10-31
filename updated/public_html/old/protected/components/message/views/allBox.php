<?php
    
$fromUser = ($m['id_from']==Yii::app()->user->id) ? true : false;
$toUser = !$fromUser;
    
$messUserId = $m['id_from'];//($fromUser) ? $m['id_from'] : $m['id_to'];
    
if ($m['video']=='1') 
{
    $dirMessageUrl  = CHelperProfile::getUserImgUrl($messUserId).'/messages';
    $videoUrl       = $dirMessageUrl.'/'.$m['id'].'.flv';
    $imgUrl         = $dirMessageUrl.'/'.$m['id'].'.png';
    $imgThUrl       = $dirMessageUrl.'/'.$m['id'].'_84x47.png';
} 
?>

<table class="messageAll">
<tr>
    <?php if ($fromUser) { ?>
        <td class="messageAllFromUser">
            
                <img src="<?php echo Yii::app()->user->Profile->imgUrl('84x47'); ?>" />
            
        </td>
    <?php } ?>

    <td class="messageAllMessage">
        <div class="messageAll<?php echo ($fromUser) ? 'From' : 'To' ?> <?php if ($m['video']=='1') {?>messageAllMessageVideo<?php } ?> ">
            
            <div class="messageAllSubject">
                <?php if ($m['welcome']!='1') { ?>
                    <?php echo $m['subject'] ?>
                <?php } else { ?>
                    <span class="welcome">Welcome!</span>
                <?php } ?>
            </div>
                    
            <div class="messageAllAdded">
                <?php echo CHelperDate::date_distanceOfTimeInWords( strtotime($m['added']), time()) ?>
                ago
            </div>                    
            <div class="clear"></div>            
            
            <?php if ($m['video']=='1') { ?>
            <div class="messageAllMessageVideoBox" >

                    <?php if ( !CHelperPlayer::playerSublime() ) { ?>
    
     
                        <video id="videoBox_<?php echo $m['id'] ?>" width="640" height="360" poster="<?php echo $imgUrl ?>" preload="none">
                          <source src="<?php echo $videoUrl ?>" />
                        </video>
                    
                        <script type="text/javascript">
                            $(document).ready(function() 
                            {
                                jwplayer("videoBox_<?php echo $m['id'] ?>").setup(
                                {
                                    'skin': "<?php echo SITE_URL ?>/js/jwplayer/skins/glow/glow.zip",
                                    'controlbar': 'over',
                                    'modes': [
                                        { type: 'html5' },
                                        { type: 'flash', src: '<?php echo SITE_URL ?>/js/players/jwplayer/player.swf' } 
                                    ]
                                });
                            });                            
                        </script>
                    
                    <?php } else { ?>
                        <video class="sublime" width="640" height="360" poster="<?php echo $imgUrl ?>" preload="none">
                          <source src="<?php echo $videoUrl ?>" />
                        </video>
                        
                        <script type="text/javascript">
                            $(document).ready(function() 
                            {
                                sublime.load();
                            });                            
                        </script>                        
                        
                    <?php } ?>

                                                  
                <?php } else { ?>
                    
                    <?php echo $m['text'] ?>                
                            
                <?php } ?>
            </div>
        </div>
    
    </td>
    
    <?php if ($toUser) { ?>
        <td class="messageAllToUser">
            
                <?php $this->widget('application.components.ProfileBoxWidget', array('id'=>$messUserId, 'imgSize'=>'84x47', 'infoType'=>6)); ?>
        </td>
    <?php } ?>

</tr>
</table>

<?php return; ?>

















<li id="messBox_<?php echo $m['id'] ?>" class="messBox" >

    <div class="image">
        <a href="/profile/<?php echo Yii::app()->secur->encryptID($id) ?>">
            <img src="<?php echo $profile->imgUrl('84x47'); ?>" />
        </a>
    </div>
    
    <div class="username">
        <a href="/profile/<?php echo Yii::app()->secur->encryptID($id) ?>" class="color2">
            <?php echo Yii::app()->helperProfile->truncName($profile->getDataValue('username'), 12); ?>
        </a>
    </div>    
    
    <div class="message">
        <?php if ($inboxAll && !$m['readed']) { ?>
            <div class="messNew">new</div>
        <?php } ?>
        
        <?php if ($m['video']!='1') { ?>
            <div class="subject">
                <?php if ($m['welcome']!='1') { ?>
                    <a href="<?php echo $messageAllUrl ?>">    
                        <?php echo Yii::app()->helperProfile->truncStr($m['subject'], 25) ?>
                    </a>
                    &nbsp;
                <?php } else { ?>
                    Welcome
                <?php } ?>  
            </div>
        <?php } ?>
        
        <div class="text" id="messText_<?php echo $m['id'] ?>">

            <?php if ($m['video']=='1') {
                
                $dirMessageUrl  = CHelperProfile::getUserImgUrl($m['id_from']).'/messages';
                //$videoUrl       = $dirMessageUrl.'/'.$m['id'].'.flv';
                //$imgUrl         = $dirMessageUrl.'/'.$m['id'].'.png';
                $imgThUrl         = $dirMessageUrl.'/'.$m['id'].'_84x47.png'; 
                ?>
                                        
                <a href="<?php echo $messageAllUrl ?>" >
                    <img width="84" height="47" src="<?php echo $imgThUrl ?>" />
                </a>
                                              
            <?php } else { ?>
                
                <?php if ($m['welcome']!='1') { ?>
                
                    <?php echo Yii::app()->helperProfile->truncStr($m['text'], 60) ?>
                
                <?php } else { ?>
                
                    <span class="welcome">Welcome!</span>
            
                <?php } ?>
                
                &nbsp;&nbsp;
                
                <a href="<?php echo $messageAllUrl ?>" >Read</a>                
                
            <?php } ?>

        </div>
        
    </div>              

    <div class="clear"></div>

</li>






















<?php /*
<li id="messBox_<?php echo $m['id'] ?>" class="messBox" >

    <div class="image">
        <a href="/profile/<?php echo Yii::app()->secur->encryptID($id) ?>">
            <img src="<?php echo $profile->imgUrl('84x47'); ?>" />
        </a>
    </div>
    
    <div class="username">
        <a href="/profile/<?php echo Yii::app()->secur->encryptID($id) ?>" class="color2">
            <?php echo Yii::app()->helperProfile->truncName($profile->getDataValue('username'), 12); ?>
        </a>
    </div>    
    
    <div class="message">
        <?php if ($inboxAll && !$m['readed']) { ?>
            <div class="messNew">new</div>
        <?php } ?>
        
        <div class="subject">
            <?php if ($m['welcome']!='1') { ?>
                <a href="javascript:void(0)" onclick="javascript:readMessageTextFull(<?php echo $m['id'] ?>)">    
                    <?php echo Yii::app()->helperProfile->truncStr($m['subject'], 25) ?>
                </a>
                &nbsp;
            <?php } else { ?>
                Welcome
            <?php } ?>  
        </div>
        
        <div class="text" id="messText_<?php echo $m['id'] ?>">
            <?php if ($m['welcome']!='1') { ?>
                
                <?php if ($m['video']=='1') { 
                    
                    $dirMessageUrl  = CHelperProfile::getUserImgUrl($m['id_from']).'/messages';
                    $videoUrl       = $dirMessageUrl.'/'.$m['id'].'.flv';
                    $imgUrl         = $dirMessageUrl.'/'.$m['id'].'.png';
                    
                ?>
                    
                    
                    <?php if (CHelperPlayer::playerSublime()) { ?>
                        
                        <a class="sublime" style="cursor: pointer;" >
                           <img width="64" height="36" src="<?php echo $imgUrl ?>" alt="" />
                           <span class="videoIcon"></span>
                        </a>
                        <video 
                                style="display:none"
                                class="sublime zoom"
                                width="640" height="360"
                                poster="<?php echo $imgUrl ?>"
                                preload="none">
                            <source src="<?php echo $videoUrl ?>" />
                        </video>
                        
                        <script type="text/javascript">
                            $(document).ready(function()
                            {
                                sublimevideo.load();
                            });                            
                        </script>
                        
                    <?php } else { ?>
                    
                        <a href="javascript:void(0)" onclick="javascript:openJWVideoDialog('<?php echo $imgUrl ?>', '<?php echo $videoUrl ?>', '<?php echo SITE_URL ?>')">
                            <img width="64" height="36" src="<?php echo $imgUrl; ?> " />
                            <span class="videoIcon"></span>
                        </a>
                    
                    <?php } ?>
                    
                    <br />  
                          
                <?php } ?>                
                
                <?php echo Yii::app()->helperProfile->truncStr($m['text'], 60) ?>
            <?php } else { ?>
                <span class="welcome">Welcome!</span>
            <?php } ?>
            
            &nbsp;&nbsp;
            
            <a href="javascript:void(0)" onclick="javascript:readMessageTextFull(<?php echo $m['id'] ?>)" >Read</a>
        </div>
        
        <div class="textFull" id="messTextFull_<?php echo $m['id'] ?>">
            <?php if ($m['welcome']!='1') { ?>
                <?php echo $m['text'] ?>
            <?php } else { ?>
                <span class="welcome">Welcome!</span>
            <?php } ?>
            
            <br />
            
            <div style="text-align: right; font-weight: bold; margin-top: 10px;">
                <?php if ( $m['id_to']==Yii::app()->user->id ) { ?> 
                    <?php $this->widget('application.components.userprofile.UserReportAbuseFormWidget', array('id'=>$profile->getDataValue('id'))); ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;                
                <?php } ?>
                
                <?php if ( $m['readed']=='0' && $m['id_to']==Yii::app()->user->id ) { ?> 
                    <a href="javascript:void(0)" onclick="javascript:markAsReadMessage(<?php echo $m['id'] ?>, '<?php echo $inboxAll ?>')" >Mark as read</a>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                <?php } ?>
                <a href="javascript:void(0)" onclick="javascript:hidePrivateMessage(<?php echo $m['id'] ?>)" >Delete</a>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <a href="javascript:void(0)" onclick="javascript:closeMessageTextFull(<?php echo $m['id'] ?>)" >Close</a>
            </div>
        </div>        
        
    </div>              

    <div class="clear"></div>

</li>
*/ ?>

