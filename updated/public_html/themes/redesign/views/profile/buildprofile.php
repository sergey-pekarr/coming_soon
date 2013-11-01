<form class="profile_<?php echo $type; ?>_form">

<?php if($type == 'myprofile') { ?>
<div class="profile_field_def profile_field_collapse">	
    <div class="field_left">
	    <a href="#" onclick="return false;">Age</a>
    </div>
    <div class="field_right">
	<div class="field_right_summary">
		<?php echo $profile->getDataValue('age'); ?>
	</div>
    </div>
</div>
<?php } ?>

<?php foreach($items as $key => $item){	
/* Next
+ Check current value -> create summary, or "I'd Rather Not Say"
+ Render as hidden
+ Add javascript code to extend, and constrain for single select
+ Add javacript to serialize form and save to server
*/
?>

<div class="profile_field_def profile_field_collapse">	
<div class="field_left">
	<a href="#" onclick="toggleFieldItem(this); return false;"><?php echo $item['text']; ?></a>
</div>
<div class="field_right">
	<div class="field_right_summary">
		<?php echo $item['selectedText']; ?>
	</div>
	<div class="field_right_control <?php echo ($item['multiple'] === true?'field_multiple':'field_single') ; ?>">
		<div>
			<input type="checkbox" name="<?php echo $key; ?>" value="0"  onclick="onFieldValueClick(this);" />
			<span> <?php echo (isset($defaulttext)?$defaulttext:"I'd Rather Not Say"); ?></span>
		</div>
		<?php 
		$values = $item['values'];
		$selected = $item['selected'];
		$count = count($values);
		for($i=0;$i<$count;$i++){ 
			$value = $values[$i];
			$checked = '';
			if(in_array($i + 1, $selected)) $checked = 'checked="checked"';
		?>
		<div>
			<input type="checkbox" name="<?php echo $key; ?>" value="<?php echo ($i + 1); ?>" <?php echo $checked; ?> onclick="onFieldValueClick(this);" />
			<span><?php echo $value; ?></span>
		</div>
		<?php } ?>
	</div>
</div>
<div class="clear"></div>
</div>
<?php } ?>
<?php if($type=='quickprofile'){?>
    <table border="0" cellspacing="5" cellpadding="5">
        <tbody>
            <tr>
                <td class="desc">
                    Turn Ons
                </td>
            </tr>
            <tr>
                <td>
                    <input id="turn_on" name="turn_on" value="<?php echo $profile->getPersonalValue('turn_on'); ?>" type="text">
                </td>
            </tr>
            <tr>
                <td class="desc">
                    Turn Offs
                </td>
            </tr>
            <tr>
                <td>
                    <input id="turn_off" name="turn_off" value="<?php echo $profile->getPersonalValue('turn_off'); ?>" type="text">
                </td>
            </tr>
        </tbody>
    </table>
	<script>
		$(document).ready(function () {
		    $('.profile_quickprofile_form input:text').change(function () {
		        myprofile.change['quickprofile'] = true;
			});
		});
	</script>
<?php } ?>
    <hr>
    <a class="content_button content_button_Save" onclick="save<?php echo $type; ?>(); return false;" href="javascript:void(0);">
        Save Changes <span>
            <img class="iconSave" alt="" src="/images/img/blank.gif"></span> </a>
    <div class="clear"></div>
</form>

