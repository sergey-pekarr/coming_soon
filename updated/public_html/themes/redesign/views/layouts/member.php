<?php $this->beginContent(LAYOUT); ?>
<div id="wrapper" class="member">
	<div id="sidebar" class="span-7">
		<?php 
		if ( Yii::app()->user->checkAccess('free') )
        	include dirname(__FILE__).'/ui/sidebar.php'; 
        ?>
        
	</div>
	<div id="main" class="span-17 last">
		<div id="content">
			<?php echo $content; ?>
		</div>
	</div>
	<div class="clear"></div>
</div>
<?php $this->endContent(); ?>