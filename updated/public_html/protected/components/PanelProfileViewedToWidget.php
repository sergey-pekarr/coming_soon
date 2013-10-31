<?php
class PanelProfileViewedToWidget extends CWidget
{
    public $ajax=0;
    public $page=0;
    
    public $all;
    
    public function init()
    {
        if ($this->ajax && Yii::app()->user->id)
        {
            $pageSize = ($this->all) ? 18 : 8;
            $pagesMax = ($this->all) ? 50 : 10;
            
            $model = new Profiles;
            
            $res = $model->findProfileViewedTo($this->page, $pageSize, $pagesMax);
    
            $pages=new CPagination($res['count']);
            $pages->pageSize = $pageSize;
            $pages->setCurrentPage($this->page);             
        }        
                                
        $this->render('PanelProfileViewedTo', array('profiles'=>$res['ids'], 'pages'=>$pages, 'ajax'=>$this->ajax, 'all'=>$this->all));
    }    
}
?>
