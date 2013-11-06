
<h3>All fangirls (<?php echo count($fangirls); ?>)</h3>

<div>      
    <?php 
    $this->widget(
    	'CLinkPager', 
    	array(
    			'pages' => $pages,
    			'currentPage'=>$pages->getCurrentPage(),//(false)
    			'header'=>'',
    			'htmlOptions'=>array('class'=>'pagination'),                        
    			)
    );?>
    <div class="clear"></div>        
</div>

<table id="fangirl-unapproved" class="table summary">
<thead>
	<tr>
		<td>No</td>
		<td>UserId</td>
		<td>Username</td>
		<td>Joined</td>
		<td>Status</td>
		<td><!-- decline --></td>
		<td><!-- decline --></td>
		<td><!-- status --></td>
	</tr>
</thead>
<tbody>
	<?php for($i=0;$i<count($fangirls); $i++) {
		$req = $fangirls[$i];
	?>
<tr>
		<td><?php echo $i + 1; ?></td>
		<td><?php echo $req['user_id']; ?></td>
		<td><?php echo $req['loginAnchor']; ?></td>
		<td><?php echo date('Y-m-d H:i:s', strtotime($req['joined'])); ?></td>
		<td><?php echo $req['status']; ?></td>
</tr>
	<?php } ?>
</tbody>
</table>