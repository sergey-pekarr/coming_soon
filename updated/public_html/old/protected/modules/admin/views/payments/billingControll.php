<h3>Billing  controll for <?php echo $formText ?></h3>

<form method="get">
<table cellspacing="5" cellpadding="0" border="0">
<tr>
<td>
	<a style="font-size:16px; font-weight:bold;" href="/admin/payments/billingControll/?date=<?php echo $prev ?>&form=<?php echo $form ?>">&laquo;</a>
	&nbsp;
	<input style="width: 100px; margin-bottom: 0;" type="text" name="date" value="<?php echo $date ?>" />
	&nbsp;
	<a style="font-size:16px; font-weight:bold;" href="/admin/payments/billingControll/?date=<?php echo $next ?>&form=<?php echo $form ?>">&raquo;</a>
</td>

<td width="50">&nbsp;</td>

<td width="150">
	<select name="form" style="margin:0; width:150px">
		<option value="93" <?php if ($form==93) {?>selected="selected"<?php } ?> >allin1 (form 93)</option>
		<option value="932" <?php if ($form==932) {?>selected="selected"<?php } ?> >allin9 (form 932)</option>
	</select>
</td>

<td width="50">&nbsp;</td>

<td><input class="btn" type="submit" value="Go!" /></td>

<td width="50">&nbsp;</td>

<?php if (!$isToday) { ?>
<td><a href="/admin/payments/billingControll/?form=<?php echo $form ?>">Today</a></td>
<?php } ?>

</tr>
</table>
</form>

<br />
<br />



<?php if ($isToday) { ?>
	<form method="post">
	
	<?php  /* <input type="hidden" name="form" value="<?php echo $form ?>" /> */ ?>
	
	<table class="table table-condensed billingControll">
		<thead>
			<th>Daily order</th>
			<th>Paymod</th>
			<th>
			    Sales per day
			    <br />
			    <span class="smaller">0 - all remaining that day</span>
			</th>
			<th>Delete</th>
		</thead>
		
		<tbody>
			<?php 
			$orderMax=0;
			if ($bil) { ?>
				<?php foreach ($bil as $b) { ?>
					<tr>
					<td style="text-align:center"><input style="width: 40px" name="order[]" value="<?php echo $b['order'] ?>" size="2" /></td>
					<td>
						<input type="hidden" name="id[]" value="<?php echo $b['id'] ?>" />
						<select name="paymod[]">
							<?php foreach ($paymods[$form] as $pm) { ?>
								<option value="<?php echo $pm ?>"	 <?php if ($b['paymod']==$pm) { ?>selected="selected"<?php } ?> ><?php echo $pm ?></option>
							<?php } ?>
						</select>		
						
					</td>
					<td style="text-align:center"><input style="width: 40px;" name="sales[]" value="<?php echo $b['sales'] ?>" size="5" /></td>
					
					<td style="text-align:center;"><input type="checkbox" name="del[]" value="<?php echo $b['id'] ?>" /></td>
					</tr>
					
					<?php if ($b['order']>$orderMax) $orderMax = $b['order'];
				
				} ?>
					
				<tr><td colspan="4" style="height: 40px"></td></tr>
				
			<?php } ?>
			
			<tr>
			<td style="text-align:center">
				<input style="width: 40px" name="add_order" value="<?php echo (++$orderMax) ?>" size="2" />
			</td>
			<td>
				<select name="add_paymod">
					<option value="" selected="selected">Add new...</option>
					<?php foreach ($paymods[$form] as $pm) { ?>
						<option value="<?php echo $pm ?>"	><?php echo $pm ?></option>
					<?php } ?>
				</select>	
			</td>
			<td style="text-align:center">
				<input style="width: 40px;" name="add_sales" value="0" size="5" />
			</td>
			<td></td>
			</tr>	
		</tbody>
	</table>
	
	<input class="btn" type="submit" value="save" />
	
	</form>
	
	<br /><br />
	Today sales from <span class="bold"><?php echo $formText ?></span>
	<?php if ($sales_done) 
		foreach ($sales_done as $k=>$b) { ?>
			<br />
	    	<span class="bold"><?php echo $k ?></span>: <?php echo $b ?>    
	<?php } ?>
	
	<br/><br/><br/>
	Time on server: <?php echo date("Y-m-d H:i:s") ?> 
	<br/> 
	Next billing: <span class="bold"><?php echo $nextBilling ?></span>

<?php } else { ?>

	<?php echo $date ?> sales from <span class="bold"><?php echo $formText ?></span>
	<?php if ($sales_done) 
		foreach ($sales_done as $k=>$b) { ?>
			<br />
	    	<span class="bold"><?php echo $k ?></span>: <?php echo $b ?>    
	<?php } ?>


<?php } ?>
