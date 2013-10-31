<?php
/**
 * This is class UserController
 * 
 */
class UserController extends Controller
{
	public function init(){     
		parent::init();
		$this->layout = '//layouts/static' ;
	}
	
	private function setSession($key, $value){
		Yii::app()->session[$key] = $value;
	}
	
	private function getSession($key, $default = null){
		if(isset(Yii::app()->session[$key])) return Yii::app()->session[$key];
		return $default;
	}
	
	private function getHomeLink($userid){
		if (Yii::app()->user->checkAccess('limited')){
			return '/';
		}
		else {
			return Yii::app()->helperProfile->getAutoLoginUrl($userid);
		}
	}


	public function actionRemindPassword(){
		$code = 0; //nothing
		if($_POST){
			$email = isset($_POST['f_email'])?$_POST['f_email']:false;
			$userId = false;
			if($email){
				//I removed RemindPasswordForm to make it simple
				$userId = Profile::emailExist($email);

				//Send email here
				Yii::app()->mail->prepareMailHtml(
			        	$email, 
			            '', 
			            'passwordReset', 
			            array(
			                'user_id'	=> $userId,
						)
				);
			}
			$code = $userId?1:2; //success:failed
		}
		
		$this->layout='//layouts/guest';
		$this->render('remindpassword', $code);
	}
	
	public function actionChangePassword(){
		if($_GET && count($_GET) == 2){
			//$get = array('idd'=>$_GET[0], 'hash'=>$_GET[1]);
			$get = $_GET;
			$userid =  Yii::app()->helperProfile->getUseridFromHashParameterEx($get, 'changepassword', 1209600); //A week				
			if($userid){				
				$this->setSession('changepassword','ok');
				$this->setSession('changepassworduser',$userid);
			}
			else {
				$this->setSession('changepassword','failed');
			}
			$this->redirect('/changepassword'); //Hide the link from user, show user pure url;
		} 
		else{
			$userid = $this->getSession('changepassworduser');
			$data = array();
			$data['homelink'] = $this->getHomeLink($userid);
			if($_POST && $userid){
								
				$newpassword = 	$_POST['f_password'];
				$confirmnewpassword = 	$_POST['f_confirmpassword'];
				
				if($newpassword != $confirmnewpassword){
					$data['error'] = 'Sorry, your passwords do not match';
				} else if(strlen($newpassword)<5) {
					$data['error'] = 'Please enter your 5 character length password';
				} else {
					$profile = new Profile($userid);
					$ok = $profile->Update('password', md5(SALT.$newpassword));
					if($ok){
						$this->render('changepasswordsuccess', array('data' => $data));	
					}
					else {
						$data['error'] = 'Sorry, your passwords can not change';
					}
				}
				$this->render('changepassword', array('data' => $data));
				
			}
			else if($this->getSession('changepassword')	=='ok' && $userid) {	
				$this->render('changepassword', array('data' => $data));					
			}
			else {
				$this->render('changepasswordrequestfailed', array('data' => $data));
			}
		}
		
	}
	
	public function actionUnsubscribe(){		
		if($_GET && count($_GET) == 2){
			//$get = array('idd'=>$_GET[0], 'hash'=>$_GET[1]);
			$get = $_GET;
			$userid =  Yii::app()->helperProfile->getUseridFromHashParameterEx($get, 'unsubscribe', 1209600); //A week				
			if($userid){				
				//Need review if email_notification is right field?
				$profile = new Profile($userid);
				//$profile->settingsUpdate('email_notifications', 0);
				$profile->settingsUpdate('hided_notify', 1);
				
				$this->setSession('unsubscribe','ok');
				$this->setSession('unsubscribeuser',$userid);
			}
			else {
				$this->setSession('unsubscribe','failed');
			}
			$this->redirect('/unsubscribe'); //Hide the link from user, show user pure url;
		}
		else{
			$data = array();
			$userid = $this->getSession('unsubscribeuser');
			if($this->getSession('unsubscribe')	=='ok' && $userid){
				$data['message'] = 'You have been unsubscribed from all account activity emails, however for your peace of mind you will still receive emails relating to your account billing';
			}
			else{
				$data['message'] = 'Your request could not be completed.';
			}
			$data['homelink'] = $this->getHomeLink($userid);
			$this->render('unsubscribe', array('data' => $data));
		}
	}
}

