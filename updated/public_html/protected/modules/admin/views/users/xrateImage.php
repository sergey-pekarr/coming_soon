<h3>xRate Images</h3>

<ul class="xRateTopForm">
	<li <?php if ($type=='') { ?> class="active" <?php } ?> >
		<a href="/admin/users/xrateImage?type=&ppp=<?php echo $ppp ?>" >Not checked</a> 
	</li>
  	
  	<li <?php if ($type=='clothed') { ?> class="active" <?php } ?> >
  		<a href="/admin/users/xrateImage?type=clothed&ppp=<?php echo $ppp ?>" style="color: green" class="bold" >clothed</a>
  	</li>
  	
  	<li <?php if ($type=='naked') { ?> class="active" <?php } ?> >
  		<a href="/admin/users/xrateImage?type=naked&ppp=<?php echo $ppp ?>" style="color: red" class="bold" >naked</a>
  	</li>
  	
  	
  	<li></li>
  	
  	<li>
  		<form action="/admin/users/xrateImage" method="get">
	  		<input type="hidden" name="type" value="<?php echo $type ?>" />
	  		
	  		Per page:
	  		
	  		<select name="ppp" value="<?php echo $ppp ?>" style="width: 80px">
	  			<option <?php if ($ppp==5) { ?> selected="selected" <?php } ?> value="5">5</option>
	  			<option <?php if ($ppp==10) { ?> selected="selected" <?php } ?> value="10">10</option>
	  			<option <?php if ($ppp==20) { ?> selected="selected" <?php } ?> value="20">20</option>
	  			<option <?php if ($ppp==50) { ?> selected="selected" <?php } ?> value="50">50</option>
	  			<option <?php if ($ppp==100) { ?> selected="selected" <?php } ?> value="100">100</option>
	  			<option <?php if ($ppp==200) { ?> selected="selected" <?php } ?> value="200">200</option>
	  			<option <?php if ($ppp==500) { ?> selected="selected" <?php } ?> value="500">500</option>
	  			<option <?php if ($ppp==1000) { ?> selected="selected" <?php } ?> value="1000">1000</option>
	  		</select>
	  		
  	</li>
  	<li>
	  		<input class="btn" type="submit" value="Go" />
  		
  		</form>
  	</li>
</ul>
<div class="clear"></div>

<br />
<div class="listsInfo">
    <div class="left">
        Found: <?php echo $images['count'] ?>
    </div>
      
    <?php 
        $this->widget(
                    'CLinkPager', 
                    array(
                        'pages' => $pages,
                        'currentPage'=>$pages->getCurrentPage(),//(false)
                        'header'=>'',
                        'htmlOptions'=>array('class'=>'pagination'),                        
                    )
    	);?>
    <div class="clear"></div>        
</div>

<form <?php /*action="/admin/users/xrateImage" */?> method="post">
	<input type="hidden" name="ppp" value="<?php echo $ppp ?>" />
	<input type="hidden" name="type" value="<?php echo $type ?>" />
	<input type="hidden" name="page" value="<?php echo ($pages->getCurrentPage()+1) ?>" />
<?php
if ($images['list'])
{
        foreach ($images['list'] as $k=>$row)
        {
            $userId = $row['user_id'];
        	$profile = new Profile($userId);
            ?>
            

			<div id="tr_<?php echo $row['user_id'].'_'.$row['n'] ?>" class="xRateBox xRateBox_<?php echo $row['xrated'] ?>" >
				
				<input type="hidden" name="user_id[]" value="<?php echo $row['user_id'] ?>" />
				<input type="hidden" name="n[]" value="<?php echo $row['n'] ?>" />
				<input type="hidden" name="rated[]" id="image_xrate_<?php echo $row['user_id'].'_'.$row['n'] ?>" value="<?php echo $row['xrated'] ?>" />
				
				<img 
					title="Rate (user_id: <?php echo $row['user_id'] ?>, n: <?php echo $row['n'] ?>)"
					class="img-big" 
					alt="" src="<?php echo $profile->imgUrl('big', $profile->imgGetIndx($row['n']), false) ?>"
					onClick="javascript:clickImage(<?php echo $row['user_id'] ?>, <?php echo $row['n'] ?>);" 
				/>
                
                <?php $this->widget('application.modules.admin.components.user.UserInfoShotWidget', array('userId'=>$userId)) ?>
				
				
				<br />
                <a class="btn btn-inverse smaller" onclick="javascript:imageApprove(<?php echo $row['user_id'] ?>, <?php echo $row['n'] ?>,'declined')" >Delete</a>
            
            </div>
             
        <?php
        }
} ?>

<div class="clear"></div>

<div class="center">
	<br />
	<input class="btn btn-success" type="submit" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Save &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" />
	<br />
</div>


</form>

<br /><br /><br />
<div class="listsInfo">
    <?php 
        $this->widget(
                    'CLinkPager', 
                    array(
                        'pages' => $pages,
                        'currentPage'=>$pages->getCurrentPage(),//(false)
                        'header'=>'',
                        'htmlOptions'=>array('class'=>'pagination'),                        
                    )
    	);?>
    <div class="clear"></div>        
</div>


<script>
<!--

	function clickImage(user_id, n)
	{
	    var image_xrate_value = jQuery("#image_xrate_"+user_id+"_"+n).val();
	    
	    var new_value="";
	    var new_class="";
	    
	    jQuery("#tr_"+user_id+"_"+n).removeClass("xRateBox_");
	    jQuery("#tr_"+user_id+"_"+n).removeClass("xRateBox_clothed");
	    jQuery("#tr_"+user_id+"_"+n).removeClass("xRateBox_naked");
	    
	    switch(image_xrate_value)
	    {
	    	case '':
	        case '0':
	        case 'clothed':
	            new_value = "naked";
	            new_class = "xRateBox_naked";
	            break;
	        case 'naked':
	            new_value = "clothed";
	            new_class = "xRateBox_clothed";
	            break;
	    }
	    
	    jQuery("#image_xrate_"+user_id+"_"+n).val(new_value);
	    jQuery("#tr_"+user_id+"_"+n).addClass(new_class);
	}

//-->
</script>

