<?php


class UsersFindForm extends CFormModel
{
	public $userId;
	public $username;
	public $profileIdEncr;
	public $email;
	public $ref_domain;
	
	public $netbilling_2_member_id;
	public $zombaio_sub_id;
	public $vendo_trans_id;
	public $wd_GUWID;
	public $ccname1;
	public $ccname2;
	
    public function init()
    {
        parent::init();
        
    }

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
            //array('albumIdStr', 'required'),
            array('userId, netbilling_2_member_id, zombaio_sub_id, vendo_trans_id', 'numerical'),
            array('username', 'length', 'min'=>PROFILE_USERNAME_LEN_MIN, 'max'=>PROFILE_USERNAME_LEN_MAX),// for compatible if changed .../// array('username', 'length', 'min'=>PROFILE_USERNAME_LEN_MIN, 'max'=>PROFILE_USERNAME_LEN_MAX),
            array('profileIdEncr', 'length', 'min'=>32, 'max'=>32),
            array('ccname1, ccname2', 'length', 'min'=>1, 'max'=>255),
            array('wd_GUWID', 'length', 'min'=>20, 'max'=>25),
            array('email', 'email'),
			array('ref_domain', 'length', 'min'=>1, 'max'=>255),	
            
            
            //array('', 'email'),
		);
	}
/*    
	public function check_albumIdStr()
	{
		if ($this->albumIdStr) 
		{
			$this->albumIdStr = trim($this->albumIdStr);
//FB::warn('***************************');
			//if entered as url
			$url = parse_url($this->albumIdStr);
			$path = $url['path'];
//FB::warn($path, 'path');
			$pattern = "/^\/([a-z0-9]{5,12})\/*.*$/";
			preg_match($pattern, $path, $matches);
			
			if (isset($matches[1]))
			{
//FB::warn($matches, 'matches');
				
				$this->albumIdStr = $matches[1];
			}
			
//FB::warn('***************************');
		}
		
	}
*/	
	
	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
            'userId'  =>  "User ID",
			'profileIdEncr'=> "Profile ID (encrypted)",
			'ref_domain'=> "Referring url (domain)",
		
			'netbilling_2_member_id' => "Netbilling MEMBER id",
			'zombaio_sub_id' => "Zombaio SUBSCR. id",
			'vendo_trans_id' => "Vendo ID",
			'wd_GUWID'		 => 'Wirecard GUWID',
			'ccname1'		 => 'First name on card',
			'ccname2'		 => 'Last name on card',
		
		);
	}
   
}
