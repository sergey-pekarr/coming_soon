<?php 
if ($profiles)
foreach ($profiles as $k=>$r) 
{
$profile = new Profile($r['id']); ?> 

<?php /* <div class="videoBoxFront" id="videoBoxFront_<?php echo $k; ?>">
<div style="position: relative;">
    <div class="closeButton" style="display: none;">
        <a href="javascript:void(0)" onclick="javascript:$('.videoBoxFront').hide(); $('.closeButton').hide()">
            <img src="/images/design/video_close_button.png" />
        </a>
    </div>            


    <div class="videoBox2">
        <video width="640" height="360" id="videoBox_<?php echo $k; ?>" poster="<?php echo SITE_URL.'/'.$profile->imgUrl('original') ?>">
            <source src="<?php echo $profile->videoUrl(0,true,'mp4'); ?>" type="video/mp4" >
        </video>
    </div>        


</div>
</div>
*/ ?>

<?php /*
<video class="videoBoxFront" id="videoBoxFront_<?php echo $k; ?>" width="640" height="360" poster="<?php echo SITE_URL.'/'.$profile->imgUrl('original') ?>">
    <source src="<?php echo $profile->videoUrl(0,true,'mp4'); ?>" type="video/mp4" >
</video>
*/ ?>
<?php } ?>

<?php /* 
<div id="videoBoxFront" >
    <video id="videoBox" width="640" height="360" poster="<?php //echo SITE_URL.$profile->imgUrl('original') ?>" >
        <source src="<?php //echo $profile->videoUrl(0,true,'mp4'); ?>" type="video/mp4" >
    </video>
</div>


<div class="closeButton_2" style="display: none;">
    <a href="javascript:void(0)" onclick="javascript:$('#videoBoxFront').hide(); $('.closeButton_2').hide()">
        <img src="/images/design/video_close_button.png" />
    </a>
</div>
*/ ?>

<div id="NearBox">
    
    
    <h5>Members near <?php echo Yii::app()->user->location('city') ?></h5>




   
    <ul class="profiles">
        <?php 
        if ($profiles)
        
            foreach ($profiles as $k=>$r) 
            {
            
            $profile = new Profile($r['id']); ?> 
            <li>
                   
                <div>
                <?php if ($profile) { ?>
                    
                    <div class="imgBox">

                        <a href="javascript:void(0)" onclick="javascript:openGuestVideoDialog_2('<?php echo SITE_URL.$profile->imgUrl('original') ?>', '<?php echo $profile->videoUrl(0,true,'mp4'); ?>',    '<?php echo $profile->getDataValue('username') ?>')">
                            <img src="<?php echo SITE_URL.$profile->imgUrl('152x114') ?> " />
                            <span class="videoIcon"></span>
                        </a>
                    
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
        function openGuestVideoDialog_2(imgUrl, videoUrl, videoview)
        {
            $("#videoBoxFront").show();
            $(".closeButton_2").fadeIn(1000);
            //$("#videoBoxFront").attr('poster', imgUrl);
            $("#videoBoxFront source").attr('src', videoUrl);            
                        
            jwplayer("videoBox").setup(
            {
                'width': '640',
                'height': '360',
                'image': imgUrl,
                'levels': [
                   {
                       'file': videoUrl
                   }
                ],

                
                'skin': "<?php echo SITE_URL ?>/js/jwplayer/skins/glow/glow.zip",
                'controlbar': 'over',
                'modes': [
                            { type: 'html5'},
                            { type: 'flash', src: '<?php echo SITE_URL ?>/js/players/jwplayer/player.swf' } 
                ]
            });
            jwplayer().load(videoUrl);//!!!
            jwplayer().play();
            
            metricVideoPlay('<?php echo Yii::app()->secur->encryptID( $profile->getDataValue('id') ) ?>');
            
            <?php if (METRICS_ID) { ?>
                _springMetq.push(['setdata', {'Video view ('+videoview+'):': 'YES'}]);
            <?php } ?>
            
        }
    </script>


</div>

<?php /*
<div id="guestVideoDialog" class="modal hide fade" style="width: 670px; height: 430px;">
        <div class="modal-header">
            <a href="#" class="close">&times;</a>
        </div>
        <div class="modal-body">
            <div id="videoBoxFront"></div>
        </div>
</div>
*/ ?>