<?php
class ProfileImagesBoxWidget extends CWidget
{
    public $id;
    public $infoType;
    
    public function init()
    {
        if (!$this->id)
            $this->id = Yii::app()->user->id;

        if ($this->id)
        {
            $profile = new Profile($this->id);
            $this->render('ProfileImagesBox', array('profile'=>$profile));
        }
    }
}
?>
