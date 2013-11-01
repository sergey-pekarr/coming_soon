<?php
class AccessDeniedWidget extends CWidget
{
    
    public function init()
    {
        $showWindow = false;
        $showButtonCreateVideo = true;
        
        if ( 
            Yii::app()->user->id 
            && 
            !Yii::app()->user->checkAccess('approved') 
            && 
            Yii::app()->user->checkAccess('limited') 
        )
        {
            if (
                    !stristr(Yii::app()->getRequest()->requestUri, "/profile/myVideos") 
                    &&
                    Yii::app()->getRequest()->requestUri != '/site/terms'
                    &&
                    Yii::app()->getRequest()->requestUri != '/site/privacy'
                    &&
                    Yii::app()->getRequest()->requestUri != '/site/about'
                    &&
                    Yii::app()->getRequest()->requestUri != '/help'                
                )
            {
                ///Yii::app()->user->setFlash('accessDenied', '---'); 
                Controller::redirect(Controller::createUrl( '/profile/myVideos' ));
            }
            
            ///$showWindow = Yii::app()->user->hasFlash('accessDenied');
        }

        $this->render('accessDenied', array('showWindow'=>$showWindow, 'showButtonCreateVideo'=>$showButtonCreateVideo));
    }
}
?>
