<?php
    $id = $profile->getDataValue('id');
?>
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
            <span class="when"><?php echo Yii::app()->helperDate->date_distanceOfTimeInWords(strtotime($m['added']), time()) ?> ago</span>
        </div>
        <div class="content <?php if ($m['welcome']=='1') echo 'ww-content' ?>">
            <?php if ($m['welcome']!='1') { ?>
                <?php echo Yii::app()->helperProfile->truncStr($m['text'], 200) ?>
                &nbsp;&nbsp;
            <?php } else { ?>
                <h2>Welcome!</h2>
                <br />
                <p class="ww-upload">
                    <a title="Upload pictures" href="/profile/edit">Upload</a>
                    a picture &rarr;
                </p>
                <p class="ww-edit">
                    <a title="Edit your profile info" href="/profile/edit">Edit</a>
                    your profile &rarr;
                </p>
                <p class="ww-search">
                    <a title="Search for a match" href="/search">Search</a>
                    to like and message &rarr;
                </p>
                <p class="ww-help">
                    <a title="Ask for help" href="/help">Questions</a>
                    ?
                </p>
                <p class="ww-username"> &mdash; <?php echo $profile->getDataValue('username'); ?></p>
                
            <?php } ?>
        </div>
    </div>                    
    <div class="message-footer"> </div>

    <div class="clear"></div>
                    
</li>

