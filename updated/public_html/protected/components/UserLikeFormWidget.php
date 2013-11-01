<?php
class UserLikeFormWidget extends CWidget
{
    public $profileId;
    
    public function init()
    {
        if ($this->profileId)
        {
            $tmp = Yii::app()->user->Profile->LikeToIs($this->profileId);
            $likeToIs = $tmp['like'];        
            
            $tmp = Yii::app()->user->Profile->LikeFromIs($this->profileId);
            $likeFromIs = $tmp['like']; 
            
            $profile = new Profile($this->profileId);                    

            $this->render('UserLikeForm', array('model'=>$model, 'profile'=>$profile, 'likeToIs'=>$likeToIs, 'likeFromIs'=>$likeFromIs));
        }
    }
}
?>
