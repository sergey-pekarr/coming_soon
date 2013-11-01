<div id="reg_step2_form_box" class="form">

<?php 

	$urlAfter = '/';

    $form=$this->beginWidget('CActiveFormSw', array(
            'action'=>Yii::app()->createUrl('/UserRegistration/Step2'),
            'id' => 'reg_step2_form',
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
                                                            window.location.href='".$urlAfter."';
                                                        }
                                                      });
                                                    }
                                                    else
                                                    {
                                                        $('#reg_step2_form_box .btn').button('reset');
                                                        $('input.submit').removeAttr('disabled');
                                                    }
                                                    
                                                    
                                                    return false;
                }" 
            ),
    )); 
?>
<dl class="step2left">



	<div class="row username">
		<dt><?php echo $form->labelEx($model,'username'); ?></dt>
		<dd>
        <?php echo $form->textField(
                            $model,
                            'username',
                            array('value'=>Yii::app()->user->data('username'))
                    );?>
        </dd>
		<?php echo $form->error($model,'username'); ?>
        <div class="clear"></div>
	</div>
	
	<div class="row birthday" >
		<dt><?php echo $form->labelEx($model,'birthday'); ?></dt>
        
        <dd>
        <?php echo $form->hiddenField($model,'birthday'); ?>
        
        <?php 
        	echo $form->dropDownList($model,'birthday[day]', Yii::app()->helperProfile->getDays(false));    
            echo $form->dropDownList($model,'birthday[month]', Yii::app()->helperProfile->getMonth(false, false));
            echo $form->dropDownList($model,'birthday[year]', Yii::app()->helperProfile->getYear(false));             
        ?>
        </dd>
        <?php echo $form->error($model,'birthday'); ?>
        <div class="clear"></div>
	</div>	
	
