<?php

class CHelperAssets
{
	
	static public function clearOld($nameLike)
	{
		$files = CHelperFile::findFiles(DIR_ROOT.'/assets', array('level'=>0));
		foreach ($files as $f)
			if (is_file($f))
				if (stristr($f, $nameLike))
					@unlink($f);
		
	}


	/**
	 * @types 'site', 'admin', 'ie' , 'ie9'
	 */
	static function cssUrl($type='site', $compress=true, $combine=true)
	{
		switch($type)
		{
			case 'site': 
				$prefix = 'css';
				
				$css =  array(
					"/css/utf8.css",    
					//"/css/font.css",
					"/css/ui/jquery-ui.css",
					//"/css/bubbles.css",
					"/css/bootstrap.css",
					"/css/forms.css",
					"/css/main.css",
					"/css/content.css",
					//"/css/payment.css",
					//"/css/allin1.css",                
					"/css/footer.css",           
					"/css/profile.css",     
					"/css/sidebar.css",
					);                               
				break;

			case 'payment':
				$prefix = 'cssPayment';
				
				$css = array(
					"/css/payment.css"
					);              
				break;
			case 'search':				
				$prefix = 'cssSearch';
				
				$css = array(
					"/css/search.css"
					); 
				break;          

			case 'allin1':
				$prefix = 'cssAllin1';
				
				$css = array(
					"/css/allin1.css"
					);              
				break;        

			case 'allin1Mobile':
				$prefix = 'cssAllin1';
				
				$css = array(
					"/css/allin1_mobile.css"
					);              
				break; 

			case 'allin1Tablet':
				$prefix = 'cssAllin1';
				
				$css = array(
					"/css/allin1_tablet.css"
					);              
				break;         
				

				
			case 'allin9':
				$prefix = 'cssAllin9';
				
				$css = array(
					"/css/allin9.css"
					);              
				break;        

			case 'allin9Mobile':
				$prefix = 'cssAllin9';
				
				$css = array(
					"/css/allin9_mobile.css"
					);              
				break; 

			case 'allin9Tablet':
				$prefix = 'cssAllin9';
				
				$css = array(
					"/css/allin9_tablet.css"
					);              
				break;
				
			//landingCams
			case 'cams':
				$prefix = 'cssCams';
			
				$css = array(
						"/css/landingcams.css"
				);
				break;
				
			case 'camsMobile':
				$prefix = 'cssCamsMobile';
			
				$css = array(
					"/css/landingcams_mobile.css"
				);
				break;
				
			case 'camsTablet':
				$prefix = 'cssCamsTablet';
				
				$css = array(
					"/css/landingcams_tablet.css"
				);
				break;				
				
				
			case 'ie':
				$prefix = 'cssIE';
				
				$css = array(
					"/css/ie.css"
					);              
				break;   

			case 'ie9':
				$prefix = 'cssIE9';
				
				$css = array(
					"/css/ie9.css"
					);              
				break;
			
			case 'admin':
				$prefix = 'cssAdmin';

				$css = array(
					"/protected/modules/admin/css/admin.css",
					"/protected/modules/admin/css/adminform.css"
				);           
				break;
		}

		$mkeyBlocked = "{$prefix}_blocked";
		$blocked = Yii::app()->cache->get($mkeyBlocked);
		
		if ($combine && $blocked===false)
		{
			$mkey = "{$prefix}";
			$cssCache=Yii::app()->cache->get($mkey);            
			$noCacheUse = true;//was a BUG if more than 1 node...            
			if ( LOCAL || $noCacheUse || $cssCache===false )  
			{
				foreach ($css as $f)
					$versions[] = filemtime(DIR_ROOT.$f);                    

				rsort($versions);
				
				$version = $versions[0];                

				$css_all     = DIR_ROOT."/assets/{$prefix}.".$version.".css";
				$css_all_min = DIR_ROOT."/assets/{$prefix}.".$version.".min.css";
				
				if (!file_exists($css_all_min) || !filesize($css_all_min))
				{
					Yii::app()->cache->set($mkeyBlocked, '1', 10);
					
					//delete prior version
					self::clearOld("{$prefix}.");
					
					$cmd = "cat " .DIR_ROOT.implode(' '.DIR_ROOT, $css) . " > ".$css_all;
					@exec($cmd);
					
					if ($compress)
					{
						//$cmd = "/bin/nice -n 19 java -jar ".DIR_ROOT."/protected/vendors/utils/yuicompressor-2.4.7.jar"." -v -o ".$css_all_min." ".$css_all;
						$cmd = "java -jar ".DIR_ROOT."/protected/vendors/utils/yuicompressor-2.4.7.jar"." -v -o ".$css_all_min." ".$css_all;
						@exec($cmd);
					}
					
					if (!file_exists($css_all_min) || !filesize($css_all_min))
						@rename($css_all, $css_all_min);
					else
						@unlink($css_all);
					
					@chmod($css_all_min, 0777);
				}
				
				$css = array( "/assets/{$prefix}.".$version.".min.css" );
				Yii::app()->cache->set($mkey, $css, 60);
				
				
				Yii::app()->cache->delete($mkeyBlocked);
			}
			else
				$css = $cssCache;
		}
		
		foreach ($css as $j)
			echo '<link rel="stylesheet" type="text/css" href="'.$j.'" media="all" />';
	}








