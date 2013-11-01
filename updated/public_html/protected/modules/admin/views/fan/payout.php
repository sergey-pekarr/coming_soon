<style>
#fangirl-payout td
{
	text-align: center;
}
#fangirl-payout td:nth-child(3)
{
	text-align: left;
}
#fangirl-payout .cellbold
{
	font-weight: bold;
}
#fangirl-payout .cellgray
{
	color: #aaa;
}
</style>

<h3>Payouts - pending payout per week:</h3>
<table id="fangirl-payout" class="table summary">
<thead>
	<tr>
		<td>No</td>
		<td>UserId</td>
		<td>Username</td>
		<td>Week</td>
		<td>Valid messages</td>
		<td>Amount</td>
		<td>Status</td>
		<td></td>
	</tr>
</thead>
<tbody>
	<?php 
		$curWeek = null;
	for($i=0;$i<count($payouts); $i++) {
		$req = $payouts[$i];
		
		if($curWeek != $req['week']){
			$weekclass = 'cellbold';
		}
		else {
			$weekclass = 'cellgray';
		}
		$curWeek = $req['week'];
	?>
<tr>
		<td><?php echo $i + 1; ?></td>
		<td><?php echo $req['user_id']; ?></td>
		<td><?php echo $req['loginAnchor']; ?></td>
		<td class="<?php echo $weekclass; ?>"><?php echo $req['weekrange']; ?></td>
		<td><?php echo $req['valid_count']; ?></td>
		<td><?php echo $req['amt']; ?></td>
		<td><?php echo $req['pay_status']; ?></td>
		<td><input type="button" value="Payment infor" title="<?php echo $req['user_id']; ?>" title2="<?php echo $req['week']; ?>" 
                onclick="ShowPaymentPopup(<?php echo "'{$req['user_id']}', '{$req['username']}', '{$req['week']}'"; ?>);" /></td>
</tr>
	<?php } ?>
</tbody>
</table>

<ul>
	<li>(*) Declined: Wel will ask user upload another image</li>
