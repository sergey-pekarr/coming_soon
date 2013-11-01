<?php

class RiskController extends Controller
{
	public function actionIndex(){
		$this->actionRisk();
	}
	
	public function actionSetting(){
		$config = RiskDetection::getConfig();
		$range = $config['location_range'];
		$ranks = $config['figure_print']['ranks'];
		$threshold = $config['figure_print']['threshold'];
		$this->render('setting', array('config' => $config, 'threshold' => $threshold, 'range' => $range, 'ranks' =>$ranks));		
	}
	
	public function actionRisk(){
		$page = (isset($_REQUEST['page'])) ? intval($_REQUEST['page']) : 0;
		$page = ($page) ? $page : 1;
		$page--;
		
		//$type = isset($_REQUEST['type'])?$_REQUEST['type']:'all';
		
		$incbrowser = false;
		if(isset($_REQUEST['incbrowser']) && $_REQUEST['incbrowser'] == 'true'){
			$incbrowser = true;
		}
		
		$ids = null;
		if(isset($_GET['ids'])) $ids = $_GET['ids'];
		
		$perpage = 20;
		
		$model = $this->getRisks($page, $perpage, $incbrowser, $ids);
		$risks = $model['risks'];

		$pages=new CPagination($model['count']);
		$pages->pageSize = $perpage;
		$pages->setCurrentPage($page);
		
		$this->doAutoLogin($risks);
		
		$this->render('risk', array('model'=>$model, 'risks'=>$risks, 'pages'=>$pages, 'count' => $model['count'], 'base' => $page * $perpage));
	}
	
	public function actionSave(){
		$configs = array('location_range' => round(floatval($_POST['distance']),4), 
			'figure_print' => array('threshold' => round(floatval($_POST['threshold']),4), 
					'ranks' => array(
						'screen' => intval($_POST['screen']),
						'availscreen' => intval($_POST['availscreen']),
						'browser' => intval($_POST['browser']),
						'agent' => intval($_POST['agent']),
						'platform' => intval($_POST['platform']),
						'fonts' => intval($_POST['fonts']),
						'plugins' => intval($_POST['plugins']),
						'screen_nor' => intval($_POST['screen_nor']),
						'availscreen_nor' => intval($_POST['availscreen_nor']),
						'plugins_pur' => intval($_POST['plugins_pur']),
						)));
		RiskDetection::saveConfig($configs);
	}
	
	public function actionResolve(){
		if(isset($_GET['id']) && isset($_GET['maxid'])){
			$id = intval($_GET['id']);
			$maxid = intval($_GET['maxid']);
			$qry = "update pm_today_risk set resolve = '1' where $id <= id and id <= $maxid";
			Yii::app()->db->createCommand($qry)->execute();
		}
		else if (isset($_GET['id'])){
			$id = intval($_GET['id']);
			$qry = "update pm_today_risk set resolve = '1' where id=$id";
			Yii::app()->db->createCommand($qry)->execute();
		}
	}
	
	public function actionAffCardInfo(){
		$model = new AffCardInfoForm;
		$model->attributes = (isset($_REQUEST['AffCardInfoForm'])) ? $_REQUEST['AffCardInfoForm'] : '';
		
		$page = (isset($_REQUEST['page'])) ? intval($_REQUEST['page']) : 0;
		$page = ($page) ? $page : 1;
		$page--;        
		
		$rows = false;
		if ($model->validate()){
			$items = $this->getAffCardInfor($model->date1, $model->date2, $model->aff, $page, $model->perPage, $model->sort, $model->ids);
		}
		else{
			$items = array('count' => 0, 'items' => array());
			$errors = $model->getErrors();
		}
		
		$pages=new CPagination($items['count']);
		$pages->pageSize = $model->perPage;
		$pages->setCurrentPage($page);
		
		$this->render(
			'affcard',
			array(
					'model'=>$model,
					'items'=> $items['items'],
					'pages'=>$pages,
					'ids' => explode(',', $model->ids)
					)
				);	
	}
	
	private function doAutoLogin(&$items, $idKey = 'user_id', $userKey = 'username'){
		foreach($items as &$item){
			if(isset($item[$idKey]) && isset($item[$userKey])){
				$url = CHelperProfile::getAutoLoginUrl($item[$idKey]);
				$item['loginAnchor'] = "<a href='$url'>{$item[$userKey]}</a>";
			}
		}
	}
	
