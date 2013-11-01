<?php 
$data = $profile->getData();
$dataPayment = $profile->getPayment();
?>


<h2>
	Edit user 
	&nbsp;&nbsp;&nbsp;&nbsp;
	
	<span class="userNameInfo">		
		<?php echo $data['username'] ?>
	</span>	
	
	&nbsp;&nbsp;
		
	<span title="level" class="label smaller role_user-<?php echo $profile->getDataValue('role') ?>">
		<?php echo $profile->getDataValue('role') ?>
	</span>
	
	&nbsp;&nbsp;    	

	<?php if ($data['promo']) { ?>
    	<span title="level" class="label user-promo smaller">promo</span>
    <?php } else { ?>
    	
    	&nbsp;&nbsp;
    	<span class="smaller">    	
    	Affid: <?php echo $data['affid'] ?>
    	&nbsp;&nbsp;
    	Form: <?php echo $data['form'] ?>
    	</span>
    <?php } ?>
    	
</h2>

<ul class="nav nav-tabs">

    <li class="active"><a href="#mainEditTab" data-toggle="tab" onclick="javascript:$('#userBox').show();">Main</a></li>
	
	<?php if ($dataPayment || $trnExists || $data['role']=='gold') { ?>
	<li><a href="#paymentInfoTab" data-toggle="tab" onclick="javascript:$('#userBox').hide();">Payments/Transactions</a></li>
	<?php } ?>

<?php /* ?>
    <li><a href="#appearanceEditTab" data-toggle="tab" onclick="javascript:$('#userBox').show()">Appearance</a></li>
    <li><a href="#lifestyleEditTab" data-toggle="tab" onclick="javascript:$('#userBox').show()">Lifestyle</a></li>
<?php */ ?>
    
    <li><a href="#locationEditTab" data-toggle="tab" onclick="javascript:$('#userBox').show();">Location</a></li>

</ul>


<div id="userBox">
	
	<div class="center">
		<?php
		$images = $data['image'];
		
		if ($images)
		{
			foreach ($images as $i=>$row) { ?>
				<img class="img-small" src="<?php echo $profile->imgUrl('small', $i, false) ?>" />
			<?php }
		} ?>	
	</div>

    <div class="box_rs1">
		
		<?php $this->widget('application.modules.admin.components.user.UserPreviewWidget', array('userId'=>$data['id'], 'size'=>'big')) ?>    
		
		
        <div class="userInfoShot">
            <?php 
                if (!$profile->getDataValue('promo')) 
                    echo CHelperProfile::showProfileInfoSimple($profile, 1); 
            ?>
        </div>

    </div>
    
    <div class="userActions">
        
        
    <?php 
    if ( $profile->getDataValue('promo')=='0' ) { ?>

    	<?php if ($dataPayment) { ?>
    		<a class="btn" href="/admin/payments/reversalForm?user=<?php echo $data['id'] ?>">Reversal</a>
    	<?php } ?>
    	
    	<?php if ($data['role']=='free') { ?>
    		<a class="btn" style="color:#AB8401" href="javascript:usersRoleChange(<?php echo $data['id'] ?>, 'gold')">Make GOLD 30 days</a>
    	<?php } ?>

    	<?php if ($data['role']=='gold') { ?>
    		<a class="btn" style="color:green" href="javascript:usersRoleChange(<?php echo $data['id'] ?>, 'free')">Make free</a>
    	<?php } ?>
    	
    	<br /><br />

        <button  
            id="userBanBtn" 
            class="btn btn-danger"
			onclick="javascript:usersRoleChange(<?php echo $data['id'] ?>, 'banned')"
            <?php if ($data['role']=='banned' || $data['role']=='deleted') { ?>style="display: none;"<?php } ?>
        >Ban</button>
        
        
        <button 
            id="userUnBanBtn" 
            class="btn btn-success"  
            onclick="javascript:usersRoleChange(<?php echo $data['id'] ?>, 'free')"
            <?php if ($data['role']!='banned') { ?>style="display: none;"<?php } ?>
        >UnBan</button>
		
<?php /*        
        <a class="btn" href="#" data-controls-modal="sendMessageBox" data-backdrop="static" >Send Message</a>
        <?php $this->widget('application.modules.admin.components.forms.SendFormWidget', array('profile'=>$profile)); ?>
*/ ?>        
    <?php } else { ?>
        <span style="color: red;">Promo user</span>
    <?php } ?>
    
    
    <div class="center" style="margin-top: 20px">
        <button 
            id="userBanBtn" 
            class="btn btn-danger"  
			onclick="javascript:usersRoleChange(<?php echo $data['id'] ?>, 'deleted')"
            <?php if ($data['role']=='deleted' || $data['role']=='banned') { ?>style="display: none;"<?php } ?>
        >Delete</button>    
    </div>
	
