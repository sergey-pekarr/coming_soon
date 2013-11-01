<h3>
    Weekly/Monthly Reversal Report</h3>

<div id="refundFormControl">
<?php 

$form=$this->beginWidget('CActiveFormSw', array(
	'action'=>Yii::app()->createUrl('/admin/payments/RfdNChbkReport'),
	'method'=>'get',
	)); 
?>
<table>
	<tr>
		<td>
			<?php 
				$this->widget(
					'application.modules.admin.components.forms.DateControlWidget', 
					array(
							'model'=> $model
							)
						);
			?>
		</td>
		<td style="vertical-align: top;">
		    <button class="btn" data-loading-text="loading..." 
				onclick="javascript:$('#refundFormControl .btn').button('loading'); $('#refundFormControl form').submit();">
				&nbsp;&nbsp;&nbsp;Show&nbsp;&nbsp;&nbsp;
			</button>
		</td>
	</tr>
	<tr>
		<td colspan="2">
		    <?php 
			/*
		    $this->widget(
		    	'CLinkPager', 
		    	array(
		    			'pages' => $pages,
		    			'currentPage'=>$pages->getCurrentPage(),
		    			'header'=>'',
		    			'htmlOptions'=>array('class'=>'pagination'),                        
		    			)
		    );*/?>
		</td>
	</tr>
</table>
<?php $this->endWidget(); ?>
</div>

<hr style="margin: 10px 5px;" />
<div>
    <table id="refundItems" class="table summary">
        <thead>
            <td>
                No
            </td>
            <td>
                Manager Id
            </td>
            <td>
                Manager Name
            </td>
            <td>
                Sale
            </td>
            <td>
                Refund
            </td>
            <td>
                Chargeback
            </td>
            <td>
                Percent
            </td>
            <?php /*<td>Chargeback %</td> */ ?>
            <td>Rebills</td>
            <td>Rebill %</td>
        </thead>
        <tbody>
		<?php
		$i = 1;
		$totalSale = 0;
		$totalRefund = 0;
		$totalChargeback = 0;
		$totalRebills = 0;
		foreach($items as $item){
			$totalSale += $item['paid'];
			$totalRefund += $item['Refund'];
			$totalChargeback += $item['Chargeback'];
			$totalRebills += $item['rebills'];
		?>
<tr>
				<td><?php echo $i; ?></td>
				<td><?php echo $item['id']; ?></td>
				<td><?php echo $item['login']; ?></td>
				<td><?php if($item['paid']) echo number_format($item['paid']); ?></td>
				<td><?php if($item['Refund']) echo number_format($item['Refund']); ?></td>
				<td><?php if($item['Chargeback']) echo number_format($item['Chargeback']); ?></td>
				<td><?php if($item['percent'] != '0%') echo $item['percent']; ?></td>
				
				<?php /*<td><?php if($item['CB_percent']) echo $item['CB_percent'].'%'; ?></td>*/?>
				<td><?php if($item['rebills']) echo $item['rebills']; ?></td>
				<td><?php if($item['rebills_percent']) echo $item['rebills_percent'].'%'; ?></td>
</tr>
		<?php 
		$i++;
		} ?>
		<tr style="font-weight: bold; font-size: 14;">
			<td colspan="2">Summary</td>
			<td></td>
			<td><?php if($totalSale) echo number_format($totalSale); ?></td>
			<td><?php if($totalRefund) echo number_format($totalRefund); ?></td>
			<td><?php if($totalChargeback) echo number_format($totalChargeback); ?></td>
			<td><?php if($totalSale) echo round(($totalRefund + $totalChargeback)/$totalSale * 100).'%'; ?></td>
			<?php /*<td><?php if($totalSale) echo round(100*($totalChargeback)/$totalSale).'%'; ?></td>*/?>
			<td><?php echo $totalRebills; ?></td>
			<td><?php if($totalSale) echo round(100*($totalRebills)/$totalSale).'%'; ?></td>
		</tr>		
        </tbody>
    </table>
</div>
<style>
table#refundItems thead td
{
	text-align:center;
}
table#refundItems tbody td
{
	text-align:center;
}
table#refundItems tbody td:nth-child(2), table#refundItems tbody td:nth-child(3)
{
	text-align:left;
}
</style>