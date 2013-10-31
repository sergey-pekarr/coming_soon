<?php 
	
	$userData = $profile->getData();

	echo $userData['email'];
	
	if ($userData['settings']['email_activated_at']=='0000-00-00 00:00:00') { ?>
    	<br />
    	<span class="label smaller">not approved</span>
    <?php } 

	if ($userData['settings']['email_bounced']=='1') { ?>
    	<br />
    	<span class="label label-important">BOUNCED</span>
    <?php }
    