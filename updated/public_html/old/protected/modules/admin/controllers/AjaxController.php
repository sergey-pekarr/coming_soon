<?php

class AjaxController extends Controller
{

	public function init()
	{
		parent::init();
		$this->layout='/layouts/ajax';//    '//'... - это глобально          '/'... - это в модуле
	}
	
	
	
	public function actionIndex()
	{
		$this->render('index');
	}

	public function actionUpdate()
	{
		Yii::app()->controller->module->admin->isGuest;//надо для обновления сессии админа
		
		echo json_encode(array('success'=>'Yes', 'ts'=>time()));
		Yii::app()->end();
	}
	
	/**
	 * SYSTEM clear memcache & assets
	 */    
	public function actionClearCache()
	{
		$model = new System;
		$model->clearCache();
		$model->clearAssets();
		
		echo json_encode(array(
			'success'=> 'Yes',//($res) ? 'Yes' : 'No'
			));
		
		Yii::app()->end();
	}    

	public function actionImageApprove()
	{
		$user_id = 	intval($_POST['user_id']);
		$n = 		intval($_POST['n']);
		$action = 	$_POST['action'];
		
		
		if ($user_id && $n)
		{
			$profile = new Profile($user_id);
			
			if ($action=='clothed')
			{
				$profile->imageUpdate($n, 'xrated', 'clothed');
				$profile->imageUpdate($n, 'approved', '1');
			}
			
			if ($action=='naked')
			{
				$profile->imageUpdate($n, 'xrated', 'naked');
				$profile->imageUpdate($n, 'approved', '1');
			}
			
			if ($action=='declined')
			{
				$profile->imgDel( $n );
				
				$reason = '';
				if(isset($_POST['reason'])) $reason = $_POST['reason'];
				$reason = trim($reason);
				/*
				 * moved to template
				 * if($reason == '' || $reason == ''){
					$reason = 'We are sorry but your image was declined. Please read our Image upload rules before uploading any more images.';
				}*/
				$autoLoginUrl = CHelperProfile::getAutoLoginUrl($profile->getId());
				Yii::app()->mail->prepareMailHtml($profile->getDataValue('email'),	'','declineimage', 
					array('reason' => $reason, 
							'subject' => 'Your image was declined', 
							'autoLoginUrl' => $autoLoginUrl,
							'user_id' => $profile->getId()));	
			}
			
			
		}
		
		echo json_encode(array(
			'success'=> 'Yes',//($res) ? 'Yes' : 'No'
			));
		
		Yii::app()->end();
	} 
	
	public function actionUsersRoleChange()
	{
		$id = (isset($_POST['id'])) ? intval($_POST['id']) : 0;
		$role = (isset($_POST['role'])) ? $_POST['role'] : "";
		
		if ($id && $role)
		{
			$profile = new Profile($id);
			
			switch ($role)
			{
				case "free":
					$profile->makeFree();
					break;

				case "gold":
					$profile->makeGold(30);
					break;        			
				
				case "deleted":
					Profile::deleteUser($id);
					break;        			
				
				default:
					$profile->Update('role', $role); 
			}
			
			echo json_encode(array('success'=> 'Yes'));        
			Yii::app()->end();	        	
		}
	} 	
	
	/*public function actionUserDelete()
		{
	    Profile::deleteUser($_POST['id']);
	    echo json_encode(array('success'=> 'Yes'));        
	    Yii::app()->end();
	}    
	   
	public function actionUserBan()
		{
	    $profile = new Profile($_POST['id']);
	    $profile->banUser();
	    echo json_encode(array('success'=> 'Yes'));        
	    Yii::app()->end();
		}
	   
	public function actionUserUnBan()
		{
	    $profile = new Profile($_POST['id']);
	    $role = $profile->UnBanUser();        
	    echo json_encode(array('success'=> 'Yes', 'role'=>$role));        
	    Yii::app()->end();
		}*/

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/*
	 * using for EPG at now - needs to do test transaction on EPG to approve in LIVE 
	 */
	public function actionPaymentCancelSubscription()
	{
		$id = (isset($_POST['id'])) ? intval($_POST['id']) : 0;
		if ($id)
		{
			$profile = new Profile($id);
			$paymentInfo = $profile->getPayment();

			if ($paymentInfo)
			{
				$modelPayment = CHelperPayment::getModelPayment($paymentInfo['paymod']);
				
				$success = $modelPayment->cancelRecurring($id);
			}
			else
				$success = false;
			
			
			$success = ($success) ? "Yes" : "No";
			
			echo json_encode(array('success'=> $success));        
			Yii::app()->end();	        	
		}
	}

	
	
	
	
	
	
	
	
	public function actionSendFlirtMessage()
	{
		$id = (isset($_POST['id'])) ? intval($_POST['id']) : 0;
		//$subject = (isset($_POST['subject'])) ? $_POST['subject'] : "";
		$message = (isset($_POST['message'])) ? $_POST['message'] : "";
	
		$success = false;
		
		if ($id /*&& $subject*/ && $message)
		{
			$sql = "SELECT * FROM `profile_messages` WHERE id={$id} LIMIT 1";
			$originalMsg = Yii::app()->db->createCommand($sql)->queryRow();
			
			if ($originalMsg)
			{
				$id_to = $originalMsg['id_to'];
				$id_from = $originalMsg['id_from'];
				
				$parentId = ($originalMsg['parent']!=0) ? $originalMsg['parent'] : $originalMsg['id'];
				
				$sql = "INSERT INTO profile_messages 
						(id_to, id_from, added, `read`, `text`, `parent`)
						VALUES 
						(:id_to, :id_from, NOW(), '0', :text, :parent)
				";
				Yii::app()->db->createCommand($sql)
					->bindValue(":id_to", $id_from, PDO::PARAM_INT)
					->bindValue(":id_from", $id_to, PDO::PARAM_INT)
					//->bindValue(":subject", $subject, PDO::PARAM_STR)
					->bindValue(":text", $message, PDO::PARAM_STR)
					->bindValue(":parent", $parentId, PDO::PARAM_INT)
					->execute();
				
					
				if (Yii::app()->db->getLastInsertId('profile_messages'))
				{
					$success = true;
					
					//$sql = "UPDATE `profile_messages` SET `read`='1', `autoflirt_answered`='1' WHERE id={$id} LIMIT 1";
					$sql = "UPDATE `profile_messages` SET `read`='1', `autoflirt_answered`='1' WHERE id_to={$id_to} AND id_from={$id_from}";
					Yii::app()->db->createCommand($sql)->execute();
					
					$profilePromo = new Profile($id_to);
					$profilePromo->activityUpdate('activityLast');
				}
			}
		}
			
		$success = ($success) ? "Yes" : "No";
			
		echo json_encode(array('success'=> $success));        
		Yii::app()->end();	        			
	}
}











