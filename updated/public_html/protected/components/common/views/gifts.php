<div id="gifts_received" class="sendgift_icons">
    <div class="content_bar">
		<div class="profile-headline" style="font-weight: bold;">
        Flirts received from other members
		</div>
	</div>
    <div id="gifts_received_icons" style="magin-bottom:20px; min-height: 80px;">
		<?php foreach($items as $item) { 
			echo "<a href='javascript:void();' onclick='return false;' class='gifticon gifticon_{$item['gift']}'></a>";
		}?>
    </div>
	<div class="clear"></div>
</div>