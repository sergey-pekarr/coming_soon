<?php		
$userid = Yii::app()->user->id;
$profile = new Profile($userid);	

$iconMap = array('winks' => 'Winks', 'messages' => 'Email', 'photorequest' => 'Camera');

foreach($items as $item){
	if($userid == $item['id_from']){
		$id = $item['id_to'];
	}
	else if($userid == $item['id_to']){
		$id = $item['id_from'];      
	}
	else {
		continue;
	}
	$targetprofile = new Profile($id);
	$threadLink = '#';
	if($item['type'] == 'messages'){
		$threadid = '';
		if(isset($item['parent']) && $item['parent']!='') $threadid = $item['parent'];
		else $threadid = $item['id'];
		$threadLink = "/thread/".Yii::app()->secur->encryptID($id).'/'.$threadid;
	}
	
	if($userid == $item['id_from']){
		$item['fromName'] = 'you';
		$item['toName'] = $targetprofile->getDataValue('username');
		$item['toPos'] = $targetprofile->getDataValue('username')."'s";
	}
	else {
		$item['fromName'] = $targetprofile->getDataValue('username');
		$item['toName'] = 'you';
		$item['toPos'] = 'your ';   
	}
	
	if(isset($iconMap[$item['type']])) $icon = $iconMap[$item['type']];
	$text = '';
	switch($item['type']){
		case 'messages': 
			$text = "{$item['fromName']} emailed to {$item['toName']}";
			break;
		case 'winks': 
			$text = "{$item['fromName']} winked to {$item['toName']}";
			break;
		case 'photorequest': 
			$text = "{$item['fromName']} requested {$item['toPos']} photo";
			break;
	}
	$name = $icon;
	if($name == 'Camera') $name = "Photo-Request";
	
?>
    <li><a href="<?php echo $threadLink; ?>">
        <img class="profile-small left" alt="" src="<?php echo $targetprofile->imgUrl(); ?>"
width="50" height="50" style="margin:5px;">
</a>
<p style="width: 410px; margin-left:31px;" class="left">
            <a href="<?php echo $threadLink; ?>">
                <img class="icon<?php echo $icon; ?>" alt="<?php echo $icon; ?> icon" style="margin-top: 5px; margin-right: 4px; margin-bottom: 5px; margin-left: -25px;" src="/images/img/blank.gif"></a> 
            <a href="<?php echo $threadLink; ?>" style="font-weight:bold;"> <?php echo $name; ?></a><br>
            <span class="strong" style="color:#333333;"><?php echo CHelperProfile::showProfileInfoSimple($targetprofile, 4) .', '.CHelperProfile::showProfileInfoSimple($targetprofile, 7, 50); ?></span><br>
            <a style="font-weight: normal;" href="<?php echo $threadLink; ?>">
                <?php echo $text; ?> </a><?php echo CHelperDate::date_distanceOfTimeInWords(time(), strtotime($item['added']), true);  ?>
</p>
<div class="clear">
</div>
</li>
<?php } ?>