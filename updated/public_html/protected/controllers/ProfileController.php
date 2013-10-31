<?php

class ProfileController extends Controller
{
	private $levelup = false;
	private $editphoto = false;
	
	public function init()
	{
		parent::init();

		if ( !Yii::app()->user->checkAccess('free') && !ADMIN )
			$this->redirect(Yii::app()->homeUrl);             
		
		$this->layout='//layouts/member';
	}	
	
	/**
	 * view profile
	 */
	public function actionIndex()
	{
		$id = null;
		if(isset($_GET['id'])) $id = Yii::app()->secur->decryptID($_GET['id']);

		if ($id && is_numeric($id) && $id != Yii::app()->user->id)
		{
			$profile = new Profile($id);
			$role = $profile->getDataValue('role');
			if(in_array($role, array('banned' , 'deleted'))){
				$this->render('profiledeleted');
				Yii::app()->end();
			}
			
			$act = Activity::createActivity();
			$act->sendView($id);
			
			FB::warn($profile, 'PROFILE VIEW');		
			$mybasic = $this->prepareValueByType($profile, 'basic');	
			$myprofile = $this->prepareValueByType($profile, 'profiles');
			$mylifestyle = $this->prepareValueByType($profile, 'lifestyle');
			$dating = $this->prepareValueForDating($profile);
			
			$recentActs = $act->getRecentActivities($id);
			$name = $profile->getDataValue('username');
			foreach($recentActs as &$item){
				if($item['id_from'] == $id){
					$item['fromName'] = $name;
					$item['toName'] = 'you';
					$item['toPos'] = 'your';
				}
				else {
					$item['toName'] = $name;
					$item['fromName'] = 'you';
					$item['toPos'] = "$name's";
				}
			}
			
			$this->render('profile', array('profile'=>$profile, 'edit'=>false, 
				'myprofile' => $myprofile, 'mylifestyle' => $mylifestyle, 'dating' => $dating,
				'activities' => $recentActs, 'mybasic' => $mybasic));
		}
		elseif (!isset($_GET['id']) || ($id && $id == Yii::app()->user->id) )
		{			
			$profile = Yii::app()->user->Profile;		

			$this->render('myprofile', array('profile'=>$profile, 'edit'=>true, 'showeditphoto' => $this->editphoto, 'showlevelup' => $this->levelup));
		} 
		else {
			$this->render('noprofile');
		}   
	}
	
	public function actionLevelup(){
		$this->levelup = true;
		$this->actionIndex();
	}
	
	public function actionEditPhotos(){
		$this->editphoto = true;
		$this->actionIndex();
	}
	
	/**
	 * Action is used for test purpose only
	 *
	 * @return mixed This is the return value description
	 *
	 */	
	/*
	public function actionViewProfile()
	{
		$id = Yii::app()->user->id;
		$profile = new Profile($id);			
		$myprofile = $this->prepareValueByType($profile, 'profiles');
		$mylifestyle = $this->prepareValueByType($profile, 'lifestyle');
		$dating = $this->prepareValueForDating($profile);
		
		$this->render('profile', array('profile'=>$profile, 'edit'=>false, 
			'myprofile' => $myprofile, 'mylifestyle' => $mylifestyle, 'dating' => $dating));
	}
	*/
	
	private function explodeMultiValue($text){
		$pItems = explode(',', $text);
		$result = array();
		foreach($pItems as $pItem){
			if($pItem != null) $pItem = trim($pItem);
			if($pItem != '' && $pItem != 0) $result[] = $pItem;
		}
		return $result;
	}
	
