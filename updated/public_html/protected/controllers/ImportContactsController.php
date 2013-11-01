<?php

class ImportContactsController extends Controller
{
	public function init()
    {
        parent::init();
        $this->layout = '//layouts/one-column' ;
    }
    
    public function actionIndex()
	{
        //$this->render('index');
	}
}