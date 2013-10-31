<?php

class Img
{
    private $sizesAvailable;// = array('smaller','small','medium','big', '152x86', '84x47', 'big2','biger','original');
    
    //adaptiveThumb, resizeCanvas, resize
    private $sizes = array(
        //'smaller' => array(40,40,'adaptiveThumb'),
        //'small' => array(48,48,'adaptiveThumb'),
        'big' => array(200,200,'thumb', 90),
        //'big' => array(150,150,'adaptiveThumb'),
        'small' => array(32,32,'adaptiveThumb', 75),
        'medium'  => array(82,82,'adaptiveThumb', 75),
        //'big2' => array(160,200,'adaptiveThumb'),
        //'biger' => array(320,310,'adaptiveThumb'),
        'original' => array(2000,2000,'thumb', 90),//resizeCanvas//'original' => array(640,360,'resize'),//resizeCanvas
    );
    private $imgAvailableTypes = array("image/jpeg", "image/jpg", "image/jpe", "image/pjpeg", "image/pjpg", "image/x-jpeg", "x-jpg", "image/gif", "image/x-gif", "image/png", "image/x-png",		"image/tiff", "image/x-tiff",		"image/bmp", "image/x-bmp", "image/x-bitmap", "image/x-xbitmap", "image/x-win-bitmap", "image/x-windows-bmp", "image/ms-bmp", "image/x-ms-bmp", "application/bmp", "application/x-bmp", "application/x-win-bitmap" );

    
    
    /**
     * constructor
     */
	public function __construct()
	{
        foreach ($this->sizes as $k=>$v)
        {
            $this->sizesAvailable[] = $k;
        }
       
	}
	
	/*
	 * store image into user media dir after uploaded
	 * return name as time() or FALSE if error
	 */
	public function saveUploaded($imgFile, $userId)
	{
//FB::error(imgFile .'-'. $imgSize .'-'. $userId);

        if (!$imgFile || !$userId)
        	return false;

		$path = CHelperProfile::getUserImgDir($userId);
		if (!CHelperFile::createDir($path))//not dublicate, will use for import
			return false;			
		
        $filename = time();        
        $fileOriginal = $path.'/'.$filename;
        
        //needs for import...
        if (file_exists($fileOriginal))
        	while(file_exists($fileOriginal))
        	{
		        $filename++;        
		        $fileOriginal = $path.'/'.$filename;
        	}
        

		if (!@rename($imgFile, $fileOriginal))
			return false;
        
        
        @chmod($fileOriginal, 0777);
        	
        $image = @getimagesize($fileOriginal);
		$width = $image[0];
	    $height = $image[1];
	                
        if ( !in_array($image['mime'], $this->imgAvailableTypes) )
            return false;


        if (stristr($image['mime'], 'png'))
            $toFormat = 3;//IMG_PNG;
        elseif(stristr($image['mime'], 'gif'))
            $toFormat = 1;//IMG_GIF;
        elseif(stristr($image['mime'], 'tiff'))
            $toFormat = 4;//tiff;
        elseif(stristr($image['mime'], 'bmp'))
            $toFormat = 5;//bmp;
        else
            $toFormat = 2;//IMG_JPEG;
        
        $fileSize = filesize($fileOriginal);
        
        if ($fileSize<IMG_SIZE_MIN || $fileSize>IMG_SIZE_MAX)
            return false;    
//FB::info($this->sizes);

//FB::warn($toFormat, '$toFormat');

			
		$fileOriginalTMP = $path.'/tmp_'.$filename;

		
		switch($toFormat)
		{
			case 1: //needs for transparent gif
					$input = imagecreatefromgif($fileOriginal);
	                //list($width, $height) = getimagesize($fileOriginal);
	                $output = imagecreatetruecolor($width, $height);
	                $white = imagecolorallocate($output,  255, 255, 255);
	                imagefilledrectangle($output, 0, 0, $width, $height, $white);
	                imagecopy($output, $input, 0, 0, 0, 0, $width, $height);
	                imagejpeg($output, $fileOriginalTMP, 90);
					break;				
				
				
			case 2: copy ($fileOriginal, $fileOriginalTMP);	
					break;
					
			case 3: //needs for transparent png
					$input = imagecreatefrompng($fileOriginal);
	                //list($width, $height) = getimagesize($fileOriginal);
	                $output = imagecreatetruecolor($width, $height);
	                $white = imagecolorallocate($output,  255, 255, 255);
	                imagefilledrectangle($output, 0, 0, $width, $height, $white);
	                imagecopy($output, $input, 0, 0, 0, 0, $width, $height);
	                imagejpeg($output, $fileOriginalTMP, 90);
					break;
					
			case 4:
			case 5: $fileOriginalTMP = $fileOriginalTMP.'.jpg';//!!! +.jpg
		        	
		        	$im = new Imagick($fileOriginal);
					$im->setImageColorspace(255); 
					$im->setCompression(Imagick::COMPRESSION_JPEG);
					$im->setCompressionQuality(100);
					$im->setImageFormat('jpeg');
					//resize
					/////$im->resizeImage($this->sizes[$size][0], $this->sizes[$size][1], imagick::FILTER_LANCZOS, 1); 
					//write image on server
					$im->writeImage($fileOriginalTMP);
					$im->clear();
					$im->destroy(); 
					break;
			
			default: return false;            
		}
		
		if ( !file_exists($fileOriginalTMP) )
			return false;	
		
		//IMPORT IMAGE EXTENSION
		//Yii::import('application.extensions.image.Image');
		$ih = new CImageHandler();
			
		$toFormatTHUMB = 2;//(($toFormat>3) ? 2 : $toFormat);
			
        foreach ($this->sizes as $size=>$v)
        {
			/*if (!CHelperFile::createDir($path.'/'.$size))
				return false;*/
            
            $thumb = $path.'/'. $filename . '_'.$size.'.jpg';
			
            $ih->load($fileOriginalTMP);
        	switch($this->sizes[$size][2])
            {
            	case 'adaptiveThumb':
                        $ih->adaptiveThumb($this->sizes[$size][0],$this->sizes[$size][1]);
                        break; 
            	
				case 'thumb':
						$ih->thumb($this->sizes[$size][0],$this->sizes[$size][1]);
                        break;
                        
            	case 'resize':
                        $ih->resize($this->sizes[$size][0],$this->sizes[$size][1]);
                        break;             		
			}
			$ih->save($thumb, $toFormatTHUMB, $this->sizes[$size][3]);
            
			if (!file_exists($thumb))
				return false;
			
			@chmod($thumb, 0777);             
        }
        
		if (file_exists($fileOriginalTMP))        
			@unlink($fileOriginalTMP);
        

        return $filename;        	
	}
	
    
    /**
     * return array(full path to the image, imgInfo)
     */
	public function get($id, $n=1, $size='')
	{
        if (!$userId = Yii::app()->secur->decryptID($id))
            return false;

        if ( !in_array($size, $this->sizesAvailable) ) 
            $size = $this->sizesAvailable[2];//small
        
        $mkey = "img_{$userId}_{$n}_{$size}";
        $res = Yii::app()->cache->get($mkey);  
        if ( $res === false )
        {
			$dir = Yii::app()->helperProfile->getUserImgDir($userId);//$this->_getDirPath($userId);            
            $res = $this->_getFile($dir, $n, $size, $userId);
                
            if (!$res['imgPath'])
            {
                // + do ERROR LOG
                $profile = new Profile($userId);
                $mf = ($profile->getDataValue('gender') == 'M') ? 'male' : 'female';

                $file = DIR_ROOT. "/images/design/nophoto_{$mf}_{$size}.jpg";
                $imgInfo = getimagesize($file);
                $filemtime = filemtime($file);
                    
                $res = array('imgPath'=>$file, 'imgInfo'=>$imgInfo, 'filemtime'=>$filemtime);
                
                Yii::app()->cache->set($mkey, $res, 300);
            }
            else            
            {
                Yii::app()->cache->set($mkey, $res);
            }
        }
        
        return $res;
	}
    
