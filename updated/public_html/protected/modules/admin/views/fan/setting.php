<h3>Settings</h3>
<div id="fangirl-setting">
	<span style="font-weight: bold;">Pay per message: </span>
	<input type="text" value="<?php echo $value; ?>" style="width: 80px; text-align:right;" />
	<input type="button" value="Update" onclick="updateSettings2();" />
	<img src="/images/img/blank.gif">
	<span class="err"></span>
<script>
    /* study later: Dont understand why can't to call method updateSettings here!!!*/
	function updateSettings(){
		var value = $('#fangirl-setting input[type="text"]').val();
		
		$.post('/admin/fan/updatesettings', {'value': value}, function(data){
		}, 'json')
		.success(function(data){
			$('#fangirl-setting img').attr('src', '/images/img/check.png');
		})
		.fail(function(data){
			$('#fangirl-setting img').attr('src', '/images/img/alert.gif');
		});
	}
</script>
</div>

<script>
    function updateSettings2() {
        var value = $('#fangirl-setting input[type="text"]').val();
        $.post('/admin/fan/updatesettings', { 'value': value }, function (data) {
        }, 'json')
		.success(function (data) {
		    $('#fangirl-setting img').attr('src', '/images/img/check.png');
		})
		.fail(function (data) {
		    $('#fangirl-setting img').attr('src', '/images/img/alert.gif');
		});
    }
</script>