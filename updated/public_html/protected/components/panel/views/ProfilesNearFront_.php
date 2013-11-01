<div id="NearBox">
    
    
    <h5>Members near <?php echo Yii::app()->user->location('city') ?></h5>

<div id="videoBoxFront"></div>

<?php /*
    <div id="guestVideoDialog" class="modal hide fade" style="width: 670px; height: 430px;">
        <div class="modal-header">
            <a href="#" class="close">&times;</a>
        </div>
        <div class="modal-body">
            <div id="videoBoxFront"></div>
        </div>
    </div>
*/?>
    
    <ul class="profiles">
        <?php 
        if ($profiles)
        
            foreach ($profiles as $r) { ?>
            
            <li>
                <?php //$this->widget('application.components.ProfileBoxWidget', array('id'=>$r['id'], 'imgSize'=>'152x114', 'infoType'=>6)); 
                $profile = new Profile($r['id']);
                ?>
                <div>
                <?php if ($profile) { 
                    /*$images = $profile->getInfoValue('imgs');
                    $imgPrimary = $profile->getInfoValue('img_primary');
                    $n = $images[$imgPrimary];
                    $streamName = SITE_URL.'/video/'.Yii::app()->secur->encryptID($profile->getDataValue('id')).'/'.$n.'.mp4';    
                    $imgPreview = $profile->imgUrl('original');*/
                ?>

                    <div class="imgBox">
                    <?php /* <a href="javascript:void(0)" onclick="javascript:openGuestVideoDialog('<?php echo $streamName ?>', '<?php echo $imgPreview ?>')"> */ ?>
                    <a href="javascript:void(0)" onclick="javascript:openGuestVideoDialog('<?php echo Yii::app()->secur->encryptID($r['id']) ?>')">
                        <div class="videoIcon">
                            <img src="/images/design/video-icon.png" />
                        </div>                    
                        <img src="<?php echo $profile->imgUrl('152x114') ?> " />
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
        function openGuestVideoDialog(id)
        {
            playerSetup(id);
        }
    </script>
    
</div>



