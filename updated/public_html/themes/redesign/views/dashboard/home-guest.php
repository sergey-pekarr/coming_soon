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
</style>
<div class="container"></div>
<div class="row-fluid">
    <div class="span6">
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
<hr>
<div class="container">
    <div class="row-fluid">
        <div class="span6">
            <h3>Members near your place (<?php echo $city; ?>)</h3>
            <div class="users">
                <ul class="profiles front">
                    <?php if(!empty($users)) : ?>
                        <?php foreach ($users as $user) : ?>
                            <?php
                                if (empty($user->images[0]->n)) {                    
                                    $male = ($user->gender === 'M') ? 'male' : 'female';    
                                    $img = '/../../images/nophoto_' . $male . '_big.png';
                                } else {
                                    $img = $user->id . '/' . $user->images[0]->n[0] . '_big.jpg';
                                }
                            ?>
                            <li>
                                <div class='user_profile'>
                                    <img src="<?php echo $img; ?>" />
                                    <span class="username"><?php echo $user->username; ?></span>
                                    <div class="clear"></div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <!-- REGISTRATION FORM -->
        <div class="span6">
            <div id="reg-forms-box">    
                <?php 
                if ( Yii::app()->user->isGuest ) { ?>
                    <h1>Sign up here to get started</h1>
                    <?php $this->widget('application.components.UserRegistrationFormWidget'); ?>
                <?php } else {
                    $this->widget('application.components.UserRegistrationStep2FormWidget');
                } ?>

                <?php $this->widget('application.components.facebook.FacebookLoginWidget'); ?>
            </div>
        </div>
    </div>
</div>