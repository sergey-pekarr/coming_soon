<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class UserLocationForm extends CFormModel
{
    public $location_id;
    public $country;
    public $zip;

    
	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            //array('zip', 'required'),
            
            array('location_id', 'locationCheck'),
            
            array('zip', 'zipCheck'),
            array('zip', 'length', 'min'=>5, 'max'=>13), 
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

	public function locationUpdate()
	{
        if ($this->location_id)
        {
            Yii::app()->user->Profile->locationUpdate($this->location_id, $this->zip);
        }
	}     
        
}
