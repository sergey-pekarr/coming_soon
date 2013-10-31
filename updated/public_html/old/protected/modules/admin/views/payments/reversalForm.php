<?php if ($success) { ?>
    <h3>Reversal form. Step 3</h3>
    
    <div class="alert alert-success">Success</div>
    <br />
    <a href="/admin/payments/reversalForm">Create new</a>
    
<?php } elseif (!$users) { ?>    
    
    <h3>Reversal form. Step 1 (search user)</h3>
    <div style="width:95%; padding:10px">
    <form method="get" id="form" >
    <table>
        <tr>
            <td><label for="user">Username/email/id: </label></td>
            <td><input name="user" type="text" name="user" style="width:200px" value="<?php echo (isset($_GET['user'])) ? $_GET['user'] : "" ?>"></td>
        </tr>
        
        <tr>
            <td></td>
            <td>OR</td>
        </tr>
        
        <tr>
            <td><label for="user">Payment</label></td>
            <td>
                <select id="payment" name="payment" onchange="javascript:payment_show()">
                    <option selected="selected">Select...</option>
                    <option value="zombaio">Zombaio</option>
                </select>
            </td>
        </tr>    
    </table>
    
    <table>
   
    <tr id="payment_zombaio" style="display:none">
        <td><label for="zombaio_sub_id">Zombaio SUBSCRIPTION id:</label></td>
        <td><input name="zombaio_sub_id" type="text" style="width:200px" value="<?php echo (isset($_GET['zombaio_sub_id'])) ? $_GET['zombaio_sub_id'] : "" ?>"></td>
    </tr>

    
    </table>
    <input type="submit" class="btn" value="Find" />
    
    
    
    
    
    </form>
    </div>
    
    
    <script type="text/javascript">
        function payment_show()
        {
            document.getElementById('payment_zombaio').style.display = 'none';
            
            var form = document.getElementById('form');
            var payment = form.payment.value;
            document.getElementById('payment_'+payment).style.display = 'block';
        }
    </script>


<?php } else { ?>

	<form id="form2" method="post">
	<input type="hidden" name="step2" value="step2" />
	<?php if ($users) { ?>
<h3>Step2. Users</h3>
<table class="table table-condensed albums">
<thead>
	    <th>&nbsp;</th>
	    <th>ID</th>
	    <th>Preview</th>
	    <th>Info</th>
	    <th>Email</th>
	    <th>Activity</th>
	    <th>Affid</th>
</thead>	    
<tbody>
	<?php foreach ($users as $k=>$s) { 
		$profile = new Profile($s['id']);
		$data = $profile->getData();
	?>
	    <tr>
		    <td>
		        <input type="radio" name="user_id[]" value="<?php echo $s['id'] ?>" <?php if ($k==0) { ?>checked="checked"<?php } ?> />
		    </td>
		    <td><?php echo $s['id'] ?></td>
			
			<td>
            	<?php $this->widget('application.modules.admin.components.user.UserPreviewWidget', array('userId'=>$s['id'])) ?>
            </td>                
		    
			<td>
            	<?php $this->widget('application.modules.admin.components.user.UserInfoShotWidget', array('userId'=>$s['id'])) ?>
            </td>		    

			<td>
            	<?php echo $data['email']; ?>
			</td>		    
		    
            <td>
                    <?php $activity = $data['activity'] ?>
                    Joined: <?php echo $activity['joined'] ?>
                    <br />
                    <?php if ( $activity['loginCount'] ) { ?>
                        
                        Last activity: 
                        <?php echo CHelperDate::date_distanceOfTimeInWords( strtotime($activity['activityLast']), time()).' ago'; ?>
                        <br />
                        Logins: <?php echo $activity['loginCount'] ?>,
                    <?php } ?>
                    
                    <?php 
                    $ext = $data['ext'];
                    if ($ext['facebook']) { ?>
                        <br />
                        Joined from <span class="label notice">facebook</span>
                    <?php } ?>
                    
                    
			</td>
                
		    <td><?php echo $data['affid'] ?></td>

	    </tr>	
	<?php } ?>
</tbody>
</table>	    


	    
	
	    <table style="margin-top:20px">
	        <tr>
	            <td>Date (YYYY-MM-DD)</td>
	            <td>
	                <input type="text" value="<?php echo $today ?>" name="date_real" maxlength="10" />
	            </td>
	        </tr>
	        
	        <tr>
	            <td>Type</td>
	            <td>
	                <select name="type">
	                    <?php foreach (HelperReversals::getModsForSelect('Select...') as $kkk=>$vvv ) { ?>
	                    	<option value="<?php echo $kkk ?>"><?php echo $vvv ?></option>	                    
	                    <?php } ?>
	                </select>
	            </td>
	        </tr>        
	        
	        <tr>
	            <td>Amount</td>
	            <td>
	                <input type="text" name="amount" value="0.00" />
	            </td>
	        </tr>        
	        
	        
	
	    </table>
	<input type="submit" value="Save" />
	
	
	
	
	<?php } ?>
	
	
	
	
	
	</form>


















<?php } ?>