	private function &prepareValueByType($profile, $type){
		if(!in_array($type, array('basic','profiles', 'lifestyle' ))) return array();
		$items = CHelperProfileNDating::getItemsByGroup($type);
		foreach($items as $key => &$item){
			$curValue = $profile->getPersonalValue($key);
			if(in_array($key, array('interests', 'looking_for'))){
				$item['multiple'] = true;
			} else{
				$item['multiple'] = false;
			}
			$item['selected'] = $this->explodeMultiValue($curValue);
			//if(in_array($key, array('interests', 'looking_for'))){
			//	$item['selected'] = $this->explodeMultiValue($curValue);
			//} else {
			//	if($curValue != null && $curValue != 0 && $curValue != '0'){
			//		$item['selected'] = array($curValue);
			//	} else{
			//		$item['selected'] = array();
			//	}
			//}
			$selectedText = '';
			$defValues = $item['values'];
			foreach($item['selected'] as $selected){
				if($selected<=0 || $selected> count($defValues)) continue;
				$selectedText .= ($selectedText==''?'':', ').$defValues[$selected-1];
			}
			if($selectedText == ''){
				$selectedText = "I'd Rather Not Say";
			}
			$item['selectedText'] = $selectedText;
		}
		return $items;
	}
	
	private function &prepareValueForDating($profile){
		$items = CHelperProfileNDating::getDatingItems();
		$curValues = $profile->getDating();
		foreach($items as $key => &$item){
			
			$curValue = isset($curValues[$key])?$curValues[$key]:'';
			
			if(in_array($key, array('age', 'maxage', 'income', 'maxincome', 'height', 'maxheight'))){
				$item['multiple'] = false;
			} else{
				$item['multiple'] = true;
			}
			$item['selected'] = $this->explodeMultiValue($curValue);
			$selectedText = '';
			$defValues = $item['values'];
			foreach($item['selected'] as $selected){
				if($selected<=0 || $selected> count($defValues)) continue;
				$selectedText .= ($selectedText==''?'':', ').$defValues[$selected-1];
			}
			if($selectedText == ''){
				$selectedText = "I don't mind";
			}
			$item['selectedText'] = $selectedText;
		}
		return $items;
	}
	
	public function actionGetQuickProfile(){
		
		$id = Yii::app()->user->id;
		$profile = new Profile($id);
		$this->layout='//layouts/ajax';
		
		$items = $this->prepareValueByType($profile,'basic');

		$this->render('buildprofile', array('profile'=>$profile, 'type'=>'quickprofile', 'items' => $items));			
	}
	
	public function actionGetMyProfile(){		
		$this->layout='//layouts/ajax';
		$id = Yii::app()->user->id;
		$profile = new Profile($id);
		
		$items = $this->prepareValueByType($profile,'profiles');

		$this->render('buildprofile', array('profile'=>$profile, 'type'=>'myprofile', 'items' => $items));		
	}
	
	public function actionGetMyLifeStyle(){
		
		$id = Yii::app()->user->id;
		$profile = new Profile($id);
		$this->layout='//layouts/ajax';		

		$items = $this->prepareValueByType($profile,'lifestyle');

		$this->render('buildprofile', array('profile'=>$profile, 'type'=>'mylifestyle', 'items' => $items));		
	}
	
	public function actionGetMyDate(){
		
		$id = Yii::app()->user->id;
		$profile = new Profile($id);
		$this->layout='//layouts/ajax';
		
		$items = $this->prepareValueForDating($profile);

		$this->render('buildprofile', array('profile'=>$profile, 'type'=>'mydate', 'items' => $items, 'defaulttext' => "I don't mind"));		
	}

	/* Next:
	Save dating
	Save personal
	Load input value
	View Profile: Render value...
	*/
	private function savePersonal(){
		$id = Yii::app()->user->id;
		$profile = new Profile($id);
		
		if($_POST){
			foreach($_POST as $key => $value){
				if(in_array($key, array('interests','looking_for')) && $value != '' && $value != '0'){
					$value = ",$value,";
				}
				$profile->personalUpdate($key,$value);
			}
		}
	}
	
	public function actionSaveFavourite(){
		$this->savePersonal();
		Yii::app()->end();
	}
	
	public function actionSaveAboutMe(){
		$this->savePersonal();
		Yii::app()->end();
	}

	public function actionSaveQuickProfile(){
		$this->savePersonal();
		Yii::app()->end();
	}

