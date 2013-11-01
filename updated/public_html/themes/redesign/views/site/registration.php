<div class="regBox2 bgRadBox">
	
	<h1>Create your free profile</h1>
	
    <?php $this->widget('application.components.UserRegistrationFormWidget'); ?>
        
<?php /* 
        <h3 class="or-separator">or</h3>
        <?php  
        
        if (Yii::app()->user->isGuest) {
            
            if(Yii::app()->user->hasFlash('facebookError')) { ?>
                
                <div class="flash-error">    
                    <?php echo Yii::app()->user->getFlash('facebookError'); ?>
                </div>
            
            <?php 
            } else { 
                $this->widget('application.components.facebook.FacebookLoginWidget');
            }    
        } ?>
*/ ?>        
</div>
