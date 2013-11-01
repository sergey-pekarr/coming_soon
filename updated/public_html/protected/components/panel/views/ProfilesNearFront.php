<div id="NearBox">
    
    
    <h5>Members near <?php echo Yii::app()->user->location('city') ?></h5>
    
   
    <ul class="profiles">
        <?php 
        if ($profiles)
        
            foreach ($profiles as $k=>$r) { ?>
            
            <li>
                <?php $profile = new Profile($r['id']); ?>
                <div>
                <?php if ($profile) { ?>

                    <div class="imgBox">
                   
<a
    class="sublime" 
    style="cursor: pointer;" 
    href="<?php echo $profile->videoUrl(0, true, 'mp4') ?>" 
    onclick="javascript:metricVideoPlay('<?php echo Yii::app()->secur->encryptID( $profile->getDataValue('id') ) ?>');"
    <?php /* if (METRICS_ID) { ?>
        onclick="javascript:_springMetq.push(['setdata', {'Video view (<?php echo $profile->getDataValue('username') ?>)': 'YES'}]);"
    <?php }*/ ?>    
>
    
   <img src="<?php echo $profile->imgUrl('152x114') ?>" alt="" />
   <span class="videoIcon"></span>
</a>
<video 
        style="display:none"
        class="sublime zoom"
        width="640" height="360"
        poster="<?php //echo $profile->imgUrl('original') ?>"
        preload="none" >
    <source src="<?php echo $profile->videoUrl(0, true, 'mp4') ?>" />
    <source src="<?php echo $profile->videoUrl(0, true, 'ogv') ?>" />
</video>                        

                                           
                    
                    <?php /*
                    <a href="javascript:void(0)" onclick="javascript:openGuestVideoDialog('<?php echo Yii::app()->secur->encryptID($r['id']) ?>')">
                        <div class="videoIcon">
                            <img src="/images/design/video-icon.png" />
                        </div>                    
                        <img src="<?php echo $profile->imgUrl('152x114') ?> " />
                    </a> */ ?>
                    
                    </div>
                    
                    <div class="name">
                        <?php 
                        if (!$b)
                            echo CHelperProfile::truncName($profile->getDataValue('username'), 24);
                        else 
                        {
                            $len = 28;
                            $res = CHelperProfile::truncName($profile->getDataValue('username'), 10).', '.$profile->getDataValue('age').', '.$r['city'];
                            if (strlen($res) > $len)
                            {
                                $newCityLen = strlen($r['city']) - (strlen($res)-$len) - 3;
                                $res = CHelperProfile::truncName($profile->getDataValue('username'), 10).', '.$profile->getDataValue('age').', '. CHelperProfile::truncStr($r['city'], $newCityLen);
                            } 
                            echo $res;
                        }
                        ?>                        
                    </div>
                    
                <?php } else { ?>
                    <img src="/images/nophoto_female_152x114.png" />
                    <div class="name"></div> 
                <?php } ?>
                </div>
                
            </li>
        
        <?php } ?>
    </ul>
    
    <div class="clear"></div>
    
    <script type="text/javascript">
        /*sublimevideo.ready(function() {
            
            sublimevideo.onStart(function(sv) {
            
            var strId = sv.element.id;
            strId = strId.replace(/video_/g,"");
            restorePlayerVideoContent(strId);
                       
          });            
            
        sublimevideo.onEnd(function(sv) {
            
            var strId = sv.element.id;
            strId = strId.replace(/video_/g,"");

$("#sublime_video_wrapper_"+strId+" .sublime_video_content").hide().after('<div class="afterPlayBox"> <div class="videoTopBox" >&nbsp;</div> <div class"videoLinks"> <a href="#" onclick="javascript:videoPlayAgain('+strId+')">Replay</a> | <a href="#" id="close222" onclick="javascript:playerBeforeSignup('+strId+')">Sign up to see members in your area!</a> </div> </div>');
            
            //sublimevideo.stop();            
          });
        });
        
        function videoPlayAgain(id)
        {
            restorePlayerVideoContent(id);
            sublimevideo.play("video_"+id);
        }
        
        function restorePlayerVideoContent(id)
        {
            $("#sublime_video_wrapper_"+id+" .afterPlayBox").remove();
            $("#sublime_video_wrapper_"+id+" .sublime_video_content").show();
            return false;
        }
        
        function playerBeforeSignup(id)
        {
            $("#sublime_zoom").hide();
            $("#sublime_zoom").prev('div').remove();
            
            //window.location.reload(); //restorePlayerVideoContent(id);
            
            //$("html").removeAttr('id');
            
            //$("body #UserRegistrationForm_birthday_year").click();
            //$("#UserRegistrationForm_birthday_year").click();
            
            //$("#sublime_zoom").remove();          
            
            
            //sublimevideo.unprepare("video_"+id);
            //sublimevideo.prepare("video_"+id);
        }*/

    </script>
    
</div>


