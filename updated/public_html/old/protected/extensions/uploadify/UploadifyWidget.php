<?php 
Yii::import('zii.widgets.jui.CJuiInputWidget');
/**
 * Uploadify extension for Yii.
 */
class UploadifyWidget extends CInputWidget 
{

	public $baseUrl;
    public $fileExt='';
    public $fileDesc='';
    public $callbackUrl;
    
    public $onOpen;
    public $onCancel;
    public $onComplete = 'window.location.reload()';

	/**
	 * Publishes the required assets
	 */
	public function init() 
    {
		parent::init();
		
        $this->publishAssets();        
        
        if ($this->fileExt)
        {
            $this->fileExt = str_replace(', ', ',', $this->fileExt);
            $formats = explode(',', $this->fileExt);
            $this->fileExt = '*.'.implode(';*.', $formats ).';';
            $this->fileDesc =  'Video files ('. '*.'.implode('; *.', $formats ).';' .')';
FB::error($this->fileExt, '$this->fileExt');
FB::error($this->fileDesc, '$this->fileDesc');
//fileDesc=Image Files (.JPG, .GIF, .PNG)
//fileExt=*.jpg;*.gif;*.png            
        }

	}

	/**
	 * Generates the required HTML and Javascript
	 */
	public function run() {
        $this->render('Uploadify', array('baseUrl'=>$this->baseUrl, 'fileDesc'=>$this->fileDesc, 'fileExt'=>$this->fileExt, 'callbackUrl'=>$this->callbackUrl, 'onCancel'=>$this->onCancel, 'onComplete'=>$this->onComplete, 'onOpen'=>$this->onOpen) );
	}

	/**
	 * Publises and registers the required FLASH, CSS and Javascript
	 * @throws CHttpException if the assets folder was not found
	 */
	public function publishAssets() {
		$assets = dirname(__FILE__) . '/uploadify';
		$this->baseUrl = Yii::app()->assetManager->publish($assets);
		if (is_dir($assets)) {
		      
            //дублировать!!!
            /*Yii::app()->clientScript->registerScriptFile(
                Yii::app()->assetManager->publish(
                    Yii::app()->basePath.'/../js/jquery_min.js',//
                    true
                )
            );*/
            //Yii::app()->clientScript->registerScriptFile($this->baseUrl . '/js/jquery_min.js');
            
            
                    
            Yii::app()->clientScript->registerScriptFile($this->baseUrl . '/swfobject.js', CClientScript::POS_END/*, CClientScript::POS_HEAD*/);
            Yii::app()->clientScript->registerScriptFile($this->baseUrl . '/jquery.uploadify.v2.1.4.min.js', CClientScript::POS_END/*, CClientScript::POS_HEAD*/);
            ///Yii::app()->clientScript->registerCssFile($this->baseUrl . '/uploadify.css');
		} else {
			throw new CHttpException(500, 'UploadifyWidget - Error: Couldn\'t find assets to publish.');
		}
	}
}