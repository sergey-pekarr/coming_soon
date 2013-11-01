<?php


class UserEditAppearanceTabForm extends CFormModel
{
    public $height;
    public $race;
    public $religion;
    public $hairColor;
    public $eyeColor;
    public $bodyType;
    
    private $_lists;
    
    public function init()
    {
        $this->_lists = Yii::app()->helperProfile->getPersonalValueList();
        
        if (Yii::app()->user->id)
        {
            $userData = Yii::app()->user->Profile->getData();
            $this->height = $userData['personal']['height'];
            $this->race = $userData['personal']['race'];
            $this->religion = $userData['personal']['religion'];
            $this->hairColor = $userData['personal']['hairColor'];
            $this->eyeColor = $userData['personal']['eyeColor'];
            $this->bodyType = $userData['personal']['bodyType'];
        }
    }
    
	public function rules()
	{
		return array(
            array('height', 'heightCheck'),
            array('race', 'raceCheck'),
            array('religion', 'religionCheck'),
            array('hairColor', 'hairColorCheck'),
            array('eyeColor', 'eyeColorCheck'),
            array('bodyType', 'bodyTypeCheck'),
		);
	}


	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
            'hairColor'=>'Hair color',
            'eyeColor'=>'Eye color',
            'bodyType'=>'Body type',
		);
	}


    public function heightCheck()
    {
        if (!$this->_lists['height'][$this->height])  
            $this->addError('height','Bad height value');
    }
    public function raceCheck()
    {
        if (!$this->_lists['race'][$this->race])
            $this->addError('race','Bad race value');
    }
    public function religionCheck()
    {
        if (!$this->_lists['religion'][$this->religion])
            $this->addError('religion','Bad religion value');
    }    
    public function hairColorCheck()
    {
        if (!$this->_lists['hairColor'][$this->hairColor])
            $this->addError('hairColor','Bad hairColor value');
    }
    public function eyeColorCheck()
    {
        if (!$this->_lists['eyeColor'][$this->eyeColor])    
            $this->addError('eyeColor','Bad eyeColor value');
    }    
    public function bodyTypeCheck()
    {
        if (!$this->_lists['bodyType'][$this->bodyType])    
            $this->addError('bodyType','Bad bodyType value');
    }     

    public function saveAppearance()
    {
        if (Yii::app()->user->id)
        {
            $profile = Yii::app()->user->Profile;
            
            $profile->personalUpdate('height', $this->height);
            $profile->personalUpdate('race', $this->race);
            $profile->personalUpdate('religion', $this->religion);
            $profile->personalUpdate('hairColor', $this->hairColor);
            $profile->personalUpdate('eyeColor', $this->eyeColor);
            $profile->personalUpdate('bodyType', $this->bodyType);
        }
    }    
    
    
    
}