    /**
     * return array(full path to the image, imgInfo)
     * if image not exist then create
     */
    private function _getFile($dir, $n, $size, $userId=0)
    {
        $file = $dir.'/'.$n.'_'.$size.'.jpg';
//FB::error($file);    
        if ( file_exists($file) && is_file($file) )
        {
            $imgInfo = getimagesize($file);
            $filemtime = filemtime($file);
        }
        else
        {
            $file = "";
            $imgInfo = false;
            $filemtime = false;
        }
        
        return array('imgPath'=>$file, 'imgInfo'=>$imgInfo, 'filemtime'=>$filemtime);
    }
    
    
    /**
     * for JW Player ...
     */
    public function getImgPath($userId, $n, $size='original')
    {
        $dir = $this->_getDirPath($userId);
        return $dir.'/'.$n.'_'.$size.'.jpg';
    }
    
    public function saveProfileImage($userId, $tmpName, $n)
    {
        if (!$userId || !$tmpName || !$n)
            return false;
       
        $path = CHelperProfile::getUserImgDir($userId);
        
        foreach ($this->sizes as $size=>$v)
        {
        	$fileSour = $path.'/'.$tmpName.'_'.$size.'.jpg';
        	$fileDest = $path.'/'.$n.'_'.$size.'.jpg';

        	if (!@rename($fileSour, $fileDest))
        		return false;
        }
		
        @rename($path.'/'.$tmpName, $path.'/'.$n);//image uploaded by user
        
        return true;
    }

    
    /**
     * delete image
     */
    public function delProfileImg($userId, $n)
    {
        $dir = Yii::app()->helperProfile->getUserImgDir($userId);
        
        foreach($this->sizesAvailable as $s)
        {
            $file = $dir.'/'.$n.'_'.$s.'.jpg';
            if (file_exists($file))
                @unlink($file);
        }
        
        //delete original
        $file = $dir.'/'.$n;
        @unlink($file);
    }    

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    //ext FB images
    /**
     * load FB profile image
     */
    public function prepareExtFBImage($userId)
    {
        if (!$userId)
            return;
            
        $profile = new Profile($userId);
        $fb_id = $profile->getDataValue('ext', 'facebook', 'fb_id');                   
       
        if (!$fb_id)
            return;
        
        $imgDir = $this->createDirPath($userId);
        
        $img = @file_get_contents('https://graph.facebook.com/'.$fb_id.'/picture?type=large');
        $file = $imgDir.'/extFB_original.jpg';
        @file_put_contents($file, $img);        
        
        if (file_exists($file))
        {
             CHelperImage::image2GrayColor( $file, $file );
             @chmod($file, 0777);
             
             $size = getimagesize($file);
             
             $image = array(
                'name'=>$file,
                'type'=>$size['mime'],
                'size'=>filesize($file)
             );
//CHelperSite::vd($image);             
        }
        
        return $image;
    }
}