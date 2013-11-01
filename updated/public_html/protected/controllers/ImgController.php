<?php

class ImgController extends Controller
{
	public function init()
	{
		parent::init();
		$this->layout='//layouts/img';
	}    
	
	public function actionIndex()
	{
		$model = new Img;
		$img = $model->get($_GET['id'], $_GET['n'], $_GET['size']);
		$this->render('index', array('img'=>$img));
	}
	
	
	public function actionUploadProfile()
	{
		//$model = new UserImgUploadForm;
		//
		//$model->attributes=$_POST;
		//
		//$model->image=CUploadedFile::getInstance($model,'image');
		//if ($model->image || true)
		//{
		$temp_qq_name = $_FILES['qqfile']['tmp_name'];
			if ($userId = Yii::app()->user->id)
			{
				$path = CHelperProfile::getUserImgDir($userId);
				if (CHelperFile::createDir($path))
				{
					$tmpFile = $path .'/tmp_' . rand(11111111, 99999999);				
					
					if (@move_uploaded_file($temp_qq_name, $tmpFile))
					{
						$img = new Img();
						$tmpName = $img->saveUploaded($tmpFile, Yii::app()->user->id);
						//CHelperSite::vd($tmpName);            	
						if ($tmpName)
							$n = Yii::app()->user->Profile->imgAdd($tmpName);
						else
							@unlink($tmpFile);
					}					
				}					
			//}
		}
		
		//There is a problem here: $profile->imgUrl('small',$res, false) -> return noimage. 
		//I think Profile->imgAdd should update list of image?
		
		
		
		$profile = Yii::app()->user->Profile;
		
		$imgIndx = $profile->imgGetIndx($n);
		$data = array('success' => true, 
					'smallUrl' => $profile->imgUrl('small',$imgIndx, false),
					'bigUrl' => $profile->imgUrl('big',$imgIndx, false),
					'picId' => $n);
		echo CJavaScript::jsonEncode($data);
		Yii::app()->end();
	}    
	
	public function actionUpload()
	{
		$model = new UserImgUploadForm;
		if (isset($_POST['UserImgUploadForm']))
		{
			$model->attributes=$_POST['UserImgUploadForm'];
			
			$model->image=CUploadedFile::getInstance($model,'image');
			if ($model->image)
			{
				/*
				CHelperSite::vd($model->image);
				object(CUploadedFile)#38 (7) {
				  ["_name":"CUploadedFile":private]=>
				  string(5) "3.gif"
				  ["_tempName":"CUploadedFile":private]=>
				  string(14) "/tmp/phpLCVhgh"
				  ["_type":"CUploadedFile":private]=>
				  string(9) "image/gif"
				  ["_size":"CUploadedFile":private]=>
				  int(9641)
				  ["_error":"CUploadedFile":private]=>
				  int(0)
				  ["_e":"CComponent":private]=>
				  NULL
				  ["_m":"CComponent":private]=>
				  NULL
				}

				 */		
				if ($userId = Yii::app()->user->id)
				{
					$path = CHelperProfile::getUserImgDir($userId);
					if (CHelperFile::createDir($path))
					{
						$tmpFile = $path .'/tmp_' . rand(11111111, 99999999);				
						
						if (@move_uploaded_file($model->image->getTempName(), $tmpFile))
						{
							$img = new Img();
							$tmpName = $img->saveUploaded($tmpFile, Yii::app()->user->id);
							//CHelperSite::vd($tmpName);            	
							if ($tmpName)
								$res = Yii::app()->user->Profile->imgAdd($tmpName);
							else
								@unlink($tmpFile);
						}					
					}					
				}
			}
			
			$this->redirect(Yii::app()->createUrl('profile/imagesBox'));            
		}

		Yii::app()->end();
	}    

	public function actionDel()
	{
		/*$i = intval($_GET['n']);
		        
		if ($i>=0 && $i<9)
		{
		    Yii::app()->user->Profile->imgDel($i);
		}
		        
		$this->redirect(Yii::app()->createUrl('profile/myVideos'));*/
	}    

	public function actionSetPrimary()
	{
		$n = intval($_GET['n']);
		Yii::app()->user->Profile->imageUpdate($n, 'primary', '1');//Yii::app()->user->Profile->infoUpdate('img_primary', $_GET['n']);
		$this->redirect(Yii::app()->createUrl('profile'));
	}    
	
	/*These methods are used for test*/
		
	public function actionTest(){
		$model = new QuizzImg;
		$img = $model->get($_GET['id'], $_GET['name']);	
		$this->render('index', array('img'=>$img));	
	}
	
	public function actionUploadTest()
	{
		$temp_qq_name = $_FILES['qqfile']['tmp_name'];
		if ($userId = Yii::app()->user->id)
		{
			$model = new QuizzImg;			
			$fullpath = $model->getUniqueFilename($userId, $_FILES['qqfile']['name'], $path, $basename);	
			
			if (CHelperFile::createDir(dirname($fullpath)))
			{	
				if (@move_uploaded_file($temp_qq_name, $fullpath))
				{
				}					
			}	
			echo CJavaScript::jsonEncode(array('success'=>true, 'path' => '/img/test/'.Yii::app()->secur->encryptID($userId).'/'.$basename));		
		}	
		Yii::app()->end();
	} 
		
	/*These methods are used for fan_girl*/
	
	public function actionFanSign(){
		$id = $_GET['id'];
		$img = FanProfile::getImg($id);
		$this->render('index', array('img'=>$img));
	}
	
	public function actionUploadFanSign()
	{
		$temp_qq_name = $_FILES['qqfile']['tmp_name'];
		if ($userId = Yii::app()->user->id)
		{	
			$fullpath = FanProfile::getImgPath($userId);
			
			if (CHelperFile::createDir(dirname($fullpath)))
			{	
				if (@move_uploaded_file($temp_qq_name, $fullpath))
				{
					$fan = FanProfile::createFanProfile();
					$fan->updateSign(Yii::app()->secur->encryptID($userId));
				}					
			}	
			echo CJavaScript::jsonEncode(array('success'=>true, 'path' => '/img/fansign/'.Yii::app()->secur->encryptID($userId)));		
		}	
		Yii::app()->end();
	} 
}