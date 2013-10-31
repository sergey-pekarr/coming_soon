<?php 
if($total>1){
	$this->widget('application.components.common.PagingWidget', 
		array('title'=>$title, 'total'=>$total, 'page'=>$page, 'panelsrc' => $panelsrc )); 
}
?>

<?php
$k = 0;
foreach($items as $item) {
	$custom = '';
	if(function_exists('customeDataMethod')){
		$custom = customeDataMethod($item);
	}
	$this->widget('application.components.userprofile.ProfileBoxWidget', 
		array('id'=>$item[$dataid], 'imgSize'=>'medium', 'class'=>( ($k==4)?'last':'' ), 
				'showLocation' => (!isset($showLocation) || $showLocation), 'custom' => $custom, 
				'showBlockIcon' =>(!isset($showBlockIcon) || $showBlockIcon),
				'showIcons' => (!isset($showIcons) || $showIcons)));
	$k = (++$k)%5;
}
?>


    <div style="height: 10px !important;" class="clear">
    </div>

<?php 
if($total>1){
	$this->widget('application.components.common.PagingWidget', 
		array('title'=>$title, 'total'=>$total, 'page'=>$page, 'panelsrc' => $panelsrc )); 
}
?>