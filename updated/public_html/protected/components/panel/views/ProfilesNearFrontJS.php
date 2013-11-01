

<div id="NearBox">
    
    
    <h5>Members near <?php echo Yii::app()->user->location('city') ?></h5>
    
    <?php /**/ ?>
    <div id="videoBoxFront"></div>
    
   
    <ul class="profiles">
        <?php 
        if ($profiles)
        
            foreach ($profiles as $k=>$r) { ?>
            
            <li>
                <?php $profile = new Profile($r['id']); ?>
                <div>
                <?php if ($profile) { ?>

                    <div class="imgBox">

                        <a href="javascript:void(0)" onclick="javascript:openGuestVideoDialog('<?php echo Yii::app()->secur->encryptID($r['id']) ?>')">
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
        function openGuestVideoDialog(id/*, player*/)
        {
                    $("#videoBoxFront").hide();
                    
                    $.post(
                        '/video/videoPlayer', 
                        {id:id, player:'js'}, 
                        function(data) {
                            $("#videoBoxFront").html(data);
                            //VideoJS.setupAllWhenReady();
                            $("#videoBoxFront").show();
                            $("#videoBoxFront video").VideoJS();
                            //$("#videoBoxFront video").player.play();
                            //$("#videoBoxFront video").player.width(640);
                            //$("#videoBoxFront video").player.height(360);
                
                            //$('#guestVideoDialog').modal('show');
                        },
                        "html"
                    );                
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