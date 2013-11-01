<?php

class PaymentForm extends CFormModel
{
	public $subStep;
	
	public $ccname;
	public $ccnum;
    public $ccmon;
	public $ccyear;	
	public $ccvv;
	
	public $firstname;
	public $lastname;
    public $address;
	public $country;
	public $state="";
	public $city;
	public $zip;
	public $email;
	
	
	
	public $location;
	
    public $ccyears;
    public $ccmonths;
    
    public $priceId = 1;
    
    public function init()
    {
        parent::init();
        
        for ($y=date("Y"); $y<=(date("Y")+10); $y++ )
        	$this->ccyears[$y] = $y;
        
		for ($m=1; $m<=12; $m++ )
			$this->ccmonths[sprintf("%02d", $m)] = sprintf("%02d", $m);
        	
        $this->subStep = isset($_POST['PaymentForm']['subStep']) ? $_POST['PaymentForm']['subStep'] : 1;
        
        $this->location = Yii::app()->user->Profile->getDataValue('location');
        
        $this->state = "";
    }
    	
	
	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		
		$rules = array(
			array('ccname, ccnum, ccvv, priceId', 'required'),
			array('ccname', 'length', 'min'=>3, 'max'=>255),
			
			array('ccnum', 'length', 'min'=>16, 'max'=>16),
			array('ccnum', 'match', 'pattern'=>CCARD_PATTERN),
			
			array('ccvv', 'length', 'min'=>3, 'max'=>4),
			array('ccvv', 'match', 'pattern'=>CVV_PATTERN),
			
			array('ccmon', 'in', 'range'=>$this->ccmonths ),
			array('ccyear', 'in', 'range'=>$this->ccyears ),
			
		);
		
		$rules2 = array(
			array('firstname, lastname, address, country, city, zip, email', 'required'),
			
			array('firstname', 'length', 'min'=>2, 'max'=>40),
			array('lastname', 'length', 'min'=>2, 'max'=>40),
			array('address', 'length', 'min'=>5, 'max'=>80),
			array('zip', 'match', 'pattern'=>ZIP_PATTERN),
			array('zip', 'length', 'min'=>5, 'max'=>13),
			array('email', 'email'),
		);		

		if ($this->subStep==2)
		{
			$rules = CMap::mergeArray( $rules, $rules2 );		
		}

		return $rules;
	}

	public function expireCheck()
	{
	    if ( !$this->expire['year'] || !$this->expire['month'] || !checkdate( $this->expire['month'], '01', $this->expire['year'] ))
        {
            $this->addError('expire', 'Date is invalid');
        }	
	}

	
	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
            "ccname"           	=> "Cardholder's name:",
			"ccnum"				=> "Card number:",
			"ccvv"				=> "CVV / CVV2:",
		
			"firstname"			=> "First name:",
			"lastname"			=> "Last name:",
			"address"			=> "Street address:",
			"country"			=> "Country:",
			"state"				=> "State / Province:",
			"city"				=> "City:",
			"zip"				=> "Zip / postal code:",
			"email"				=> "E-mail:",
		);
	}

    

	public function doProccess($modelPayment/*, $price_id*/)
	{
		if(!$this->hasErrors())
        {
			
			if ($this->subStep==2) //substep of form (show address, name, ....)
			{
				$success = $modelPayment->startTransaction(Yii::app()->user->id, $this->priceId, $this->attributes);//redirect to payment page of paymode
				$redirectUrl = ($success) ? "/payment/approved" : "/payment/declined";	
				return $redirectUrl;
			}
			else
			{
				return json_encode(array());
			}        	
        }
        return false;
	}
    
    
        
}
