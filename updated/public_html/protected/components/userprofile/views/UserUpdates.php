<?php
$id = $profile->getDataValue('id');
    
foreach ($updates as $up) {    
?>
    <div class="messages-list wide-messages">
        <ul id="profile-updates-list">
            <li>
                <div class="image">
                    <a href="/profile/<?php echo Yii::app()->secur->encryptID($id) ?>">
                        <img src="<?php echo $profile->imgUrl('small'); ?>" />
                    </a>
                </div>
                                
                <div class="message">
                    <div class="header">
                        <a href="/profile/<?php echo Yii::app()->secur->encryptID($id) ?>">
                            <span class="name"><?php echo $profile->getDataValue('username'); ?></span>
                        </a>
                        
                        <?php if ($edit) { ?>
                            <a href="#" onclick="javascript:deleteUpdate(<?php echo $up['id'] ?>, this); return false" title="Delete update" class="delete">Delete</a>
                        <?php } ?>
                        
                        <span class="when"><?php echo Yii::app()->helperDate->date_distanceOfTimeInWords(strtotime($up['added']), time()) ?> ago</span>
                    </div>
                    <div class="content"><?php echo Updates::getUpdateKodName($up['kod'], $up['text'], $profile->getDataValue('gender')) ?></div>
                </div>                    
                <div class="message-footer"> </div>
            
                <div class="clear"></div>
                                
            </li>
        
        </ul>
    </div>

<?php } ?>