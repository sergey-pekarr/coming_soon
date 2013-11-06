    <style>
        .toolbar
        {
            width: 25px;
        }
        .nav
        {
            list-style: none; 
            margin-bottom: 18px; 
            margin-left: 0px;
        }
        #testeditortoolbar .nav > li > a {
            color: #666;
	        display: block;
	        padding: 5px 5px 5px 25px;
	        text-decoration: none; 
	        float: left; 
	        text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
	        background-position: 5px 5px; 
	        background-repeat: no-repeat;
            font-size: 13px;
        }
        #testeditortoolbar .nav > li > a:hover {
            color: #000;
	        text-decoration: none; 
	        /*background-color: #fafafa;*/
            font-size: 13px;
        }
        .nav > li {
	        float: left; 
	        display: block;
        }
        #testeditortoolbar .nav .active > a {
	        text-decoration: none; 
	        background-color:#fafafa;
            font-size: 13px;
            border-top:solid 1px #ddd;
            border-right:solid 1px #ddd;
            border-left:solid 1px #ddd;
        }
        #testeditortoolbar .nav .active > a:hover 
        {
            color: #000;
	        text-decoration: none; 
	        background-color: #fafafa;
            font-size: 13px;
        }
        #testeditortoolbar.navbar-inner {
            border-radius: 4px; 
            padding-right: 10px; 
            padding-left: 10px; 
			padding-top:5px;
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#eeeeee', endColorstr='#dddddd', GradientType=0); 
            background-repeat: repeat-x; 
			background-image: none;
            background-color: #eeeeee;
			box-shadow: 0 0px 0px rgba(0, 0, 0, 0.25), inset 0 -1px 0 rgba(0, 0, 0, 0.1);
			background-clip: border-box;
            /*webkit-border-radius: 4px; 
            -moz-border-radius: 4px; 
            --webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25), inset 0 -1px 0 rgba(0, 0, 0, 0.1); 
            -moz-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25), inset 0 -1px 0 rgba(0, 0, 0, 0.1);*/
        }
        .testfooter
        {
            padding-top: 10px;
            padding-left: 100px;
            border-top: solid 1px #ddd;
            display:none;
        }
		.basic .question-main, .score .question-main, .advice .question-main
		{
			width: 580px;
		}
        .leftnumber
        {
            text-align:right;
            font-size:16px!important;
            color:#999;
            padding-right:30px;
            font-weight:bold
        }
        #testpage .answer-pure select
        {
            width:auto;
            height:auto;
            padding:0px;
        }
        #testpage ul.requirements li select, #testpage ul.requirements li input
        {
            width:auto;
            height:auto;
            padding:2px 2px 2px 2px;
            margin: 2px 2px 2px 2px;
        }
        #testpage .question  .question-main input
        {
            padding:0px;
        }
    </style>
    <div id="testeditortoolbar" class="navbar-inner navbar">
        <ul class="nav">
            <li class="active"><a onclick="TestEditor.showTab(this, 'basic'); return false;" style="background-image: url('/images/img/info_bw.png');">Basics</a></li>
            <li><a onclick="TestEditor.showTab(this, 'questions'); return false;" style="background-image: url('/images/img/question.png');">Questions</a></li>
            <li><a onclick="TestEditor.showTab(this, 'score'); return false;" style="background-image: url('/images/img/graphs_bw.png');">Scores</a></li>
            <!-- <li><a onclick="TestEditor.showTab(this, 'look'); return false;" style="background-image: url('/images/img/color_bw.png');">Look</a></li> -->
            <li><a onclick="TestEditor.showTab(this, 'advice'); return false;" style="background-image: url('/images/img/tools_bw.png');">Advice</a></li>
            <!--<li><a onclick="TestEditor.showTab(this, 'launch'); return false;" style="background-image: url('/images/img/play_bw.png');">Launch</a></li>-->
            <li><a onclick="TestEditor.navSaveDraft(this); return false;" style="background-image: url('/images/img/yicons.png'); background-position-y: -114px;">Save Draft</a></li>
            <li><a onclick="" href="quizz/preview" style="background-image: url('/images/img/preview.png'); display: none;">Preview</a></li>
            <li><a onclick="TestEditor.navComplete(this); return false;" style="background-image: url('/images/img/disk.png');">Complete</a></li>
			<li><a class="navremovetest" onclick="TestEditor.navRemove(this); return false;" style="background-image: url('/images/img/cancel.png');">Remove</a></li>
        </ul>
		<div class="clear"></div>
    </div>
	
	<div class="testpage" id="testpage">
	</div>
	<script type="text/javascript">
		(function () {
			<?php if($testid == null) { ?>
				TestEditor.createDefaultTest($('#testpage').get(0));
			<?php } else {?>
				TestEditor.load($('#testpage').get(0), '<?php echo $testid; ?>');
			<?php }?>
		})($);
	</script>

<div class="clear"></div>