<?php
class ProfileUserBoxWidget extends CWidget
{
    public $id;
    public $imgSize;
    public $infoType;
    
    public function init()
    {
        if (!$this->id)
        {
            $this->id = Yii::app()->user->id;
        }

        if (!$this->imgSize)
        {
            $this->imgSize = '152x86';
        }
        
        if ($this->id)
        {
            $profile = new Profile($this->id);
            $this->render('ProfileUserBox', array('profile'=>$profile/*, 'infoType'=>$this->infoType*/));
        }
    }
}
?>
