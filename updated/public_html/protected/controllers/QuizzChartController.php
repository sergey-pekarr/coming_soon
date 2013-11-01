<?php

class QuizzChartController extends Controller
{
	public function init()
	{
		parent::init();
		$this->layout='//layouts/img';
	}
	
	public function actionAnalyze(){
		$testid = CHelperQuizz::GetValueFromKey($_GET, 'id', 0);
		$testid = intval($testid);

		$userid =Yii::app()->user->id;
		
		$db = Yii::app()->dbquizz;
	}
	
	
	
}