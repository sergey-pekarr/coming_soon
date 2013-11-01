
<div class="visitorsOnlineCounterBox">
    	<div class="left">
    		<?php if ($count) { 
				for ($i=0; $i<strlen($count); $i++) { ?>					
					<img src="/images/design/counter_<?php echo $count[$i] ?>.png">				
				<?php } ?>    
				<p>Online now</p>
			<?php } ?>
					
    	</div>
        <div class="right">
        	<ul>
            	<?php if ($profiles && count($profiles)>=5) {
            		foreach ($profiles as $k=>$id)
            		{
            			if ($k>=6) break;
            			$profile = new Profile($id);
            			$img = $profile->imgUrl();
            			?>
            				<li><a href="/site/registration"><img src="<?php echo $img ?>"/></a></li>
            			<?php 
            		}
            	} else { ?>
	            	<li style="margin: 0 6px 0 0"><img src="/images/design/profile1.jpg" /></li>
	                <li style="margin: 0 6px 0 0"><img src="/images/design/profile2.jpg" /></li>
	                <li style="margin: 0 6px 0 0"><img src="/images/design/profile3.jpg" /></li>
	                <li style="margin: 0 6px 0 0"><img src="/images/design/profile4.jpg" /></li>
	                <li style="margin: 0 6px 0 0"><img src="/images/design/profile5.jpg" /></li>
                <?php } ?>
            </ul>
  		</div>
        <div class="clearfix"></div>
        <h1>A place to meet and hookup with someone amazing and beautiful. <a href="/site/registration">JOIN NOW</a></h1>
</div>

<?php /*
<div class="visitorsOnlineCounterBox">

<?php

//CHelperSite::vd($count); 

for ($i=0; $i<strlen($count); $i++) { ?>
	
	<img src="/images/design/counter_<?php echo $count[$i] ?>.png">

<?php } ?>

<p>Online now</p>

</div>
*/ ?>