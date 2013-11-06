<?php
class ListActivityWidget extends CWidget
{
	public $items;
	
	public function init(){
	}
	
	public function run(){
		if($this->items && count($this->items)> 0){
			$this->render('listactivity', array('items'=>$this->items));
		}
	}
}