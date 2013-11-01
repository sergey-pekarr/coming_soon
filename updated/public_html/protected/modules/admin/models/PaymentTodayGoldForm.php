<?php


class PaymentTodayGoldForm extends CFormModel
{
    public $date1;
    public $date2;
	
	public $paymod;
	public $paymods;
	public $paymodsForSelect;
    
	public $status;
	
	public $aff;
	public $affsSelect;
	
    public $perPage;
    public $sort;
    
    
    public function init()
    {
        parent::init();
        
        $this->perPage = 50;
        $this->sort = "idDESC";
        
        
        $this->paymods = CHelperPayment::getPaymods();
        $this->paymods[] = '';
        
        asort($this->paymods);
        
        foreach ($this->paymods as $p)
        	if ($p=='')
        		$this->paymodsForSelect[$p] = 'All';
        	else 
        		$this->paymodsForSelect[$p] = $p;

        if (!$this->date1)
        	$this->date1=date("Y-m-d");
        if (!$this->date2)
        	$this->date2=date("Y-m-d");
    }
	
    public function initAffs()
    {
 
    }
    
	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
            array('aff', 'checkAff'),//array('aff', 'required'),
		
			array('date1, date2', 'date', 'allowEmpty'=>'false', 'format'=>'yyyy-MM-dd'),
            
            array('paymod', 'in', 'range'=>$this->paymods ),
            array('status', 'in', 'range'=>array('', 'not_active', 'active', 'expired', 'cancelled', 'refunded', 'chargeback') ),
            
            
            array('perPage', 'in', 'range'=>array('10','50','100','200','500','1000') ),
            array('sort', 'in', 'range'=>array('idASC','idDESC') ),
		);
	}
    
	public function checkAff()
	{
		
		
		
		//PREPARE  LIST of AFFs		
		//AFF
		$this->affsSelect = array(0=>'All');            
    	//managers
		$sql = "SELECT DISTINCT manager_id as id, manager_name as login FROM pm_today_gold WHERE `date`>='".$this->date1."' AND `date`<='".$this->date2."' AND manager_id<>0 ORDER BY manager_name";
		$managersRes = Yii::app()->db->createCommand($sql)->queryAll();
	
    	$managers = array();
    	if ($managersRes)
    		foreach($managersRes as $v)
    			$this->affsSelect[ $v['id'] ] = $v['id'] . ' : ' . $v['login'].' (manager)';
    			
		//aff
		$sql = "SELECT DISTINCT affid as id, agent_name as login FROM pm_today_gold WHERE `date`>='".$this->date1."' AND `date`<='".$this->date2."' AND affid>99 ORDER BY agent_name";
    	$affsRes = Yii::app()->db->createCommand($sql)->queryAll();
    	$affs = array();
    	if ($affsRes)
    		foreach($affsRes as $v)
    			$this->affsSelect[ $v['id'] ] = $v['id'] . ' : ' . $v['login'];
	}
	
	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
            'date1'     => 'From',
            'date2'     => 'To',
			
			'status'     => 'Recurring',
		
            'perPage'  =>'Per Page',
            'sort'  =>'Sort By',
		);
	}
   
}
