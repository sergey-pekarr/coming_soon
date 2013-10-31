<?php
class TestItemWidget extends CWidget
{
	public $title;
	public $panelWidth;
	public $total;
	public $page;
	public $panelsrc;
	public $options;
	
	public function init()
	{
	}
	
	public function run(){
		$this->render('testitem', array());
	}
}
