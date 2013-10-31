<?php if(in_array($status, array('initial','canceled','rejected'))) { ?>
	<?php if($status == 'rejected') { ?>
		<h3 class="intro">
		Your uploaded sign picture has been rejected because of the reason: <?php echo $notice; ?>.<br />
        <?php renderSignImg($fanProfile->getSign());  ?><br />
Please upload another sign picture.
</h3>
	<?php }
	else if($status == 'canceled') { ?>
	<h3 class="intro">
	You can make .25 every time a man messages you. 
	To apply simply upload a picture of yourself with a fan sign (a piece of paper) that says your username and then "pinkmeets.com" 
	After you upload your picture we will notify you of your approval and signup instructions for you to get paid weekly through red pass, 
	a prepaid visa ATM card that we send to you to load funds on.
	</h3>
	<?php }
	else { ?>
	<h3 class="intro">
	You can make .25 every time a man messages you. 
	To apply simply upload a picture of yourself with a fan sign (a piece of paper) that says your username and then "pinkmeets.com" 
	After you upload your picture we will notify you of your approval and signup instructions for you to get paid weekly through red pass, 
	a prepaid visa ATM card that we send to you to load funds on.
	</h3>
	<?php } ?>
<div id='uploadphotocontrol'></div>
<script>
function InitUploader(){
var uploader = new qq.FileUploader({
element: document.getElementById('uploadphotocontrol'),
action: '/img/uploadfansign',
onSubmit: function (id, fileName) { },
onProgress: function (id, fileName, loaded, total) { },
onComplete: function (id, fileName, responseJSON) {
uploadUploadComplete(responseJSON);
},
debug: true
});
}
function uploadUploadComplete(){
window.location = '/fan';
}
$(document).ready(function(){
InitUploader();
});
</script>	
<?php } 
else if(in_array($status, array('pending'))) {?>
	<h2 class="ftitle">Your sign image: </h2>
	<h3 class="intro">
	Your uploaded image is being approved. We will notify you of your approval and signup instructions for you.<br /> 
        <?php renderSignImg($fanProfile->getSign());  ?><br />
</h3>
	<?php renderPayoutForm($payoutMethod, $paymentInfor); ?>
<table style="width: 100%;">
<tr>
			<td style="width:50%;"><?php renderSummary('Total messages:', $stats); ?></td>
			<td style="width:50%;"><?php renderSummary('Payment information:', $payouts); ?></td>
</tr>
</table>	
<?php }
else if(in_array($status, array('approved'))) {?>
	<?php if($notice != null && $notice != '') { ?>
		<h3 class="intro">
		    <?php echo $notice; ?>
</h3>	
	<?php } ?>
	<?php renderPayoutForm($payoutMethod, $paymentInfor); ?>
<table style="width: 100%;">
<tr>
			<td style="width:50%;"><?php renderSummary('Total messages:', $stats); ?></td>
			<td style="width:50%;"><?php renderSummary('Payment information:', $payouts); ?></td>
</tr>
</table>	
<?php } ?>

