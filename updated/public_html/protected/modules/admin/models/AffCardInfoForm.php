<?php


class AffCardInfoForm extends CFormModel
{
	public $date1;
	public $date2;
	
	public $perPage;
	public $sort;	
	public $aff;
	public $ids;
	public $affForSelect;
	public $affIds;
	
	public function init()
	{
		parent::init();
		
		$this->perPage = 100;
		$this->sort = "Name";
		
		$time = time();
		$weekday = date('N', $time) -1;
		
		if (!$this->date2)
			$this->date2=date("Y-m-d", time());
		if (!$this->date1)
			$this->date1=date("Y-m-d", strtotime($this->date2) - 30 * 24 * 3600);
		
		$this->affForSelect = array(0=>'None');
		$this->affIds = array(0);         
		//managers
		$sql = "SELECT DISTINCT manager_id as id, manager_name as login FROM pm_today_gold WHERE manager_id<>0 ORDER BY manager_name";
		//`date`>='".$this->date1."' AND `date`<='".$this->date2."' AND
		$managersRes = Yii::app()->db->createCommand($sql)->queryAll();
		
		$managers = array();
		if ($managersRes)
		{
			foreach($managersRes as $v)
			{
				$this->affForSelect[ $v['id'] ] = $v['id'] . ' : ' . $v['login'].'';
				$this->affIds[] = $v['id'];
			}
		}
		
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
			array('ids', 'length', 'min'=>0),
			array('aff', 'in', 'range'=>$this->affIds),
			array('sort', 'in', 'range'=>array('Id','Name') ),
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
			
			//'perPage'  =>'Per Page',
			'sort'  =>'Sort By',
			'aff' => 'Affiliate Manager',
			'ids' => 'Ids'
			);
	}
	
}
