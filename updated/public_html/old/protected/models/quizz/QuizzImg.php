<?php
class QuizzImg
{
	public function get($id, $basename)
	{
		if (!$userId = Yii::app()->secur->decryptID($id)){
			return false;
		}
		$dir = Yii::app()->helperProfile->getUserQuizzImgDir($userId);
		$file = $dir.'/'.$basename;
		
		if(!file_exists($file)){
			$file = dirname(__FILE__).'/../../../images/img/blank.gif';
		}
		return array('imgPath'=>$file, 'imgInfo'=>getimagesize($file), 'filemtime'=>filemtime($file));
	}
		
	public function getUniqueFilename($userid, $filename, &$path = '', &$basename = ''){
		$path = CHelperProfile::getUserQuizzImgDir($userid);		
		$pathinfo = pathinfo($filename);
		$name = $pathinfo['filename'];
		$ext = $pathinfo['extension'];
		while (file_exists("$path/$name.$ext")) {
			$name .= ('_'.(rand()+1000));
		}
		$basename = "$name.$ext";
		return "$path/$basename";
	}
}
