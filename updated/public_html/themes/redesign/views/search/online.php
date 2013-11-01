<div class="search_results">
<style type="text/css">   
	.search_results .profileBox
	{
		height: 151px;
	}
</style>
<?php 
if($total>0){
	$this->widget('application.components.common.PagingWidget', 
		array('title'=>'Search Results', 'total'=>$total, 'page'=>$page, 'panelsrc' => 'search/result?' )); 
}
?>

<?php
$k = 0;
foreach($users as $id) {
	if(!$id) continue;
	$this->widget('application.components.userprofile.ProfileBoxWidget', 
		array('id'=>$id, 'imgSize'=>'medium', 'class'=>( ($k==3)?'last':''), 'showLocation' => true, ));
	$k = (++$k)%4;
}
?>
    <div style="height: 10px !important;" class="clear">
    </div>

<?php 
if($total>0){
	$this->widget('application.components.common.PagingWidget', 
		array('title'=>'', 'total'=>$total, 'page'=>$page, 'panelsrc' => 'search/online?' )); 
}
?>

</div>
<div class="clear"></div>
