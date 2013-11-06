<style>
#fangirl-report td
{
	text-align: center;
}
</style>

<h3>Summary payout per week</h3>
<table id="fangirl-report" class="table summary">
<thead>
	<tr>
		<td>No</td>
		<td>Week</td>
		<td>going amt</td>
		<td>pending amt</td>
		<td>paid amt</td>
		<td>rejected amt</td>
	</tr>
</thead>
<tbody>
	<?php for($i=0;$i<count($payouts); $i++) {
		$req = $payouts[$i];
	?>
	<tr>
		<td><?php echo $i + 1; ?></td>
		<td><?php echo $req['weekrange']; ?></td>
		<td style="text-align: right;"><?php if($req['going']) echo $req['going']; ?></td>
		<td style="text-align: right;"><?php if($req['pending']) echo $req['pending']; ?></td>
		<td style="text-align: right;"><?php if($req['paid']) echo $req['paid']; ?></td>
		<td style="text-align: right;"><?php if($req['rejected']) echo $req['paid']; ?></td>
	</tr>
	<?php } ?>
</tbody>
</table>