<div id="DashboardPopular" class="profiles">
	
	<h1>
		Popular Members
	</h1>
	
	</h1>
	
	
	<a class="profiles-find-more" href="/search">
		Find members near <?php echo $cityNear ?>
		<span>
			<img class="iconForward" src="/images/img/blank.gif" alt="">
		</span>
	</a>	
	
	<div class="clear"> </div>
	
	<?php 
    if ($ids)
    	foreach ($ids as $k=>$id)
			$this->widget('application.components.userprofile.ProfileBoxWidget', array('id'=>$id, 'imgSize'=>'medium', 'class'=>( ($k==3)?'last':'' )));
	?>
	
	<div class="clear"> </div>
	
</div>