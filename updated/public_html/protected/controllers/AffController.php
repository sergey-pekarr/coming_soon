<?php

class AffController extends Controller
{
	/*
	 * register affiliate
	 */
	public function actionIndex()
	{
		HelperAff::registerAffId();
		$this->redirect('/');
	}
	
	/*
	 * check user interface for affiliates, managers and master (like on NC)
	 */
	public function actionCheckUser()
	{
		$this->layout='//layouts/ajax';
		
		$user = (isset($_GET['user'])) ? $_GET['user'] : "";
		$userId = 0;
		
		if ($user)
		{
		    $userId = Yii::app()->secur->decryptID( $user );
			
			if ( $userId )
		    {
		        /*$sql = "SELECT id FROM users WHERE id=:id LIMIT 1";
	            $userId = Yii::app()->db
					->createCommand($sql)
	                ->bindValue(":id", $user, PDO::PARAM_INT)
	                ->queryScalar();*/
		    }
		    elseif (stristr($user, "@"))
		    {
		        /*$sql = "SELECT id FROM users WHERE email=:email LIMIT 1";
	            $userId = Yii::app()->db
					->createCommand($sql)
	                ->bindValue(":email", $user, PDO::PARAM_STR)
	                ->queryScalar();*/
		    	$userId = Profile::emailExist($user);
		    }
		    else
		    {
		        /*$sql = "SELECT id FROM users WHERE username=:username LIMIT 1";
	            $userId = Yii::app()->db
					->createCommand($sql)
	                ->bindValue(":username", $user, PDO::PARAM_STR)
	                ->queryScalar();*/
	            $userId = Profile::usernameExist($user);
		    }
		    
			if ($userId)
			{
				$profile = new Profile($userId);
				$row = $profile->getData();
				$payment = $profile->getPayment();
				
				if ($row['affid']>99)
				{
					$sql = "SELECT * FROM cs_users WHERE id=".$row['affid']." LIMIT 1";
		            $aff = Yii::app()->dbSTATS->createCommand($sql)->queryRow();				
				}
				else
					$aff = false;
					
				$this->render('checkuser', array('user' => $user, 'row' => $row, 'payment'=>$payment, 'aff'=>$aff));
				Yii::app()->end();
			}		    
		    
		}
		
		$this->render('checkuser', array('user' => $user));
	}
	

	
	/*
	 * check user interface for affiliates, managers and master (like on NC)
	 */
	public function actionCheckAff()
	{
		$this->layout='//layouts/ajax';
		
		$userId = (isset($_GET['user'])) ? $_GET['user'] : "";
		
		$user = false;
		$manager = false;
		$master = false;
		
		if ($userId && $userId>99)
		{
			//aff
			$sql = "SELECT * FROM cs_users WHERE id=:id LIMIT 1";
		    $user = Yii::app()->dbSTATS
		    	->createCommand($sql)
		    	->bindValue(":id", $userId, PDO::PARAM_INT)
		    	->queryRow();				
			
		    //manager
		    if ($user['manager_id']!=0)
		    {
				$sql = "SELECT * FROM cs_users WHERE id=:id LIMIT 1";
			    $manager = Yii::app()->dbSTATS
			    	->createCommand($sql)
			    	->bindValue(":id", $user['manager_id'], PDO::PARAM_INT)
			    	->queryRow();		    
		    }

		    //master
		    if ($user['master_id']!=0)
		    {
				$sql = "SELECT * FROM cs_users WHERE id=:id LIMIT 1";
			    $master = Yii::app()->dbSTATS
			    	->createCommand($sql)
			    	->bindValue(":id", $user['master_id'], PDO::PARAM_INT)
			    	->queryRow();		    
		    }
		}
		
		$this->render('checkaff', array('userId'=>$userId, 'user'=>$user, 'manager' => $manager, 'master'=>$master));
	}	

	
}