<?php
class CStaticViewAction extends CAction
{
	public $viewParam='view';
	public $defaultView='index';
	public $view;
	public $basePath='pages';
	public $layout;
	public $renderAsText=false;

	public $viewPath;

	// /* Created for test only
	public function __construct($controller, $actionname = 'page', $controlname = 'site')
	{
		parent::__construct($controller, $actionname);
	}
	// */
	
	public function getRequestedView()
	{
		$uri = Yii::app()->request->requestUri;
		$actionname = $this->getId();
		$controlname = $this->getController()->getId();
		$id = "/$actionname/$controlname/";
		if(strlen($id)< strlen($uri)){
			$this->viewPath = substr($uri,strlen($id), strlen($uri));	
		}	
		else {
			$this->viewPath ='';
		}		
		return $this->viewPath;
	}

	protected function resolveView($viewPath)
	{
		// start with a word char and have word chars, dots and dashes only
		if(preg_match('/^\w[\w\.\-]*$/',$viewPath))
		{
			$view=strtr($viewPath,'.','/');
			if(!empty($this->basePath))
				$view=$this->basePath.'/'.$view;
			if($this->getController()->getViewFile($view)!==false)
			{
				$this->view=$view;
				return;
			}
		}
		throw new CHttpException(404,Yii::t('yii','The requested view "{name}" was not found.',
				array('{name}'=>$viewPath)));
	}

	public function run()
	{
		$this->resolveView($this->getRequestedView());
		$controller=$this->getController();
		if($this->layout!==null)
		{
			$layout=$controller->layout;
			$controller->layout=$this->layout;
		}

		$this->onBeforeRender($event=new CEvent($this));
		if(!$event->handled)
		{
			if($this->renderAsText)
			{
				$text=file_get_contents($controller->getViewFile($this->view));
				$controller->renderText($text);
			}
			else
				$controller->render($this->view);
			$this->onAfterRender(new CEvent($this));
		}

		if($this->layout!==null)
			$controller->layout=$layout;
	}

	public function onBeforeRender($event)
	{
		$this->raiseEvent('onBeforeRender',$event);
	}

	public function onAfterRender($event)
	{
		$this->raiseEvent('onAfterRender',$event);
	}
}