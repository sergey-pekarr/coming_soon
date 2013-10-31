<?php
class UserUpdatesWidget extends CWidget
{
    public $profile;
    
    public function init()
    {
        if ($this->profile->getDataValue('role')!='administrator')//не показываем для профиля админа
        {
            /*$model = new Profiles;            
            $updates = $model->getUserUpdates($this->profile->getDataValue('id'));*/
			$updates = Updates::getUserUpdates($this->profile->getDataValue('id'));
            
            //owner can edit
            $edit = ($this->profile->getDataValue('id') == Yii::app()->user->id);                   
                   
            $this->render('UserUpdates', array('updates'=>$updates, 'profile'=>$this->profile, 'edit'=>$edit));            
        }
    }
}
?>
