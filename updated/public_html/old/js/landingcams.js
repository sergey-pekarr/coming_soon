
$('#landingcams-stepAll-form').keyup(function(e) {
	if (e.keyCode == 13)
		formSubmit('landingcams-stepAll-form');
});

function landingcams_onChangeCountry(el)
{
	if ($(el).val()=='US')
		$('.location.state').fadeIn(1000);
	else
		$('.location.state').fadeOut(1000);
}



function landingcams_step2_setStates(forceSelected)
{
	$("#LandingCamsAllinOneStepForm div.state").fadeIn(400);//.show();
	
	$("#LandingCamsAllinOneStepForm_state").hide(0, function(){
    	$("#state-NA").hide();
    	$("#state-loading").show();
    	//$("#LandingCamsAllinOneStepForm_state").html('');
    });

	
	country = $("#LandingCamsAllinOneStepForm_country").val();
	
    $.post(
            '/ajax/States', 
            {country:country, withSelect:true}, 
            function(data) {
                $("#state-loading").hide(0, function(){
                	
                	if (data!="")
                	{
                		$("#LandingCamsAllinOneStepForm_state").html(data).show();
                		$("#LandingCamsAllinOneStepForm_state").val(forceSelected);
                	}
                	else
                		$("#state-NA").show();
                	
                	//$("#LandingCamsAllinOneStepForm_state").removeClass('error');
                });
                
            },
            "json"
        );	
}









