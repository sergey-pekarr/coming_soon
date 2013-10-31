<?php
class PanelDashboardMatchesWidget extends CWidget
{
    public $ajax=0;
    public $page=0;
    
    public $all;    
    
    public function init()
    {
        $pageSize = ($this->all) ? 18 : 8;
        $pagesMax = ($this->all) ? 50 : 10;
            
        $model = new Profiles;
            
        $res = $model->findProfileMatches($this->page, $pageSize, $pagesMax, $this->all);
            
        $pages=new CPagination($res['count']);
        $pages->pageSize = $pageSize;
        $pages->setCurrentPage($this->page);           
                
        if ($this->all)
            $this->render('PanelProfileMatches', array('profiles'=>$res['ids'], 'pages'=>$pages, 'ajax'=>$this->ajax, 'all'=>$this->all));
        else
            $this->render('PanelDashboardMatches', array('matches'=>$res['ids'], 'pages'=>$pages, 'ajax'=>$this->ajax, 'all'=>$this->all));
    }
}
?>
