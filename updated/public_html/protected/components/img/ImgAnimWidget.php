<?php
class ImgAnimWidget extends CWidget
{
    public $userId;
    public $i=0;
    
    public function init()
    {
        $profile = new Profile($this->userId);
        $this->render('ImgAnim', array('profile'=>$profile, 'i'=>$this->i));
    }
}
?>
