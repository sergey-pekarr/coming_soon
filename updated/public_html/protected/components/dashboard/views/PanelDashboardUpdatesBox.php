<?php $id = $up['id']; ?>

<li>
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
        <div class="text">
            <?php 
                $txt = Updates::getUpdateKodName($up['kod'], $up['text'], $profile->getDataValue('gender'));
                $replLink = '<a href="/profile/'.Yii::app()->secur->encryptID($id).'">uploaded</a>'; 
                $txt = str_replace('[uploaded]', $replLink, $txt);
                echo $txt;
            ?>
        </div>
        
        <div class="when">
            <?php echo Yii::app()->helperDate->date_distanceOfTimeInWords( strtotime($up['added']), time()) ?> ago
        </div>
        
    </div>  
</li>

<?php /*
                    <li class="update">
                        <div class="image">
                            <a href="/profile/<?php echo Yii::app()->secur->encryptID($up['id']) ?>">
                                <img src="<?php echo $profile->imgUrl('small') ?>" />
                                <span class="premium"></span>
                            </a>
                        </div>                
                        
                        <div class="message">
                            <div class="header">
                                <a class="popup viewProfile" href="/profile/<?php echo Yii::app()->secur->encryptID($up['id']) ?>">
                                    <span class="name"> <?php echo $profile->getDataValue('username') ?> </span>
                                </a>
                                <span class="when"> <?php echo Yii::app()->helperDate->date_distanceOfTimeInWords( strtotime($up['added']), time()) ?> ago </span>                           
                            </div>
                            <div class="content">
                                <?php echo Updates::getUpdateKodName($up['kod'], $up['text'], $profile->getDataValue('gender')) ?>
                            </div>
                        </div>
                        <div class="message-footer"> </div>                    
                    </li>    

*/ ?>