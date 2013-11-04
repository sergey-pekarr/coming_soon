<div id="guest-top">
    
    <?php $this->widget('application.components.UserLoginFormWidget'); ?>
    
</div>

<div class="clear"></div>

<div id="guest-left-side" class="left-side">
    <?php $this->widget('application.components.panel.ProfilesNearFrontWidget', array('b'=>$b)); ?>
</div>

<div id="guest-right-side" class="right-side">

<?php /*    
    <h2 class="wolds1video">Video Profiles. Video Messaging. Completely Free.</h2>
    <div class="startvideo">&nbsp;</div>
    <h2 class="startcity">
    <?php 
        $city = Yii::app()->user->location('city');
        //$state = Yii::app()->user->location('stateName');
        //$country = Yii::app()->user->location('country');
                
        echo ($city) ? $city : 'Your area';
        //echo ($state) ? ', '.$state : '';
        //echo ($country) ? ', '.$country : ''; 
    ?>  
    </h2>  
*/ ?>
    
    <div class="signupBox">
        
        <h2>Join now. 100% free. <span>(And awesome)</span></h2>
        
        <?php $this->widget('application.components.UserRegistrationFormWidget'); ?>
        
        <h3 class="or-separator">or</h3>
<?php  

if(Yii::app()->user->isGuest) {
    
    if(Yii::app()->user->hasFlash('facebookError')) { ?>
        
        <div class="flash-error">    
            <?php echo Yii::app()->user->getFlash('facebookError'); ?>
        </div>
    
    <?php 
    } else { 
        $this->widget('application.components.facebook.FacebookLoginWidget');
    }    
} ?>        
        
        <p class="FB_recomended">
            (recommended)
        </p>
        
        <?php /* 
        <p class="noFB">
            <a class="color3" href="javascript:void(0)" onclick="javascript:signpExpand()">No facebook? Sign up using your email</a>
        </p>
        */ ?>
        
        
        
        
    </div>
    

    <h2 class="already_member">
        Already a member? 
        <a class="color3" href="javascript:void(0)" onclick="javascript:loginFromShow();" >Sign in here</a>
    </h2>

    

<?php /*    
    <div class="signup_wrap">
        <div class="signup_wrap_top"></div>            
        <div class="signup_content">
            <div class="signup_content_l">
            
                <div class="signup-header">
                    <div class="get-laid clean">Start Dating Now...</div>
                    <p>Meet Singles In Your Area</p>
                </div>
                
                <?php $this->widget('application.components.UserRegistrationFormWidget'); ?>
            </div>
        </div>
        <div class="signup_wrap_bottom"></div>
    </div>
*/ ?>        
        <?php //$this->widget('application.components.PanelOnlineNowGuestWidget'); ?>
</div>        
<div class="clear"></div> 

<?php /*        
<?php 
$quotes = array(
    array('The YouTube of online dating',                   'The Boston Globe'),
    array('The most efficient method at meeting new people','TIME'),
    array('Powerful in its simplicity',                     'Dating industry insider'),
    array('The future of online dating',                    'DateJolene'),
);
$quoteIndx = rand(0,3);
?>

<?php if (!DEMO) { ?>
<p class="quotes">
    <span class="ldquo">&ldquo;</span><?php echo $quotes[$quoteIndx][0] ?><span class="rdquo">&rdquo;</span> <span class="quoted">&mdash; <?php echo $quotes[$quoteIndx][1] ?></span>
</p>
<?php } ?>
*/ ?>

