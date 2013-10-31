<h2 class="ftitle">Weekly report</h2>
<table class="payout-table border-table" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<td style="width: 150px;">Week</td>
			<td style="width: 80px;">Messages</td>
			<td style="width: 80px;">Amount</td>
			<td style="width: 80px;">Status</td>
			<td style="width: 80px;">Note</td>
		</tr>
	</thead>
	<tbody>
<?php foreach($payoutItems as $item) { ?>
	<tr>
		<td><?php echo "{$item['weekrange']}"; ?></td>
		<td><?php echo $item['valid_count']; ?></td>
		<td><?php echo $item['amt']; ?></td>
		<td><?php echo "{$item['pay_status']}"; ?></td>
		<td><?php echo $item['note']; ?></td>
</tr>
<?php } ?>
	</tbody>
</table>