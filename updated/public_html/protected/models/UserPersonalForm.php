<?php

/**
 */
class UserPersonalForm extends CFormModel
{
    public $status;
    public $interesting;

    public $height;
    public $race;
    public $religion;
    public $hairColor;
    public $eyeColor;
    public $bodyType;
    public $profession;

   
    public $description;
    //public $looking_for_gender;
    public $birthday;
    
    private $_lists;
    
    public function init()
    {
        $this->_lists = Yii::app()->helperProfile->getPersonalValueList();
        
        $this->description = Yii::app()->user->personal('description');
        $this->status = Yii::app()->user->personal('status');
        $this->interesting = str_split(Yii::app()->user->personal('interesting'));
        
        $this->height = Yii::app()->user->personal('height');
        $this->race = Yii::app()->user->personal('race');
        $this->religion = Yii::app()->user->personal('religion');
        $this->hairColor = Yii::app()->user->personal('hairColor');
        $this->eyeColor = Yii::app()->user->personal('eyeColor');
        $this->bodyType = Yii::app()->user->personal('bodyType');
        $this->profession = Yii::app()->user->personal('profession');

        
        
        /*if (Yii::app()->user->data('looking_for_gender')=='C')
        {
            $this->looking_for_gender = array('M','F');            
        }
        else
        {
            $this->looking_for_gender = array(Yii::app()->user->data('looking_for_gender'));
        }*/
    }
    
    
	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('status', 'statusCheck'),//array('status', 'in', 'range'=>array('single', 'complicated', 'dating', 'married') ),
            array('interesting', 'interestingCheck'),//
            
            array('height', 'heightCheck'),
            array('race', 'raceCheck'),
            array('religion', 'religionCheck'),
            array('hairColor', 'hairColorCheck'),
            array('eyeColor', 'eyeColorCheck'),
            array('bodyType', 'bodyTypeCheck'),
            array('profession', 'professionCheck'),
            
            array('description','descriptionCheck'),//array('description', 'length', 'min'=>0, 'max'=>255),
            
            //array('looking_for_gender','looking_for_genderCheck'),
            array('birthday','birthdayCheck'),
		);
	}
    
    
	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'status'        => "My Status:",
            'interesting'   => "I'm interested in:",
            
            'hairColor'=>'Hair color',
            'eyeColor'=>'Eye color',
            'bodyType'=>'Body type',
            //'looking_for_gender'=> 'I am looking for',
            'description'   => 'Short description about me (less than 255 characters)',
		);
	}
    
    

    public function statusCheck()
    {
        if (!in_array($this->status, array('single', 'complicated', 'dating', 'married')))  
            $this->addError('status','Bad status value');
        else
            Yii::app()->user->Profile->personalUpdate('status', $this->status);
    }    

    public function interestingCheck()
    {
        if (empty($this->interesting))
            $this->addError('interesting','Please choose at least one group');
        else
            Yii::app()->user->Profile->personalUpdate('interesting', $this->interesting);
    } 

    public function heightCheck()
    {
        if (!$this->_lists['height'][$this->height])  
            $this->addError('height','Bad height value');
        else
            Yii::app()->user->Profile->personalUpdate('height', $this->height);
    }
    public function raceCheck()
    {
        if (!$this->_lists['race'][$this->race])
            $this->addError('race','Bad race value');
        else
            Yii::app()->user->Profile->personalUpdate('race', $this->race);
    }
    public function religionCheck()
    {
        if (!$this->_lists['religion'][$this->religion])
            $this->addError('religion','Bad religion value');
        else
            Yii::app()->user->Profile->personalUpdate('religion', $this->religion);
    }    
    public function hairColorCheck()
    {
        if (!$this->_lists['hairColor'][$this->hairColor])
            $this->addError('hairColor','Bad hairColor value');
        else
            Yii::app()->user->Profile->personalUpdate('hairColor', $this->hairColor);
    }
    public function eyeColorCheck()
    {
        if (!$this->_lists['eyeColor'][$this->eyeColor])    
            $this->addError('eyeColor','Bad eyeColor value');
        else
            Yii::app()->user->Profile->personalUpdate('eyeColor', $this->eyeColor);
    }    
    public function bodyTypeCheck()
    {
        if (!$this->_lists['bodyType'][$this->bodyType])    
            $this->addError('bodyType','Bad bodyType value');
        else
            Yii::app()->user->Profile->personalUpdate('bodyType', $this->bodyType);
    }     
    public function professionCheck()
    {
        if (!$this->_lists['profession'][$this->profession])    
            $this->addError('profession','Bad profession value');
        else
            Yii::app()->user->Profile->personalUpdate('profession', $this->profession);
    }
    
    /*public function looking_for_genderCheck()
    {
        if ($this->looking_for_gender)
            $v = implode(',',$this->looking_for_gender);
        else
            $v = '';
        
        switch ($v)
        {
            case 'F':
            case 'M':
                Yii::app()->user->Profile->Update('looking_for_gender', $v);
                break;
            case 'F,M':
            case 'M,F':
                Yii::app()->user->Profile->Update('looking_for_gender', 'M,F');
                break;            
            default: 
                $this->addError('looking_for_gender','Bad looking for value');
        }
    }    */
    public function descriptionCheck()
    {
        $this->description = trim($this->description);
        if (strlen($this->description)>255)
        {
            $this->description = substr($this->description, 0, 255);
        }
        Yii::app()->user->Profile->personalUpdate('description', $this->description);
    }
    public function birthdayCheck()
    {
        if (!Yii::app()->helperProfile->checkBirthday($this->birthday))
        {
            $this->addError('birthday','Date is invalid');
        }
        else
        {
            if (!Yii::app()->helperProfile->checkBirthdayAge18($this->birthday))
            {
                $this->addError('birthday', 'You must be at least 18 years old.');
            }
            else
            {
                $birthday = date('Y-m-d', strtotime($this->birthday['year'].'-'.$this->birthday['month'].'-'.$this->birthday['day']));
                Yii::app()->user->Profile->Update('birthday', $birthday);
            }
        }
    }    
    
    







        
}
