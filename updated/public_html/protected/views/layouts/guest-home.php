<?php $this->beginContent(LAYOUT); ?>
	
	<div class="members-map">	
	
		<div id="wrapper" >
			
			<?php echo $content; ?>
			
		</div>
		
	</div>
	
	<div class="clear"></div>
	
	
	<?php $this->widget('application.components.panel.PanelOnlineNowCounterWidget'); ?>
	
	
	
<?php $this->endContent(); ?>