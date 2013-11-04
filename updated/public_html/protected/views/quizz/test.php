
	<style>
		.question-left
		{
			width: 90px;
			height:20px;
			text-align:right;
			font-size:14px!important;
			color:#999;
			padding-right:20px;	
			font-weight:bold		
		}
		h2.testheader > label
		{
			width:40px;
		}
		.star:hover
		{
		    background-image:url(/images/img/darkstar-quizzy.png);
		    background-position-y: -24px;
		    background-repeat:repeat-x;
		}
		.star-current
		{
		    background-image:url(/images/img/darkstar-quizzy.png);
		    background-position-y: -48px;
		    background-repeat:repeat-x;
		}
	</style>
	<div id="testpage" class="testpage" style="min-height:300px;">
	</div>
	<?php if(!isset($type) || $type != 'preview') { ?>
    <div style="margin: 50px 10px 10px 0px; padding: 20px 10px 20px 10px; background-color: rgb(242, 242, 242);">
		<h3 style="float:left; text-transform:capitalize; margin: 0px 10px 0px 0px; font-size:14px; color:#aaa; ">RATE IT: </h3>
        <div style="float:left; background-image:url(/images/img/darkstar-quizzy.png); background-repeat:repeat-x; width:165px; height: 24px; position:relative;">
            <div class="star <?php if($rate == 1) echo 'star-current' ?>" style="position:absolute; width:20%; height: 24px; z-index: 6;" onclick="TestEditor.rateTest(this, 1, <?php echo $testid; ?>); return false;" ></div>
            <div class="star <?php if($rate == 2) echo 'star-current' ?>" style="position:absolute; width:40%; height: 24px; z-index: 5;" onclick="TestEditor.rateTest(this, 2, <?php echo $testid; ?>); return false;" ></div>
            <div class="star <?php if($rate == 3) echo 'star-current' ?>" style="position:absolute; width:60%; height: 24px; z-index: 4;" onclick="TestEditor.rateTest(this, 3, <?php echo $testid; ?>); return false;" ></div>
            <div class="star <?php if($rate == 4) echo 'star-current' ?>" style="position:absolute; width:80%; height: 24px; z-index: 3;" onclick="TestEditor.rateTest(this, 4, <?php echo $testid; ?>); return false;" ></div>
            <div class="star <?php if($rate == 5) echo 'star-current' ?>" style="position:absolute; width:100%; height: 24px; z-index: 2;" onclick="TestEditor.rateTest(this, 5, <?php echo $testid; ?>); return false;" ></div>
        </div>
        <div class="clear"></div>
    </div>
	<?php } ?>
	<script type='text/javascript' src='/js/jsapi.js'></script>
	<script type="text/javascript">
    google.load('visualization', '1.0', { 'packages': ['corechart'] });
	</script>
	<script type="text/javascript">
		(function () {
			TestEditor.loadTest($('#testpage').get(0), '<?php echo $testid; ?>', 0);
			window.setTimeout(function(){updateIm = function(){};},1000);
		})($);
	</script>