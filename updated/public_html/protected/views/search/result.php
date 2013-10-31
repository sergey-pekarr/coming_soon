<?php 
$compress  = (LOCAL) ? false : true;
$combine  = (LOCAL) ? false : true;
CHelperAssets::cssUrl('search', $compress, $combine); 
?>
<style type="text/css">
    .search_results
    {
        width: 540px;
        float: left;
        margin-right:8px;
    }
    .content_nav
    {
        width: 540px;
    }
    .content_nav_sright
    {
        width: 0px;
    }
    #content .search_filter
    {
        margin-top:31px;
    }
	.search_results .profileBox
	{
		height: 161px;
	}
</style>

<div class="search_results">
<?php 
$this->widget('application.components.common.PagingWidget', 
	array('title'=>'Search Results', 'panelWidth'=>540, 'total'=>$total, 'page'=>$page, 'panelsrc' => 'search/result?', 'options' => $options )); 
?>

<?php
$k = 0;
foreach($users as $user) { 
	$this->widget('application.components.userprofile.ProfileBoxWidget', 
		array('id'=>$user['id'], 'imgSize'=>'medium', 'class'=>( ($k==3)?'last':''), 'showLocation' => true, 'showOnlineStatus' => true, ));
	$k = (++$k)%4;
}
?>


    <div style="height: 10px !important;" class="clear">
    </div>

<?php 
$this->widget('application.components.common.PagingWidget', 
	array('title'=>'', 'panelWidth'=>540, 'total'=>$total, 'page'=>$page, 'panelsrc' => 'search/result?', 'options' => $options )); 
?>

<!--
<form action="/search/result?page=<?php echo $page ?>" onsubmit="" method="post">
    <input type="hidden" name="searchcondition" value="" />
 </form>-->
<script>
//    $(document).ready(function () {
//        $('.search_results form input').val("<?php echo addslashes(json_encode($options)) ?>");
//        $('a.content_nav_page').click(function (evt) {
//            $('.search_results form').attr('action', $(evt.target).attr('href')).submit();
//            evt.preventDefault();
//        });
//    });
</script>

</div>


<?php $this->widget('application.components.common.SearchFilterWidget', array('profile'=>$profile, 'filter' => $filter)); ?>
<div class="clear"></div>


