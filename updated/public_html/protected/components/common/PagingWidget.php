<?php
class PagingWidget extends CWidget
{
	public $title;
	public $panelWidth;
	public $total;
	public $page;
	public $panelsrc;
	public $options;
	public $ajax;
	public $ajaxMethod;
	
	public function init()
	{
	}
	
	public function run(){
		$this->render('paging', array('paneltitle'=>$this->title, 'panelsrc' => $this->panelsrc, 'panelWidth' => $this->panelWidth, 
			'total' => $this->total, 'page'=>$this->page));
	}
	
	public function getLink($page){
		if($this->ajax === true){
			return "javascript:".$this->ajaxMethod."($page)";
		}
		else {
			return "/".$this->panelsrc."page=$page".($this->options?"&{$this->options}":"");
		}
	}
}
