<?php

class SiteController extends Controller {

    /**
     * Declares class-based actions.
     */
    public function actions() {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'CViewAction',
                'layout' => '//layouts/static2',
            ),
        );
    }

    public function actionPage() {
        $action = new CStaticViewAction($this, 'page');
        $action->layout = '//layouts/static2';
        $this->runAction($action);
    }

    public function actionSnoopy() {
        $this->layout = '//layouts/member';
        $this->render('snoopy');
    }

    public function actionSupport() {
        $this->layout = '//layouts/member';
        $this->render('support');
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex() {

        $this->layout = '//layouts/guest';
        // renders the view file 'protected/views/site/index.php'
        // using the default layout 'protected/views/layouts/main.php'		

        $this->pageTitle = SITE_NAME;

        if (Yii::app()->user->checkAccess('limited')) {
            $this->render('index'); //$this->render('index-trial');
        } else {
            $this->render('index-guest');
        }
    }

    public function actionLandingpage1() {
        $this->layout = '//layouts/guest1';
        // renders the view file 'protected/views/site/index.php'
        // using the default layout 'protected/views/layouts/main.php'		

        $this->pageTitle = SITE_NAME;

        if (Yii::app()->user->checkAccess('limited')) {
            $this->redirect('dashboard/inbox');
            //$this->render('index-trial');
        } else {
            $this->render('index-guest');
        }
    }

    public function actionBSide() {
        $this->layout = '//layouts/guest1';
        // renders the view file 'protected/views/site/index.php'
        // using the default layout 'protected/views/layouts/main.php'		

        $this->pageTitle = SITE_NAME;

        if (Yii::app()->user->checkAccess('limited')) {
            $this->redirect('dashboard/inbox');
            //$this->render('index-trial');
        } else {
            $this->render('index-guest', array('b' => true));
        }
    }

    public function actionTestPlayers() {
        $this->layout = '//layouts/guest1';
        // renders the view file 'protected/views/site/index.php'
        // using the default layout 'protected/views/layouts/main.php'		

        $this->render('index-testplayers');
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError() {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    /**
     * custom error messages
     */
    public function actionErrors() {
        //$this->layout = '//layouts/errors';
        if (CAMS)
            $this->layout = '//layouts/landingcams';
        $this->render('errors');
    }

    /**
     * Displays the contact page
     */
    /* public function actionContact()
      {
      $model=new ContactForm;
      if(isset($_POST['ContactForm']))
      {
      $model->attributes=$_POST['ContactForm'];
      if($model->validate())
      {
      $headers="From: {$model->email}\r\nReply-To: {$model->email}";
      mail(Yii::app()->params['adminEmail'],$model->subject,$model->body,$headers);
      Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
      $this->refresh();
      }
      }
      $this->render('contact',array('model'=>$model));
      } */

    /**
     * Displays the login page
     */
    public function actionLogin() {
//FB::warn($_POST, 'actionLogin');

        if (!Yii::app()->user->isGuest)
            $this->redirect(Yii::app()->homeUrl);


        $service = Yii::app()->request->getQuery('service');
        if (isset($service)) {
            $authIdentity = Yii::app()->eauth->getIdentity($service);
            $authIdentity->redirectUrl = Yii::app()->user->returnUrl;
            $authIdentity->cancelUrl = $this->createAbsoluteUrl('site/login');

            if ($authIdentity->authenticate()) {
//CHelperSite::vd($authIdentity->attributes);

                $profile = new Profile();
                $userId = $profile->getUserIdService($authIdentity);


                $identity = new UserIdentity('', '');
                $identity->authenticateByUserId($userId);

                $error = $identity->errorCode;
                if ($error === UserIdentity::ERROR_NONE) {
                    $duration = 3600 * 24 * 365; // 365 days
                    Yii::app()->user->login($identity/* ,$duration */);

                    // Специальный редирект с закрытием popup окна
                    $authIdentity->redirect();
                } else {
                    // Закрываем popup окно и перенаправляем на cancelUrl
                    $authIdentity->cancel();
                }
            }

            // Что-то пошло не так, перенаправляем на страницу входа
            $this->redirect(array('site/login'));
        }





        $model = new LoginForm;
        // if it is ajax validation request
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
            //echo CActiveForm::validate($model);
            $validateRes = CActiveForm::validate($model);
            if (count($validateRes) == 0) {
                $model->login();
            }
            echo $loginres;
            Yii::app()->end();
        }

        // collect user input data
        if (isset($_POST['LoginForm'])) {

            $model->attributes = $_POST['LoginForm'];
            // validate user input and redirect to the previous page if valid
            /* if($model->validate() && $model->login())
              $this->redirect(Yii::app()->user->returnUrl); */
            if ($model->validate()) {
                $model->login();
                /* if (Yii::app()->user->role=='justjoined')
                  $urlAfter = Yii::app()->createAbsoluteUrl('site/registrationStep2');
                  else
                  $urlAfter = Yii::app()->homeUrl;

                  echo "window.location.href='{$urlAfter}';";

                  Yii::app()->end(); */

                if (Yii::app()->user->Profile->getSettingsValue('email_activated_at') == '0000-00-00 00:00:00')
                    $this->redirect('/profile/verifyemail');
                else
                    $this->redirect(Yii::app()->homeUrl);
            }
        }
        // display the login form
        //$this->render('login',array('model'=>$model));

        $this->layout = '//layouts/static2';
        $this->render('signin', array('model' => $model));
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout() {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

    public function actionAutoLogin() {
//FB::error($_GET)      ;        

        if (Yii::app()->user->id) {
            Yii::app()->user->logout(); //$this->redirect(Yii::app()->homeUrl);
            $this->redirect($_SERVER['REQUEST_URI']);
        }


        if ($userId = Yii::app()->helperProfile->getUserIdFromAutoLoginUrl($_GET)) {
//FB::error($userId)      ; 
//die();      
            /* $identity = new UserIdentity('','');
              if ($identity->authenticateByUserId($userId))
              {
              $duration = 3600*24*365; // 365 days
              Yii::app()->user->login($identity,$duration);
              } */

            $identity = new UserIdentity('', '');
//FB::warn($identity)  ;
//echo Yii::app()->createAbsoluteUrl($url);
            $identity->authenticateByUserId($userId);

            $error = $identity->errorCode;
//FB::warn($error, 'eee')  ;
//die(); 
            if ($error === UserIdentity::ERROR_NONE) {
                $duration = 3600 * 24 * 365; // 365 days
                Yii::app()->user->login($identity/* ,$duration */);

                if (isset($_GET['redirect']) && $_GET['redirect'])
                    $url = urldecode($_GET['redirect']);
                else
                    $url = Yii::app()->homeUrl; //'site/index';

                $this->redirect(Yii::app()->createAbsoluteUrl($url));
            }
            else {
                return false;
            }
        }



        $this->redirect(Yii::app()->homeUrl);
    }

    //protected $_identity;
    //public $_logouturl;
    public function actionFblogin() {
        //FB::warn(Yii::app()->facebook->getMe());
        //Yii::app()->end();
        if (LOCAL) {
            $fb_me = unserialize('a:11:{s:2:"id";s:15:"100003105993753";s:4:"name";s:13:"Oleg OlegTest";s:10:"first_name";s:4:"Oleg";s:9:"last_name";s:8:"OlegTest";s:4:"link";s:54:"http://www.facebook.com/profile.php?id=100003105993753";s:8:"birthday";s:10:"05/16/1978";s:6:"gender";s:4:"male";s:5:"email";s:17:"cytae00@tst.pp.ua";s:8:"timezone";i:2;s:6:"locale";s:5:"en_US";s:12:"updated_time";s:24:"2011-10-29T06:21:39+0000";}');
            //$fb_me = Yii::app()->facebook->getMe();

            if ($fb_me && Yii::app()->user->isGuest) {
                $profile = new Profile;
                $loginFbSuccess = $profile->loginFB($fb_me);

                if (!FACEBOOK) {
                    if (!$loginFbSuccess) {
                        $this->redirect(Yii::app()->createAbsoluteUrl(Yii::app()->homeUrl));
                    }
                } else {
                    //echo "<script type='text/javascript'>top.location.href = 'http://apps.facebook.com/cytaeswoonr';</script>"; // <-- swoonr here
                    //Yii::app()->end();
                    $this->redirect(Yii::app()->createAbsoluteUrl(Yii::app()->homeUrl));
                }
            }
        }
    }

    public function actionRegistration() {
        if (Yii::app()->user->data('role') == 'justjoined')
            $this->redirect('/site/registrationStep2');

        if (!Yii::app()->user->isGuest)
            $this->redirect(Yii::app()->homeUrl);

        $this->layout = '//layouts/static2';
        $this->render('registration');
    }

    public function actionRegistrationStep2() {
        if (Yii::app()->user->data('role') != 'justjoined')
            $this->redirect(Yii::app()->homeUrl);

        /* if (!Yii::app()->user->Profile->getDataValue('pics') && Yii::app()->user->Profile->getDataValue('ext', 'facebook', 'fb_id'))
          {
          $modelImg = new Img;
          $image = $modelImg->prepareExtFBImage(Yii::app()->user->data('id'));
          Yii::app()->user->Profile->imgAdd($image);
          } */

        $this->layout = '//layouts/static2';
        $this->render('registrationStep2'/* ,array('model'=>$model) */);
    }

    public function actionSignin() {
        if (!Yii::app()->user->isGuest)
            $this->redirect(Yii::app()->homeUrl);

        $this->render('signin');
    }

    /*
      public function actionImportHelper()
      {
      Yii::app()->location->importGeoNames(Yii::app()->basePath.'/../../allCountries.txt');
      } */

// Facebook log in (not for APP)
    public function actionFacebooklogin() {
        Yii::import('ext.facebook.*');
        $ui = new FacebookUserIdentity(FB_APPID, FB_SECRET);
        if ($ui->authenticate()) {
            $user = Yii::app()->user;
            $user->loginFB($ui);
            $this->redirect($user->returnUrl);
        } else {
            throw new CHttpException(401, $ui->error);
        }
    }

}