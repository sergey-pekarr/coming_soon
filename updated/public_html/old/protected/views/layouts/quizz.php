<?php $this->beginContent(LAYOUT); ?>
<div id="wrapper" class="member">
	<div id="sidebar" class="span-7">
		<?php 
		if ( Yii::app()->user->checkAccess('free') )
			include dirname(__FILE__).'/ui/sidebar.php'; 
		?>
        
	</div>
	<div id="main" class="span-17 last">
	
		<link type="text/css" rel="Stylesheet" href="css/quizz.css" />	
		<?php if(LIVE) {?>
			<script type="text/ecmascript" src="/js/QuizzMakerScript.debug.js"></script>
		<?php } else { ?>
			<script type="text/ecmascript" src="/js/QuizzMakerScript.debug.js"></script>
		<?php } ?>
		
		<div id="content">
			<?php echo $content; ?>
		</div>
	</div>
	<div class="clear"></div>
</div>
<?php $this->endContent(); ?>