<?php 
function renderPayoutForm($payoutMethod, $paymentInfor){
    /*
    Will do later. need to study payment method from stats
    */
?>
    <div>
        <h2 id="payoutMethod" class="ftitle" <?php if($payoutMethod == null) echo 'style="display:none;"'; ?> >Your payout method: <?php echo $payoutMethod ?>. <a href="javascript:void();" onclick="showPayoutform(); return false;">Change</a></h2>
        <div id="payoutForm" <?php if($payoutMethod != null) echo 'style="display:none;"'; ?>>
            <h2 class="ftitle">Submit your financial information: </h2>
			<div>
				<?php 
				if($paymentInfor == null){
					$payoutMethod = 'Wire transfer';
					$paymentInfor = CHelperFanProfile::getPaymentInfor($payoutMethod);
				}
				?>
				<table>				
					<tr>
						<td><h3 class="intro" style="text-align:right;">Payment method:</h3></td>
						<td style="width: 10px;"></td>
						<td>
							<select onchange="onPaymentMethodChange();" class="formInput" name="pm">
								<option <?php if($payoutMethod =='Paypal') echo 'selected="selected"'; ?> value="Paypal">Paypal</option>
								<option <?php if($payoutMethod =='Check') echo 'selected="selected"'; ?> value="Check">Check</option>
								<option <?php if($payoutMethod =='Wire transfer') echo 'selected="selected"'; ?> value="Wire transfer">Wire transfer</option>
								<option <?php if($payoutMethod =='Webmoney') echo 'selected="selected"'; ?> value="Webmoney">Webmoney</option>
							</select>
						</td>
						<td></td>
					</tr>	
					<?php foreach($paymentInfor as $field) { 
						if(!isset($field['value'])) $field['value'] = '';
					?>
					<tr valign="top">
						<td valign="top"><h3 class="intro" style="text-align:right;"><?php echo $field['field']; ?></h3></td>
						<td style="width: 10px;"></td>
						<td>
						<?php if($field['length'] > 255) { ?>
							<textarea style="height: 60px;" title="<?php echo $field['field']; ?>"><?php echo $field['value']; ?></textarea>
						<?php } else { ?>
							<input type="text" name="" value="<?php echo $field['value']; ?>" title="<?php echo $field['field']; ?>" />
						<?php } ?>
						</td>
						<td><?php if($field['required'] === true || $field['required'] === 'true') { ?> <div style="padding-left: 10px; width: 80px; "><span style="color:red;">*</span> Required</div> <?php } ?></td>
					</tr>
					<?php } ?>
				</table>
                <div style="padding-left: 190px;">
                    <input type="button" value="Update" onclick="updatePayment();" />
                </div>
			</div>
        </div>
	</div>
    <script>
        function showPayoutform() {
            $('#payoutMethod').css('display', 'none');
            $('#payoutForm').slideDown();
        }

        function updatePayment(){
        	var method = $('#payoutForm select[name="pm"]').val();
			var infor = allPayoutInfors[method];

            var data = {};
            $('#payoutForm input,#payoutForm textarea').each(function(index, ele){
                data[$(ele).attr('title')] = $(ele).val();
            });

            var err = false;
            for(i=0;i<infor.length;i++){
                field = infor[i];
                if(field.required && !data[field.field]){
                    err = field.field + " is required";
                    break;
                }
                else{
                    field.value = data[field.field].substr(0, field.length);					
                }
            }
            if(err){
                alert(err);
                return;
            }
            $.post('/fan/updatepayout', {'method':method, 'fields' : infor}, function(data){
            }, 'json')
            .success(function(data){
                $('#payoutForm').css('display', 'none');
                $('#payoutMethod').html('Your payout method: ' + method);
                $('#payoutMethod').slideDown();
            })
            .fail(function(data){
            });
        }
		
		function buildPaymentField(fields){
			$('#payoutForm table tr').each(function(index, ele){
                if(index>0) $(ele).remove();
            });
            
            for(i=0;i<fields.length;i++){
                var field = fields[i];
                var html = '<tr valign="top">';
                html += '<td valign="top"><h3 class="intro" style="text-align:right;">' + field['field'] + '</h3></td>';
                html += '<td style="width: 10px;"></td>';
                if(field['length'] > 255){
                    html += '<td><textarea style="height: 60px;" title="' + field['field'] + '"></textarea></td>';
                }
                else{
                    html += '<td><input type="text" name="" value="" title="' + field['field'] + '" /></td>';
                }
                if(field['required']){
                    html += '<td><div style="padding-left: 10px; width: 80px; "><span style="color:red;">*</span> Required</div></td>';
                }
                else{
                    html += '<td></td>';
                }
                html += '</tr>';
                $(html).appendTo('#payoutForm table');
            }
		}
		
		var allPayoutInfors = <?php echo json_encode(CHelperFanProfile::getAllPaymentMethodsInfor()); ?>;

	
		function onPaymentMethodChange(){
			var method = $('#payoutForm select[name="pm"]').val();

			var infor = allPayoutInfors[method];
            if(infor == null) infor = allPayoutInfors['Wire transfer'];

            buildPaymentField(infor);
		}
    </script>
<?php
}
?>

<?php
function renderSignImg($signImg){
?>
    <img src="<?php echo $signImg;  ?>" onload="changeSignImgSize(this);" />
    <script>
        function changeSignImgSize(ele) {
            if (ele.naturalWidth > 300) {
                ele.width = 300;
            }
            if (ele.height > 300) {
                ele.height = 300;
            }
        }
    </script>
<?php
}
?>

<?php
function renderSummary($title, $items){
?>
	<h2 class="ftitle"><?php echo $title; ?></h2>
	<table class="summary-table" style="margin-left:10px;">
		<?php foreach($items as $key => $value) { ?>
			<tr>
				<td style="text-align: right;"><?php echo $key; ?>:</td>
				<td style="width: 10px;"></td>
				<td style="text-align: right;"><?php echo $value; ?></td>
			</tr>
		<?php } ?>
	</table>
<?php
}
?>

<?php
/*
<!-- 
Unjoined:
Upload pic

Joined
When user has upload pic
	- Status
	- Show current pic
	- Remove
	- ReUpload sign

Payout setting
	- Payout method
	- Payout number
-->


Sumary:
+ Today message
+ This week message
+ Thie month

Payment infor (change/form)


Detail
Option: Select fromToDate, Today, This Week, This Month
Order: 
Table
Username	SendTime	

Payout
Week	Amt	Status	PayoutDate

*/
?>