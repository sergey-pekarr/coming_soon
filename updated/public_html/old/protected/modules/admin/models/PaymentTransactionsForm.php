<?php


class PaymentTransactionsForm extends CFormModel
{
    public $date1;
    public $date2;
	
	public $paymod;
	/*public $paymods;
	public $paymodsForSelect;*/
    
	public $status;
	
	public $form;
	
    public $perPage;
    public $sort;
    
    public function init()
    {
        parent::init();
        
        $this->perPage = 50;
        $this->sort = "idDESC";
        
        
        /*$this->paymods = CHelperPayment::getPaymods();
        $this->paymods[] = '';
        
        asort($this->paymods);
        
        foreach ($this->paymods as $p)
        	if ($p=='')
        		$this->paymodsForSelect[$p] = 'All';
        	else 
        		$this->paymodsForSelect[$p] = $p;*/

        if (!$this->date1)
        	$this->date1=date("Y-m-d");
        if (!$this->date2)
        	$this->date2=date("Y-m-d");
    }
    
	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		$rules = array(
			array('date1, date2', 'date', 'allowEmpty'=>'false', 'format'=>'yyyy-MM-dd'),
			
			array('form', 'in', 'range'=>CHelperAdmin::getForms()/*array('1','88','93','932')*/ ),
				
            array('paymod', 'in', 'range'=>CHelperPayment::getPaymods() /*$this->paymods*/ ),
            array('status', 'in', 'range'=>array('', 'started','authed','completed','completed_cams','renewal','renewal_declined','cancelled') ),
            
            
            array('perPage', 'in', 'range'=>array('10','50','100','200','500','1000') ),
            array('sort', 'in', 'range'=>array('idASC','idDESC') ),
		);
		
		return $rules;
	}

	
	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
            'date1'     => 'From',
            'date2'     => 'To',
			
			'status'     => 'Status',
		
            'perPage'  =>'Per Page',
            'sort'  =>'Sort By',
		);
	}
   
}