<?php /*?>	
		<div id="editlocation" style="margin-top: 10px; padding-left: 50px; display: none">
		<table>
			<tr>
				<td style="">Country
				</td>
				<td>
					<select id="country" name="country" style="width: 140px;" onchange="countrychanged();">
						<option value="AF">Afghanistan</option>
						<option value="AL">Albania</option>
						<option value="DZ">Algeria</option>
						<option value="AS">American Samoa</option>
						<option value="AD">Andorra</option>
						<option value="AO">Angola</option>
						<option value="AI">Anguilla</option>
						<option value="AQ">Antarctica</option>
						<option value="AG">Antigua and Barbuda</option>
						<option value="AR">Argentina</option>
						<option value="AM">Armenia</option>
						<option value="AW">Aruba</option>
						<option value="AU">Australia</option>
						<option value="AT">Austria</option>
						<option value="AZ">Azerbaijan</option>
						<option value="BH">Bahrain</option>
						<option value="BD">Bangladesh</option>
						<option value="BB">Barbados</option>
						<option value="BY">Belarus</option>
						<option value="BE">Belgium</option>
						<option value="BZ">Belize</option>
						<option value="BJ">Benin</option>
						<option value="BM">Bermuda</option>
						<option value="BT">Bhutan</option>
						<option value="BO">Bolivia</option>
						<option value="BA">Bosnia and Herzegovina</option>
						<option value="BW">Botswana</option>
						<option value="BV">Bouvet Island</option>
						<option value="BR">Brazil</option>
						<option value="IO">British Indian Ocean Territory</option>
						<option value="VG">British Virgin Islands</option>
						<option value="BN">Brunei</option>
						<option value="BG">Bulgaria</option>
						<option value="BF">Burkina Faso</option>
						<option value="BI">Burundi</option>
						<option value="KH">Cambodia</option>
						<option value="CM">Cameroon</option>
						<option value="CA">Canada</option>
						<option value="CV">Cape Verde</option>
						<option value="KY">Cayman Islands</option>
						<option value="CF">Central African Republic</option>
						<option value="TD">Chad</option>
						<option value="CL">Chile</option>
						<option value="CN">China</option>
						<option value="CX">Christmas Island</option>
						<option value="CC">Cocos (Keeling) Islands</option>
						<option value="CO">Colombia</option>
						<option value="KM">Comoros</option>
						<option value="CG">Congo (Brazzaville)</option>
						<option value="CD">Congo (Kinshasa)</option>
						<option value="CK">Cook Islands</option>
						<option value="CR">Costa Rica</option>
						<option value="CI">Cote D'Ivoire</option>
						<option value="HR">Croatia</option>
						<option value="CU">Cuba</option>
						<option value="CY">Cyprus</option>
						<option value="CZ">Czech Republic</option>
						<option value="DK">Denmark</option>
						<option value="DJ">Djibouti</option>
						<option value="DM">Dominica</option>
						<option value="DO">Dominican Republic</option>
						<option value="TL">East Timor</option>
						<option value="EC">Ecuador</option>
						<option value="EG">Egypt</option>
						<option value="SV">El Salvador</option>
						<option value="GQ">Equatorial Guinea</option>
						<option value="ER">Eritrea</option>
						<option value="EE">Estonia</option>
						<option value="ET">Ethiopia</option>
						<option value="FK">Falkland Islands (Islas Malvinas)</option>
						<option value="FO">Faroe Islands</option>
						<option value="FJ">Fiji</option>
						<option value="FI">Finland</option>
						<option value="FR">France</option>
						<option value="GF">French Guiana</option>
						<option value="PF">French Polynesia</option>
						<option value="TF">French Southern and Antarctic Lands</option>
						<option value="GA">Gabon</option>
						<option value="GE">Georgia</option>
						<option value="DE">Germany</option>
						<option value="GH">Ghana</option>
						<option value="GI">Gibraltar</option>
						<option value="GR">Greece</option>
						<option value="GL">Greenland</option>
						<option value="GD">Grenada</option>
						<option value="GP">Guadeloupe</option>
						<option value="GU">Guam</option>
						<option value="GT">Guatemala</option>
						<option value="GN">Guinea</option>
						<option value="GW">Guinea-Bissau</option>
						<option value="GY">Guyana</option>
						<option value="HT">Haiti</option>
						<option value="HM">Heard Island and McDonald Islands</option>
						<option value="VA">Holy See (Vatican City)</option>
						<option value="HN">Honduras</option>
						<option value="HK">Hong Kong</option>
						<option value="HU">Hungary</option>
						<option value="IS">Iceland</option>
						<option value="IN">India</option>
						<option value="ID">Indonesia</option>
						<option value="IR">Iran</option>
						<option value="IQ">Iraq</option>
						<option value="IE">Ireland</option>
						<option value="IL">Israel</option>
						<option value="IT">Italy</option>
						<option value="JM">Jamaica</option>
						<option value="SJ">Svalbard</option>
						<option value="JP">Japan</option>
						<option value="JO">Jordan</option>
						<option value="KZ">Kazakhstan</option>
						<option value="KE">Kenya</option>
						<option value="KI">Kiribati</option>
						<option value="KW">Kuwait</option>
						<option value="KG">Kyrgyzstan</option>
						<option value="LA">Laos</option>
						<option value="LV">Latvia</option>
						<option value="LB">Lebanon</option>
						<option value="LS">Lesotho</option>
						<option value="LR">Liberia</option>
						<option value="LY">Libya</option>
						<option value="LI">Liechtenstein</option>
						<option value="LT">Lithuania</option>
						<option value="LU">Luxembourg</option>
						<option value="MO">Macau</option>
						<option value="MK">Macedonia</option>
						<option value="MG">Madagascar</option>
						<option value="MW">Malawi</option>
						<option value="MY">Malaysia</option>
						<option value="MV">Maldives</option>
						<option value="ML">Mali</option>
						<option value="MT">Malta</option>
						<option value="MH">Marshall Islands</option>
						<option value="MQ">Martinique</option>
						<option value="MR">Mauritania</option>
						<option value="MU">Mauritius</option>
						<option value="YT">Mayotte</option>
						<option value="MX">Mexico</option>
						<option value="FM">Micronesia</option>
						<option value="MD">Moldova</option>
						<option value="MC">Monaco</option>
						<option value="MN">Mongolia</option>
						<option value="ME">Montenegro</option>
						<option value="MS">Montserrat</option>
						<option value="MA">Morocco</option>
						<option value="MZ">Mozambique</option>
						<option value="MM">Myanmar (Burma)</option>
						<option value="NA">Namibia</option>
						<option value="NR">Nauru</option>
						<option value="NP">Nepal</option>
						<option value="NL">Netherlands</option>
						<option value="AN">Netherlands Antilles</option>
						<option value="NC">New Caledonia</option>
						<option value="NZ">New Zealand</option>
						<option value="NI">Nicaragua</option>
						<option value="NE">Niger</option>
						<option value="NG">Nigeria</option>
						<option value="NU">Niue</option>
						<option value="NF">Norfolk Island</option>
						<option value="KP">North Korea</option>
						<option value="MP">Northern Mariana Islands</option>
						<option value="NO">Norway</option>
						<option value="OM">Oman</option>
						<option value="PK">Pakistan</option>
						<option value="PW">Palau</option>
						<option value="PA">Panama</option>
						<option value="PG">Papua New Guinea</option>
						<option value="PY">Paraguay</option>
						<option value="PE">Peru</option>
						<option value="PH">Philippines</option>
						<option value="PN">Pitcairn Islands</option>
						<option value="PL">Poland</option>
						<option value="PT">Portugal</option>
						<option value="PR">Puerto Rico</option>
						<option value="QA">Qatar</option>
						<option value="RE">Reunion</option>
						<option value="RO">Romania</option>
						<option value="RU">Russia</option>
						<option value="RW">Rwanda</option>
						<option value="SH">Saint Helena</option>
						<option value="KN">Saint Kitts and Nevis</option>
						<option value="LC">Saint Lucia</option>
						<option value="PM">Saint Pierre and Miquelon</option>
						<option value="VC">Saint Vincent and the Grenadines</option>
						<option value="WS">Samoa</option>
						<option value="SM">San Marino</option>
						<option value="ST">Sao Tome and Principe</option>
						<option value="SA">Saudi Arabia</option>
						<option value="SN">Senegal</option>
						<option value="RS">Serbia</option>
						<option value="SC">Seychelles</option>
						<option value="SL">Sierra Leone</option>
						<option value="SG">Singapore</option>
						<option value="SK">Slovakia</option>
						<option value="SI">Slovenia</option>
						<option value="SB">Solomon Islands</option>
						<option value="SO">Somalia</option>
						<option value="ZA">South Africa</option>
						<option value="GS">South Georgia and the South Sandwich Islands</option>
						<option value="KR">South Korea</option>
						<option value="ES">Spain</option>
						<option value="LK">Sri Lanka</option>
						<option value="SD">Sudan</option>
						<option value="SR">Suriname</option>
						<option value="SZ">Swaziland</option>
						<option value="SE">Sweden</option>
						<option value="CH">Switzerland</option>
						<option value="SY">Syria</option>
						<option value="TW">Taiwan</option>
						<option value="TJ">Tajikistan</option>
						<option value="TZ">Tanzania</option>
						<option value="TH">Thailand</option>
						<option value="BS">The Bahamas</option>
						<option value="GM">The Gambia</option>
						<option value="TG">Togo</option>
						<option value="TK">Tokelau</option>
						<option value="TO">Tonga</option>
						<option value="TT">Trinidad and Tobago</option>
						<option value="TN">Tunisia</option>
						<option value="TR">Turkey</option>
						<option value="TM">Turkmenistan</option>
						<option value="TC">Turks and Caicos Islands</option>
						<option value="TV">Tuvalu</option>
						<option value="UG">Uganda</option>
						<option value="UA">Ukraine</option>
						<option value="AE">United Arab Emirates</option>
						<option value="GB">United Kingdom</option>
						<option selected="selected" value="US">United States</option>
						<option value="UY">Uruguay</option>
						<option value="UZ">Uzbekistan</option>
						<option value="VU">Vanuatu</option>
						<option value="VE">Venezuela</option>
						<option value="VN">Vietnam</option>
						<option value="VI">Virgin Islands</option>
						<option value="WF">Wallis and Futuna</option>
						<option value="EH">Western Sahara</option>
						<option value="YE">Yemen</option>
						<option value="ZM">Zambia</option>
						<option value="ZW">Zimbabwe</option>
					</select>
				</td>
                <td></td>
			</tr>
			<tr>
				<td>City</td>
				<td>
                    <input id='city' name="city" style="width: 130px;" />
                    
                    <input  id='locationcode' name="locationcode"  type="hidden" />
                </td>
                <td><img style="display: inline;" id="validate_location" src="/images/img/blank.gif"></td>
			</tr>
			<tr>
				<td>Zip</td>
				<td><input id='zipcode' disabled="disabled" name="zipcode" style="width: 130px;" />
                </td>
                <td id='zipcode-validate'></td>
			</tr>
			<tr>
				<td></td>
				<td><input type="button" value="Save" onclick="saveLocation();" /></td>
			</tr>
		</table>
		<script>
		    function saveLocation() {
		        if ($('#zipcode').val() == '') {
		            $('#zipcode-validate').html('<span style="color:red;">* Required</span>');
		            return;
		        }
		        else {
		            $('#zipcode-validate').html('');
		        }
				var url = '/admin/users/editlocationtab';
				$.post(url, { 'user_id': $('#UserEditMainTabForm_user_id').val(),
				    'country': $('#country').val(),
				    'city': $('#city').val(),
				    'zipcode': $('#zipcode').val(),
				    'locationcode': $('#locationcode').val()
				}, function () {
				},
                        "json")
				.success(function (data) {
				    if (!data.country) return;
				    //country, city, state, state as stateName, latitude, longitude, locationcode, zipcode
				    $('#locationtext').html(data.country + ', ' + data.stateName + ', ' + data.city + ', ' + data.zipcode);
				    $('#googleMap').attr('src', 'http://maps.google.com/maps/api/staticmap?center='
                    + data.latitude + ',' + data.longitude
                    + '&zoom=6&size=550x400&maptype=roadmap&sensor=false&markers=color:blue|' 
                    + data.latitude + ',' + data.longitude);
				})
				.fail(function () {
				});
            }

            function countrychanged() {
                $("#zipcode").val('');
                $("#zipcode").attr('disabled', 'disabled');
                $("#city").val('');
                $('#validate_location').attr('src', '/images/img/blank.gif');
            }

			function findZip(locationId) {
			    $.post('/ajax/ZipFind',
                        { locationId: locationId },
                        function (data) {
                            if (data == null || data == '') {
                                $("#zipcode").removeAttr('disabled');
                            }
                            else {
                                $("#zipcode").attr('disabled', 'disabled');
                            }
                            $("#zipcode").val(data);
                        },
                        "json"
                    );
                    };

                    $(document).ready(function () {

                        $('#country').val("<?php echo $profile->getLocationValue('country'); ?>");
                        $('#city').val("<?php echo $profile->getLocationValue('city'); ?>");
                        $('#zipcode').val("<?php echo $profile->getLocationValue('zip'); ?>");

                        $("input#city").autocomplete({
                            autoFocus: true,
                            delay: 500,
                            source: function (request, response) {
                                $.ajax({
                                    url: "/ajax/sityfind/",
                                    type: "POST",
                                    dataType: "json",
                                    data: {
                                        city: request.term,
                                        country: $('#country').val()
                                    },
                                    beforeSend: function () {
                                        $("#locationcode").val('');
                                        $('#validate_location').attr('src', '/image/design/loading.gif');
                                        $('#validate_location').css('display', 'inline');
                                    },
                                    complete: function () {
                                        $("#locationcode").val('');
                                        $('#validate_location').css('display', 'none');

                                    },
                                    success: function (data) {
                                        response($.map(data, function (item) {
                                            return {
                                                value: item.city + ", " + item.state,
                                                id: item.id,
                                                latitude: item.latitude,
                                                longitude: item.longitude
                                            }
                                        }));
                                    }
                                });
                            },

                            select: function (e, ui) {
                                findZip(ui['item']['id']);
                                $("#locationcode").val(ui['item']['id']);
                                $("#city").attr('title', ui['item']['value']);
                                $('#validate_location').css('display', 'inline');
                                $('#validate_location').attr('src', '/ldrsc/img/ico_check_green.gif');
                            },

                            minLength: 3
                        });
                    });
		</script>
		</div>
*/ ?>    	
    </div>
    
