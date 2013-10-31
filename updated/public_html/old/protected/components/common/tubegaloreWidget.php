<?php
class tubegaloreWidget extends CWidget
{	
	public $userprofile;
		
	public function init()
	{
	}
	
	public function run(){
		$this->render('tubegalore', array('userprofile' => $this->userprofile));
	}
}