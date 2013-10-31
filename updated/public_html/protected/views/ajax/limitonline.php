
<div class="profiles panel-list">
	<script>
		$(document).ready(function(){
			<?php 
			CHelperProfile::getPaymentLinkWithAction('onlinemore', '', $link, $nav);
			echo $link;
			?>
		});
	</script>
</div>