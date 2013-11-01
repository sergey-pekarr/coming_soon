<?php

class AutoflirtController extends Controller
{
	public function actionIndex(){
		$this->render('index');
	}
	
	private function getValueInt($arr, $i, $default = 0){
		if(isset($arr[$i])){
			try{
				return intval($arr[$i]);
			}
			catch(exception $ex){
			}
		}
		return 0;
	}
	
	private function getValueFloat($arr, $i, $default = 0){
		if(isset($arr[$i])){
			try{
				return  floatval($arr[$i]);
			}
			catch(exception $ex){
			}
		}
		return 0;
	}
	
	//private function parseSaveData($type, $isOnline = false){
	//	$inactive = isset($_POST['inactive'])?$_POST['inactive']:5;
	//	try{
	//		$inactive = intval($inactive);
	//	}
	//	catch(exception $ex){
	//	}
	//	$messages = isset($_POST['messages'])?$_POST['messages']:array();
	//	$winks = isset($_POST['winks'])?$_POST['winks']:array();
	//	$photorequest = isset($_POST['photorequest'])?$_POST['photorequest']:array();
	//	$view = isset($_POST['view'])?$_POST['view']:array();
	//	
	//	$maxItem = max(count($messages), max(count($winks), max(count($photorequest), count($view))));
	//	if($maxItem>100) $maxItem = 100;
	//	
	//	if($isOnline){
	//		$duration = isset($_POST['duration'])?$_POST['duration']:array();
	//		$maxItem = max(count($duration), $maxItem);
	//	}
	//	
	//	$insItems = array();
	//	for($i=0;$i<$maxItem;$i++){
	//		
	//		if($isOnline){
	//			$m = $this->getValueFloat($messages, $i);
	//			$w = $this->getValueFloat($winks, $i);
	//			$p = $this->getValueFloat($photorequest, $i);
	//			$v = $this->getValueFloat($view, $i);
	//			$d = $this->getValueInt($duration, $i);
	//			$insItems[] = "('$type', $i,$d,$m,$w,$p,$v)";
	//		}
	//		else {
	//			$m = $this->getValueInt($messages, $i);
	//			$w = $this->getValueInt($winks, $i);
	//			$p = $this->getValueInt($photorequest, $i);
	//			$v = $this->getValueInt($view, $i);
	//			if($i + 1 == $inactive) $inact = 1;
	//			else $inact = 0;
	//			$insItems[] = "('$type',$i,$m,$w,$p,$v, $inact)";
	//		}
	//	}
	//	return $insItems;					
	//}
	//
	//private function findInactive($items){
	//	foreach($items as $item){
	//		if($item['stop_inactive']){
	//			return $item['day'] + 1;
	//			break;
	//		}
	//	}
	//	return 5;
	//}
	
	private function getDataByType($type){
		$qry = "select data from autoflirt_config where `type` = '$type'";
		$data = Yii::app()->db->createCommand($qry)->queryScalar();
		if($data){
			try{
				$obj = get_object_vars(json_decode(stripslashes($data)));
			}
			catch(exception $ex){
				$obj = array();
			}
		}
		else{
			$obj = array();
		}
		if(!isset($obj['inactive'])){
			$obj['inactive'] = 5;
		}
		if(!isset($obj['items'])){
			$obj['items'] = array(array('duration'=>0),array('duration'=>0),array('duration'=>0),array('duration'=>0),array('duration'=>0));
		}
		return $obj;
	}
	
	private function saveDataByType($type){
		if($_POST && isset($_POST['inactive']) && isset($_POST['items']) && gettype($_POST['items']) == 'array'){
			//Corect int value
			$data = array();
			$data['inactive'] = $this->getValueInt($_POST, 'inactive');
			$data['items'] = array();
			foreach($_POST['items'] as $item){
				$dataItem = array('subItems'=>array());
				foreach($item['subItems'] as $subItem){
					$dataItem['subItems'][] = array(
						'duration' => $this->getValueInt($subItem,'duration'),
						'messages' => $this->getValueInt($subItem,'messages'),
						'winks' => $this->getValueInt($subItem,'winks'),
						'photorequest' => $this->getValueInt($subItem,'photorequest'),
						'view' => $this->getValueInt($subItem,'view'),
						);
				}
				$data['items'][] = $dataItem;
			}
			$data = addslashes(json_encode($data));
			//$data = addslashes(json_encode($_POST));
		}
		else {
			$data = addslashes(json_encode(array('inactive'=>5, 'items'=> array())));
		}
		$insQry = "insert into autoflirt_config(`type`, data) 
						values('$type','$data')
						on duplicate key update data = '$data'";
		$ok = Yii::app()->db->createCommand($insQry)->execute();
	}
	
