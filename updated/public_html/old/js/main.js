
var update_interval = 600000;//840000;
var update_interval_2 = 60000;

var srnupd = false;//needs to update screen resolution

$(document).ready(function() 
{
	if (srnupd)
		srnupdate();	
	
	updStart();
	
/*    $(".alert-message").alert();
    $('.btn').button();
    $('#topbar').dropdown();
    $('.tabs').tabs();
*/    
    //alertDialog('hggfghfghfghf');
//    var template = "<div class='content nobg'><p class='triangle-left top'>Welcome! Swoonr social video makes connection with people around you easier then ever. <br /> Discaver <span style='font-weight:bold'>real people</span>, first and have fan! <span style='text-decoration:underline'>See for yourself</span> just how easy it is to get started!</p></div>";
//    $("#logoMain").popover({placement:'below-left', html:true, template:template, delayIn:200, delayOut:400});//.popover('show');

    
    
/*
    $("input:submit").button();
    $("input:button").button();
    $("button").button();
    $("a.button").button();
*/    
    //$('.selector').dialog({ dialogClass: 'alert' });
    
    //alertDialog('hello!');    



    
});

function updStart(){
    setTimeout( "upd()", update_interval );
}

function srnupdate()
{
    var x = getClientWidth();
    var y = getClientHeight();
	
	$.post(
			"/ajax/srupdate", 
			{x:x, y:y}, 
            function(data){}, 
            "json"
    );	
}

function upd(){
    $.post("/ajax/update", {}, 
            function(data){
                if(typeof(data) != 'undefined')
				{
                    setTimeout( "upd()", update_interval );
                    /*if(!data.success == 'Yes')
                        setTimeout( "upd()", update_interval );
                    else
                        setTimeout( "upd()", update_interval_2 );*/                    
				}
                /*else
                    setTimeout( "upd()", update_interval_2 );*/
    }, "json");
}

$.preloadImages = function () {
    for (var i = 0; i < arguments.length; i++) {
        $("<img />").attr("src", arguments[i]);
    }
};

function signpExpand()
{
    $('#signup-area').slideDown(400/*,function(){
        $('.signupBox .or-separator').slideDown(800);
    }*/);
}

function signin()
{
 
    if ($.find("#reg_login_form").length && !$.find("#reg_login_form.signin2").length)
    {
        loginFromShow();
        return;
    }

    if (!$.find("#reg_login_form.signin2").length)
    {
        document.location.href = "/site/signin";
    }

}

function loginFromShow()
{
    $('html, body').animate(
        {scrollTop:0}, 
        'fast', 
        function(){
            $('#guest-top h1').fadeOut(200,function(){
                $('#reg_login_form').fadeIn(400);
            });
        }
    );
}

function coockieUsernameDelete()
{
    $.post(
        '/ajax/coockieUsernameDelete', 
        {}, 
        function(data) {
            if (data.success=='Yes')
                window.location.reload();                //$("#reg_login_form h2").html('Sign in');
        },
        "json"
    );
} 

//report
function reportExpand(elLink)
{
    var container = $(elLink).parent();
    $(elLink).fadeOut(300, function(){
        container.find('.reportActions').fadeIn(200);
    });
}
function reportSend(elBtn, id)
{
    $.post(
        '/ajax/reportabuse', 
        {id:id}, 
        function(data) {
            //reportClose(elBtn);
            
            var container = $(elBtn).parent().parent();
            container.fadeOut(600);
            
        },
        "json"
    );   
    
    //reportClose(elBtn)   
}
function reportClose(elBtn)
{
    var container = $(elBtn).parent().parent();
    container.find('.reportActions').fadeOut(300, function(){
        container.find('a').fadeIn(200);
    });
}



//submit form with bootstrap and some stupid brawsers
function formSubmit(box)
{
    $('#'+box+' .btn').button('loading'); 
    $('#'+box+' form').submit();
}

function regStep2FormBoxLoad()
{
    if (!$(".guest-right").length)
    {
    	window.location.href = '/site/registrationStep2';
    	return;    	
    }
	
	$.post("/userRegistration/step2_FormLoad", 
        {}, 
        function(data){
            if (typeof(data) != 'undefined')
        	{
            	$(".guest-right").addClass('step2');
            	
            	$('#reg-forms-box').html(data);
            	
            	$('.signup-header p').hide();
 			}
    }, "html");     
}

function loginFormShow()
{
	$('.login1, .login2').fadeOut(400, function(){
		$('#reg_login_form').fadeIn(1000);
	});	
}






































function sendWink(id)
{
    $.post(
        '/ajax/sendWink/', 
        {id:id}, 
        function(data) {
            $("#sendWink").removeAttr('href');
            $("#sendWink").removeAttr('onclick');
            $("#sendWink").addClass('winkSent');
        },
        "json"
    );
}


