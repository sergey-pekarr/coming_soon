<?php
class DateControlWidget extends CWidget
{
    public $model;
    public $dates;
    
    public function init()
    {
		$this->render( 'dateControl' );            
    }
}
?>