	public function actionSaveMyProfile(){
		$this->savePersonal();
		Yii::app()->end();
	}

	public function actionSaveMyLifeStyle(){
		$this->savePersonal();
		Yii::app()->end();
	}

	public function actionSaveMyDate(){		
		$id = Yii::app()->user->id;
		$profile = new Profile($id);
		
		$profile->datingUpdate($_POST);
		Yii::app()->end();
	}







	public function actionDeleteImage(){
		$imgid = null;
		if(isset($_GET['id'])) $imgid = $_GET['id'];
		if($imgid !== null){
			
			$profile = Yii::app()->user->Profile;
			
			//$imgIndx = $profile->imgGetIndx($imgid);
			
			//Delete also require imageid!
			$profile->imgDel($imgid);
			
			$res = array('nextBigUrl' => null, 'nextPicId' => null);
			echo CJavaScript::jsonEncode($res);
			Yii::app()->end();
		}
	}
	
	public function actionSelectPrimary(){
		$imgid = null;
		if(isset($_GET['id'])) $imgid = $_GET['id'];
		if($imgid !== null){
			
			$profile = Yii::app()->user->Profile;
			
			//$imgIndx = $profile->imgGetIndx($imgid);
			
			//Update require image id, not index
			$profile->imageUpdate($imgid, 'primary', '1');
			
			$res = array('nextBigUrl' => null, 'nextPicId' => null);
			echo CJavaScript::jsonEncode($res);
			Yii::app()->end();
		}
	}
	
	//public function actionUploadImage(){
	//}


	public function actionUpdateMood(){
		$define = array(
			'1' => 'Excited',
			'2' => 'Loved Up',
			'3' => 'Angry',
			'4' => 'Sad',
			'5' => 'Happy',
			);
		if($_POST && isset($_POST['id'])){
			$id = $_POST['id'];
			if(!isset($define[$id])){
				$id = '0';
				$text = '';
			}
			else {
				$text = $define[$id];
			}
			$profile = Yii::app()->user->Profile;
			if($profile->getSettingsValue('email_activated') 
				|| $profile->getSettingsValue('email_activated_at') != '0000-00-00 00:00:00'
			){
				//Avoid hack: Only verified user can be change mood!
				//Status must be in defined range
				$profile->settingsUpdate('mood', $id);
				$profile->settingsUpdate('moodstatus', $text);
			}
		}
	}


	public function actionTestPrestige(){
		$this->render('testprestige');
	}





	public function actionConfirmEmail()
	{
		$code = (isset($_GET['code'])) ? $_GET['code'] : '';
		
		$data['confirmed'] = Yii::app()->user->Profile->getDataValue('settings', 'email_activated_at');
		
		if ( $data['confirmed'] == '0000-00-00 00:00:00' )
		{
			if ($code)
			{
				$userEmail = Yii::app()->user->Profile->getDataValue('email');
				
				$data['confirmed'] = (MD5( SALT . $userEmail ) == $code);
				
				if ($data['confirmed'])
				{
					Yii::app()->user->Profile->settingsUpdate('email_activated_at', date("Y-m-d H:i:s"));
					Yii::app()->user->Profile->settingsUpdate('email_bounced', '0');
				}					
				
			}
		}

		if ($code)
			$this->redirect(Yii::app()->createUrl('profile/confirmEmail'));	//Hide the link from user, show user pure url;
		
		$data['homelink'] = Yii::app()->homeUrl;
		
		$this->render('confirmEmail', array('data'=>$data));		
	}
	
	public function actionVerifyEmail(){
		$this->render('verifyemail');
	}
	
	public function actionResendVerify(){
		$profile = Yii::app()->user->Profile;
		$email = $profile->getDataValue('email');
		$userId = $profile->getId();
		
		Yii::app()->mail->prepareMailHtml(
			$email, 
			'', 
			'verifyemail', 
			array(
				'user_id' => $userId,
			)
		);
	}
	
	private function returnJson($json){		
		$this->layout=false;
		echo CJavaScript::jsonEncode($json);
		Yii::app()->end();
	}
	
