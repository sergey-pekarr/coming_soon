		
		<table class="table" style="width:1024px">
		  <tr>
		    <th>Month</th>
		    <th>Sales</th>
		    <th>Rebills</th>
		    <th>Refunds</th>
		    <th>Chargebacks</th>
		  </tr>
		<?php

		$total = array(
			'Sales'=>0.0,
			'Rebills'=>0.0,
			'Refunds'=>0.0,
			'Chargebacks'=>0.0,
		);
		
		foreach ($trns as $date=>$t)
		{
			
			$total['Sales'] += $t[0];
			$total['Rebills'] += $t[1];
			$total['Refunds'] += $t[2];
			$total['Chargebacks'] += $t[3];
			
			?>
			  <tr>
			    <td><?php echo date("M-Y", strtotime($date)) ?></td>
			    <td>$<?php echo $t[0] ?></td>
			    <td>$<?php echo $t[1] ?></td>		    
			    <td>$<?php echo $t[2]?></td>
			    <td>$<?php echo $t[3]?></td>
			  </tr>
			<?php 			
			
		}
		
		
		
		
		
		
		?>
		  <tr>
		    <td>Summary:</td>
		    <td>$<?php echo $total['Sales'] ?></td>
		    <td>$<?php echo $total['Rebills'] ?></td>		    
		    <td>$<?php echo $total['Refunds'] ?></td>
		    <td>$<?php echo $total['Chargebacks'] ?></td>
		  </tr>
		</table>		