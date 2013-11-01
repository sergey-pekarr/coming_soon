
$(document).ready(function() 
{
	
	//remove error message, popup when onFocus
	$(".form input").bind(
			'focus',
			function(){
			
				var container = $(this).closest('div');

				if (container.hasClass('error'))
				{
					container.removeClass('error');
					container.find('.errorMessage').hide();
					container.addClass('success');
					container.popover('hide').popover('disable');
				}

			}
	);
	
	$(".form select").bind(
			'focus',
			function(){
			
				var container = $(this).closest('div');

				if (container.hasClass('error'))
				{
					container.removeClass('error');
					container.find('.errorMessage').hide();
					container.addClass('success');
					container.popover('hide').popover('disable');
				}

			}
	);

    
});
