<?php

class AjaxController extends Controller
{

	public function init()
    {
        parent::init();
        $this->layout='//layouts/ajax';
    }	
    
    
    
    public function actionIndex()
	{
		$this->render('index');
	}
    
    public function actionUpdate()
	{
        //update every 5 minutes for prevent losting session...
        
		//call user to init session
		Yii::app()->user->isGuest;
		
		echo json_encode(array('success'=>'Yes'));
        Yii::app()->end();
	}
    
    public function actionSrupdate()//update screen resolution
	{
        $x = intval( $_POST['x'] );
        $y = intval( $_POST['y'] );
		
        if ($x && $y)
        {
			$sr = $x."x".$y;
			Yii::app()->user->Profile->infoUpdate('screen_resolution', $sr);
        }
		
		echo json_encode(array('success'=>'Yes'));
        Yii::app()->end();
	}    
	
	public function actionUpdateClient(){
		/*
		      var clientinfor = { 'screen': screenSize, 'availscreen': availscreen,
		          'screen_nor': screenSize_nor, 'availscreen_nor': screenSize_nor,
		          'browser': browser, 'agent': agent, 'platform': platform,
		          'fonts': fonts, 'plugins': plugins, 'plugins_pur': plugins_pur
		      };
		*/
		
		if(!$_POST || !isset($_POST['screen']) || !isset($_POST['availscreen']) 
			|| !isset($_POST['browser']) || !isset($_POST['agent']) ) return;
		
		$key = 'clientupdate-'.Yii::app()->user->id;
		if(!isset(Yii::app()->session[$key]) || Yii::app()->session[$key] > 1){
			Yii::app()->session[$key] = 0;
		}
		Yii::app()->session[$key] += 1;
		
		$_POST['http_accept'] = $_SERVER["HTTP_ACCEPT"];
		
		ProfileClientInfo::updateClientInfo($_POST);
	}	
	
	
    public function actionStates()
    {
    	$this->render(
            'states', 
            array(
            	'states'=>Yii::app()->location->getStatesList($_POST['country']),
            )
        );
    }
    
    public function actionSityFind()
    {
        echo json_encode( Yii::app()->location->findCityLike($_POST['city'], $_POST['country']) );
        Yii::app()->end();
    }
    
    /**
     * find nearest ZIP from latitude and longitude
     */
    public function actionZipFind()
    {
        echo json_encode( Yii::app()->location->findZipNearestLocationId($_POST['locationId']) );
        Yii::app()->end();
    }    

    public function actionUsernameExists()
    {
        echo json_encode( Profile::usernameExist($_POST['username']) );
        Yii::app()->end();
    }

    public function actionEmailExists()
    {
        echo json_encode( Profile::emailExist($_POST['email']) );
        Yii::app()->end();
    }
    
    
    
    public function actionPanelNewMembers()
    {
        $this->widget('application.components.PanelNewMembersWidget', array('ajax'=>true, 'page'=>$_POST['page'], 'all'=>$_POST['all']));
        Yii::app()->end();
    }
    public function actionPanelOnlineNow()
    {
        $page = (isset($_POST['page'])) ? $_POST['page'] : 0;
        $all = (isset($_POST['all'])) ? $_POST['all'] : false;
    	
		//n 2012-07-21
		if($page>=5 &&!Yii::app()->user->checkAccess('gold')){
			$this->renderPartial('limitonline');
			Yii::app()->session['page_online_now_viewed'] = 0;
			Yii::app()->end();
		}
		else
			Yii::app()->session['page_online_now_viewed'] = $page;
		
		$this->renderPartial('panelOnlineNow', array('page'=>$page, 'all'=>$all), false, true);
    }
    public function actionPanelProfileViewedTo()
    {
        $this->widget('application.components.PanelProfileViewedToWidget', array('ajax'=>true, 'page'=>$_POST['page'], 'all'=>$_POST['all']));
        Yii::app()->end();
    }    
    public function actionPanelProfileViewedFrom()
    {
        $this->widget('application.components.PanelProfileViewedFromWidget', array('ajax'=>true, 'page'=>$_POST['page'], 'all'=>$_POST['all']));
        Yii::app()->end();
    }    
    public function actionPanelProfileLikeTo()
    {
        $this->widget('application.components.PanelProfileLikeToWidget', array('ajax'=>true, 'page'=>$_POST['page'], 'all'=>$_POST['all']));
        Yii::app()->end();
    }    
    public function actionPanelProfileLikeFrom()
    {
        $this->widget('application.components.PanelProfileLikeFromWidget', array('ajax'=>true, 'page'=>$_POST['page'], 'all'=>$_POST['all']));
        Yii::app()->end();
    }
    public function actionPanelProfileMatches()
    {
        $this->widget('application.components.dashboard.PanelDashboardMatchesWidget', array('ajax'=>true, 'page'=>$_POST['page'], 'all'=>$_POST['all']));
        Yii::app()->end();
    }    
    /*public function actionPanelInterest()
    {
        $this->widget('application.components.userprofile.ProfilesInterestWidget', array('ajax'=>true/*, 'page'=>$_POST['page'], 'all'=>$_POST['all']*//*));
        Yii::app()->end();
    }*/
 
    
    public function actionPanelMessagesInboxAll()
    {
        $this->widget('application.components.dashboard.PanelDashboardMessagesInboxWidget', array('ajax'=>true, 'page'=>$_POST['page'], 'all'=>$_POST['all']));
        Yii::app()->end();
    }
    public function actionPanelMessagesSent()
    {
        $this->widget('application.components.dashboard.PanelDashboardMessagesSentWidget', array('ajax'=>true, 'page'=>$_POST['page'], 'all'=>$_POST['all']));
        Yii::app()->end();
    }
        
