<?php

            $players = array(
                'jw',
                'js',
                'litepro',
                'sublime'
            );
            $player = $_GET['player'];
            
            if (!in_array($player, $players))
            {
                echo 'bad player...';
                return;
            }

switch ($player)
{
    case 'js':
        //Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/players/jsplayer/video.js', POS_LOAD);
        //Yii::app()->clientScript->registerCssFile( "/js/players/jsplayer/video-js.css" );
        break;
    
    
    case 'jw':
        Yii::app()->clientScript->registerScriptFile( Yii::app()->baseUrl.'/js/players/jwplayer/jwplayer.js' ); 
        /*?>
        <script type="text/javascript" src="<?php echo SITE_URL ?>/js/players/jwplayer/jwplayer.js"></script>
        <script type="text/javascript">
            function setupJW()
            {
                jwplayer("videoBox").setup(
                {
                    modes: [
                        { type: 'html5' },
                        { type: 'flash', src: '<?php echo SITE_URL ?>/js/players/jwplayer/player.swf' }
                    ]
                });
            }
        </script>        
        
        <?php  */  
        break;
    case 'litepro':
        Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/players/litepro/js/swfobject.js');
        /*?>
        
        <script type="text/javascript" src="<?php echo SITE_URL ?>/js/players/litepro/js/swfobject.js"></script>
        
        <?php*/
        break;
        
    case 'sublime':
        ?>
        
        
        
        <?php
        break;
}

?>

<div id="NearBox">
    
    
    <h5>Members near <?php echo Yii::app()->user->location('city') ?></h5>

<div id="videoBoxFront"></div>

    
    <ul class="profiles">
        <?php 
        if ($profiles)
            
            foreach ($profiles as $k=>$r) { ?>
            
            <li>
                <?php //$this->widget('application.components.ProfileBoxWidget', array('id'=>$r['id'], 'imgSize'=>'152x114', 'infoType'=>6)); 
                $profile = new Profile($r['id']);
                ?>
                <div>
                <?php if ($profile) { ?>

                    <div class="imgBox">
                    <?php /* <a href="javascript:void(0)" onclick="javascript:openGuestVideoDialog('<?php echo $streamName ?>', '<?php echo $imgPreview ?>')"> */ ?>
                        <a href="javascript:void(0)" onclick="javascript:openGuestVideoDialog('<?php echo Yii::app()->secur->encryptID($r['id']) ?>', '<?php echo $player ?>')">
                            <img src="<?php echo $profile->imgUrl('152x114') ?> " />
                            <span class="videoIcon"></span>
                        </a>
                    </div>
                    
                    <div class="name">
                        <?php echo CHelperProfile::truncName($profile->getDataValue('username'), 24); ?>
                    </div>
                    
                    <?php 
                    
                    /*$len = 24;
                    $res = $profile->getDataValue('age').', '.$profile->getDataValue('gender').', '.$r['city'].', '.$r['country'];
                    if (strlen($res) > $len)
                    {
                        $newCityLen = strlen($r['city']) - (strlen($res)-$len) - 3;
                        $res = $profile->getDataValue('age').', '.$profile->getDataValue('gender').', '. CHelperProfile::truncStr($r['city'], $newCityLen).', '.$r['country'];
                    } 
                    echo $res;*/
                    ?> 
                    
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
        function openGuestVideoDialog(id, player)
        {
            switch (player)
            {

                
                case 'js': 
                    
                    $("#videoBoxFront").hide();
                    
                    $.post(
                        '/video/videoTestPlayer', 
                        {id:id, player:player}, 
                        function(data) {
                            $("#videoBoxFront").html(data);
                            //VideoJS.setupAllWhenReady();
                            $("#videoBoxFront").show();
                            $("#videoBoxFront video").VideoJS();
                            //$("#videoBoxFront video").player.play();
                            $("#videoBoxFront video").player.width(640);
                            $("#videoBoxFront video").player.height(360);
                
                            //$('#guestVideoDialog').modal('show');
                        },
                        "html"
                    );                      
                    
                    break;                
                
                case 'jw': 

                    $("#videoBoxFront").hide();
                    
                    $.post(
                        '/video/videoTestPlayer', 
                        {id:id, player:player}, 
                        function(data) {
                            $("#videoBoxFront").html(data);
                            $("#videoBoxFront").show();
                            //setupJW();
                            jwplayer("videoBox").setup(
                            {
                                modes: [
                                    { type: 'html5' },
                                    { type: 'flash', src: '<?php echo SITE_URL ?>/js/players/jwplayer/player.swf' }
                                ]
                            });
                        },
                        "html"
                    );                
                
                    break;
                
                
                case 'litepro':  
                    
                    $("#videoBoxFront").hide();
                    
                    $.post(
                        '/video/videoTestPlayer', 
                        {id:id, player:player}, 
                        function(data) {
                            $("#videoBoxFront").html(data);
                            $("#videoBoxFront").show();
                        	swfobject.embedSWF("swf/standalone/playerLite.swf", "playerLite", flashvars.vidWidth, flashvars.vidHeight, "9.0.0","swf/expressInstall.swf", flashvars, params, attributes);
                    	

                        },
                        "html"
                    );
                                     
                    break;
                    
                case 'sublime':  
                    
                    $("#videoBoxFront").hide();
                    
                    $.post(
                        '/video/videoTestPlayer', 
                        {id:id, player:player}, 
                        function(data) {
                            $("#videoBoxFront").html(data);
                            $("#videoBoxFront").show();
                        },
                        "html"
                    );
                                     
                    break;                    
                    
            }
        }
    </script>
    
</div>



