<?php
class PanelDashboardMessagesBoxWidget extends CWidget
{
    public $message;
    public $direction='id_from';  
    public $inboxAll = false;      
    
    public function init()
    {
        $profile = new Profile($this->message[$this->direction]);
        $this->render('PanelDashboardMessagesBox', array('m'=>$this->message, 'profile'=>$profile, 'inboxAll'=>$this->inboxAll));
    }
}
?>
