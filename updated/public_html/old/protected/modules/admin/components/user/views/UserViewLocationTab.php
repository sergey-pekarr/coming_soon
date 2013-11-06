        <img 
            width="550" 
            height="400" 
            src="http://maps.google.com/maps/api/staticmap?center=<?php echo $location['latitude'].',',$location['longitude']; ?>&amp;zoom=6&amp;size=550x400&amp;maptype=roadmap&amp;sensor=false&amp;markers=color:blue|<?php echo $location['latitude'].',',$location['longitude']; ?>" 
            id="googleMap" 
        />
        
        <div id="locationtext" style="text-align: center; font-weight: bold; padding-top: 10px;">
        <?php
            echo $location['country'].', '.$location['stateName'].', '.$location['city'].', '.$location['zip'];
        ?>
        </div>

        
        
        
        
        
        
        
<div id="AdminUserLocationFormBox" class="form">

<?php 


    $form=$this->beginWidget('CActiveFormSw', array(
            'action'=>Yii::app()->createUrl('/admin/users/editLocationTab'),
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
                                                            window.location.reload();
                                                        }
                                                      });
                                                    }
                                                    else
                                                    {
                                                        
                                                    }
                                                    $('#AdminUserLocationFormBox .btn').button('reset');
                                                    
                                                    return false;
                }" 
            ),
    )); 
?>
<dl>

	<div class="row location">
		<?php echo $form->hiddenField($model,'location_id', array( 'value'=>$location_id )); ?>
		<?php echo $form->hiddenField($model,'user_id', array( 'value'=>$this->id )); ?>
        
		<dt><?php echo $form->labelEx($model,'country'); ?></dt>
		<dd>
            <?php echo $form->dropDownList(
                        $model,
                        'country', 
                        Yii::app()->location->getCountriesList(), 
                        array(
                            'options' => array($location['country']=>array('selected'=>true)),
                            'onChange'=> 'javascript: countryAdminUserEdit = this.value; $("input#AdminUserLocationForm_city").val("").focus()',
                        )
                   ); ?>
        </dd>
		<?php echo $form->error($model,'country'); ?>
        <div class="clear"></div>
    </div>
    <div class="row location city">    
		<dt><?php echo $form->labelEx($model,'city'); ?></dt>
        <dd>
		<?php echo $form->textField(
                        $model,
                        'city',
                        array(
                            'value'=>$location['city'].', '.$location['stateName'],
                            //'class'=>'ac_loading',
                            //'onKeyUp'=> 'javascript: ',
                        )
                    ); ?>
        <div id="cityLoading"></div>
        </dd>
		<?php echo $form->error($model,'city'); ?>
        <div class="clear"></div>
	</div>
    
	<div class="row zip">
		<dt><?php echo $form->labelEx($model,'zip'); ?></dt>
        <dd>	
            <?php echo $form->textField(
                            $model,
                            'zip',
                            array(
                                'value'=>$location['zip'],
                            )
            );?>
            <span id="zipLoading">&nbsp;&nbsp;&nbsp;&nbsp;</span>
        </dd>        
		<?php echo $form->error($model,'zip'); ?>
        <div class="clear"></div>
	</div> 
	
</dl>

<div class="clear"></div>


<?php $this->endWidget(); ?>

<div class="center">
    <input type="button" class="btn hide" onclick="javascript:formSubmit('AdminUserLocationFormBox');" value="Update" />
</div>

<script type="text/javascript">
            
    var countryAdminUserEdit = <?php echo '"'.$location['country'].'"' ?>;

    $(document).ready(function() 
    {
        $( "input#AdminUserLocationForm_city" ).autocomplete({
            autoFocus: true,
            delay: 1000,
            source: function(request, response){
                $.ajax({
                    url: "/ajax/sityfind/",
                    type: "POST",
                    dataType: "json",
                    data:{
                        city: request.term, 
                        country: countryAdminUserEdit
                    },
                    beforeSend: function() {
                        $("#cityLoading").show();
                        $("#zipLoading").addClass('loadingAjax16');
                        $("#AdminUserLocationForm_zip").attr('disabled', 'disabled');
                        $("#AdminUserLocationForm_zip").val('');
                    },
                    complete: function() {
                        $("#AdminUserLocationForm_location_id").val('');
                        $("#cityLoading").hide();
                        $('#AdminUserLocationFormBox .btn').show();
                    },
                    success: function(data){

                        response($.map(data, function(item){
                            return{
                                //label: item.city + ", " + item.state,
                                value: item.city + ", " + item.state,
                                id:    item.id,
                                latitude: item.latitude,
                                longitude: item.longitude
                            }
                        }));
                    }
                });
            },	            
            

            select: function(e, ui) {

                var src = 'http://maps.google.com/maps/api/staticmap?center='+ui['item']['latitude']+','+ui['item']['longitude']+'&zoom=6&size=550x400&maptype=roadmap&sensor=false&markers=color:blue|'+ui['item']['latitude']+','+ui['item']['longitude'];
                $("#googleMap").attr(
                    'src', 
                    src
                );
                
                $(".location.city").removeClass('error');
                $(".location.city .errorMessage").hide();
                
                findZip(ui['item']['id']);
                
                $("#AdminUserLocationForm_location_id").val(ui['item']['id']);
                $("#AdminUserLocationForm_city").attr('title',  ui['item']['value']);
            }, 
            
            minLength: 3
        });
    })
    
    
    
    
    function findZip(locationId)
    {
        $.post(
            '/ajax/ZipFind', 
            {locationId:locationId}, 
            function(data) {
                $("#zipLoading").removeClass('loadingAjax16');
                $("#AdminUserLocationForm_zip").removeAttr('disabled');
                        
                $("#AdminUserLocationForm_zip").val(data);
            },
            "json"
        );
    }
    
</script>
</div>        