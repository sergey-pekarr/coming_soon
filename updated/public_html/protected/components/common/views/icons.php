<?php
	foreach($icons as $icon){
		$type = $icon['type'];
		$action = isset($icon['action'])? $icon['action']: "doAction('$type','$profileid',this); return false;";
		$title = isset($icon['title'])?$icon['title']:$icon['type'];
?>
	<a title="<?php echo $title; ?>" onclick="<?php echo $action; ?>; return false;" href="javascript:void(0);">
	<img class="icon<?php echo $type; ?>" alt="<?php echo $type; ?> icon" src="/images/img/blank.gif"></a>
	<?php
}?>