<div id="profileLocation" class="form">

<img 
    width="550" 
    height="400" 
    src="http://maps.google.com/maps/api/staticmap?center=<?php echo $profile->getLocationValue('latitude').',',$profile->getLocationValue('longitude'); ?>&amp;zoom=6&amp;size=550x400&amp;maptype=roadmap&amp;sensor=false&amp;markers=color:blue|<?php echo $profile->getLocationValue('latitude').',',$profile->getLocationValue('longitude'); ?>" 
    id="googleMap" 
/>


<?php 
    
    $form=$this->beginWidget('CActiveFormSw', array(
    	'action'=>Yii::app()->createUrl('profile/location'),
            'id' => 'profile-location',
            'enableAjaxValidation' => true,
            'clientOptions' => array(
                'validateOnSubmit' => true,
                'validateOnChange' => true,
                'afterValidate' => "js: function(form, data, hasError) {
                                                    //if no error in validation, send form data with Ajax
                                                    
                                                    if (! hasError) {
                                                      $.ajax({
                                                        type: 'POST',
                                                        url: form[0].action,
                                                        data: $(form).serialize(),
                                                        success: function(ret) {
                                                            //$('#profileLocation').hide();
                                                            //$('#locationInfo').show();
                                                            //$('#locationAction').show();
                                                            locationInfoOld = $('#locationInfo').html();
                                                            googleOld = $('#googleMap').attr('src');
                                                            
                                                            
                                                            $('#locationEditTab .btn').button('success').addClass('success');
                                                            setTimeout(function() { $('#locationEditTab .btn').button('reset'); $('#locationEditTab *').removeClass('success'); }, 2000);                                                            
                                                            
                                                        }
                                                      });                                                        
                                                    }
                                                    else
                                                        $('#locationEditTab .btn').button('reset');                                                    
                }" 
            ),
    )); 
?>


    
		<?php echo $form->hiddenField($model,'location_id', array('value'=>$location_id)); ?>

<dl>

    <div class="row">
        <dt><?php echo $form->labelEx($model,'country'); ?></dt>
		<dd>
        <?php echo $form->dropDownList(
                        $model,
                        'country', 
                        Yii::app()->location->getCountriesList(), 
                        array(
                            //'style'=>'width:160px;',
                            'options' => array($location['country']=>array('selected'=>true)),
                            'onChange'=> 'javascript: countryProfileLocation = this.value; countryChange();',
                        )
                   ); ?>
		<?php //echo $form->error($model,'country'); ?>
        </dd>
    </div>
    
    <div class="row">
		<dt><?php echo $form->labelEx($model,'city'); ?></dt>        
        <dd>
        <?php echo $form->textField(
                        $model,
                        'city',
                        array(
                            'value'=>$cityAndStateName,
                            'style'=>'float:left;',
                            //'onchange'=>'$("#UserLocationForm_location_id").val("")',
                        )
                    ); ?>
        </dd>
		<?php echo $form->error($model,'city'); ?>
        <div class="clear"></div>
    </div>
    

    <div class="row">
    	<dt><?php echo $form->labelEx($model,'zip'); ?></dt>
        <dd>
        <?php echo $form->textField(
                            $model,
                            'zip',
                            array(
                                'value'=>$location['zip'], 
                                'style'=>'float:left;',
                            )
            );?>
        <span id="zipLoading">&nbsp;&nbsp;&nbsp;&nbsp;</span>
        </dd>
        <?php echo $form->error($model,'zip'); ?>
        <div class="clear"></div>        
    </div>
</dl>        
        
    <?php //echo $form->error($model,'reg2'); ?>

        <?php /* echo CHtml::submitButton('Ok', array('class' => 'button', 'id'=>'buttonOk')); ?>
        <a class="bottom" href="javascript:void(0)" 
            onclick="javascript:
                $('#googleMap').attr('src',googleOld);
                $('#locationInfo').html(locationInfoOld);
                $('#profileLocation').hide();
                $('#locationAction').show();
                $('#locationInfo').show();
            "
        >
            Cancel
        </a>
        */ ?>

	
<?php $this->endWidget(); ?>

<div class="submit">
    <button 
        class="btn" 
        data-loading-text='Saving...'
        data-success-text='Saved'
        onclick="javascript:formSubmit('locationEditTab');" 
    >Save</button>
</div>




<script type="text/javascript">
            
    var countryProfileLocation = <?php echo '"'.$location['country'].'"' ?>;
    var googleOld = "";
    var locationInfoOld = "";
    
    $(document).ready(function() 
    {
        googleOld = $("#googleMap").attr('src');
        locationInfoOld = $("#locationInfo").html();
        
        $( "input#UserLocationForm_city" ).autocomplete({
            autoFocus: true,
            delay: 1000,
            source: function(request, response){
                $.ajax({
                    url: "/ajax/sityfind/",
                    type: "POST",
                    dataType: "json",
                    data:{
                        city: request.term, 
                        country: countryProfileLocation
                    },
                    beforeSend: function() {
                        $("input#UserLocationForm_city").addClass('ac_loading');
                        $("#UserLocationForm_zip").attr('disabled', 'disabled');
                        $("#UserLocationForm_zip").val('');
                    },
                    complete: function() {
                        //$("input#UserRegistrationStep2Form_city").val('');
                        $("#UserLocationForm_location_id").val('');
                        $("input#UserLocationForm_city").removeClass('ac_loading');
                    },
                    success: function(data){
                        // приведем полученные данные к необходимому формату и передадим в предоставленную функцию response
                        response($.map(data, function(item){
                            
                            var locInfo = "";
                            locInfo = item.city;
                            if (item.state)
                            {
                                locInfo += ", " + item.state;
                            }
                            
                            return{
                                //label: item.city + ", " + item.state,
                                value: locInfo,//item.city + ", " + item.state,
                                id:    item.id,
                                latitude: item.latitude,
                                longitude: item.longitude
                            }
                        }));
                    }
                });
            },	            
            
            //Определяем обработчик селектора
            select: function(e, ui) {
                $("#UserLocationForm_location_id").val(ui['item']['id']);
                $("#UserLocationForm_city").attr('title',  ui['item']['value']);
                
                
                var src = 'http://maps.google.com/maps/api/staticmap?center='+ui['item']['latitude']+','+ui['item']['longitude']+'&zoom=6&size=550x400&maptype=roadmap&sensor=false&markers=color:blue|'+ui['item']['latitude']+','+ui['item']['longitude'];
                $("#googleMap").attr(
                    'src', 
                    src
                );
                
                $("#locationInfo").html(ui['item']['value'] + ", " + countryProfileLocation);
                
                findZip(ui['item']['id']);
                
            }, 
            
            minLength: 3
        });
    })
    
    function countryChange()
    {
        $("input#UserLocationForm_city").val("").focus(); 
        $("#buttonOk").hide();
        
        $("#UserLocationForm_zip").attr('disabled', 'disabled');
        $("#UserLocationForm_zip").val('');        
    }
    
    
    function findZip(locationId)
    {
        $.post(
            '/ajax/ZipFind', 
            {locationId:locationId}, 
            function(data) {
                $("#zipLoading").removeClass('loadingAjax16');
                $("#UserLocationForm_zip").removeAttr('disabled');
                        
                $("#UserLocationForm_zip").val(data);
                
                //$("#buttonOk").show();
            },
            "json"
        );
    }    
    
</script>


</div>