	/**
	 * @types 'site', 'admin', 'ie' 
	 */
	static function jsUrl($type='site', $compress=true, $combine=true)
	{
		switch($type)
		{
			case 'site': 
				$prefix = 'js';
				
				$js =  array(
					"/js/jquery_min.js",
					"/js/jquery-ui_full_min.js",
					
					"/js/bootstrap.js",
					
					//                    "/js/jquery.placehold-0.2.min.js",
					//                    "/js/modernizr-1.7.min.js",
					//                    "/js/html5compatible.js",
					
					"/js/jquery.yiiactiveformSw.js",
					"/js/main.js",                    
					
					"/js/forms.js",
					
					"/js/allin1.js",
					"/js/allin9.js",
					"/js/payment.js",
					"/js/debug.js",
					"/js/main2.js",
					"/js/profile.js",
					);                  
				break;
			
			//landingCams
			case 'cams':
				$prefix = 'jsCams';				
				$js = array(
					"/js/landingcams.js"
				);             
				break;
				
			case 'search':
				$prefix = 'jsSearch';				
				$js = array(
					"/js/search.js"
					);             
				break;
			
			case 'ie':
				$prefix = 'jsIE';
				
				$js = array(
					"/css/pie/PIE.js",
					"/js/ie.js",
					);             
				break;
				   
            case 'ie10':
                $prefix = 'jsIE10';
                    
                $js = array(
                    "/js/ie/bootstrap-transition_ie10.js",
                );             
                break; 
                			
			case 'admin':
				$prefix = 'jsAdmin';
				
				$js = array(
					"/protected/modules/admin/js/admin.js",
					"/protected/modules/admin/js/dc.js",
					);   
				break;
		}

		$mkeyBlocked = "{$prefix}_blocked";
		$blocked = Yii::app()->cache->get($mkeyBlocked);

		if ($combine && $blocked===false)
		{
			$mkey = "{$prefix}";
			$jsCache = Yii::app()->cache->get($mkey);
			
			$noCacheUse = true;//was a BUG if more than 1 node...           
			
			if ( LOCAL || $noCacheUse || $jsCache===false )  
			{
				foreach ($js as $f)
					$versions[] = filemtime(DIR_ROOT.$f);                    

				rsort($versions);
				
				$version = $versions[0];
				

				$js_all = DIR_ROOT."/assets/{$prefix}.".$version.".js";
				$js_all_min = DIR_ROOT."/assets/{$prefix}.".$version.".min.js";
				
				if (!file_exists($js_all_min) || !filesize($js_all_min))
				{
					Yii::app()->cache->set($mkeyBlocked, '1', 10);
					
					//delete prior version
					self::clearOld("{$prefix}.");
					
					$cmd = "cat " .DIR_ROOT.implode(' '.DIR_ROOT, $js) . " > ".$js_all;
					@exec($cmd);
					
					/*if ($compress)
					{
					    got problems in Safari and chrome using compiler.jar ... 
					    $cmd = "java -jar ".DIR_ROOT."/protected/vendors/utils/compiler.jar"." --js ".$js_all." --js_output_file ".$js_all_min;
					    exec($cmd);                        
					}*/
					
					if ($compress)
					{
						//$cmd = "/bin/nice -n 19 java -jar ".DIR_ROOT."/protected/vendors/utils/yuicompressor-2.4.7.jar ".$js_all." -o ".$js_all_min." --charset utf-8";// --compilation_level SIMPLE_OPTIMIZATIONS --js ".$js_all." --js_output_file ".$js_all_min;
						$cmd = "java -jar ".DIR_ROOT."/protected/vendors/utils/yuicompressor-2.4.7.jar ".$js_all." -o ".$js_all_min." --charset utf-8";// --compilation_level SIMPLE_OPTIMIZATIONS --js ".$js_all." --js_output_file ".$js_all_min;
						@exec($cmd);                        
					}

					
					if (!file_exists($js_all_min) || !filesize($js_all_min))
						@rename($js_all, $js_all_min);
					else
						@unlink($js_all);
					
					@chmod($js_all_min, 0777);
				}
				
				$js = array( "/assets/{$prefix}.".$version.".min.js" );
				Yii::app()->cache->set($mkey, $js, 60);
				
				
				Yii::app()->cache->delete($mkeyBlocked);                                
			}
			else
				$js = $jsCache;
		}
		
		foreach ($js as $j)
			echo '<script type="text/javascript" src="'.$j.'"></script>';
	}

	/**
	 * Publish Fonts
	 */
	/*static function fontsPublish()
	{
	    $assets = DIR_ROOT . '//fonts';
	    return Yii::app()->assetManager->publish($assets);
	}*/
	
}