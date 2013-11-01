<?php 
$profileLink = '/profile/'.Yii::app()->secur->encryptID($profile->getDataValue('id'));
$data = $profile->getData();
?>

<div class="profileUserBox box_rs1">
    <div class="img">
        <img src="<?php echo $profile->imgUrl($this->imgSize) ?>" />
    </div>
    
    <div class="username">
        <?php echo CHelperProfile::truncName($data['username']) ?>
    </div>
    
    <span class="menu">
        <a href="<?php echo $profileLink ?>">View</a>
    </span>
    <span class="menu">
        <a href="/profile/edit">Edit Profile</a>
    </span>
    <span class="menu last">
            <a href="/profile/myVideos">Videos</a>
    </span>
</div>
