
<div id="todayGoldFormBox" class="form">
<div class="row">
<?php 

$form=$this->beginWidget('CActiveFormSw', array(
    'action'=>Yii::app()->createUrl('/admin/payments/todayGold'),
    'method'=>'get',
)); 

	$this->widget(
		'application.modules.admin.components.forms.DateControlWidget', 
		array(
			'model'=>$model
		)
	);

?>

    <dt><?php echo $form->labelEx($model,'perPage'); ?></dt>
    <dd>
        <?php echo $form->dropDownList($model,'perPage', array('10'=>'10','50'=>'50','100'=>'100','200'=>'200','500'=>'500','1000'=>'1000')); ?>
    </dd> 

    <dt><?php echo $form->labelEx($model,'aff'); ?></dt>
    <dd>
        <?php echo $form->dropDownList($model,'aff', $model->affsSelect); ?>
    </dd> 

    <dt><?php echo $form->labelEx($model,'paymod'); ?></dt>
    <dd>
        <?php echo $form->dropDownList($model,'paymod', $model->paymodsForSelect); ?>
    </dd> 

    <dt><?php echo $form->labelEx($model,'status'); ?></dt>
    <dd>
        <?php echo $form->dropDownList($model,'status', array(''=>"All", 'not_active'=>"All not active", 'active'=>"active", 'expired'=>"expired", 'cancelled'=>"cancelled", 'refunded'=>"refunded", 'chargeback'=>"chargeback")); ?>
    </dd> 
<?php $this->endWidget(); ?>


<dd style="margin-left: 40px;">
    <button 
        class="btn" 
        data-loading-text="loading..." 
        onclick="javascript:$('#todayGoldFormBox .btn').button('loading'); $('#todayGoldFormBox form').submit();" 
    >
        &nbsp;&nbsp;&nbsp;Show&nbsp;&nbsp;&nbsp;
    </button>
</dd>


</div>
</div>
<div class="clear"></div>


<div class="listsInfo">
    <div class="left">
        <h3>
        	Today's Gold Users
        	<span class="smaller">(<?php echo $rows['count'] ?>)</span>
        </h3>        
        <?php if ($rows['countWaiting']) { ?>
        	&nbsp;&nbsp;&nbsp;
        	<span style="color: red">Waiting for schedule: <?php echo $rows['countWaiting'] ?></span>
        <?php } ?>
        
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










<table class="table table-condensed todayGold">
<thead>
    <tr>
	    <th>id</th>
	    <th>Username</th>
	    <th>Email</th>
	    <th>Affid</th>
	    <th>Managerid</th>
	    <th>Masterid</th>
	    <th>Regdate / Payment time</th>
	    <th>Pics</th>
	    <th>Amount</th>
	    <th>Billing</th>
	    <th>Logins</th>
	    <th>Msgs</th>
	    <th>Winks</th>
	    <th style="text-align:center; white-space:nowrap">Country/Region/City</th>
	    <th style="text-align:center; white-space:nowrap">GeoIP <br/>IP/Country/Region/City</th>
	    <th>Form</th>
	    <th>Phone</th>
	    <th>Zombaio Billing Info</th>
	    <th>Proxy</th>
	    <th>Maxmind Proxy</th>
	    <th>OS,<br />browser</th>
	    <th>IP info</th>
	    <th>Recurring</th>
	    <th>Ref url</th>
	</tr>
</thead>

<tbody>
  	
  	<?php 
