<div id="guest-top">
    <h2>Welcome! New here?</h2>
    <h3>
        
<?php /* 
	Swoonr is a fun video based community for making new friends, or may be more ..
        <br />
        Next level dating, Completely free. Meet you inside!
*/ ?>
    Video profiles make connecting with people easier than ever.
    <br />
    Discover new friends, flirt, network and have fun. Completely free.


    </h3>
    <div class="clear"></div>
</div>

<div id="guest-left-side" class="left-side">

    <?php $this->widget('application.components.panel.ProfilesNearTestPlayersWidget'); ?>

    <?php /*
        <form method="get" action="/search/">
            <button id="view-more-profiles-guest" class="white" type="submit">View More</button>
        </form>
    */ ?>       

</div>

<div id="guest-right-side" class="right-side">
    
    <div class="signupBox">
        <h2>Join <span style="text-decoration: underline;">now</span> using Facebook</h2>

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
}
    

/*if(Yii::app()->user->isGuest)
    $this->widget('application.extensions.facebook.FbLogin',
    array(
        'devappid'=>FB_APPID,   //your appilaction id
        'devsecret'=>FB_SECRET, //your application secret
        'cookie'=>FB_COOKIE,   
    ));*/
?>
<?php /* <a class="fb-button" href="#" ><div></div></a> */ ?>        
        
        <p class="FB_recomended">
            (recommended)
        </p>
        
        <p class="noFB">
            <a class="color3" href="javascript:void(0)" onclick="javascript:signpExpand()">No facebook? Sign up using your email</a>
        </p>
        
        <h3 class="or-separator">or</h3>
        
        <?php $this->widget('application.components.UserRegistrationFormWidget'); ?>
    </div>
    
    <h2 class="already_member">
    Already a member? 
    <a class="color3" href="#" data-controls-modal="reg_login_form" data-backdrop="static" >Sign In Here</a>
    <?php /* <a class="color3" href="javascript:void(0)" onclick="javascript:dialogRegLogin()">Sign In Here</a> */?>
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
        
        


