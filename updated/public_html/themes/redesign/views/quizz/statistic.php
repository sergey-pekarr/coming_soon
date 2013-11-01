<html>
<head>
<script type="text/javascript" src="/js/jquery_min.js"></script>
<script type="text/javascript" src="/js/QuizzMakerScript.debug.js"></script>
<script type='text/javascript' src='/js/jsapi.js'></script>
<script type="text/javascript">
	google.load('visualization', '1.0', { 'packages': ['corechart'] });
	
	if (google.visualization) {
        drawChart();
    }
    else {
        google.setOnLoadCallback(drawChart);    
	}
	
	function addChart(name, samplePoints, minX, maxX){
        var chartHtml = "<div class='chart' style='float: left; margin: 10px 10px 10px 10px; width: 210px; height: 135px;'>" + "<div class='chart-chart'></div>" + "<div class='chart-title'></div>" + '</div>';
        var chartjq = $(chartHtml).appendTo('#chart_div');
        var chart = new Quizz.QuizzChart();
        chart.init(samplePoints, minX, minX, maxX, 210, 95);
        chart.draw(chartjq.find('.chart-chart').get(0));
        var title = name;
        chartjq.find('.chart-title').html(title);
	}
	
	function drawChart(){
		<?php foreach($chartDatas as $key => $chartData) { 
			$samplePoints = array();
			if(isset($chartData['samplePoints'])) $samplePoints = $chartData['samplePoints'];
			echo "addChart('$key',".json_encode($samplePoints).",{$chartData['min']},{$chartData['max']});"; 
		}
		?>                
	}
</script>
</head>
<body>
    <div id="chart_div">
    </div>
</html>