</div>


<div class="tab-content" id="userEdit">
    
    
    <!-- MAIN -->
    <div class="tab-pane active" id="mainEditTab">
        <?php $this->widget('application.modules.admin.components.forms.UserEditMainTabFormWidget', array('id'=>$data['id'])) ?>
    </div>
    
    
    <!-- Payments -->
    <?php if ($dataPayment || $trnExists || $data['role']=='gold') { ?>
    <div class="tab-pane" id="paymentInfoTab">
        <?php $this->widget('application.modules.admin.components.user.UserViewPaymentTabWidget', array('id'=>$data['id'])) ?>
    </div>    
    <?php } ?>

    
    <!-- LOCATION -->
    <div class="tab-pane" id="locationEditTab">
        <?php $this->widget('application.modules.admin.components.user.UserViewLocationTabWidget', array('id'=>$data['id'])) ?>
    </div>
    
</div>

<div class="clear"></div>



<script>
$(document).ready(function(){
	// Javascript to enable link to tab
	var url = document.location.toString();
	if (url.match('#')) {
	    $('.nav-tabs a[href=#'+url.split('#')[1]+']').tab('show') ;

		if (url.split('#')[1]=='paymentInfoTab')
			$('#userBox').hide();
		else
			$('#userBox').show();
	} 

	// Change hash for page-reload
	$('.nav-tabs a').on('shown', function (e) {
	    window.location.hash = e.target.hash;
	})
});
</script>

