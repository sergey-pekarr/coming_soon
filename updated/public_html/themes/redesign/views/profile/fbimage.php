<div class="box-contain" id="fbcontent">
    <div class="box-header" style="text-transform: none; height: 32px;">
        Please select images to import<span></span>
        <a style="float:right; margin-right:5px; margin-top:3px; color:White; font-weight:bold;" href="<?php echo $logoutUrl; ?>">
            <img id="fb_logout_image" alt="Connect" src="/images/img/fb_logout_small.gif">
        </a>
    </div>
    <div class="clear"></div>
    <div class="box-content round">
        <div style="margin: 15px 10px 15px 10px; min-height:100px;">
        <?php 
		$i=0;
        foreach($images as $image) { 
            $imgurl = $image['url'];
        	$check = $image['existed']?'checked="checked" disabled="disabled"':'';
        ?>
<div style="float:left; width:200px; height:200px; margin: 10px 10px 10px 10px; position:relative" >
			<input id='fbphoto[<?php echo $i; ?>]' class='checkbox ' name='fbphoto[<?php echo $i; ?>]' value='<?php echo $imgurl; ?>' type='checkbox' <?php echo $check; ?> style="position:absolute; bottom: 1px; right: 1px;" />
			<img src='<?php echo $imgurl; ?>' width="200" height="200" style="width:200px; height:200px;" />
		</div>
        <?php
			$i++; 
		} ?>
		<div class="clear"></div>
        </div>
        <input type="button" value="Submit" onclick="importFbImage();" />
    </div>
</div>
<!--
<?php print_r($res); ?>
-->
<script>
	function importFbImage(){
		var images = {};
		$('#fbcontent .box-content input[type="checkbox"]:checked').not('[disabled="disabled"]').each(function(index, ele){
			images[ele.name] = ele.value;
		});
		if(images.length ==0){
			alert("Please select images to import");
		}
		else {
			$.post('/profile/fbimport', images, function(res){
			})
			.success(function(res){
				alert("Your selected images have been imported");
				$('#fbcontent .box-content input[type="checkbox"]:checked').attr('disabled','disabled');
			})
			.fail(function(res){
				alert("There is an error while importing your selected images. Please try again later");
			});
		}
	}
</script>