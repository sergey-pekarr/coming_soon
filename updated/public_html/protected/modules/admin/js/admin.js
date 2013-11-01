
var upd_timer;

var ts = 0;//datatime in seconds
var ts_timer;
var ts_offset = -5*3600;


$(document).ready(function() 
{
///    $('.tabs').tabs();
///    $('#topbar').dropdown();   
    
	upd_timer = setTimeout( "adminUpd()", 60000 );
    
    showDateTime(ts+ts_offset);
   
});

function formatDT(v) 
{
	if (v<10)
		return "0" + v;
	return v;
}
function showDateTime(ts)
{

	clearTimeout(ts_timer);
	ts_timer = window.setTimeout(function() { showDateTime(ts+1); }, 1000);
	
	var dt = new Date(1000*ts);
	//dt = Date.UTC(hour, min, sec, ms)(hour, min, sec, ms);
	
	var current_date = dt.getUTCDate();
	var current_month = 1 + dt.getUTCMonth();
	var current_year = dt.getUTCFullYear();	
	
	var hours = dt.getUTCHours();
	var minutes = dt.getUTCMinutes();
	var sec = dt.getUTCSeconds();

	//var dtText = dt.toString();//
	var dtText = current_year + "-" + formatDT(current_month) + "-" + formatDT(current_date) + " " + formatDT(hours) + ":" + formatDT(minutes) + ":" + formatDT(sec); 
	
	$(".dateTime").html(dtText);
}


function adminUpd(){
	
	clearTimeout(ts_timer);	
	clearTimeout(upd_timer);
	
	$.post("/admin/ajax/update", {}, 
            function(data){
                
				upd_timer = setTimeout( "adminUpd()", 60000 );
    			
                if (typeof(data.ts)!='undefined')
                	showDateTime(data.ts+ts_offset);
//alert(data.ts);                

	}, "json");
}


function userDelete(id)
{
    $.post(
        '/admin/ajax/userDelete', 
        {id:id},
        function(data) {
            if (data.success == 'Yes')
            {
                $('#tr_'+id).fadeOut(600);
            }                
        },
        "json"
    );
}

function userBan(id)
{
    $.post(
        '/admin/ajax/userBan', 
        {id:id},
        function(data) {
            if (data.success == 'Yes')
            {
                $('#userBanBtn').button('reset');
                $('#userBanBtn').hide();
                $('#userUnBanBtn').show();
                
                $("#userStatus").attr('class', 'role_banned');
                $("#userStatus").html('banned');
            }                
        },
        "json"
    );
}
function userUnBan(id)
{
    $.post(
        '/admin/ajax/userUnBan', 
        {id:id},
        function(data) {
            if (data.success == 'Yes')
            {
                $('#userUnBanBtn').button('reset');
                $('#userUnBanBtn').hide();
                $('#userBanBtn').show();
                
                $("#userStatus").attr('class', 'role_'+data.role);
                $("#userStatus").html(data.role);
            }                
        },
        "json"
    );
}


function hideReportAbuse(reportId)
{
    $.post(
        '/admin/ajax/hideReportAbuse', 
        {reportId:reportId},
        function(data) {
            if (data.success == 'Yes')
            {
                $("#row_"+reportId).remove();
            }                
        },
        "json"
    );
}




function clearCache()
{
    $.post(
        '/admin/ajax/ClearCache', 
        {},
        function(data) {
            alert('Cache cleared!');//alertDialog('Cache cleared!');
        },
        "json"
    ); 
}





function imageApprove(user_id,n,action, reason)
{
	if (action=='declined' && !confirm('Are you sure want to DECLINE and DELETE?'))
		return;
		
	
	$.post(
        '/admin/ajax/imageApprove',
        { user_id: user_id, n: n, action: action, reason: reason },
        function(data) {
            if (data.success == 'Yes')
            {
            	if (action=='clothed')
            	{
            		$('#tr_'+user_id+'_'+n).addClass('trApprovedClothed');
            		$('#tr_'+user_id+'_'+n+' img').addClass('imgApprovedClothed');
            	}
            		
            	if (action=='naked')
            	{
            		$('#tr_'+user_id+'_'+n).addClass('trApprovedNaked');
            		$('#tr_'+user_id+'_'+n+' img').addClass('imgApprovedNaked');
            	}            	
            	
            	if (action=='declined')
            		$('#tr_'+user_id+'_'+n).fadeOut(1000, function(){
            			$(this).remove();
            		});
            }                
        },
        "json"
    );
}








//Modal for all
function modalBoxPrepare(title)
{
	$("#modalBox .modal-body").html('<div class="loadingAjax"></div>');
	$("#modalBox").modal('show');
	modalBoxSetTitle(title);
}
function modalBoxSetTitle(title)
{
	$("#modalBox .modal-header h3").html(title);
}
function modalBoxSetBody(body)
{
	$("#modalBox .modal-body").html(body);
}








function usersRoleChange(id, role)
{
    if (!confirm('Are you sure to make user "'+role+'"?'))
    	return;
	
	$.post(
    	'/admin/ajax/usersRoleChange', 
        {id:id, role:role},
        function(data) {
        	if (data.success == 'Yes')
        		window.location.reload();
        	else
        		alert('error...');
        },
        "json"
    ); 	
}



function paymentCancelSubscription(userId)
{
    if (!confirm('Are you sure?')) return;

	$.post(
    	'/admin/ajax/paymentCancelSubscription', 
        {id:userId},
        function(data) {
        	if (data.success == 'Yes')
        	{
        		//$('.cancelSubscription').hide();//
        		window.location.reload();//window.location.href = window.location.href+'#paymentInfoTab';
        	}
        	else
        		alert('error...');
        },
        "json"
    );	
}



function slideUpDownBox(id)
{
	if ($('#'+id).css('display')=='none')
		$('#'+id).slideDown(200);
	else
		$('#'+id).slideUp(200);
}

