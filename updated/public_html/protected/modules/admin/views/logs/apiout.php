
<?php if (isset($model)) { ?>
<div id="logsApiOutFormBox" class="form">
<div class="row">
<?php 
$form=$this->beginWidget('CActiveFormSw', array(
    'action'=>Yii::app()->createUrl('/admin/logs/ApiOutLog'),
    'method'=>'get',
)); 

	$this->widget(
		'application.modules.admin.components.forms.DateControlWidget', 
		array(
			'model'=>$model
		)
	);

?>

<?php $this->endWidget(); ?>


<dd style="margin-left: 40px;">
    <button 
        class="btn actionShow" 
        data-loading-text="loading..." 
        onclick="javascript:$('#logsApiOutFormBox .actionShow').button('loading'); $('#logsApiOutFormBox form').submit();" 
    >
        &nbsp;&nbsp;&nbsp;Show&nbsp;&nbsp;&nbsp;
    </button>
</dd>


</div>
</div>
<div class="clear"></div>
<?php } ?>








<?php if (isset($apiLog)) { ?>

<h3>API Log for: <?php echo $api ?></h3>

<table class="table table-condensed apiOutLog">
<tr>
<th>Date / Time</th>
<th>User ID</th>
<th>Response</th>
</tr>

<?php foreach ($apiLog as $l) { ?>
<tr>
<td><?php echo $l['added'] ?></td>
<td><a href="/admin/users/edit?id=<?php echo $l['user_id'] ?>"><?php echo $l['user_id'] ?></a></td>
<td><?php 
	$resp = $l['response'];
	$resp = str_replace('<script>', "", $resp);
	$resp = str_replace('</script>', "", $resp);
	echo $resp; 
?>
</td>
</tr>
<?php } ?>
</table>

<p><a href="/admin/logs/ApiOutLog">Go back</a></p>




<?php } else { ?>



<h3>API Stats</h3>

<table class="table table-condensed apiOutLog">
<tr>
<th>Date</th>
<?php foreach ($apis as $api) { ?>
<th><?php echo $api ?></th>
<?php } ?>
</tr>

<?php foreach ($log as $l) { ?>
<tr>
<td><?php echo $l['date'] ?></td>


<?php foreach ($apis as $api) { ?>
<td>
	<a href="/admin/logs/ApiOutLog_api?date=<?php echo $l['date'] ?>&log=1&api=<?php echo $api ?>"><?php echo $l[$api]['oks'] ?></a> 
	/ 
	<a href="/admin/logs/ApiOutLog_api?date=<?php echo $l['date'] ?>&log=-1&api=<?php echo $api ?>"><?php echo $l[$api]['errors'] ?></a>
</td>
<?php } ?>

</tr>
<?php } ?>

</table>

<?php } ?>