<?php

class AdminUserLocationForm extends CFormModel
{
    public $user_id;
	public $country;
    public $city;
    public $location_id;
    public $zip;
	
    public function init()
    {
        $this->zip = '';
    }

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
            array('city, user_id', 'required'),
            array('location_id', 'locationCheck'),
            array('city', 'length', 'min'=>3, 'max'=>150), 
            array('zip', 'zipCheck'),
            array('zip', 'length', 'min'=>3, 'max'=>13),            
		);
	}    
    
    public function locationCheck()
    {
        $mess = "Please enter correct city name.";
        if (!$this->location_id)
        {
            $this->addError('city',$mess);
        }
        else
        {
            $id = Yii::app()->dbGEO
                    ->createCommand("SELECT COUNT(id) FROM location_geoip_cities WHERE id=:id LIMIT 1")
                    ->bindParam(":id", $this->location_id, PDO::PARAM_INT)
                    ->queryScalar();
            if (!$id)
            {
                $this->addError('city',$mess);
            }
        }
    }


    public function zipCheck()
    {
        if (!$this->zip)
        {
            $this->addError('zip', "Don't forget your Zipcode!");
        }
        elseif ( !preg_match(ZIP_PATTERN, $this->zip) )
        {
            $this->addError('zip', 'Wrong format of Zip code.');
        }
    }
	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'country'       =>"Country",
            'location_id'   =>'Location',
            'city'          =>'Location',
            'zip'           =>'Zip Code',
		);
	}


	public function locationUpdate()
	{
        $profile = new Profile($this->user_id);
		
		return $profile->locationUpdate($this->location_id, $this->zip);
	}
     
        
}
