<div id="UserLikeForm" class="form">

<?php 

$HisHer = ($profile->getDataValue('gender')=='F') ? "her" : "him";
$SheHe = ($profile->getDataValue('gender')=='F') ? "she" : "he";

FB::warn($likeToIs.'-'.$likeFromIs);

if ($likeToIs==1 && ($likeFromIs==0 || $likeFromIs==-1) ) { ?>

    <div class="liked">You like <?php echo $HisHer ?>!</div>

<?php } elseif ($likeToIs==1 && $likeFromIs==1) { ?>
    
    <div class="liked">You like each other!</div>

<?php } elseif ($likeToIs==-1) { ?>
    
    <div class="liked" style="color: black;">Ignored</div>

<?php } elseif ($likeToIs==0 && $likeFromIs==1) {?>
<div class="likedYou">
    <span>
    <?php echo ucfirst($SheHe) ?> likes you. Do you like <?php echo $HisHer; ?>?
    </span>
    <br />


    <?php echo CHtml::ajaxSubmitButton('Yes', '/profile/like', array(
        'type' => 'POST',
        'data' => array('id'=>Yii::app()->secur->encryptID($profile->getDataValue('id')),'like'=>1,'g'=>''),
        'update' => '#UserLikeForm',
    ),
    array(
       'type' => 'submit',
       'class' => 'button'
    )); ?>
    
    &nbsp;&nbsp;&nbsp;
    
    <?php echo CHtml::ajaxSubmitButton('No', '/profile/like', array(
        'type' => 'POST',
        'data' => array('id'=>Yii::app()->secur->encryptID($profile->getDataValue('id')),'like'=>-1,'g'=>''),
        'update' => '#UserLikeForm',
    ),
    array(
       'type' => 'submit',
       'class' => 'button'
    )); ?>
</div>

<?php } else { ?>

    <?php echo CHtml::ajaxSubmitButton('Like '.$HisHer.'?', '/profile/like', array(
        'type' => 'POST',
        'data' => array('id'=>Yii::app()->secur->encryptID($profile->getDataValue('id')),'like'=>1,'g'=>$profile->getDataValue('gender')),
        'update' => '#UserLikeForm',
    ),
    array(
       'type' => 'submit',
       'class' => 'button'
    )); ?>

<?php } ?>
</div>