	/**
	 * 
	 *
	 * @param mixed $page 
	 * @param mixed $perpage 
	 * @param mixed $type all, location, figure_print
	 * @return mixed 
	 *
	 */	
	private function getRisks($page, $perpage, $browser = false, $ids = null){
		$result = array('count' => 0, 'risks' => array());
		
		$idArr = array();
		if($ids != null && $ids != ''){
			$idArr = explode(',', $ids);
			for($j = count($idArr) -1; $j>=0;$j--){
				$id =$idArr[$j];
				if($id) $id = trim($id);
				if($id <= 0 || $idArr[$j] == '') unset($idArr[$j]);
			}
		}
		//		$where = "where resolve = '0' ";
		//		if($type == 'location'){
		//			$where .= " and duplicate_by_location is not null";
		//		}
		//		else if($type == "figure_print") {
		//			$where .= " and duplicate_by_figure_print is not null";
		//		}
		//		else {
		//			
		//			$where .= " and (duplicate_by_location is not null 
		//					 or duplicate_by_figure_print is not null)";
		//		}

		$where = " where resolve = '0' and duplicate_by_figure_print is not null";
		//if($browser) $where .= " and figure_print_risk_detail is not null";
		if(count($idArr) > 0){
			$where .= " and r.id in (". implode(',', $idArr).") ";
		}
		
		$qry = "select count(*)
				from pm_today_risk as r
				$where";
		$result['count'] = Yii::app()->db->createCommand($qry)->queryScalar();
		
		if(ceil($result['count']/$perpage) - 1 < $page) $page = max(ceil($result['count']/$perpage) -1,0);
		
		$start = $page*$perpage;
		
		$qry = "select r.*, u.username, g.manager_name, g.manager_id, g.agent_name, g.affid, g.date as saledate
				from pm_today_risk as r
				inner join users as u on u.id = r.user_id
				inner join pm_today_gold as g on g.user_id = r.user_id
				$where
				order by id
				limit $start, $perpage";
		$result['risks'] = Yii::app()->db->createCommand($qry)->queryAll();
		
		return $result;
	}
	
	private function getAffCardInfor($date1, $date2, $aff, $page, $perpage, $sort = "Name", $ids = null){
		if(!$aff || $aff == '') return array('items' => array(), 'count' => 0);

		if($sort == "Name") $sort = "order by t.firstname, t.lastname, g.id";
		else $sort = "order by g.id";
		
		$idArr = array();
		if($ids != null && $ids != ''){
			$idArr = explode(',', $ids);
			for($j = count($idArr) -1; $j>=0;$j--){
				$id =$idArr[$j];
				if($id) $id = trim($id);
				if($id <= 0 || $idArr[$j] == '') unset($idArr[$j]);
			}
			if(count($idArr)>0){
				$ids = "and g.id in (".implode(',', $idArr).")";
			}
			else {
				$ids = '';
			}
		}
		else{
			$ids = '';
		}
		
		$wheredate = ' 1 ';
		$whereaff = ' and 1 ';
		if($ids == '') {
			$wheredate = " '$date1' <= g.date and g.date < '$date2 23:59:59' ";
			$whereaff = " and g.manager_id = '$aff' ";
		} 
		
		$qry = "select count(*)
				from pm_transactions as t 
				inner join pm_today_gold as g on g.user_id = t.user_id and g.date = t.date
				where $wheredate $whereaff 
					and t.status = 'completed' $ids";
		$count = Yii::app()->db->createCommand($qry)->queryScalar();
		if(!$count) $count = 0;
		
		if(ceil($count/$perpage) - 1 < $page) $page = max(ceil($count/$perpage) -1,0);
		
		$start = $page*$perpage;
		$perpage++;
		
		$qry = "select g.manager_id, g.manager_name, g.id, g.user_id, g.username, g.email, 
						t.ccname, t.ccnum, t.firstname, t.lastname, t.address, t.country, 
						t.state, t.city, t.email, t.date as transdate
				from pm_transactions as t 
				inner join pm_today_gold as g on g.user_id = t.user_id and abs(datediff(g.date,t.date)) <= 1
				where $wheredate $whereaff 
					and t.status = 'completed' $ids
				$sort
				limit $start, $perpage";
		$items = Yii::app()->db->createCommand($qry)->queryAll();
		if(!$items) $items = array();
		
		if(count($idArr) > 0){
			$orderArr = array();
			$default = count($items);
			foreach($items as $item){
				$index = array_search($item['id'], $idArr);
				if($index === 0 || $index > 0){
					$orderArr[] = $index;
				}
				else{
					$orderArr[] = $default++;
				}
			}
			array_multisort($orderArr, $items, SORT_ASC);		
		}
		
		return array('items' => $items, 'count' => $count);
		
	}
}