<?php
class PanelDashboardMessagesInboxWidget extends CWidget
{
    public $title="Inbox";
    
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
                
                $messages = $model->getPrivateMessagesTo(Yii::app()->user->id, false, $this->page, $pageSize);
    
                $pages=new CPagination($messages['count']);
                $pages->pageSize = $pageSize;
                $pages->setCurrentPage($this->page);
            }

            $this->render('PanelDashboardMessagesInbox', array('messages'=>$messages['messages'], 'title'=>$this->title, 'pages'=>$pages, 'ajax'=>$this->ajax, 'all'=>$this->all));
        }
    }
}
?>