</ul>
<div id="signimgpopup" style="display:none;">
	<div style="width:500px; height: 420px; background-color:white;">
        <div style="padding:5px;">
		    <h4 class="username" style="margin-bottom: 10px;">Username: <span></span></h4>
            <h4 class="accountmethod" style="margin-bottom: 10px;">Payment information: <span></span></h4>
		    <div class="accountdetail" style="width: 490px; height: 200px; overflow:hidden;">
                <table>
                </table>
		    </div>
            <h4>Note to user</h4>
            <textarea class="paidnote" style="width:97%; height: 80px;"></textarea>
		    <div style="width: 400px; height: 30px; padding-left: 10px;">
			    <input style="" type="button" value="<< Back" title="" onclick="showBackSignImg();" />
			    <input style="" type="button" value="Next >>" title="" onclick="showNextSignImg();" />
			    <input style="" type="button" value="Paid" title="" onclick="popupPaid();" />
			    <input style="" type="button" value="Reject" title="" onclick="popupReject();" />
			    <input style="" type="button" value="Cancel" title="" onclick="$('#signimgpopup').dialog('close');" />
		    </div>
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
	var payouts = <?php 
            foreach($payouts as &$payout){
            }
            echo json_encode($payouts); 
    ?>;
    //var payoutStatus = {};

    function findPayout(userid, week, dir){
        for(i=0;i<payouts.length;i++){
             payout = payouts[i];
             if(payout['user_id'] == userid && payout['week'] == week){
                if(dir == null){
                    return payout;
                }
                if(dir == 'next' && i < payouts.length - 1){
                    return payouts[i + 1];
                }
                if(dir == 'next'){
                    return null;
                }
                if(dir == 'back' && i >0){
                    return payouts[i-1];
                }
                return null;
             }
        }
        return null;
    }

    function findPayoutStatus(userid, week){
        var payout = findPayout(userid, week);
        if(payout != null) return payout['pay_status'];
        return null;
    }

	function ShowPaymentPopup(userid, username, week){		
		var jq = $('#signimgpopup');
        var payout = findPayout(userid, week);
				
		jq.attr('userid', userid);
		jq.attr('week', week);
		jq.find('h4.username span').html(payout['username']);
		jq.find('h4.accountmethod span').html(payout['payment_method']);
		
		jq.dialog({width: 520, resizable: false});

        paymentInfor = payout['payment_infor'];
        $('#signimgpopup table tr').remove();
        if(paymentInfor != null){
            for(i=0;i<paymentInfor.length;i++){
                var field = paymentInfor[i];
                var html = '<tr valign="top">';
                html += '<td valign="top"><span style="text-align:right;">' + field['field'] + '</span></td>';
                html += '<td style="width: 10px;"></td>';
                if(field['length'] > 255){
                    html += '<td>' + field['value'] + '</td>';
                }
                else{
                    html += '<td>' + field['value'] + '</td>';
                }
                html += '</tr>';
                $(html).appendTo('#signimgpopup table');
            }
        }
        		
		checkButtonStatus(userid, week);
	}
	
	function checkButtonStatus(userid, week){
		window.setTimeout(function(){
			if(findPayoutStatus(userid, week) != 'pending'){
				var jq = $('#signimgpopup');
				jq.find('input[value="Paid"],input[value="Reject"]').attr('disabled', 'disabled');
			}
			else{
				var jq = $('#signimgpopup');		
				jq.find('input[value="Paid"],input[value="Reject"]').removeAttr('disabled');
			}
		}, 100);
	}
		
	function showBackSignImg(){
		var userid = $('#signimgpopup').attr('userid');
		var week = $('#signimgpopup').attr('week');
        var payout = findPayout(userid, week, 'back');
        if(payout != null){
            ShowPaymentPopup(payout['user_id'], payout['username'], payout['week']);
        }
	}
	
	function showNextSignImg(){
		var userid = $('#signimgpopup').attr('userid');
		var week = $('#signimgpopup').attr('week');
        var payout = findPayout(userid, week, 'next');
        if(payout != null){
            ShowPaymentPopup(payout['user_id'], payout['username'], payout['week']);
        }
	}

    function updateRowStatus(userid, week, status){
         var i=0;
         for(i=0;i<payouts.length;i++){
             payout = payouts[i];
             if(payout['user_id'] == userid && payout['week'] == week){
                break;
             }
        }

        if(i>0){
            $('#fangirl-payout tbody tr').eq(i).find('td').eq(6).html(status);
        }
    }

    function popupPaid(){
		var userid = $('#signimgpopup').attr('userid');
		var week = $('#signimgpopup').attr('week');
                       
        var payout = findPayout(userid, week);

        data = {'user_id': userid,
                'week' : week,
                'note': $('#signimgpopup textarea.paidnote').val(), 
                'payment_method': payout['payment_method'],
                'payment_infor' : payout['payment_infor']
               };

        $.post('/admin/fan/payoutpaid', data, function(data){
        }, 'json')
        .success(function(data){
            if(payout != null){
                payout['pay_status'] = 'paid';
                updateRowStatus(userid, week, 'paid');
                checkButtonStatus();
            }
        })
        .fail(function(data){
        });
    }

    function popupReject(){
		var userid = $('#signimgpopup').attr('userid');
		var week = $('#signimgpopup').attr('week');

        var payout = findPayout(userid, week);

        data = {'user_id': userid,
                'week' : week,
                'note': $('#signimgpopup textarea.paidnote').val()
               };

        $.post('/admin/fan/payoutreject', data, function(data){
        }, 'json')
        .success(function(data){
            if(payout != null){
                payout['pay_status'] = 'rejected';
                updateRowStatus(userid, week, 'rejected');
                checkButtonStatus();
            }
        })
        .fail(function(data){
        });
    }

</script>
