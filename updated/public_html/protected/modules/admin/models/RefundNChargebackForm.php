<?php


class RefundNChargebackForm extends CFormModel
{
	public $date1;
	public $date2;
	
	public $perPage;
	public $sort;	
	
	public function init()
	{
		parent::init();
		
		$this->perPage = 50;
		$this->sort = "idDESC";
		
		$time = time();
		$weekday = date('N', $time) -1;
		
		if (!$this->date1)
			$this->date1=date("Y-m-d", $time - ($weekday)* 24*3600);
		if (!$this->date2)
			$this->date2=date("Y-m-d", $time + (6 - $weekday)* 24*3600);		
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
			
			//array('perPage', 'in', 'range'=>array('10','50','100','200','500','1000') ),
			//array('sort', 'in', 'range'=>array('idASC','idDESC') ),
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
			
			'perPage'  =>'Per Page',
			'sort'  =>'Sort By',
			);
	}
	
}
