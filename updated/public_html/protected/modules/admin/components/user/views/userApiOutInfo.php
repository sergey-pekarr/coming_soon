<div class="userApiOutInfo">
<?php
    if ($apiOut) 
	{
		foreach($apiOut as $ka=>$api)
		{
			$success = ($api['status']=='1');
			$urlShow = ($success && stristr($api['response'], 'http://') );
			?>
			
			<?php if ($urlShow) { ?><a target="_blank" href="<?php echo $api['response']; ?>"><?php } ?>
			
			<span 
				class="label smaller apiOut_<?php echo ($success) ? 'ok' : 'nok' ?>"
				title="<?php echo $api['response']; ?>"
			>
				<?php echo $api['api'].': '.$api['date']; ?>							
			</span>
			
			<?php if ($urlShow) { ?></a><?php } ?>
			
			<?php
			if ($ka!=(count($apiOut)-1)) echo '<br />'; 
		}
	}
?>
</div>
