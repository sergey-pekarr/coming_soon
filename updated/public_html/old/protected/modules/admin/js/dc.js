
	function dc_init()
	{
		$('.dateControl').find('.btn').each(function(){
			$(this).attr('data-loading-text', $(this).html());
		});

		$('.dateControl').find('i.icon-calendar').each(function(){
			$(this).attr('data-loading-text', $(this).html());
		});
		
		//calendarDate1
		Calendar.setup({
		    inputField : $('.date1').attr('id'),
		    trigger    : "calendarDate1",
		    min: 20100101,
		    max: dc_Ymd,
		    onSelect   : function() { 
			    this.hide();
			    dc_submit( $("#calendarDate1") );  
			}
		});	

		Calendar.setup({
		    inputField : $('.date2').attr('id'),
		    trigger    : "calendarDate2",
		    min: 20100101,
		    max: dc_Ymd,
		    onSelect   : function() { 
			    this.hide();
			    dc_submit( $("#calendarDate2") );  
			}
		});	
	}
	
	function dc_submit(el)
	{
		$(el).button('loading');
		$(el).parents('form').submit();
	}

	//safari bug...
	//http://stackoverflow.com/questions/3085937/safari-js-cannot-parse-yyyy-mm-dd-date-format
	function parseDate(input, format) 
	{
		format = format || 'yyyy-mm-dd'; // default format
		var parts = input.match(/(\d+)/g), i = 0, fmt = {};
		// extract date-part indexes from the format
		format.replace(/(yyyy|dd|mm)/g, function(part) { fmt[part] = i++; });

		return new Date(parts[fmt['yyyy']], parts[fmt['mm']]-1, parts[fmt['dd']]);
	}

	
	function dc_setToday(el)
	{
		var date = parseDate(dc_dateServer);
		date = dc_formatDate(date);
		$(el).parents('form').find('.date1').val(date); 
		$(el).parents('form').find('.date2').val(date);

		dc_submit(el);
	}

    function dc_formatDate(date) 
    {
        var month = date.getMonth() + 1;
        if (month < 10) month = '0' + month;
        var d = date.getDate();
        if (d < 10) d = '0' + d;
        return date.getFullYear() + "-" + month + "-" + d;
    }

	function dc_changeDate(el, dateBox, shift)
	{
		var date = parseDate( $(el).parents('form').find('.date'+dateBox).val() );
		date.setDate(date.getDate() + shift);
		date = dc_formatDate(date);
		$(el).parents('form').find('.date'+dateBox).val(date); 
	}	

	function dc_setDate(el, dateBox, shift)
	{
		dc_changeDate(el, dateBox, shift);

		dc_submit(el);
	}

	function dc_setDates(el, shift)
	{
		dc_changeDate(el, '1', shift); 
		dc_changeDate(el, '2', shift);
		
		dc_submit(el);
	}

	function dc_setDateMain(el)
	{
		$(el).parents('form').find('.date2').val( $(el).parents('form').find('.date1').val() );

		dc_submit(el);		
	}


	function dc_setWeek(el, shift)
	{
		
		if (shift==0)//this week
			var curr = parseDate( dc_dateServer );
		else
			var curr = parseDate( $(el).parents('form').find('.date1').val() );
		
		//fix for Sunday
		if (curr.getDay()==0)
			shift=-1;
		
		var first = curr.getDate() - curr.getDay() + 1 + 7*shift; // First day is the day of the month - the day of the week
		var firstday = new Date(curr.setDate(first));

		var last = firstday.getDate() - firstday.getDay() + 1 + 6; // last day is the first day + 6
		var lastday = new Date(curr.setDate(last));

		var date1 = dc_formatDate(firstday);
		var date2 = dc_formatDate(lastday);
		
		$(el).parents('form').find('.date1').val(date1);
		$(el).parents('form').find('.date2').val(date2);
		
		dc_submit(el);
	}


	//	http://javascript.about.com/library/bllday.htm
	function dc_daysInMonth(month,year) 
	{
		var dd = new Date(year, month, 0);
		return dd.getDate();
	} 
		
	function dc_setMonth(el, shift)
	{
		if (shift==0)//this week
			var curr = parseDate( dc_dateServer );
		else
			var curr = parseDate( $(el).parents('form').find('.date1').val() );

		var firstday = new Date(curr.setDate(1));

		var y = firstday.getFullYear();
		var m = firstday.getMonth();
		m = m + shift + 1;
		if (m<1)
		{
			m = 12;
			y--;
		}
		if (m>12)
		{
			m = 1;
			y++;
		}
		if (m<10) m='0'+m; 
		firstday = parseDate( y+'-'+m+'-01' );

		var last = dc_daysInMonth( m, y );
		var lastday = parseDate( y+'-'+m+'-'+last );

		var date1 = dc_formatDate(firstday);
		var date2 = dc_formatDate(lastday);
		
		$(el).parents('form').find('.date1').val(date1);
		$(el).parents('form').find('.date2').val(date2);
		
		dc_submit(el);
	}
	
	