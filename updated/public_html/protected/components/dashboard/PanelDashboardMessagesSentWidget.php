<?php
class PanelDashboardMessagesSentWidget extends CWidget
{
    public $title="Sent Messages";

    public $page=0;
    public $all=1;
    public $ajax;

    public function init()
    {
        if (Yii::app()->user->id)
        {
            if ($this->ajax)
            {
                $pageSize = ($this->all) ? 15 : 15;
                
                $model = new Messages;
                
                $messages = $model->getPrivateMessagesFrom(Yii::app()->user->id, $this->page, $pageSize);
    
                $pages=new CPagination($messages['count']);
                $pages->pageSize = $pageSize;
                $pages->setCurrentPage($this->page);
            }
            
           $this->render('PanelDashboardMessagesSent', array('messages'=>$messages['messages'], 'title'=>$this->title, 'pages'=>$pages, 'ajax'=>$this->ajax, 'all'=>$this->all));
        }        
        
        
        
        /*if (Yii::app()->user->id)
        {
            $model = new Messages;
            
            $messages = $model->getPrivateMessagesFrom(Yii::app()->user->id);
                 
            $this->render('PanelDashboardMessagesSent', array('messages'=>$messages));
        }*/
    }
}
?>
