<div class="dashboard-home-center">
<?php include dirname(__FILE__).'/ui/recentactivities.php'; ?>

<?php 
	$this->widget('application.components.panel.ProfilesFeaturedWidget');

	$this->widget('application.components.panel.ProfilesLatestVerifiedWidget');
	
	$this->widget('application.components.panel.ProfilesPopularWidget');
?>


</div>




<div class="dashboard-home-right">
	
	<?php $this->widget('application.components.common.SearchFilterWidget', array()); ?>
	
	<?php if ( Yii::app()->user->role == 'free' ) { //if ( Yii::app()->user->checkAccess('free') && !Yii::app()->user->checkAccess('gold')) { ?>
		<div id="right_upgrade" class="dashboard-home-right-box">
			<a href="/payment">
				<img src="/images/img/sidebar-banners/upgrade.png">
			</a>
		</div>
	<?php } ?>	
	
<?php /*	
	<div class="dashboard-home-right-box">

	</div>
*/ ?>

</div>

<div class="clear"></div>