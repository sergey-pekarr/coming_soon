<?php


class UserEditBadgets extends CFormModel
{
    public $badgets;
    
    public function init()
    {
        if (Yii::app()->user->id)
        {
            $userData = Yii::app()->user->Profile->getData();
            $this->badgets = ($userData['personal']['badgets']) ? explode(',',$userData['personal']['badgets']) : array();
        }
    }
    
	public function rules()
	{
		return array(
            //array('badgets', 'in', 'range'=>array('FB','TW','G+') ), 
            array('badgets', 'badgetsCheck' ),
		);
	}


	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
            array('badgets', 'Show badgets'), 
		);
	}

    public function badgetsCheck()
    {
        
    }
    

    public function saveBadgets()
    {
FB::error($this->badgets);
        if (Yii::app()->user->id)
        {
            $profile = Yii::app()->user->Profile;
            
            $profile->personalUpdate('badgets', $this->badgets);
            
        }
    }    
    
    
    
}
