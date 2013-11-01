<h2 class="ftitle">Daily messages in the last four weeks</h2>
<table class="report-table border-table" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<td style="width: 80px;">Date</td>
			<td style="width: 200px;">Friend's Name</td>
			<td style="width: 80px;">Messages</td>
		</tr>
	</thead>
	<tbody>
<?php foreach($dailyItems as $item) { ?>
	<tr>
		<td><?php echo $item['date']; ?></td>
		<td>
		<?php
			$encid = Yii::app()->secur->encryptID($item['id_from']);
			echo "<a href='/profile?id={$encid}'>{$item['username_from']}</a>";
		?>
		</td>
		<td><?php echo $item['count']; ?></td>
	</tr>
<?php } ?>
	</tbody>
</table>