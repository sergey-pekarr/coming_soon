<!-- <div class="container"></div> -->
<div class="row">
    <div class="col-md-7">
        <div class="inline">
            <h3>Welcome back, Roy</h3>
            <span><a class="wrong-user" href="#">Not Roy?</a></span><br>
        </div>
        <?php $this->widget('application.components.UserLoginFormWidget'); ?>                   
    </div>
    <div class="col-md-4 col-md-offset-1 login-form-wrapper">
        <span class="login-form-text">
            The communication marcketplace for people who are anything but average
        </span>
    </div>    
</div>
<br><hr><br><br>
<!-- <div class="container"> -->
    <div class="row">
        <!-- USERS NEAR YOUR PLACE WIDGET -->
        <div class="col-md-7">
            <?php $this->widget('application.components.UsersNearYourPlace', array('users'=>$users, 'city'=>$city)); ?>
        </div>

        <!-- REGISTRATION FORM -->
        <div class="guest-right"><!-- dirty leftovers from previous yii-based cms --></div>
        <div id="reg-forms-box" class="col-md-4 col-md-offset-1">
                <?php 
                if ( Yii::app()->user->isGuest ) { ?>
                    <h1>Sign up here to get started</h1>
                    <?php $this->widget('application.components.UserRegistrationFormWidget'); ?>
                <?php } else {
                    $this->widget('application.components.UserRegistrationStep2FormWidget');
                } ?>

                <p class="word_devider"><span class="line_center">OR</span></p>

                <div class="facebook-login-area">
                    <?php $this->widget('application.components.facebook.FacebookLoginWidget'); ?>
                    <h3>Recommended</h3>
                </div>
        </div>
    </div>
<!-- </div> -->