    public function actionMessageHide()
    {
        $newCount = Messages::hidePrivateMessage($_POST['id'], Yii::app()->user->id);
        echo json_encode(array(
            'success' => 'Yes',
            'newCount'=> $newCount
        ));
        Yii::app()->end();
    }
    
    public function actionMessageMarkAsRead()
    {
        $newCount = Messages::markAsReadPrivateMessage($_POST['id'], Yii::app()->user->id);
        echo json_encode(array(
            'success' => 'Yes',
            'newCount'=> $newCount
        ));
        Yii::app()->end();
    }    
    
    
    public function actionUpdateDelete()
    {
        Updates::deleteUpdate($_GET['id'], Yii::app()->user->id);
        Yii::app()->end();
    }    
        
    
    public function actionNotifyHide()
    {
        Yii::app()->user->Profile->settingsUpdate('hided_notify', '1');
        Yii::app()->end();
    }

    public function actionWantMeetNewVideo()
    {
        WantMeet::storeNewViewed(Yii::app()->user->id, $_POST['id']);
        Yii::app()->end();
    }



    /**
     * report abuse user
     */
	public function actionReportAbuse()
	{
        if ($id_to = Yii::app()->secur->decryptID($_POST['id']))
        {
            ReportAbuse::newReport(Yii::app()->user->id, $id_to);
            echo json_encode(array('success'=>'Yes'));
        }
        
        Yii::app()->end();
	}    
    
    public function actionSendWink()
    {
        if ($id_to = Yii::app()->secur->decryptID( $_POST['id'] ))
        {
            $model = new Winks;
            if ($model->sendWink($id_to, Yii::app()->user->id))
            {
                $modelMessages = new Messages;
                $modelMessages->addPrivateMessage( $id_to, Yii::app()->user->id, 'New Wink', ';-)' );
                
                echo json_encode(array('success'=>'Yes'));
            }            
        }
                    
        Yii::app()->end();
    }

        
    public function actionFeedbackMessage()
    {
//FB::warn($_POST, 'actionFeedbackMessage');
        $model = new FeedbackSendForm;
        if ( isset($_POST['FeedbackSendForm']) && isset($_POST['ajax']) )
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
                
        if(isset($_POST['FeedbackSendForm']))
        {
            $model->attributes = $_POST['FeedbackSendForm'];
            if($model->validate())
            {
                $model->doSend();
                //Yii::app()->end();
            }
        }        
        Yii::app()->end();
    }
        
    public function actionReportBugMessage()
    {
//FB::warn($_POST, 'actionFeedbackMessage');
        $model = new ReportBugSendForm;
        if ( isset($_POST['ReportBugSendForm']) && isset($_POST['ajax']) )
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
                
        if(isset($_POST['ReportBugSendForm']))
        {
            $model->attributes = $_POST['ReportBugSendForm'];
            if($model->validate())
            {
                $model->doSend();
                //Yii::app()->end();
            }
        }        
        Yii::app()->end();
    }
    public function actionCoockieUsernameDelete()
	{
        Profile::coockieUsernameDelete();
        echo json_encode(array('success'=>'Yes'));
        Yii::app()->end();
    }
    
    
    /**
     * show current user ID (for hdfvr)
     */
	/*public function actionCurrentUserId()
	{
        echo Yii::app()->user->id;
        exit();//Yii::app()->end();
    }    */
                
                
    public function actionAddVideoFromStream()
    {
        $userId = intval($_POST['userId']);
        
        if (Yii::app()->user->id == $userId)
        {
            $modelVideo = new Video;
            $res = $modelVideo->addVideoFromStream($userId,$_POST['streamName']);
            
            if ($res)
                echo json_encode(array('success'=>'Yes'));
        }
        Yii::app()->end();
    }
                
    /*         
    public function actionAddVideoMessageFromStream()
    {
        $userId = intval($_POST['userId']);
        
        if (Yii::app()->user->id == $userId)
        {
            $modelVideo = new Video;
            $res = $modelVideo->addVideoMessageFromStream($userId,$_POST['streamName']);
            
            if ($res)
                echo json_encode(array('success'=>'Yes'));
        }
        Yii::app()->end();
    }*/
    
    
    
    
    
    /*METRICs*/
    public function actionMetricVideoPlay()
    {
        $userId = $_POST['id'];        
        Yii::app()->user->Metric->addEvent_viewVideo($userId);
        //echo json_encode(array('success'=>'Yes'));
        Yii::app()->end();
    }
}