	public function actionChangeAndVerifyEmail(){
		if(!isset($_GET['newemail']) || !isset($_GET['confirmnewemail']) 
			|| $_GET['newemail'] == '' || $_GET['confirmnewemail'] == '')
		{
			$this->returnJson(array('error'=>'Please type your new email'));
		}
		
		$newemail = $_GET['newemail'];
		$confirmnewemail = $_GET['confirmnewemail'];
		
		if($newemail != $confirmnewemail){
			$this->returnJson(array('error'=>'You must confirm email'));
		}
		
		$model = new EmailForm();
		$model->email = $newemail;
		$model->validate('email');
		$err = $model->getErrors('email');
		if($err && count($err) > 0){
			$this->returnJson(array('error'=>$model->getErrors('email')));
		}
		Yii::app()->user->Profile->update('email', $newemail);
		
		$this->actionResendVerify();
	}
	
	
	private function getUrl($path) {
		if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
			|| isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'
		) {
			$protocol = 'https://';
		}
		else {
			$protocol = 'http://';
		}
		$currentUrl = $protocol . $_SERVER['HTTP_HOST'];
		$parts = parse_url($currentUrl);

		$query = '';
		if (!empty($parts['query'])) {
			// drop known fb params
			$params = explode('&', $parts['query']);
			$retained_params = array();
			foreach ($params as $param) {
				if ($this->shouldRetainParam($param)) {
					$retained_params[] = $param;
				}
			}

			if (!empty($retained_params)) {
				$query = '?'.implode($retained_params, '&');
			}
		}

		// use port if non default
		$port =
			isset($parts['port']) &&
			(($protocol === 'http://' && $parts['port'] !== 80) ||
				($protocol === 'https://' && $parts['port'] !== 443))
			? ':' . $parts['port'] : '';

