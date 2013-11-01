<style type="text/css">
    div.inline h3, div.inline span{
        display:inline-block;
    }
    div.inline h3{
        font-size: 23px;
    }
    div.inline span a{
        padding-left: 20px;
        font-size: 14px;
        color: blue;
        font-weight: bold;
    }

    span.login-form-text{
        display: inline-block;
        vertical-align: middle;
        font-size: 16px;
        font-weight: bold;
    }

    div.username{
        text-align: center;
        width: 150px;
        font-size: 16px;
    }

    #reg-forms-box{
        padding-top: 30px;
        padding-bottom: 30px;
        background-color: #f5f5f5;
        border-top: solid #b3b3b3 1px;
        border-left: solid #b3b3b3 1px;
        border-right: solid #b3b3b3 2px;
        border-bottom: solid #b3b3b3 2px;
    }

    div.facebook-login-area{
        text-align: center;
    }

    .word_devider {
        padding-top: 30px;
        padding-bottom: 30px;
        text-align: center;
        margin: 5px 0;
        position: relative;
        z-index: 2;
        text-transform: uppercase;
    }
    
    .line_center {
        font-size: small;
        color: #D1CFCD;
        margin: 0;
        padding: 0 10px;
        background: #f5f5f5;
        display: inline-block;
    }

     .word_devider:after {
        content: "";
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        border-top: solid 1px #D1CFCD;
        z-index: -1;
    }


</style>
<!-- <div class="container"></div> -->
<div class="row-fluid"><br>
    <div class="span7">
        <div class="inline">
            <h3>Welcome back, Roy</h3>
            <span><a href="#">Not Roy?</a></span><br>
        </div>
        <?php $this->widget('application.components.UserLoginFormWidget'); ?>                   
    </div>
    <div class="span4">
        <br>
        <span class="login-form-text">
            The communication marcketplace for people who are anything but average
        </span>
    </div>    
</div>
<br><hr><br><br>
<!-- <div class="container"> -->
    <div class="row-fluid">
        <!-- USERS NEAR YOUR PLACE WIDGET -->
        <div class="span7">
            <?php $this->widget('application.components.UsersNearYourPlace', array('users'=>$users, 'city'=>$city)); ?>
        </div>

        <!-- REGISTRATION FORM -->
        <div id="reg-forms-box" class="span5">
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