	private function getOnlineDataByType($type){
		$qry = "select data from autoflirt_config where `type` = '$type'";
		$data = Yii::app()->db->createCommand($qry)->queryScalar();
		if($data){
			try{
				$obj = get_object_vars(json_decode(stripslashes($data)));
			}
			catch(exception $ex){
				$obj = array();
			}
		}
		else{
			$obj = array();
		}
		if(!isset($obj['items'])){
			$obj['items'] = array(array('duration'=>600), array('duration'=>3600));
		}
		return $obj;
	}
	
	private function saveOnlineDataByType($type){
		if($_POST && isset($_POST['items']) && gettype($_POST['items']) == 'array'){
			//Corect int value
			$data = array();
			$data['items'] = array();
			foreach($_POST['items'] as $item){
				$data['items'][] = array(
					'duration' => $this->getValueInt($item,'duration'),
					'messages' => $this->getValueFloat($item,'messages'),
					'winks' => $this->getValueFloat($item,'winks'),
					'photorequest' => $this->getValueFloat($item,'photorequest'),
					'view' => $this->getValueFloat($item,'view'),
					);
			}
			$data = addslashes(json_encode($data));
		}
		else {
			$data = addslashes(json_encode(array('items'=> array())));
		}
		$insQry = "insert into autoflirt_config(`type`, data) 
						values('$type','$data')
						on duplicate key update data = '$data'";
		$ok = Yii::app()->db->createCommand($insQry)->execute();
	}
	
	public function actionFreeMember(){
		$this->layout='/layouts/ajax';
		$obj = $this->getDataByType('free');
		
		$this->render('autoflirt', array('type'=>'free', 'items' => $obj['items'], 'inactive' => $obj['inactive']));
	}
	
	public function actionSaveFreeMember(){
		$this->layout=false;
		$this->saveDataByType('free');
	}
	
	public function actionGoldMember(){
		$this->layout='/layouts/ajax';		
		$obj = $this->getDataByType('gold');
		
		$this->render('autoflirt', array('type'=>'free', 'items' => $obj['items'], 'inactive' => $obj['inactive']));
	}
	
	public function actionSaveGoldMember(){
		$this->layout=false;
		$this->saveDataByType('gold');
	}
	
	public function actionOnlineFreeMember(){
		$this->layout='/layouts/ajax';		
		$obj = $this->getOnlineDataByType('onlinefree');
		
		$this->render('online', array('type'=>'free', 'items' => $obj['items']));
	}
	
	public function actionSaveOnlineFreeMember(){
		$this->layout=false;
		$this->saveOnlineDataByType('onlinefree');
	}
	
	public function actionOnlineGoldMember(){
		$this->layout='/layouts/ajax';
		$obj = $this->getOnlineDataByType('onlinegold');
		
		$this->render('online', array('type'=>'free', 'items' => $obj['items']));
	}
	
	public function actionSaveOnlineGoldMember(){
		$this->layout=false;
		$this->saveOnlineDataByType('onlinegold');
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//Member message center 
	public function actionManual()
	{
		$model = new FlirtManualForm;
		$model->attributes = (isset($_REQUEST['FlirtManualForm'])) ? $_REQUEST['FlirtManualForm'] : '';
		
		$page = (isset($_REQUEST['page'])) ? intval($_REQUEST['page']) : 0;
		$page = ($page) ? $page : 1;
		$page--;        
		
		$rows = false;
		if ($model->validate())
			$rows = $model->getMessages($page);
		
		$pages=new CPagination($rows['count']);
		$pages->pageSize = $model->perPage;
		$pages->setCurrentPage($page);
		
		$this->render(
			'manual',
			array(
				'model'=>$model,
				'rows'=>$rows,
				'pages'=>$pages,
			)
		);		
	}	
}