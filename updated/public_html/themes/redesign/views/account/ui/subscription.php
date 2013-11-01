<h3 style="margin-top: 5px; font-size: 16px;">Subscription Details</h3>
<table class="payment-summary">
    <tbody>
        <tr>
            <td style="text-align: left; font-weight: bold; font-size:13px;">
                Your current subscription
            </td>
            <td style="text-align: left;">
                <?php if ($profile->getDataValue('role')=='gold') { ?>
                	Gold
                <?php } else { ?>
                	You currently have no subscription
                <?php } ?>
            </td>
        </tr>
        <tr>
            
			<?php if ($profile->getDataValue('role')=='gold') { ?>
	            <td style="text-align: left; font-weight: bold;">
	                Valid:
	            </td>
	            <td style="text-align: left;">
	                <?php echo date('M d, Y', strtotime($profile->getDataValue('expire_at')) ); ?>
	            </td>
            <?php } else { ?>
	            <td style="text-align: left; font-weight: bold;">
	                Free member since:
	            </td>
	            <td style="text-align: left;">
	                <?php echo date('M d, Y', strtotime($profile->getActivityValue('joined'))); ?>
	            </td>
            <?php } ?>
            
        </tr>
    </tbody>
</table>
