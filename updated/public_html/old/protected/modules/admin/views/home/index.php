
<div id="summaryFormBox" class="form">
<div class="row">
<?php 
$form=$this->beginWidget('CActiveFormSw', array(
    'action'=>Yii::app()->createUrl('/admin/home/index'),
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


<dd>
    <button 
        class="btn actionShow" 
        data-loading-text="loading..." 
        onclick="javascript:$('#summaryFormBox .actionShow').button('loading'); $('#summaryFormBox form').submit();" 
    >
        &nbsp;&nbsp;&nbsp;Show&nbsp;&nbsp;&nbsp;
    </button>
</dd>

</div>
</div>
<div class="clear"></div>



<h3>Summary for date ranges</h3>
<table class="table table-condensed summary">
    <tr>
        <td>New users</td>
        <td title="all ( from FB )">
        	<?php echo $summary['newUsers'] ?>
        	(
        		<?php echo $summary['newUsersFromFB'] ?>
        	)
        	<?php /*
        	<a href="javascript:flot_showUsers($('#SummaryForm_date1').val(), $('#SummaryForm_date2').val())"><i class="icon-signal"></i></a>
        	*/ ?>
        	
        </td>
    </tr>

    <tr>
        <td>Returned users</td>
        <td><?php echo $summary['returnedUsers'] ?></td>
    </tr>

</table>


<h3>Total exists</h3>
<table class="table table-condensed summary">
    <tr>
        <td>Users</td>
        <td title="all ( from FB )">
        	<?php echo $summary['totalUsers'] ?>
        	(
        		<?php echo $summary['totalUsersFromFB'] ?>
        	)
        </td>
	</tr>
	
	<tr>
        <td>Promo users</td>
        <td><?php echo $summary['totalPromo'] ?></td>
    </tr>

	<tr>
		<td colspan="2" >&nbsp;</td>
	</tr>

	<tr>
        <td>Profile Images</td>
        <td><?php echo $summary['totalImages'] ?></td>
    </tr>
    
	<tr>
        <td>Not Approved Images</td>
        <td>
        	<?php if ($summary['totalImagesNotApproved']) { ?>
        		<a class="bold" href="/admin/users/approveImage"><?php echo $summary['totalImagesNotApproved'] ?></a>
        	<?php } else { ?>
        		0
        	<?php } ?>
        </td>
    </tr>


	<tr>
        <td>Not Rated Images</td>
        <td>
        	<?php if ($summary['totalImagesNotRated']) { ?>
        		<a class="bold" href="/admin/users/xrateImage"><?php echo $summary['totalImagesNotRated'] ?></a>
        	<?php } else { ?>
        		0
        	<?php } ?>
        </td>        
        
    </tr>
<?php /* */ ?>


</table>


<h3>Unresolve risks</h3>
<table class="table table-condensed summary">
    <tr>
        <td>Total unresolve risks</td>
        <td title="">
		<a href="/admin/risk">
		<?php echo $summary['totalRisk']; ?></a>
        </td>
	</tr>
</table>

<?php /*
<h3>New Fan-Girl Request</h3>
<table class="table table-condensed summary">
    <tr>
        <td>Total unapproved</td>
        <td title="">
		<a href="/admin/fan">
		<?php echo $summary['totalFanFirl']; ?></a>
        </td>
	</tr>
</table>
*/ ?>