		// rebuild
		return $protocol . $parts['host'] . $port . $path;
	}
	
	private function checkFbImageExist($existingFbImages, $newUrl){
		return in_array($newUrl, $existingFbImages);
	}
	
	private function getFbImages(){
		$qry = "select image from user_image_fb where user_id = :user_id";
		$images = Yii::app()->db->createCommand($qry)
			->bindValue(":user_id", Yii::app()->user->id, PDO::PARAM_INT)
			->queryColumn();
		if(!$images) $images = array();
		return $images;
	}
	
	//n 2012-07-31: Import image from facebook
	/*
		Header contain one of these buttons: F|Login or F|Logout
		When fuser == null -> show F|Login 
			-> navigate to profile/FBLogin
				   $authIdentity = Yii::app()->eauth->getIdentity('facebook');
				   $authIdentity->redirectUrl = /profile/FBImage;
				   $authIdentity->cancelUrl = /profile/FBImage;
				   $authIdentity->authenticate()
					https://www.facebook.com/dialog/oauth?client_id=1234&redirect_uri=http%3A%2F%2F127.0.0.1%3A7800%2Fsite%2Flogin%3Fservice%3Dfacebook&scope=user_birthday,email&response_type=code"
		When fuser != null 
			-> show F|Logout, navigate to /profile/fblogout, redirect to /profile/FBImage
			-> show all fb albums and images
			-> ajax post button to /profile/fbimport
		Database: Add table user_image_fb
		Profile: change method addimage(..., optionfb), deleteimage
	*/
	public function actionFBImage(){
		include_once DIR_ROOT.'/protected/extensions/facebook/lib/facebook.php';
		$authIdentity = Yii::app()->eauth->getIdentity('facebook');
		
		$token = $authIdentity->getStoredAccessToken();
		
		$res = array();
		//$res['token'] = $token;
		
		$fb = new Facebook(array(
			'appId'  => FB_APPID,
			'secret' => FB_SECRET,
			'cookie' => FB_COOKIE
			));
		
		$res['token2'] = $fb->getAccessToken();
		if($token != null && $token != '') $fb->setAccessToken($token);
		$fbuser = 0;
		$me = array();
		$login = false;
		try 
		{ 
			$fbuser = $fb->getUser();
			$me = $fb->api('/me');
			$permissions = $fb->api("/me/permissions");
			if($permissions && isset($permissions['data'][0]['user_photos']) && $permissions['data'][0]['user_photos']){
				$login = true;
			}
			//$res['permissions'] = $permissions;
		} 
		catch(FacebookApiException $e)
		{			
			$res['err'] = $e->getMessage();			
			$fb->destroySession();
			$authIdentity->destroySession();
		}

		if($login && $fbuser != 0){	
			
			$userid = Yii::app()->user->id;
			$profile = Yii::app()->user->Profile;
			$existingFbImages = $this->getFbImages();
			
			$albums = $fb->api('/me/albums');
			$images = array();
			
			$i=0;
			foreach($albums['data'] as $album)
			{
				$photos = $fb->api("/{$album['id']}/photos");
				foreach($photos['data'] as $photo)
				{
					$images[] = array('url' => $photo['source'], 
						'existed' => $this->checkFbImageExist($existingFbImages, basename($photo['source'])));
				}
			}
			//$res['images'] = $images;
			
			//We need to delete stored value for auth to actually logout from fb
			$logoutUrl = $fb->getLogoutUrl(array('next'=>$this->getUrl('/profile/fblogout')));
			
			$this->render('fbimage', array('logoutUrl' => $logoutUrl, 'images' => $images, 'res' => $res));
			Yii::app()->end();
		}
		
		$loginUrl = $fb->getLoginUrl(array('scope' => array('user_photos'), 'redirect_uri' => $this->getUrl('/profile/fblogin')));
		//$loginUrl = $fb->getLoginUrl(array('scope' => array('user_photos')));
		$this->render('fblogin', array('res' => $res, 'loginUrl' => $loginUrl));	
	}
	
	//public function actionTestFBImage(){
	//	include_once DIR_ROOT.'/protected/extensions/facebook/lib/facebook.php';
	//	$fb = new Facebook(array(
	//		'appId'  => FB_APPID,
	//		'secret' => FB_SECRET,
	//		'cookie' => FB_COOKIE
	//		));
	//	
	//	$fbuser = 0;
	//	$me = array();
	//	try 
	//	{ 
	//		$fbuser = $fb->getUser();
	//		$me = $fb->api('/me'); 
	//	} 
	//	catch(FacebookApiException $e)
	//	{
	//	}
	//	$login = ($fbuser != 0);
	//	FB::warn($login,'TestFBImage.0');
	//	$logoutUrl = $fb->getLogoutUrl(array('next'=>'/profile/fbimage'));
	//	$images = array(array('url'=>'1', 'existed' => false), array('url'=>'2', 'existed' => true),
	//		array('url'=>'1', 'existed' => false), array('url'=>'2', 'existed' => true),
	//		array('url'=>'1', 'existed' => false), array('url'=>'2', 'existed' => true));
	//	$this->render('fbimage', array('logoutUrl' => $logoutUrl, 'images' => $images));
	//}
	
	public function actionFBLogin(){
		include_once DIR_ROOT.'/protected/extensions/facebook/lib/facebook.php';
		$fb = new Facebook(array(
			'appId'  => FB_APPID,
			'secret' => FB_SECRET,
			'cookie' => FB_COOKIE
			));
		
		try 
		{ 
			$fbuser = $fb->getUser();
			$me = $fb->api('/me');
			$permissions = $fb->api("/me/permissions");
			
			//Cause fb parse state parameters to setup peristent data
			//Inactive token error happen when dont access to fb right now
		} 
		catch(FacebookApiException $e)
		{
			
		}
		
		$this->redirect('/profile/fbimage');		
	}
	
	public function actionFbImport(){
		if($_POST){
			$userid = Yii::app()->user->id;
			$profile = Yii::app()->user->Profile;
			$existingFbImages = $this->getFbImages();
			
			$res = array();
			$tmpPath = dirname(DIR_ROOT).'/tmp_fb/';
			CHelperFile::createDir($tmpPath);
			//$res['DIR_ROOT'] = DIR_ROOT;
			//$res['tempPath'] = $tmpPath;
			$img = new Img();
			foreach($_POST as $key => $value){
				if($key == 'fbphoto' && gettype($value)=='array'){
					foreach($value as $vkey => $url){					
						
						if(!$this->checkFbImageExist($existingFbImages, basename($url))){
							$filename = $tmpPath.basename($url);
							//$res[basename($url)] = $filename;
							if(file_put_contents($filename, file_get_contents($url))){
								
								$tmpName = $img->saveUploaded($filename, Yii::app()->user->id);
								
								if ($tmpName){
									$n = Yii::app()->user->Profile->imgAdd($tmpName, basename($filename));
								}
								else{
									@unlink($filename);
								}
							}							
						}
					}
				}
			}
			//echo json_encode($res);
			Yii::app()->end();
		}
	}
		
	public function actionFBLogout(){	
		include_once DIR_ROOT.'/protected/extensions/facebook/lib/facebook.php';
		$fb = new Facebook(array(
			'appId'  => FB_APPID,
			'secret' => FB_SECRET,
			'cookie' => FB_COOKIE
			));
		$fb->destroySession();
		$authIdentity = Yii::app()->eauth->getIdentity('facebook');
		$authIdentity->destroySession();
		$this->redirect('/profile/fbimage');
	}
	
	
	/**
	 * edit profile by owner
	 */
	public function actionEdit()
	{
		$this->layout='//layouts/dashboard';
		$this->render('edit', array('profile'=>Yii::app()->user->Profile));
	}
	
	public function actionEditMainTab()
	{

		$model = new UserEditMainTabForm;
		
		if ( isset($_POST['ajax']) )
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
		
		if(isset($_POST['UserEditMainTabForm']))
		{
			$model->attributes = $_POST['UserEditMainTabForm'];
			if($model->validate())
			{
				$model->saveMain();
				Yii::app()->end();
			}
		}    
		
		echo CActiveForm::validate($model);
		Yii::app()->end();              
	}

	public function actionEditAppearanceTab()
	{

		$model = new UserEditAppearanceTabForm;
		
		if ( isset($_POST['ajax']) )
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
		
		if(isset($_POST['UserEditAppearanceTabForm']))
		{
			$model->attributes = $_POST['UserEditAppearanceTabForm'];
			if($model->validate())
			{
				$model->saveAppearance();
				Yii::app()->end();
			}
		}    
		
		echo CActiveForm::validate($model);
		Yii::app()->end();              
	}

	/*public function actionEditLifestyleTab()
	{

		$model = new UserEditLifestyleTabForm;
	     
	       if ( isset($_POST['ajax']) )
	       {
	           echo CActiveForm::validate($model);
	           Yii::app()->end();
	       }
	               
	       if(isset($_POST['UserEditLifestyleTabForm']))
	       {
	           $model->attributes = $_POST['UserEditLifestyleTabForm'];
	           if($model->validate())
	           {
	               $model->saveLifestyle();
	               Yii::app()->end();
	           }
	       }    
	       
	       echo CActiveForm::validate($model);
	       Yii::app()->end();              
	}    */
	
	public function actionEditBadgetsTab()
	{
		$model = new UserEditBadgets;
		
		if ( isset($_POST['ajax']) )
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
		
		if(isset($_POST['UserEditBadgets']))
		{
			$model->attributes = $_POST['UserEditBadgets'];
			if($model->validate())
			{
				$model->saveBadgets();
				Yii::app()->end();
			}
		}
		
		echo CActiveForm::validate($model);
		Yii::app()->end();              
	}    
	
	
	
	
	
	
	
	/**
	 * edit profile by owner
	 */
	public function actionAccountSettings()
	{
		//$this->layout='//layouts/one-column';
		$this->layout = '//layouts/dashboard' ;
		$this->render('accountSettings', array('profile'=>Yii::app()->user->Profile));
	}
	/**
	 * change location
	 */
	public function actionLocation()
	{
		//FB::info($_POST,'UserLocationForm');

		$model = new UserLocationForm;
		
		if ( isset($_POST['ajax']) && $_POST['ajax']==='profile-location' )
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
		
		if(isset($_POST['UserLocationForm']))
		{
			$model->attributes = $_POST['UserLocationForm'];
			if($model->validate())
			{
				$model->locationUpdate();
				Yii::app()->end();
			}
		}        
		
	}

	public function actionLike()
	{
		$resTxt = Yii::app()->user->Profile->Like( Yii::app()->secur->decryptID($_POST['id']), $_POST['like'], $_POST['g'] );
		echo "<div class='liked'>".$resTxt."</div>";
		Yii::app()->end(); 
	} 
	
	/**
	 * change username
	 */
	public function actionUserName()
	{
		$model = new UserNameForm;
		echo CActiveForm::validate($model);
		Yii::app()->end();
	}
	
	/**
	 * validate Personal info
	 */
	public function actionPersonal()
	{
		$model = new UserPersonalForm;
		echo CActiveForm::validate($model);
		Yii::app()->end();
	}
	
	
	/**
	 * change Settings
	 */
	public function actionSettings()
	{
		$model = new UserSettingsForm;
		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='yw0')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['UserSettingsForm']))
		{
			$model->attributes=$_POST['UserSettingsForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate())
				$model->saveSettings();
		}
		echo CActiveForm::validate($model);
		// display the login form
		Yii::app()->end();      
		
		
	}    
	
	
	
	

	public function actionDashboardMessage()
	{
		$text = trim($_POST['message']);
		$id = Updates::addUpdate(1, Yii::app()->user->id, $text);
		
		if ($id)
		{
			$sql = "SELECT u.id, up.kod, up.added, up.text FROM profile_updates as up, users as u WHERE "; 
			
			$sql .= " up.user_id=u.id AND up.id=".$id;
			
			$sql .= " LIMIT 1";
			
			$up = Yii::app()->db->createCommand($sql)->queryAll();        
			
			$this->widget('application.components.dashboard.PanelDashboardUpdatesBoxWidget',  array('update'=>$up[0]));            
		}

		Yii::app()->end(); 
	}

	public function actionUserPrivateMessage()
	{
		$model = new MessageSendForm;
		if ( isset($_POST['MessageSendForm']) && isset($_POST['ajax']) )
		{
			//FB::warn($_POST, 'actionUserPrivateMessage VALIDATION');
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
		
		if(isset($_POST['MessageSendForm']))
		{
			//FB::error($_POST, 'actionUserPrivateMessage SENDING');
			$model->attributes = $_POST['MessageSendForm'];
			if($model->validate())
			{
				$model->doSend();
				//Yii::app()->end();
			}
		}        
		
	}

	public function actionUserPrivateMessageVideo()
	{
		$idTo = Yii::app()->secur->decryptID($_POST['userIdTo']);
		$streamname = $_POST['streamName'];

		if ($idTo && $streamname)
		{
			$res = Messages::addPrivateMessageVideo($idTo, $streamname);
			
			if ($res)
				echo json_encode(array('success'=>'Yes')); 
			
		}
		

		Yii::app()->end();
		
		
		
		/*
		FB::warn($_POST, 'actionUserPrivateMessageVideo');
		        $model = new MessageVideoSendForm;
		        if ( isset($_POST['MessageVideoSendForm']) && isset($_POST['ajax']) )
		        {
		            echo CActiveForm::validate($model);
		            Yii::app()->end();
		        }
		                
		        if(isset($_POST['MessageVideoSendForm']))
		        {
		            $model->attributes = $_POST['MessageVideoSendForm'];
		            if($model->validate())
		            {
		                $model->doSend();
		                //Yii::app()->end();
		            }
		        }*/
		Yii::app()->end();
	}    


	public function actionImagesBox()
	{
		$this->render('imagesBox');
	}
	
	
	
	
	
}