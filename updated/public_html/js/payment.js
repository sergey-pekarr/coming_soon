
var countryPayment = "";

function payment_setStates()
{
    $("#PaymentForm_state").hide(0, function(){
    	$("#state-NA").hide();
    	$("#state-loading").show();
    	$("#PaymentForm_state").html('');
    });

	
	country = $("#PaymentForm_country").val();
	
    $.post(
            '/ajax/States', 
            {country:country}, 
            function(data) {
                $("#state-loading").hide(0, function(){
                	
                	if (data!="")
                		$("#PaymentForm_state").html(data).show();
                	else
                		$("#state-NA").show();
                });
                
            },
            "json"
        );	
}









