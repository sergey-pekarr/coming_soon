<?php $this->widget('application.modules.admin.components.user.UserPaymentInfoWidget', array('userId'=>$this->id)) ?>

<br />
<?php 
if ($dataPayment && ($dataPayment['paymod']=='epg' || $dataPayment['paymod']=='epg_2' || $dataPayment['paymod']=='wirecard' || $dataPayment['paymod']=='wirecard_2' || $dataPayment['paymod']=='wirecard_no3D') ) 
{
	    $cancelledRow = $profile->paymentCancelledInfo();
	    if ($cancelledRow) { ?>
	    	Cancelled at: <?php echo $cancelledRow['date'] ?>
	    <?php } else { ?>
			<a class="btn  btn-danger cancelSubscription" style="color: #fff" href="javascript: paymentCancelSubscription(<?php echo $profile->getDataValue('id') ?>)">Cancel subscription</a>
	    <?php } ?>
	    <br />
	    
	    <?php if ($dataAdditional) { ?>
	    	<span class="bold">
	    	<br />
	    	<?php echo $dataAdditional ?>
	    	</span>
	    <?php } ?>
	    
	    
<?php } ?>


<?php if ($trans) { ?>

<h3>
	Transactions 
	<span class=="smaller">
		(
		<span class="tr_transaction_authed">&nbsp;&nbsp;&nbsp;</span> - Authed,
		&nbsp;&nbsp;&nbsp;&nbsp;		
		<span class="tr_transaction_completed_cams">&nbsp;&nbsp;&nbsp;</span> - Completed cams,
		&nbsp;&nbsp;&nbsp;&nbsp;				
		<span class="tr_transaction_completed">&nbsp;&nbsp;&nbsp;</span> - Completed,
		&nbsp;&nbsp;&nbsp;&nbsp;
		<span class="tr_transaction_renewal">&nbsp;&nbsp;&nbsp;</span> - Renewal
		&nbsp;&nbsp;&nbsp;&nbsp;
		<span class="tr_transaction_renewal_declined">&nbsp;&nbsp;&nbsp;</span> - Renewal (declined)		
		&nbsp;&nbsp;&nbsp;&nbsp;
		<span class="tr_transaction_cancelled">&nbsp;&nbsp;&nbsp;</span> - Cancelled		
		)
	</span>
</h3>

<table class="table">
<thead>
	<tr>
		<th>id</th>
		<th>Date</th>
		<th>Paymod</th>
		<th>Status</th>
		<th>Amount</th>
		<th>Price ID</th>
		<th>Cardholder's name <br /> First and Last Name </th>
		<th>Last 4 of card</th>
		<th>Address</th>
		<th>Country</th>
		<th>State</th>
		<th>City</th>
		<th>Zip</th>
		<th>Email</th>
		<th>IP</th>
	</tr>
</thead>


<tbody>
	<?php foreach ($trans as $tr) { ?>
		<tr class="tr_transaction_<?php echo $tr['status'] ?>">
			<td><?php echo $tr['id'] ?></td>
			<td><?php echo $tr['added'];//$tr['date'] ?></td>
			<td><?php echo $tr['paymod'] ?></td>
			<td><?php echo $tr['status'] ?></td>
			<td><?php echo $tr['amount'] ?></td>
			<td><?php echo $tr['price_id'] ?></td>
			<td><?php echo $tr['ccname'].'<br />'.$tr['firstname'].' '.$tr['lastname'] ?></td>
			<td><?php echo $tr['ccnum'] ?></td>
			<td><?php echo $tr['address'] ?></td>
			<td><?php echo $tr['country'] ?></td>
			<td><?php echo $tr['state'] ?></td>
			<td><?php echo $tr['city'] ?></td>
			<td><?php echo $tr['zip'] ?></td>
			<td><?php echo $tr['email'] ?></td>
			<td><?php echo $tr['ip'] ?></td>
		</tr>
	<?php } ?>
</tbody>

</table>

<?php } ?>
