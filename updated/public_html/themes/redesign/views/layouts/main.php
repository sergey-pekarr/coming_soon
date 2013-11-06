<?php
$compressJs = (LOCAL) ? false : true;
$compressCss = (LOCAL) ? false : true;

$combineJs = (LOCAL) ? false : true;
$combineCss = (LOCAL) ? false : true;

$app = Yii::app();
?>
<!DOCTYPE html>
<html lang="en" xml:lang="en" xmlns:fb="http://ogp.me/ns/fb#" >
    <head>
        <meta charset="utf-8" />
        <meta name="language" content="en" />


        <?php CHelperAssets::jsUrl('site', $compressJs, $combineJs); ?>

        <link rel="shortcut icon" href="/favicon.ico" />

        <title><?php echo CHtml::encode($this->pageTitle); ?></title>

        <?php CHelperAssets::cssUrl('site', $compressCss, $combineCss); ?>
        <link href="/fonts/fonts.css" rel="stylesheet" type="text/css" />

        <?php $this->widget('application.components.userprofile.ScreenResolutionUpdateWidget'); ?>

        <?php $this->widget('application.components.metric.GoogleAnalyticsWidget'); ?>

        <?php
        if (Yii::app()->browser->isIE10()) {
            CHelperAssets::jsUrl('ie10', $compressJs, $combineJs);
        }
        ?>

    </head>
    <body>

        <?php if (Yii::app()->user->role == 'free') { //if ( Yii::app()->user->checkAccess('free') && !Yii::app()->user->checkAccess('gold')) {  ?>
            <div class="verify-banner regular ">
                <p> Don't forget... Upgrade NOW and SAVE an instant 50% on your Premium membership...</p>
                <p><a href="/payment" title="Unlock"> CLICK HERE</a> to meet thousands of like-minded singles near <?php echo Yii::app()->user->location('city') ?>!</p>
            </div>    	
        <?php } ?>


        <div class = "header">
            <div class = "topbar">
                <div class = "fill">
                    <div class = "container">
                        <h3>
                            <a id = "logoMain" href = "/" title = "<?php echo SITE_NAME_FULL ?>" rel = "popover">
                                <img src = "/images/design/logo.png" alt = "<?php echo SITE_NAME_FULL ?>" />
                            </a>
                            <?php /* <span class="socailVideo">social tube</span> */
                            ?>
                        </h3>

                        <?php
                        if (!Yii::app()->user->checkAccess('limited')) {//if ( Yii::app()->user->isGuest ) {
                            if (DEMO) {
                                die('demo content');
                                $this->widget('zii.widgets.CMenu', array(
                                    'htmlOptions' => array('class' => 'nav nav-pills secondary-nav'),
                                    'items' => array(
                                        array(
                                            'label' => 'SIGN IN',
                                            'url' => 'javascript:void(0)',
                                            'linkOptions' => array(
                                                'onclick' => 'javascript:signin()',
                                            ),
                                        //<a href="#" data-controls-modal="reg_login_form" data-backdrop="static" >Sign in</a>
                                        ),
                                        array(
                                            'label' => 'ABOUT',
                                            'url' => array('/site/index'),
                                            'active' => false,
                                        ),
                                        /* array(
                                          'label'=>'Contact Us',
                                          'url'=>array('/site/index'),
                                          'active'=>false ,
                                          ), */
                                        array(
                                            'label' => 'HELP',
                                            'url' => array('/site/index'),
                                            'active' => false,
                                            'itemOptions' => array('class' => 'last'),
                                        ),
                                    ),
                                ));
                            } else {
                                
                                $this->widget('zii.widgets.CMenu', array(
                                    'htmlOptions' => array('class' => 'nav nav-pills secondary-nav'),
                                    'items' => array(

                                        array(
                                            'label' => 'SIGN IN',
                                            'url' => 'javascript:void(0)',
                                            'visible' => Yii::app()->user->isGuest,
                                            'linkOptions' => array(
                                                'onclick' => 'javascript:signin()',
                                            ),
                                            'active' => (Yii::app()->request->requestUri == '/site/signin') ? true : false,

                                        ),

                                        array(
                                            'label' => 'ABOUT',
                                            'url' => array('/site/about'),
                                            'active' => (Yii::app()->request->requestUri == '/site/about') ? true : false,
                                        ),

                                        array(
                                            'label' => 'HELP',
                                            'url' => array('/help'),
                                            'active' => (Yii::app()->request->requestUri == '/help') ? true : false,
                                            'itemOptions' => Yii::app()->user->isGuest ? array('class' => 'last') : array(),
                                        ),
                                        array(
                                            'label' => 'SIGN OUT',
                                            'url' => array('site/logout'),
                                            'visible' => !Yii::app()->user->isGuest,
                                            'itemOptions' => array('class' => 'last'),
                                        ),
                                    ),
                                ));
                            }
                        } else {

                            if (Yii::app()->user->checkAccess('limited') && Yii::app()->user->settings('hided_new_message') == '0') {
                                $modelMessage = new Messages;
                                $messages = $modelMessage->getPrivateMessagesTo(Yii::app()->user->id);
                                $messagesNewCount = $messages['newCount'];
                            }
                            else
                                $messagesNewCount = 0;

                            $this->widget('zii.widgets.CMenu', array(
                                'htmlOptions' => array('class' => 'nav secondary-nav'),
                                'items' => array(
                                    array(//new message
                                        'label' => '#',
                                        'url' => array('/messages/index'),
                                        'visible' => ($messagesNewCount ? true : false),
                                    //'active'=>false,
                                    ),
                                    array(
                                        'label' => 'Dashboard',
                                        'url' => array('/site/index'),
                                        'linkOptions' => array('class' => 'dropdown-toggle no_arrow'),
                                        'itemOptions' => array('class' => 'dropdown', 'data-dropdown' => 'dropdown'),
                                        'submenuOptions' => array('class' => 'dropdown-menu'),
                                        'active' => ( (Yii::app()->controller->id == 'site') || (Yii::app()->controller->id == 'dashboard') /* || (Yii::app()->controller->id=='messages') */ ) ? true : false,
                                        'items' => array(
                                            array(
                                                'label' => 'Inbox',
                                                'url' => array('/dashboard/inbox'),
                                                'active' => ( Yii::app()->controller->action->id == 'inbox' || Yii::app()->controller->action->id == 'inboxAll' ) ? true : false,
                                            ),
                                            array('label' => 'Sent', 'url' => array('/dashboard/sent')),
                                            array('label' => 'Hotlist', 'url' => array('/dashboard/hotlist')),
                                        //array('label'=>'Matches', 'url'=>array('/dashboard/matches')),
                                        )
                                    ),
                                    array(
                                        'label' => 'My Profile',
                                        'url' => array('/profile/myVideos'),
                                        'linkOptions' => array('class' => 'dropdown-toggle'),
                                        'itemOptions' => array('class' => 'dropdown', 'data-dropdown' => 'dropdown'),
                                        'submenuOptions' => array('class' => 'dropdown-menu'),
                                        'active' => (
                                        (Yii::app()->controller->id == 'profile' && !isset($_GET['id'])) ||
                                        (Yii::app()->controller->id == 'profile' && Yii::app()->secur->decryptID($_GET['id']) == $app->user->id)
                                        ) ? true : false,
                                        'items' => array(
                                            array('label' => 'View Profile', 'url' => array('/profile/' . $app->secur->encryptID($app->user->id)), 'active' => (Yii::app()->secur->decryptID($_GET['id']) == $app->user->id) ? true : false),
                                            array('label' => 'My Video', 'url' => array('/profile/myVideos')),
                                            array('label' => 'Edit Profile', 'url' => array('/profile/edit')),
                                            array('label' => 'Account Settings', 'url' => array('/profile/accountSettings')),
                                        )
                                    ),
                                    array(
                                        'label' => 'Browse',
                                        'url' => array('/search'),
                                        'active' => (Yii::app()->request->requestUri == '/search') ? true : false,
                                    ),
                                    /* array(
                                      'label'=>'FAQ',
                                      'url'=>array('/site/index'),
                                      'active'=>false ,
                                      ), */
                                    array(
                                        'label' => 'Help',
                                        'url' => array('/help'),
                                        'active' => (Yii::app()->request->requestUri == '/help') ? true : false,
                                    ),
                                    array(
                                        'label' => 'Sign Out',
                                        'url' => array('site/logout')
                                    ),
                                ),
                            ));
                        }
                        ?>

                    </div><!-- /container -->
                </div> <!-- /fill -->
            </div><!-- /topbar -->    
        </div>    





        <div class="clear"></div>

        <noscript>
        <div class="alert-message error bold center">
            Javascript is disabled on your browser. Please enable JavaScript or upgrade to a Javascript-capable browser to use the site.
        </div>
        </noscript>



        <?php echo $content; ?>




        <?php $this->widget('application.components.common.ClientInfoUpdateWidget'); ?>

        <?php $this->widget('application.components.panamus.FingerprintWidget'); ?>

    </body>
</html>


<?php
CHelperSite::showTimeDebug();
?>
