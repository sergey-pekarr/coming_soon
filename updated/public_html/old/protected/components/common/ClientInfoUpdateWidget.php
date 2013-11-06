<?php
class ClientInfoUpdateWidget extends CWidget
{
	public function init()
	{
			if (!ADMIN && Yii::app()->user->id)
			{
				$key = 'clientupdate-'.Yii::app()->user->id;
				if(!isset(Yii::app()->session[$key]) || Yii::app()->session[$key] < 1){
					$this->render('clientupdate');  
				}        
			}
	}
}
?>
