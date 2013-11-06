<?php 
$data = $profile->getData();

//$profileLink = '/profile/'.Yii::app()->secur->encryptID($profile->getDataValue('id'));
$encrID = Yii::app()->secur->encryptID($data['id']);
?>

<img src="<?php echo $profile->imgUrl('medium') ?>" />

<?php
$images = $data['image'];

if ($images)
{
	
	foreach ($images as $i=>$row) { ?>
		
		<img src="<?php echo $profile->imgUrl('small', $i, false) ?>" />
		
	<?php }
}
	
