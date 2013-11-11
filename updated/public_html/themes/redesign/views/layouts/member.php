<?php $this->beginContent(LAYOUT); ?>
<?php /*
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
*/ ?>

<div id="sidebar" class="col-md-2 col-md-offset-2">
    <?php include dirname(__FILE__) . '/ui/sidebar.php'; ?>
</div>
<div id="main" class="col-md-6">
    <?php echo $content; ?>
</div>
<?php $this->endContent(); ?>