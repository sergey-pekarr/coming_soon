<?php

class DashboardController extends Controller {

    public function beforeAction($action) {
        if (parent::beforeAction($action)) {
            if (!Yii::app()->user->checkAccess('free') && $action->id != 'index')
                $this->redirect('/');

            return true;
        }

        return false;
    }

    public function init() {
        $this->layout = '//layouts/dashboard';
        parent::init();
    }

    public function actionIndex() {

        //1 select users
        //$users = new User;
        //$users = User::model()->findAll(array('condition'=>"role=:role", 'params'=>array(":role"=>$role), 'order'=>'fio', 'limit'=>'10', 'offset'=>'15'));
        //$student = Student::model()->with('records')->findByPk($id);

        $location_id = Yii::app()->location->findLocationIdByIP();
        $location = Yii::app()->location->getLocation( $location_id );        

        $users = Users::model()->with('images')->with('location')->findAll(
            array(
                'select' => 't.id, t.username, t.gender',
                'condition' => 'location.city="'.$location['city'].'"',
                'limit' => '9',
            )
        );

       // $modelProfiles = new Profiles;

       // $perPage = 20;

       // $page = (isset($_REQUEST['page'])) ? intval($_REQUEST['page']) : 1;
       // $page = ($page) ? $page : 1;
       // $page--;
       // $profiles = $modelProfiles->Search($model->attributes, $page, $perPage);

       // $pages = new CPagination($profiles['count']);
       // $pages->pageSize = $perPage;
       // $pages->setCurrentPage($page);


        // anonymous access
        if (!Yii::app()->user->checkAccess('free')) {

            if (
                    Yii::app()->user->id &&
                    Yii::app()->user->Profile->getDataValue('role') == 'justjoined' &&
                    Yii::app()->user->Profile->getDataValue('ext', 'facebook')
            ) {
                $this->redirect('/site/registrationStep2');
            } 
            else {
                if (isset($_SERVER['GEOIP_CITY'])){
                    $city = $_SERVER['GEOIP_CITY'];
                }
                elseif($location['city'] != '') {                    
                    $city = $location['city']; 
                }
                else{
                    $city = "Your Area";
                }

                $this->layout = '//layouts/guest-home';
                $this->render(
                    'home-guest', 
                    array (
                        'users' => $users,
                        'city' => $city
                    )
                );
            }

        }
        else { // logged in user
            /*$this->render(
                'index', 
                array(
                    'model' => $model,
                    'profiles' => $profiles['list'],
                    'pages' => $pages,
                    'perPage' => $perPage,
                )
            );*/
            $userProfile = new Profile(Yii::app()->user->id);
            $this->render('index', array(
            ));
        }


        /* $model=new HelpForm;

          if(isset($_POST['HelpForm']))
          {
          $model->attributes=$_POST['HelpForm'];

          if($model->validate() && $model->save())
          {
          $success = "Message sent!";
          Yii::app()->user->setFlash('HelpSuccess',$success);
          }
          } */

        //$this->render('index'/*, array('model'=>$model)*/);
    }

    public function actionMatches() {
        $this->render('matches'/* , array('model'=>$model) */);
    }

    public function actionInbox() {
        $this->render('inbox'/* , array('model'=>$model) */);
    }

    public function actionInboxAll() {
        $this->render('inboxAll'/* , array('model'=>$model) */);
    }

    public function actionSent() {
        $this->render('sent'/* , array('model'=>$model) */);
    }

    public function actionHotList() {
        $this->render('hotlist'/* , array('model'=>$model) */);
    }

}