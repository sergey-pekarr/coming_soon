<h3>Unaproved requests (<?php echo count($reqs); ?>)</h3>
<table id="fangirl-unapproved" class="table summary">
<thead>
	<tr>
		<td>No</td>
		<td>UserId</td>
		<td>Username</td>
		<td>Request on</td>
		<td><!-- view image --></td>
		<td><!-- decline --></td>
		<td><!-- decline --></td>
		<td><!-- status --></td>
	</tr>
</thead>
<tbody>
	<?php for($i=0;$i<count($reqs); $i++) {
		$req = $reqs[$i];
	?>
	<tr>
		<td><?php echo $i + 1; ?></td>
		<td><?php echo $req['user_id']; ?></td>
		<td><?php echo $req['loginAnchor']; ?></td>
		<td><?php echo date('Y-m-d H:i:s', strtotime($req['joined'])); ?></td>
		<td style="padding-top: 4px; padding-bottom:0px;">
			<input type="button" value="View image" title="<?php echo "{$req['fan_sign']}";  ?>" 
				onclick="showSignImagePopup(<?php echo "'{$req['username']}','/img/fansign/{$req['fan_sign']}', '{$req['fan_sign']}'";  ?>);" />
		</td>
		<td style="padding-top: 4px; padding-bottom:0px;">
			<input style="display: none;" type="button" value="Approve" title="<?php echo "{$req['fan_sign']}";  ?>" 
				onclick="approveSignImg(<?php echo "'{$req['fan_sign']}'";  ?>);" />
		</td>
		<td style="padding-top: 4px; padding-bottom:0px;">
			<input style="display: none;" type="button" value="Decline" title="<?php echo "{$req['fan_sign']}";  ?>" 
				onclick="declineSignImg(<?php echo "'{$req['fan_sign']}'";  ?>);" />
		</td>
	</tr>
	<?php } ?>
</tbody>
</table>
<ul>
	<li>(*) Declined: Wel will ask user upload another image</li>
</ul>
<div id="signimgpopup" style="display:none;">
	<div style="width:400px; height: 460px; background-color:white;">
		<div style="width: 400px; height: 400px;">
			<img onload="signImageLoad(this);" />
		</div>
		<div style="width: 400px; height: 60px; padding-left: 10px;">
			<h4 style="margin-bottom: 10px;">Username: <span></span></h4>
			<input style="" type="button" value="<< Back" title="" onclick="showBackSignImg();" />
			<input style="" type="button" value="Next >>" title="" onclick="showNextSignImg();" />
			<input style="" type="button" value="Approve" title="" onclick="popupApprove();" />
			<input style="" type="button" value="Decline" title="" onclick="popupDecline();" />
			<input style="" type="button" value="Cancel" title="" onclick="$('#signimgpopup').dialog('close');" />
		</div>
	</div>
	<style>
		#signimgpopup input
		{
			margin-left: 10px;
		}
	</style>
</div>

<script>
	function showSignImagePopup(username, url, id){
		$("#fangirl-unapproved input[title='" + id + "']").filter("[value='Approve'],[value='Decline']").css('display', 'inline-block');
		
		var jq = $('#signimgpopup');
		jq.find('img').removeAttr('width').removeAttr('height');
		jq.find('img').attr('src', '');
		jq.find('img').attr('src', url);
				
		jq.attr('title', id);
		jq.find('h4 span').html(username);
		
		$('#signimgpopup').dialog({width: 420, resizable: false});
		
		jq.attr('title', id);
		checkButtonStatus(id);
	}
	
	function signImageLoad(ele){
	        if (ele.naturalWidth > 400) {
                ele.width = 400;
            }
            if (ele.height > 400) {
                ele.height = 400;
            }
	}
	
	var processSignImg = {};
	
	function checkButtonStatus(id){
		window.setTimeout(function(){
			if(processSignImg[id]){
				$('#fangirl-unapproved tbody tr input[value="Approve"][title="' + id + '"]').attr('disabled', 'disabled');
				$('#fangirl-unapproved tbody tr input[value="Decline"][title="' + id + '"]').attr('disabled', 'disabled');
			
				var jq = $('#signimgpopup[title="' + id + '"]');			
				jq.find('input[value="Approve"],input[value="Decline"]').attr('disabled', 'disabled');
			}
			else{
				var jq = $('#signimgpopup[title="' + id + '"]');			
				jq.find('input[value="Approve"],input[value="Decline"]').removeAttr('disabled');
			}
		}, 100);
	}
		
	function showBackSignImg(){
		var id = $('#signimgpopup').attr('title');
		var trjq = $('#fangirl-unapproved tbody tr').has('input[title="' + id + '"]');
		trjq.prev().find('input[value="View image"]').click();	
		
		var id = $('#signimgpopup').attr('title');
		checkButtonStatus(id);
	}
	
	function showNextSignImg(){
		var id = $('#signimgpopup').attr('title');
		var trjq = $('#fangirl-unapproved tbody tr').has('input[title="' + id + '"]');
		trjq.next().find('input[value="View image"]').click();
				
		var id = $('#signimgpopup').attr('title');
		checkButtonStatus(id);
	}	
	
	function popupApprove(){
		approveSignImg($('#signimgpopup').attr('title'));
	}
	
	function popupDecline(){
		declineSignImg($('#signimgpopup').attr('title'));	
	}
	
	function approveSignImg(id){
				
		var url = "/admin/fan/approveimg/" + id;
		$.post(url, {}, function(data){
			
		})
		.success(function(data){
			processSignImg[id] = 'Approved';
			checkButtonStatus(id);
		})
		.fail(function(data){
		});		
	}
	
	function declineSignImg(id){
		var url = "/admin/fan/declineimg/" + id;
		$.post(url, {}, function(data){			
		})
		.success(function(data){
			processSignImg[id] = 'Declined';
			checkButtonStatus(id);
		})
		.fail(function(data){
		});	
	}
</script>

<?php
/*
+ Admin click to view sign -> open img popup
+ Click to approve or declined on the popup



*/
?>