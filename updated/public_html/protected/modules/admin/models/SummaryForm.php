<?php


class SummaryForm extends CFormModel
{
    public $date1;
    public $date2;
    
    public function init()
    {
        parent::init();
        
        $this->date1=date("Y-m-d");
        $this->date2=date("Y-m-d");
    }

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
            array('date1, date2', 'date', 'allowEmpty'=>'false', 'format'=>'yyyy-MM-dd'),
		);
	}
    
	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
            'date1'     => 'From',
            'date2'     => 'To',
		);
	}

}
