<?php

FB::info($profile->getDataValue('id'), 'PROFILE ID');

?>

<div class="box_rs1" style="padding: 14px 0;">

<div class="top">
    <?php /*
    <div class="short-info">
        
            <?php $this->widget('application.components.UserNameFormWidget'); ?>
            <span title="Click to edit" id="user-username" class="name info-username">
                <?php echo $profile->getDataValue('username') ?>
            </span>
        
        
        
        <span id="user-age"><?php echo $profile->getDataValue('age'); ?></span>,
        <span>
            <?php
                echo Profile::getGenderName( $profile->getDataValue('gender') );
            ?>
        </span>
        
        <?php if ($profile->getDataValue('role')=='administrator') { ?>
            <span class="administrator"></span>
        <?php } ?>
    </div>
    */ ?>
    
    
    <div class="general-info">
        <div class="primary">
            <?php //$this->widget('application.components.userprofile.UserPersonalFormWidget'); ?>
            
            <ul class="tabs">
            
                <li class="active"><a href="#mainEditTab">Main</a></li>
                
                <?php /*
                <li><a href="#appearanceEditTab">Appearance</a></li>
                */ ?>
            
                <li><a href="#lifestyleEditTab">Lifestyle</a></li>
                
                <li><a href="#locationEditTab">Location</a></li>
                
                <li><a href="#badgetsEditTab">Social Networks</a></li>
            
            </ul>            


            <div class="pill-content" id="userEdit">
                
                
                <!-- MAIN -->
                <div class="active" id="mainEditTab">
                    <?php $this->widget('application.components.userprofile.UserEditMainTabFormWidget') ?>
                </div>
                
                <?php /*
                <!-- Appearance -->
                <div id="appearanceEditTab">
                    <?php $this->widget('application.components.userprofile.UserEditAppearanceTabFormWidget') ?>
                </div>
                */ ?>
                
                <!-- SETTINGS -->
                <div id="lifestyleEditTab">
                    <?php //$this->widget('application.modules.admin.components.forms.UserEditLifestyleTabFormWidget', array('id'=>$_GET['id'])) ?>
                </div>

                <!-- LOCATION -->
                <div id="locationEditTab"  class="location">
                    <?php $this->widget('application.components.userprofile.UserLocationFormWidget'); ?>
                </div>
                
                <!-- BADGETS -->
                <div id="badgetsEditTab"  class="location">
                    <?php $this->widget('application.components.userprofile.UserEditBadgetsWidget'); ?>
                </div>                
                
            </div>
            
            <div class="clear"></div>
            
        </div>
        
        <?php /* 
        <div class="location">
            
            <img 
                width="300" 
                height="310" 
                src="http://maps.google.com/maps/api/staticmap?center=<?php echo $profile->getLocationValue('latitude').',',$profile->getLocationValue('longitude'); ?>&amp;zoom=6&amp;size=300x310&amp;maptype=roadmap&amp;sensor=false&amp;markers=color:blue|<?php echo $profile->getLocationValue('latitude').',',$profile->getLocationValue('longitude'); ?>" 
                id="googleMap" 
            />
           
            <div class="edit">                
                <span id="locationInfo">
                    <?php 
                        echo $profile->getLocationValue('city');
                        if ($profile->getLocationValue('stateName'))
                        {
                            echo ', '.$profile->getLocationValue('stateName');
                        }
                        echo ', '.$profile->getLocationValue('country');
                        //echo ', '.$profile->getLocationValue('zip'); 
                    ?>
                </span>
                    &nbsp;
                    <a class="toggleCityBox" id="locationAction" href="javascript:void(0)" 
                        onclick="javascript:$('#locationInfo').hide();$('#locationAction').hide();$('#profileLocation').show();"
                    >
                        (Change)
                    </a>
                    <?php $this->widget('application.components.UserLocationFormWidget'); ?>
            </div>    
        </div> 
        */?>
    
    </div>
    
</div>

 
<?php /*

<div class="middle">
    <div class="left">
        <?php 
//        if ($edit)
//            $this->widget('application.components.userprofile.UserSettUpdWidget');
//        else
//            $this->widget('application.components.userprofile.UserMessUpdWidget', array('profile'=>$profile));    
        ?>
    </div>
    
    <?php 
//    if ($edit)
///        $this->widget('application.components.UserPersonalFormWidget');
//    else
//        $this->widget('application.components.UserPersonalShowWidget', array('profile'=>$profile)); 
    ?>
    
    <div class="cls"></div>    
    
</div>

*/ ?>

<div class="bottom"></div>

</div>