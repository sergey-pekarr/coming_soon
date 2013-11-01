<h1 id="recent_activity" class="title" style="margin-left: 5px; padding-bottom:10px; font-size: 16px; font-weight: normal; color:#333333; line-height:18px">
    Recent Activity:</h1>
<?php 
$act = Activity::createActivity();
$items = $act->getDashboardRecent();
if(!$items) $items = array();

//$items = array_slice($items,0,5);

?>
<ul class="activityList">
    <?php $this->widget('application.components.dashboard.ListActivityWidget', array('items' =>  array_slice($items,0,5))); ?>

</ul><div class="clear" style="height:0px;">
</div>
<div style="display: none;" class="more-posts">
    <ul class="activityList">
    <?php $this->widget('application.components.dashboard.ListActivityWidget', array('items' =>  array_slice($items,5,10))); ?>
    </ul>
    <div class="clear">
    </div>
</div>
<div id="more-posts-tab" onclick="showMorePosts()" style="display: block;" >
</div>
<div class="clear" style="height:20px;">
</div>
<script>
function showMorePosts(){
	var morepost = $('.more-posts');
	if(morepost.css('display') == 'none'){
		morepost.slideDown();
		$('#more-posts-tab').css('background-position-y', 'bottom');
	}
	else {
		morepost.slideUp();
		$('#more-posts-tab').css('background-position-y', 'top');
	}
}
</script>