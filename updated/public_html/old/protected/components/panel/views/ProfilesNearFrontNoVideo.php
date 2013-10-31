<div id="NearBox">
    
    
    <h5>Members near <?php echo Yii::app()->user->location('city') ?></h5>
    
   
    <ul class="profiles front">
        <?php 
        if ($profiles)
                
            foreach ($profiles as $k=>$r) { ?>

                <li>
                    <?php 
                    $profile = new Profile($r['id']);                        
                    $profileLink = '/profile/'.Yii::app()->secur->encryptID($profile->getDataValue('id'));
                    
                    $this->widget('application.components.ProfileBoxWidget', array('id'=>$r['id'], 'imgSize'=>'152x114', 'infoType'=>8, 'forceRegistration'=>false));
                    ?>
                    
                    <div class="name">
                        <a href="<?php echo $profileLink ?>">
                            <?php
                            if (!$b)
                            {
                                //echo CHelperProfile::truncName($profile->getDataValue('username'), 24);
                                $res = CHelperProfile::truncName($profile->getDataValue('username'), 14).',&nbsp;'.$profile->getDataValue('age');
                                echo $res;                                
                                
                            }

                            else 
                            {
                                $len = 28;
                                $res = CHelperProfile::truncName($profile->getDataValue('username'), 10).',&nbsp;'.$profile->getDataValue('age').', '.$r['city'];
                                if (strlen($res) > $len)
                                {
                                    $newCityLen = strlen($r['city']) - (strlen($res)-$len) - 3;
                                    $res = CHelperProfile::truncName($profile->getDataValue('username'), 10).',&nbsp;'.$profile->getDataValue('age').',&nbsp;'. CHelperProfile::truncStr($r['city'], $newCityLen);
                                } 
                                echo $res;
                            }
                            ?>
                        </a>
                    </div>
                    
                    <div class="videoDuration">
                        <?php 
                        $videoInfo = $profile->getDataValue('video'); 
                        echo CHelperDate::secToTime($videoInfo['duration']);
                        ?>
                    </div>
                    
                    <div class="clear"></div>
                    
                </li>

        <?php } ?>
    </ul>
    
    <div class="clear"></div>
    
</div>


