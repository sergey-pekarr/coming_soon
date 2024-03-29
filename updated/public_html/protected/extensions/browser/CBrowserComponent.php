<?php
	Yii::import('application.extensions.browser.Browser');
	class CBrowserComponent extends CApplicationComponent
	{
		private $_myBrowser;
		public function init() {}
		public function __construct()
		{
			$this->_myBrowser = new Browser();
		}

		/**
		* Call a Browser function
		*
		* @return string
		*/
		public function __call($method, $params)
		{
			if (is_object($this->_myBrowser) && get_class($this->_myBrowser)==='Browser') return call_user_func_array(array($this->_myBrowser, $method), $params);
			else throw new CException(Yii::t('Browser', 'Can not call a method of a non existent object'));
		}
        
        
        
        
        
        
        public function isIE9()
        {
            if ($this->_myBrowser->getBrowser()=='Internet Explorer' && intval($this->_myBrowser->getVersion())>=9 && intval($this->_myBrowser->getVersion())<10)
                return true;
            else
                return false;
        }
        
	    public function isIE10()
        {
            if ($this->_myBrowser->getBrowser()=='Internet Explorer' && intval($this->_myBrowser->getVersion())>=10 && intval($this->_myBrowser->getVersion())<11)
                return true;
            else
                return false;
        }        
        
	}
?>