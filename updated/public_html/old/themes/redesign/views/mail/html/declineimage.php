<div style="border: 1px solid #dddad2; border-top: 0px; border-collapse: collapse;
    text-align: left; color: #727272; font-family: tahoma; font-size: 13px; line-height: 18px;
    width: 530px; padding: 10px;">
    
    <p style="font-weight: bold">
    	We are sorry but your image was declined. 
    	<br />
    	Please read our Image upload rules before uploading any more images.
    </p>
    
    <?php if ($data['reason']) { ?>
    <br /><br />
    <p>
        <?php echo $data['reason'] ?>
	</p>
	<?php } ?>
</div>