<?php 
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/calendar/jscal2.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/calendar/lang/en.js');

Yii::app()->clientScript->registerCssFile( "/css/calendar/jscal2.css");
Yii::app()->clientScript->registerCssFile( "/css/calendar/border-radius.css");
Yii::app()->clientScript->registerCssFile( "/css/calendar/reduce-spacing.css");
?>

<table class="dateControl">
   	<tr>
   		<td colspan="3">
   			<div style="position: relative;">
   			<button class="btn btn-small left" onclick="javascript: dc_setDate(this, '1', -1)" > &lt; </button>
			<?php echo CActiveFormSw::textField($this->model,'date1', array('class'=>'adminDateInput date1 left')); ?>
			<i id="calendarDate1" class="icon-calendar">&nbsp;</i>
			<button class="btn btn-small left" onclick="javascript: dc_setDateMain(this)" > <i class="icon-share"></i> </button>
			<button class="btn btn-small left" onclick="javascript: dc_setDate(this, '1', 1)" > &gt; </button>
			
			
   			<button class="btn btn-small left" style="margin-left:28px" onclick="javascript: dc_setDate(this, '2', -1)" > &lt; </button>
			<?php echo CActiveFormSw::textField($this->model,'date2', array('class'=>'adminDateInput date2 left')); ?>
			<i id="calendarDate2" class="icon-calendar">&nbsp;</i>
			<button class="btn btn-small left" onclick="javascript: dc_setDate(this, '2', 1)" > &gt; </button>
			</div>
   		</td>
   		
   	</tr>
   	
   	<tr>
   		<td>
		    <div class="btn-group">
		        <button class="btn btn-small left" onclick="javascript: dc_setDates(this, -1)" > &laquo; </button>
		        <button class="btn btn-small" onclick="javascript: dc_setToday(this);" >Today</button>
		        <button class="btn btn-small left" onclick="javascript: dc_setDates(this, 1)" > &raquo; </button>
		    </div>
   		</td>
   		<td>
		    <div class="btn-group">
		    	<button class="btn btn-small" onclick="javascript: dc_setWeek(this, -1);" > &laquo; </button>
		        <button class="btn btn-small" onclick="javascript: dc_setWeek(this, 0);" >This week</button>
		        <button class="btn btn-small" onclick="javascript: dc_setWeek(this, 1);" > &raquo; </button>
		    </div>
   		</td>
   		<td>		    
		    <div class="btn-group">
		        <button class="btn btn-small" onclick="javascript: dc_setMonth(this, -1);" > &laquo; </button>
		        <button class="btn btn-small" onclick="javascript: dc_setMonth(this, 0);" >This month</button>
		        <button class="btn btn-small" onclick="javascript: dc_setMonth(this, 1);" > &raquo; </button>
		    </div>		    
		     		        		
   		<td>
   	</tr>
</table>



<script>
	var dc_dateServer = '<?php echo date("Y-m-d"); ?>';
	var dc_Ymd = <?php echo date("Ymd"); ?>;
	
	$(document).ready(function(){
		dc_init();		   
	});
</script>

