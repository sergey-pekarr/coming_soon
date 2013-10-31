
$('#allin9-step1-form').keyup(function(e) {
	if (e.keyCode == 13)
    	formSubmit('signup-area');
});

function allin9_onChangeCountry(el)
{
	if ($(el).val()=='US')
		$('.location.state').fadeIn(1000);
	else
		$('.location.state').fadeOut(1000);
}



function allin9_step3_setStates()
{
    $("#Allin9Step3Form_state").hide(0, function(){
    	$("#state-NA").hide();
    	$("#state-loading").show();
    	$("#Allin9Step3Form_state").html('');
    });

	
	country = $("#Allin9Step3Form_country").val();
	
    $.post(
            '/ajax/States', 
            {country:country}, 
            function(data) {
                $("#state-loading").hide(0, function(){
                	
                	if (data!="")
                		$("#Allin9Step3Form_state").html(data).show();
                	else
                		$("#state-NA").show();
                });
                
            },
            "json"
        );	
}