//FB::warn($rows, 'rows');  	
  	if ($rows['list']) 
  		foreach ($rows['list'] as $row) { 
  		
  			$profile = new Profile($row['user_id']);	
  		?>
		<tr>
	   		
	   		<td><span title="Sale ID: <?php echo $row['sale_id'] ?>"> <?php echo $row['id'] ?></span></td>
	   		
	   		<td>
	   			<a target="_blank" title="AUTOLOGIN URL" href="<?php echo CHelperProfile::getAutoLoginUrl($profile->getDataValue('id'))//$profile->getUrlProfile(); ?>">
	   				<?php echo $row['username'] ?>
	   			</a>
  				
	   		</td>
	   		
		    <td>
		        <?php echo $row['email'] ?>
		        
		        <?php if ($row['email_bounced']) { ?>
		            <br />
		            <span style="color:red;">BOUNCED</span>
		        <?php } ?>
		        
		        <?php if ($row['stats_reversals_id']) { ?>
		            <br />
		            <span style="color:red;">REVERSED</span>
		        <?php } ?>        
		    </td>	   		
	   		
	   		
		    <td>
		    	<?php echo $row['affid'] ?>
				
				<?php if ($row['affid']!=1) { ?>
			        <br /><br />
			        <?php echo $row['agent_name'] ?>
			        <br />
			        <?php if ($row['agent_last_login_text']) { ?>
			            <br />
			            <sub>Last login: <?php echo $row['agent_last_login_text'] ?></sub>        
			            <br />                
			        <?php } ?>        
			        
			        <?php /*
			        {if $u.manager_id && $u.affid>100}
			            <br />
			            <a target="_blank" href="/ncadmin2/aff_ban_2.php?affid={$u.affid}">Ban</a>
			        {/if}
			        */ ?>
		        <?php } ?>
		    </td>	   		
	   		

	   		<td><?php echo $row['manager_id'] ?><br /><br /><?php echo $row['manager_name'] ?></td>


	   		<td><?php echo $row['master_id'] ?></td>

	   		
	   		<td><?php 
	   		
		   		$regDate = $profile->getDataValue('activity', 'joined');
		   		echo date("Y-m-d", strtotime($regDate));
		   		//echo date("Y-m-d", strtotime($row['regdate'])); 
	   		
	   		?>
	   		
	   		<br />
	   		<br />
	   		<span class="smaller"><?php echo $row['payment_dt'] ?></span>
	   		
	   		</td>


	   		<td><?php echo $row['pics'] ?></td>
	   		
	   		
	   		<td>$<?php echo $row['amount'] ?></td>
	   		
	   		<td><?php echo $row['paymod'] ?></td>
	   		
	   		<td><span title="updated <?php echo $row['last_update_lmw_text'] ?>"><?php echo $row['logins'] ?></span></td>
	   		
	   		<td><span title="updated <?php echo $row['last_update_lmw_text'] ?>"><?php echo $row['msgs'] ?></span></td>
	   		
	   		<td><span title="updated <?php echo $row['last_update_lmw_text'] ?>"><?php echo $row['winks'] ?></span></td>
	   		
	   		<td><?php echo $row['country'] ?> / <?php echo $row['region'] ?> / <?php echo $row['city'] ?></td>
	   		
	   		<td><?php echo $row['geo_ip_country'] ?></td>
	   		
	   		<td><?php echo $row['form'] ?></td>
	   		
	   		<td><?php echo $row['phone'] ?></td>
	   		
	   		
		    <td>
		        <?php if ($row['zom_LiabilityCode']) { ?>
		            <span style="color:red">
		                <?php if ($row['zom_LiabilityCode']==1) { ?>
		                1 - Merchant is liable for the chargeback
		                <?php } 
		                elseif ($row['zom_LiabilityCode']==2) { ?>
		                2 - Card Issuer is liable for the chargeback (3D Secure)
						<?php } 
		                elseif ($row['zom_LiabilityCode']==3) { ?>
		                3 - Zombaio is liable for the chargeback (Fraud Insurance)
		                <?php } ?>
		            </span>
		            <br /><br />
		        <?php } ?>
		        
		        <?php if ($row['billing_info']) { ?>
		            <span title="name on card, address, country/region/city/postal"><?php echo $row['billing_info'] ?></span>
		        <?php } ?>
		    </td>	   		
	   		
	   		
		    <td>
		        <?php if ($row['proxy']=='0') { ?>
		            -
		        <?php } 
		        elseif ($row['proxy']=='') { ?>
		            <sup>
		                not checked yet
		                <br />
		                <a target="_blank" href="http://www.winmxunlimited.net/utilities/api/proxy/check.php?ip=<?php echo $row['signupip'] ?>">Check manually</a>
		            </sup>
		        <?php } else { ?>
		            <span style="color:red;"><?php echo $row['proxy'] ?></span>
		        <?php } ?>
		    </td>	   		
	   		
		    <td>
		        <span <?php if ($row['proxy_maxmind']!='proxyScore=0.00') {?>style="color: red"<?php } ?>>
		        	<?php echo $row['proxy_maxmind'] ?>
		        </span>
		    </td>	   		
			   		
		    <td>
		        
		        <?php if ($row['pc_info_os']) { ?>
	         	
		         	<?php echo $row['pc_info_os'] . ', ' . $row['pc_info_browser'] . ' ' . $row['pc_info_browser_version'] . ', ' . $row['pc_info_screen_resolution']  ?>
		         	
		         	<?php if ($row['dublicate_sales_by_OS']) { ?>
		                <br />
		                <br />
		                <span style="color:red;" title="Duplicate sales OS/browser/browser version, manager_id">os/browser dupes:</span>
		                <br />
		                
		                <?php foreach ($row['dublicate_sales_by_OS'] as $k=>$oss) { ?>
		                    <a target="_blank" href="/admin/payments/todayGold/?sale=<?php echo $oss ?>"><?php echo $oss ?></a>
		                    <br />		                
		                <?php } ?>
                
		                <sub><a target="_blank" href="/admin/payments/todayGold/?sale=<?php echo $row['dublicate_sales_by_OS_str'] ?>">Show all</a></sub>
		            <?php } ?>
		                
		                
		                
		            <?php if ($row['dublicate_sales_by_OS_2']) { ?>
		                    <br />
		                    <br />
		                    <span style="color:red;" title="Duplicate sales manadger and resolution">screen resolution dupes:</span>
		                    <br />

			                <?php foreach ($row['dublicate_sales_by_OS_2'] as $oss) { ?>
			                    <a target="_blank" href="/admin/payments/todayGold/?sale=<?php echo $oss ?>"><?php echo $oss ?></a>
			                    <br />		                
			                <?php } ?>
		                    <sub><a target="_blank" href="/admin/payments/todayGold/?sale=<?php echo $row['dublicate_sales_by_OS_2_str'] ?>">Show all</a></sub>
		            <?php } ?>
		            
		        <?php } ?>
		        
		    </td>
		    

		    <td>
    
		        <?php if (!$row['ipFresh']) { ?>
		            fresh
		        <?php } else { ?>
		            <span style="color:red;">duplicates:</span>
		            <br />
		            <?php foreach ($row['ipFresh'] as $ipf) { ?>
		                <a target="_blank" href="/admin/payments/todayGold/?sale=<?php echo $ipf ?>"><?php echo $ipf ?></a>
		                <br />
		            <?php } ?>
		            <sub><a target="_blank" href="/admin/payments/todayGold/?sale=<?php echo $row['ipFresh_str'] ?>">Show all</a></sub>
		        <?php } ?>
		        <br />
		        <br />
		        Who is: <a href="http://whois.domaintools.com/<?php echo $row['signupip'] ?>" target="_blank"><?php echo $row['signupip'] ?></a>
		        <br />
		        Google:
		        <a title="Google search results for the IP" href="https://www.google.com/#sclient=psy-ab&hl=en&site=&source=hp&q=%22<?php echo $row['signupip'] ?>%22+proxy&pbx=1&oq=%22<?php echo $row['signupip'] ?>%22+proxy&aq=f&aqi=&aql=&gs_sm=e&gs_upl=18471l21134l0l21292l9l9l0l0l0l0l178l825l8.1l9l0&bav=on.2,or.r_gc.r_pw.r_cp.,cf.osb&fp=730251ab25f89bca&biw=1680&bih=882" target="_blank"><?php echo $row['signupip'] ?></a>
		        
		        <?php if ($row['dublicate_sales_by_IP_B']) { ?>
		            <br />
		            <br />
		            <span style="color:red;">Duplicate sales with ip range (class B):</span>
		            <br />
		            <?php foreach ($row['dublicate_sales_by_IP_B'] as $ips) { ?>
		                <a target="_blank" href="/admin/payments/todayGold/?sale=<?php echo $ips ?>"><?php echo $ips ?></a>
		                <br />
		            <?php } ?>		            
		            
		            <sub><a target="_blank" href="/admin/payments/todayGold/?sale=<?php echo $row['dublicate_sales_by_IP_B_str'] ?>">Show all</a></sub>
		        <?php } ?>
		
				<?php if ($row['dublicate_sales_by_IP']) { ?>
		            <br />
		            <br />
		            <span style="color:red;">Duplicate sales with ip range 123.123.123.0-255:</span>
		            <br />
		            <?php foreach ($row['dublicate_sales_by_IP'] as $ips) { ?>
		                <a target="_blank" href="/admin/payments/todayGold/?sale=<?php echo $ips ?>"><?php echo $ips ?></a>
		                <br />
		            <?php } ?>
		            		            
		            <sub><a target="_blank" href="/admin/payments/todayGold/?sale=<?php echo $row['dublicate_sales_by_IP_str'] ?>">Show all</a></sub>
		        <?php } ?>
		       
		    </td>    
    
    
    
    
    
    
    		<td <?php if ($row['status']!='active') { ?>style="color:red"<?php } ?> ><?php echo $row['status'] ?></td>
    
    	   		
	   		<td>
	   			<a href="<?php echo $profile->getDataValue('info', 'ref_url'); ?>" target="_blank">
	   				<?php echo $profile->getDataValue('info', 'ref_url'); ?>
	   			</a>
	   		</td>
	   		
	   		
		</tr>
	
	<?php } else { ?>
	
	<tr>
		<td colspan="22" nowrap="nowrap" style="padding:64px">
			Empty...
		</td>
	</tr>
  	
  	<?php } ?>
  	
</tbody>
</table>




<div style="margin-top: 60px">
	<h3>Helper for Maxmind Proxy output:</h3>
	<img src="/images/mmProxyOutput.png" />
</div>





