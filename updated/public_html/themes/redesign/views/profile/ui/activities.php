    <h3> 
	<?php if(count($activities)>0){
		echo 'Recent Activity:';
	}
	else {
		echo 'You have had no interaction with this user yet.'; 
	}?>
        </h3>
<?php 
$iconMap = array('winks' => 'Winks', 'messages' => 'Email', 'photorequest' => 'Camera');
if(count($activities)>0) { 
	foreach($activities as $item) {
		$icon = $iconMap[$item['type']];
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
?>
<ul class="activityList">
	<li>
		<div class="left"><img class="icon<?php echo $icon; ?>" alt="<?php echo $icon; ?> icon" src="/images/img/blank.gif"></div>
		<p style="text-align: left; margin-left: 25px;"><?php echo $text; ?> <strong><?php echo CHelperDate::date_distanceOfTimeInWords(time(), strtotime($item['added']), true);  ?></strong></p>
		<div class="clear"></div>
	</li>
</ul>
<?php 
}
} ?>