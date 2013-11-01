<?php 
FB::info($profile, 'profile');
    
    $lists = Yii::app()->helperProfile->getPersonalValueList(); //$model->getPersonalValueList();
    foreach ($lists as $k=>$v)
    {
        $keys[] = $k;        
    }
    
    $data = $profile->getData();
FB::warn($data);    
?>
<dl>
    
    <?php if ($data['personal']['description']) { ?>
    <dl class="descr">
        <span class="titleDesc">About me</span>
        <br />
        <?php echo $data['personal']['description'] ?>
    </dl>
    <?php }
    
    
    if ($profile->getLocationValue('city')) {?>
    <dl>
        <dt>Location</dt>        
        <dd><?php echo CHelperProfile::showProfileInfoSimple($profile, 10) ?></dd>
    </dl>
    <?php 
    }
    
/*
    if ($profile->getLocationValue('country')) {?>
    <dl>
        <dt>Country</dt>        
        <dd><?php echo $profile->getLocationValue('country') ?></dd>
    </dl>
    <?php 
    }

    if ($profile->getLocationValue('stateName')) {?>
    <dl>
        <dt>State/Region</dt>        
        <dd><?php echo CHelperProfile::truncStr($profile->getLocationValue('stateName'), 25)  ?></dd>
    </dl>
    <?php 
    }

    if ($profile->getLocationValue('city')) {?>
    <dl>
        <dt>City</dt>        
        <dd><?php echo CHelperProfile::truncStr($profile->getLocationValue('city'), 25) ?></dd>
    </dl>
    <?php 
    } 
    
    /* 
    <dl>
        <dt>Age</dt>        
        <dd><?php echo $profile->getDataValue('age') ?></dd>
    </dl>

    <dl>
        <dt>Gender</dt>        
        <dd><?php echo CHelperProfile::textGender($data['gender']) ?></dd>
    </dl>     
*/ ?>    
    <?php
    /*
    $lookGender = CHelperProfile::getLookGender($data['id'], true);
    if ($lookGender) {
    ?>    
    <dl>
        <dt>Looking for</dt>        
        <dd><?php echo $lookGender ?></dd>
    </dl>     
    <?php
    }*/ ?>
    

    <dl>
        <dt>Here for</dt>        
        <dd><?php 
            echo CHelperProfile::textInteresting( $profile->textInteresting(), 2 ) ?>            
        </dd>
    </dl>  
    
    <?php 
/*           
    if ($data['personal']['height']) { ?>
    <dl>
        <dt>Height</dt>        
        <dd><?php echo $lists['height'][$data['personal']['height']] ?></dd>
    </dl>
     
    <?php } ?>
    

     
    <?php if ($data['personal']['bodyType']) { ?>     
    <dl>
        <dt>Body Type</dt>        
        <dd><?php echo $lists['bodyType'][$data['personal']['bodyType']] ?></dd>
    </dl>
     
    <?php 
    }
    
    if ($data['personal']['religion']) { ?>
    <dl>
        <dt>Religion</dt>        
        <dd><?php echo $lists['religion'][$data['personal']['religion']] ?></dd>
    </dl>
     
    <?php 
    }
    
    if ($data['personal']['race']) { ?>
    <dl>
        <dt>Ethnicity</dt>        
        <dd><?php echo $lists['religion'][$data['personal']['race']] ?></dd>
    </dl>
     
    <?php 
    }
    
    if ($data['personal']['smoker']) { ?>
    <dl>
        <dt>Smoker</dt>        
        <dd><?php echo $lists['religion'][$data['personal']['smoker']] ?></dd>
    </dl>
     
    <?php 
    }
    
    if ($data['personal']['drink']) { ?>
    <dl>
        <dt>Drinks</dt>        
        <dd><?php echo $lists['religion'][$data['personal']['drink']] ?></dd>
    </dl>
     
    <?php 
    } 
*/
    ?>
  
</dl>