<?php /*    
    <div class="row">
        <dt><?php echo $form->labelEx($model,'gender'); ?></dt>
        <dd>
            <?php echo $form->dropDownList($model,'gender', array('M'=>'Male','F'=>'Female')); ?>
        </dd>
        <?php echo $form->error($model,'gender'); ?>
        <div class="clear"></div>
    </div>

    <div class="row">
        <dt><?php echo $form->labelEx($model,'status'); ?></dt>
        <dd>
            <?php echo $form->dropDownList($model,'status', array('single'=>"Single", 'complicated'=>"It's Complicated", 'dating'=>"Dating", 'married'=>"Married")); ?>
        </dd>
        <?php echo $form->error($model,'status'); ?>
        <div class="clear"></div>
    </div>

    <div class="row interesting">
        <dt><?php echo $form->labelEx($model,'interesting'); ?></dt>
        <dd>
            <?php echo $form->hiddenField($model,'interesting'); ?>
            <?php //echo $form->dropDownList($model,'interesting', array('friends'=>"Friends", 'flirting_dating'=>"Flirting, Dating", 'networking'=>"Networking")); ?>
            <?php echo $form->checkBoxList(
                        $model, 
                        'interesting', 
                        CHelperProfile::getPersonalInteresting(),
                        array('separator'=>'<br />')
            ); ?>
        </dd>
        <?php echo $form->error($model,'interesting'); ?>
        <div class="clear"></div>
    </div>    

    <div class="row">
        <dt><?php echo $form->labelEx($model,'height'); ?></dt>
        <dd>
            <?php 
            $lists = Yii::app()->helperProfile->getPersonalValueList();
            echo $form->dropDownList($model,'height', $lists['height']); ?>
        </dd>
        <?php echo $form->error($model,'height'); ?>
        <div class="clear"></div>
    </div>  
    
    <div class="row description">
        <dt style="width: 340px;">
            <label>
                Describe what you're about in less than 255 characters. Make it count!
            </label>
        </dt>
        <?php echo $form->error($model,'description'); ?>
        
        <?php //echo $form->labelEx($model,'description'); ?>
        <br />
        <?php echo CHtml::activeTextArea($model, 'description'); ?>
        
        <div class="clear"></div>
    </div> 
</dl>

<dl class="step2right">


    <img 
        width="400" 
        height="310" 
        src="http://maps.google.com/maps/api/staticmap?center=<?php echo $location['latitude'].',',$location['longitude']; ?>&amp;zoom=6&amp;size=400x310&amp;maptype=roadmap&amp;sensor=false&amp;markers=color:blue|<?php echo $location['latitude'].',',$location['longitude']; ?>" 
        id="googleMap" 
    />
*/ ?>            

	<div class="row location">
		<?php echo $form->hiddenField($model,'location_id', array( 'value'=>$location_id )); ?>
        
		<dt><?php echo $form->labelEx($model,'country'); ?></dt>
		<dd>
            <?php echo $form->dropDownList(
                        $model,
                        'country', 
                        Yii::app()->location->getCountriesList(), 
                        array(
                            'options' => array($location['country']=>array('selected'=>true)),
                            'onChange'=> 'javascript: countryRegStep2 = this.value; $("input#UserRegistrationStep2Form_city").val("").focus()',
                            //'onChange'=> 'javascript:updateStates(this.value)',
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
	
	
	<div class="row notifyMe">
		<dt><?php echo $form->labelEx($model,'notifyMe'); ?></dt>
        <dd><?php echo $form->checkBox($model,'notifyMe'); ?></dd>        
		<?php echo $form->error($model,'notifyMe'); ?>
        <div class="clear"></div>
	</div>	
	
</dl>

<div class="clear"></div>

<div class="center">
<?php 
    //echo CHtml::submitButton('Continue >>', array('id' => 'continueStep2', 'onclick'=>'javascript:$(this).hide(); $("#continueStep2Loading").show();'));
    /*echo CHtml::submitButton(
        'Continue >>', 
        array(
            'class' => 'btn success',
            'data-loading-text'=>'Continue...',
        )
    );
 
    <script type="text/javascript">
        $(function() {
            var btn = $('#reg_step2_form_box .btn').click(function () {
                btn.button('loading');
            })
        })     
    </script>
*/?>

<?php /* <div id="continueStep2Loading" class="loadingAjax16" style="margin: 0 auto; display: none;"></div> */ ?>
</div>


<?php $this->endWidget(); ?>

<div class="center">
    <?php /* ?><button class="btn success" data-loading-text="wait..." onclick="javascript:formSubmit('reg_step2_form_box');" >Continue >></button><?php */ ?>
    <input type="button" class="submit" onclick="javascript: $(this).attr('disabled', 'disabled'); formSubmit('reg_step2_form_box');" />
    
    <p class="reg2notice">
		By submitting this form you certify you are 18 years or older, you agree to our
		<a onclick="window.open(this.href,'','width=400,height=500,scrollbars=yes,toolbar=no'); return false;" href="/site/page/terms">terms &amp; conditions</a>
		, and understand that this site is for adult entertainment purposes and agree to the use and nature of virtual cupids and our
		<a onclick="window.open(this.href,'','width=400,height=500,scrollbars=yes,toolbar=no'); return false;" href="/site/page/privacy">privacy policy</a>
		.
    </p>
    
    
</div>

<script type="text/javascript">
            
    var countryRegStep2 = <?php echo '"'.$location['country'].'"' ?>;

    $(document).ready(function() 
    {
        //$("#reg_step2_form_box").modal({backdrop:'static'}).modal('show');
        
        
        //$("#UserRegistrationStep2Form_looking_for_gender_1").attr('checked', 'checked');
        
        
        $( "input#UserRegistrationStep2Form_city" ).autocomplete({
            autoFocus: true,
            delay: 1000,
            source: function(request, response){
                $.ajax({
                    url: "/ajax/sityfind/",
                    type: "POST",
                    dataType: "json",
                    data:{
                        city: request.term, 
                        country: countryRegStep2
                    },
                    beforeSend: function() {
                        //$("input#UserRegistrationStep2Form_city").addClass('ac_loading');
                        $("#cityLoading").show();// addClass('loadingAjax16');
                        $("#zipLoading").addClass('loadingAjax16');
                        $("#UserRegistrationStep2Form_zip").attr('disabled', 'disabled');
                        $("#UserRegistrationStep2Form_zip").val('');
                    },
                    complete: function() {
                        //$("input#UserRegistrationStep2Form_city").val('');
                        $("#UserRegistrationStep2Form_location_id").val('');
                        //$("input#UserRegistrationStep2Form_city").removeClass('ac_loading');
                        $("#cityLoading").hide();//removeClass('loadingAjax16');

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

                var src = 'http://maps.google.com/maps/api/staticmap?center='+ui['item']['latitude']+','+ui['item']['longitude']+'&zoom=6&size=400x310&maptype=roadmap&sensor=false&markers=color:blue|'+ui['item']['latitude']+','+ui['item']['longitude'];
                $("#googleMap").attr(
                    'src', 
                    src
                );
                
                $(".location.city").removeClass('error');
                $(".location.city .errorMessage").hide();
                
                findZip(ui['item']['id']);
                
                $("#UserRegistrationStep2Form_location_id").val(ui['item']['id']);
                $("#UserRegistrationStep2Form_city").attr('title',  ui['item']['value']);
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
                $("#UserRegistrationStep2Form_zip").removeAttr('disabled');
                        
                $("#UserRegistrationStep2Form_zip").val(data);
            },
            "json"
        );
    }
    
</script>
</div>