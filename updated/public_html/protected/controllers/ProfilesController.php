<?php

class ProfilesController extends Controller
{
	public function init()
    {
        parent::init();
        
        $this->layout = '//layouts/one-column' ;
    }	
    
    public function actionViewedAll()
	{
        $this->layout = '//layouts/one-column-tabs' ;
        $this->render('viewedAll');
	}


    public function actionOnline()
	{
        $this->layout = '//layouts/dashboard' ;
		$this->render('onlineNowAll');
	}	
	
    public function actionOnlineNowAll()
	{
		$this->render('onlineNowAll');
	}    

    public function actionNewMembersAll()
	{
        $this->render('newMembersAll');
	} 

    public function actionLikeAll()
	{
        $this->layout = '//layouts/one-column-tabs' ;
        $this->render('likeAll');
	}
}