<?php
class PanelOnlineNowWidget extends CWidget
{
    public $ajax=0;
    public $page=0;
    
    public $all;
    
    public function init()
    {
    	if ($this->ajax && Yii::app()->user->id)
        {
            $pageSize = ($this->all) ? 20 : 8;
            $pagesMax = ($this->all) ? 50 : 10;
                        
            $model = new Profiles;
            
            $res = $model->findOnlineNow($this->page, $pageSize, $pagesMax);
            
            $pages=new CPagination($res['count']);
            $pages->pageSize = $pageSize;
            $pages->setCurrentPage($this->page);

            $this->render('PanelOnlineNow', array('profiles'=>$res['ids'], 'pages'=>$pages, 'all'=>$this->all));
        }
        else
        {
        	if (isset(Yii::app()->session['page_online_now_viewed']))
        		$this->page = Yii::app()->session['page_online_now_viewed'];
        		
        	$this->render('PanelOnlineNow', array('profiles'=>false));
        }        
        	
    }
}
?>