function alertDialog(txt)
{
    $("#modalAlert .modal-body p").html(txt);
    $("#modalAlert").modal({backdrop:"static"});
    $("#modalAlert").modal('show');
    
    
    
    /*//��������� ��� �������� ������� (���� ������ ����� ���� �� �������� ������� ��� �� ������� ���������� ������ ������)
    $(".ui-dialog-content").dialog("close");

    txt = '<div class="alertDialog"><br />' + txt + '</div>';
    
    $(txt).dialog({
        width: 'auto',
        height: 'auto',
        dialogClass: 'ui-alert',
        draggable:false,
        resizable:false,
        modal: true,
        buttons: [{
            text: "Ok",
            click: function() { $(this).dialog("close"); }
        }],
        open: function() {
            $('.ui-dialog-buttonset').css('float', 'none'),
            $('.ui-dialog-buttonpane').css('text-align', 'center'),//find('button:contains("Cancel")').addClass('ui-icon-cancel');
            $('.ui-dialog-buttonpane').css('padding', '0'),
            $('.ui-dialog-buttonpane').css('border', 'none'),
            $('.ui-dialog-content').css('min-height', '40px'),
            $('.ui-button').css('margin-right', '0')
        }
    });*/
}

function simpleDialog(id, w, h)
{
    $(".ui-dialog-content").dialog("close");

    //txt = '<div class="alertDialog"><br />' + txt + '</div>';
    
    $("#"+id).dialog({
        width: w,
        height: h,
        dialogClass: 'ui-alert',
        draggable:false,
        resizable:false,
        modal: true
        /*,
        buttons: [{
            text: "Close",
            click: function() { $(this).dialog("close"); }
        }],
        open: function() {
            $('.ui-dialog-buttonset').css('float', 'none'),
            $('.ui-dialog-buttonpane').css('text-align', 'center'),//find('button:contains("Cancel")').addClass('ui-icon-cancel');
            $('.ui-dialog-buttonpane').css('padding', '0'),
            $('.ui-dialog-buttonpane').css('border', 'none'),
            $('.ui-dialog-content').css('min-height', '40px'),
            $('.ui-button').css('margin-right', '0')
        }*/
    });
}


function loadPanel(panel,page,all)
{
    if (typeof(all)=='undefined')
    {
        all = 0;
    }
    
    $("#"+panel+"Box .panel-list").html('');
    $("#"+panel+"Box").addClass('loadingAjax');

    $.post(
        '/ajax/Panel'+panel, 
        {page:page, all:all}, 
        function(data) {
            $("#"+panel+"Box").removeClass('loadingAjax');
            $("#"+panel+"Box").html(data);
        },
        "html"
    );                
}


function sendUpdateMessage()
{
    var message = $("#messageBody").val();

    $.post(
        '/profile/DashboardMessage', 
        {message:message}, 
        function(data) {
            $("#PanelDashboardUpdates ul li.write").after(data);
        },
        "html"
    );             
}

/*function sendPrivateMessage(id_to)
{
    var subject = $("#messageSubject").val();
    var message = $("#messageBody").val();

    $.post(
        '/profile/UserPrivateMessage', 
        {id_to:id_to, subject:subject, message:message}, 
        function(data) {
            $('#sendMessageBox').dialog('close');
            //$("#profile-private-messages-list li.write").after(data);
        },
        "html"
    );             
}*/

function hidePrivateMessage(id)
{
    $("#messBox_"+id).fadeOut(200);
    $.post(
        '/ajax/messagehide', 
        {id:id}, 
        function(data) {
            if (data.success=='Yes')
            {
                $("#messagesNewCount").html(data.newCount);
            }
        },
        "json"
    );             
}

function readMessageTextFull(id)
{
    $("#messText_"+id).fadeOut(200, function(){
        $("#messTextFull_"+id).fadeIn(200); 
    });
}
function closeMessageTextFull(id)
{
    $("#messTextFull_"+id).fadeOut(200, function(){
        $("#messText_"+id).fadeIn(200); 
    });    
}
function markAsReadMessage(id,inboxAll)
{
    if (!inboxAll)
        $("#messBox_"+id).fadeOut(200);
   
    $.post(
        '/ajax/messageMarkAsRead', 
        {id:id}, 
        function(data) {
            if (data.success=='Yes')
            {
                $("#messagesNewCount").html(data.newCount);
                $("#messBox_"+id+" .messNew").fadeOut(400,function(){
                    $(this).remove();
                });
            }
        },
        "json"
    );     
}



function hideNotify()
{
    $('.notify-notice').fadeOut(500, function(){$('.notify-notice').remove();});
    
    $.post(
        '/ajax/notifyhide', 
        {}, 
        function(data) {
            ;
        },
        "json"
    );
    
    return false;        
}


function deleteUpdate(id, el)
{
    $.post(
        '/ajax/updatedelete/'+id, 
        {id:id}, 
        function(data) {
            var box = $(el).parent().parent().parent();
            $(box).fadeOut(500, function(){$(box).remove();});
        },
        "json"
    );
}


function textchange(id,countMax)
{
    var a = $("#"+id).val().length;
    if (a > countMax) {
        $("#"+id).val($("#"+id).val().substring(0,countMax));
    }
    a = $("#"+id).val().length;
    $("#"+id+"_count_sym").html(countMax - a);
}




function getClientWidth()
{
  return document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientWidth:document.body.clientWidth;
}
function getClientHeight()
{
  return document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientHeight:document.body.clientHeight;
} 




function contactUsShow()
{
	var e = "support";
	var eH = "pink";
	var eH2 = "meets.com";

	document.write("<a href=" + "mail" + "to:" + e + "@" + eH + eH2+ ">" + e + "@" + eH  + eH2 + "</a>");
}


