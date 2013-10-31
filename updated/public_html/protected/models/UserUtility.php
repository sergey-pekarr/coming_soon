<?php
//
///**
// * Provide static methods to work with user. In fact, it should me merge to Profile as static methods(june28)
// * Now, this class avoid conflict with Oleg's User class
// */
//class UserUtility
//{		
//	static private $_error;//1-bad username/email ,  2 - bad password,     3- bad id (user deleted or blocked, ...)
//	
//	static public function getError()
//	{
//		return self::$_error;
//	}
//	
//	/**
//	* find record in DB
//	* @param username or email
//	* @param password
//	* return userID if success
//	*/
//	static public function findUserById($userId){
//		$res = Yii::app()->db
//			->createCommand("SELECT * FROM users WHERE id=:id LIMIT 1")
//			->bindParam(":id", $userId, PDO::PARAM_INT)
//			->queryRow();
//		
//		if ($res){
//			self::$_error = 0;
//			return $res;
//		}
//		else{
//			self::$_error = 3;
//			return $res;
//		}
//	}  
//	
//	/**
//	 * This is method findUserByEmail
//	 *
//	 * @param mixed $email This is a description
//	 * @return mixed This is the return value description
//	 *
//	 */		
//	static public function findUserByEmail($email){
//		$res = Yii::app()->db
//			->createCommand("SELECT * FROM users WHERE email=:email LIMIT 1")
//			->bindParam(":email", $email, PDO::PARAM_STR)
//			->queryRow();
//
//		if ($res){
//			self::$_error = 0;
//			return $res;
//		}
//		else{
//			self::$_error = 3;
//			return $res;
//		}
//	}
//	
//	/**
//	 * Used when after user request remind password to email
//	 *
//	 * @param mixed $userid This is a description
//	 * @param mixed $newpassword This is a description
//	 * @return mixed This is the return value description
//	 *
//	 */	
//	//static public function changePassword($userid, $newpassword){
//	//	$password = md5(SALT.$newpassword);
//	//	Yii::app()->db
//	//		->createCommand("update users set password = :password")
//	//		->bindParam(":password", $password, PDO::PARAM_STR)
//	//		->execute();
//	//}
//	
//	static public function checkEmailExist($email){
//		$user = self::findUserByEmail($email);
//		return $user != null;
//	}
//	
//	static public function sendConfirmCode($email){
//		$user = self::findUserByEmail($email);
//		if($user != null){
//			
//			$userid = $user['id'];
//			$app=Yii::app();
//			$encid = $app->secur->encryptID($userId);
//			$code = $app->getSecurityManager()->hashData($userId, SECURE_KEY.'confirmseed4234234');
//			
//			//send email here	
//			
//			self::$_error = 0;	
//			return true;	
//		}
//		self::$_error = 1;
//		return false;
//	}
//	
//	static public function confirmCode($encid, $code){
//		$app=Yii::app();
//		$userid = $app->secur->decryptID($encid);
//		$checkcode = $app->getSecurityManager()->hashData($userid, SECURE_KEY.'confirmseed4234234');
//		
//		if($checkcode == $code){
//			$user = self::findUserById($userid);
//			if($user != null){
//				
//				//Update
//				$profile = new Profile($userid);
//				$role = $profile->getDataValue('role');
//				if($role == 'justjoined'){
//					$profile->Update('role', 'free');
//				}
//				$profile->settingsUpdate('email_activated', 1);
//				
//				self::$_error = 0;
//				return true;
//			}
//			else{
//				self::$_error = 1;
//				return false;
//			}	
//		}
//		self::$_error = 4;
//		return false;
//	}
//		
//	static public function sendRemindPassword($email){
//		$user = self::findUserByEmail($email);
//		if($user != null){
//			
//			$userid = $user['id'];
//			$oldpass = $user['password'];
//			$app=Yii::app();
//			$encid = $app->secur->encryptID($userId);
//			$code = $app->getSecurityManager()->hashData($userId, SECURE_KEY.'remindseed4234234'.$oldpass); //haskh key will change in the next request
//			
//			//send email here	
//			
//			self::$_error = 0;
//			return true;		
//		}
//		self::$_error = 1;
//		return false;
//	}
//	
//	static public function changePassword($encid, $code, $newpassword){
//		$app=Yii::app();
//		$userid = $app->secur->decryptID($encid);
//		$user = self::findUserById($userid);
//		if($user == null){
//			self::$_error = 1;
//			return false;
//		}
//		
//		$oldpass = $user['password'];		
//		$checkcode = $app->getSecurityManager()->hashData($userid, SECURE_KEY.'remindseed4234234'.$oldpass);
//		
//		if($checkcode == $code){
//			
//			$profile = new Profile($userid);
//			$profile->Update('password', md5(SALT.$newpassword));			
//			
//			self::$_error = 0;
//			return true;	
//		}
//		self::$_error = 4;
//		return false;
//	}
//	
//	static public function createUnsubscibe($userid, &$encid, &$code){
//		if($user != null){			
//			$app=Yii::app();
//			$encid = $app->secur->encryptID($userId);
//			$code = $app->getSecurityManager()->hashData($userId, SECURE_KEY.'subcseed4234234');	
//						
//			self::$_error = 0;	
//			return true;
//		}
//		self::$_error = 1;
//		return false;
//	}
//	
//	static public function unsubscibe($encid, $code){
//		$app=Yii::app();
//		$userid = $app->secur->decryptID($encid);
//		$checkcode = $app->getSecurityManager()->hashData($userid, SECURE_KEY.'subcseed4234234');
//		
//		if($checkcode == $code){
//			$user = self::findUserById($userid);
//			if($user != null){
//				
//				//unsuscribe
//				$profile = new Profile($userid);
//				$profile->settingsUpdate('email_notification', 0);
//				
//				self::$_error = 0;	
//				return true;
//			}
//			else{
//				self::$_error = 1;
//			}	
//		}
//		self::$_error = 4;
//		return false;
//	}
//	
//}
//
//
///* Only use static secure model is ok. 
//static private function generateSecureCode($length = 14){
//	substr(md5(uniqid(mt_rand(), true)),0,$length);
//}
//	
//static private function saveSecureCode($userid, $action, $code){
//	
//}
//	
//static private function removeSecureCode($userid, $action, $code){
//}	
//// */
//
///* 
//static private function generateStaticSecureCode($seed, $length = 20){
//    $app=Yii::app();
//	substr($app->getSecurityManager()->hashData($seed, SECURE_KEY),0,$length);		
//}
////*/