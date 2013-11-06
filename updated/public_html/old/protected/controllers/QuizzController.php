<?php

class QuizzController extends Controller
{
	public function init()
	{
		parent::init();
		$this->layout='//layouts/quizz';
	}    
	
	public function actionIndex()
	{
		$this->render('index', array());
	}
	
	public function actionBrowse(){
		return $this->actionIndex();
	}
	
	public function actionTaken(){
		return $this->actionIndex();
	}
	
	public function actionCreate(){
		$testid = isset($_GET['id'])?$_GET['id']:null;
		$userid = Yii::app()->user->id;
		if($testid == null){
			$qry = "select id from test where author = $userid and status = 'editing' order by regdate desc limit 0, 1";
			$testid = Yii::app()->dbquizz->createCommand($qry)->queryScalar();
			if($testid){
				$this->redirect("/quizz/create/$testid");
			}
		}
		$this->render('edit', array('testid' => $testid));
	}
	
	public function actionRemove(){
		$testid = isset($_GET['id'])?$_GET['id']:null;
		$testid = intval($testid);
		if($testid){
			$userid = Yii::app()->user->id;
			//$qry = "delete from test where author = $userid and status = 'editing' and id=$testid";
			$qry = "delete from test where author = $userid and id=$testid";
			Yii::app()->dbquizz->createCommand($qry)->execute();
		}
		$this->redirect('/quizz/create');		
	}
	
	public function actionEdit(){
		return $this->actionCreate();
	}
	
	public function actionCreated(){
		return $this->actionIndex();
	}
	
	public function actionTest(){
		$id = CHelperQuizz::GetValueFromKey($_GET,'id', null);
		$userid = Yii::app()->user->id;
		$id = intval($id);
		if(!$id){
			$this->error("The test does not exist or has been deleted");
		}
		$qry = "select * from test where id = :id";
		$db = Yii::app()->dbquizz;
		$testObj = $db->createCommand($qry)
			->bindValue(":id", $id, PDO::PARAM_INT)
			->queryRow();
		if(!$testObj){
			$this->error("The test does not exist or has been deleted");
		}
		
		$userid = Yii::app()->user->id;
		if($testObj['author'] == $userid){
			$this->redirect("/quizz/create/$id");
		}
		
		$mkey = "retest_{$userid}_{$id}";
		
		//Allow retest
		if(!isset(Yii::app()->session[$mkey]) || Yii::app()->session[$mkey]!== true){
			$qry = "select * from taken 
					where test_id = $id and user_id = $userid 
					order by id desc limit 0,1";
			$taken = $db->createCommand($qry)->queryRow();
			if($taken && $taken['id']){
				$this->redirect("/quizz/testresult/$id");
			};
		}
		
		$ratekey = "quizz_rate_{$userid}_{$id}";
		$rate = isset(Yii::app()->session[$ratekey])?Yii::app()->session[$ratekey]:null;
		$testid = $testObj['id'];
		$this->render('test', array('testid' => $testid, 'rate' => $rate));
	}
	
	public function actionPreview(){
		$id = CHelperQuizz::GetValueFromKey($_GET,'id', null);
		$userid = Yii::app()->user->id;
		$id = intval($id);
		if(!$id){
			$this->error("The test does not exist or has been deleted");
		}
		$qry = "select * from test where id = :id";
		$db = Yii::app()->dbquizz;
		$testObj = $db->createCommand($qry)
			->bindValue(":id", $id, PDO::PARAM_INT)
			->queryRow();
		if(!$testObj || $testObj['author'] != $userid){
			$this->error("The test does not exist or has been deleted");
		}
		
		$testid = $testObj['id'];
		$this->render('test', array('testid' => $testid, 'type' => 'preview'));
	}
	
	public function actionReTakeTest(){
		$testid = CHelperQuizz::GetValueFromKey($_GET,'id', null);
		$userid =Yii::app()->user->id;
		
		$mkey = "retest_{$userid}_{$testid}";
		
		Yii::app()->session[$mkey] = true;
		
		$this->redirect("/quizz/test/$testid");
	}
	
	public function actionTestResult(){
		$id = CHelperQuizz::GetValueFromKey($_GET,'id', null);
		$id = intval($id);
		if(!$id){
			$this->error("The test does not exist or has been deleted");
		}
		$qry = "select * from test where id = :id";
		$db = Yii::app()->dbquizz;
		$testObj = $db->createCommand($qry)
			->bindValue(":id", $id, PDO::PARAM_INT)
			->queryRow();
		if(!$testObj){
			$this->error("The test does not exist or has been deleted");
		}
		
		$userid = Yii::app()->user->id;
		if($testObj['author'] == $userid){
			$this->redirect("/quizz/create/$id");
		}
		
		$ratekey = "quizz_rate_{$userid}_{$id}";	
		$rate = isset(Yii::app()->session[$ratekey])?Yii::app()->session[$ratekey]:null;	
		$testid = $testObj['id'];
		$this->render('test', array('testid' => $testid, 'rate' => $rate));
	}
	
	public function actionTestResultDetail(){
	}
	
	public function actionList(){
		$input = isset($_POST)?$_POST:$_GET;
		$search = isset($input['search'])?$input['search']:'';
		$order = isset($input['order'])?$input['order']:'';
		$myTest = isset($input['type']) && $input['type'] == 'taken';
		$myCreated = isset($input['type']) && $input['type'] == 'created';
		$page = intval(isset($input['page'])?$input['page']:1);
		if(!$page) $page = 1;
		$this->layout = false;
		
		if($myTest){
			$paneltitle = "My Tests";
		}
		else if($myCreated){
			$paneltitle = "My Created";
		}
		else {
			$paneltitle = "All Tests";
		}
		
		$model = $this->findItems(Yii::app()->user->id, $page, $search, $order, $myTest, $myCreated, 50);
		$this->render($myTest?'list-taken':($myCreated?'list-written':'list'), 
			array('panelTitle' => $paneltitle, 'total'=>ceil($model['total']/50), 
					'page' => $page, 'tests' => $model['tests'], 'mytest' => $myTest));		
	}
	
	public function actionDeleteTakenTest(){
		$id = CHelperQuizz::GetValueFromKey($_GET,'id', null);
		$id = intval($id);
		$db = Yii::app()->dbquizz;		
		$userid = Yii::app()->user->id;
		
		$qry = "delete from taken where test_id = $id and user_id = $userid";
		$ok = $db->createCommand($qry)->execute();
		
		$this->redirect('/quizz/taken');	
		
	}
	
	public function actionDeleteTest(){
		$id = CHelperQuizz::GetValueFromKey($_GET,'id', null);
		$id = intval($id);
		$db = Yii::app()->dbquizz;		
		$userid = Yii::app()->user->id;
		
		$qry = "update test set status = 'deleted' where id = $id and author = $userid";
		$ok = $db->createCommand($qry)->execute();	
		
		$this->redirect('/quizz/created');	
	}
	
	private function FindMinMaxMap($test){
		$minMaxMap = array();
		
		//Find maxValue and minValue for each variable. 
		//maxValue($var) = sumAllQuestion(max($var of all answer))
		foreach($test->items as $qItem){
			$qMinMax = array();
			if($qItem->itemType != ItemTypeDef::$multiChoice){
				continue;
			}
			foreach($qItem->answers as $answer){
				foreach($answer->pures as $pure => $value){
					if(!isset($qMinMax[$pure])){
						$qMinMax[$pure] = array('min' => $value, 'max' => $value);
					}
					else{
						if($qMinMax[$pure]['min'] > $value){
							$qMinMax[$pure]['min'] = $value;
						}
						if($qMinMax[$pure]['max'] < $value){
							$qMinMax[$pure]['max'] = $value;
						}								
					}
				}
			}
			
			foreach($qMinMax as $pure=>$qValue){
				if(!isset($minMaxMap[$pure])){
					$minMaxMap[$pure] = $qValue;
				}
				else{
					//Note: User can chose only one answer
					$minMaxMap[$pure]['min'] += $qValue['min'];
					$minMaxMap[$pure]['max'] += $qValue['max'];
				}
			}
		}
		return $minMaxMap;
	}
	
	public function actionStatistic(){
		$id = CHelperQuizz::GetValueFromKey($_GET,'id', null);
		$id = intval($id);
		$db = Yii::app()->dbquizz;		
		$userid = Yii::app()->user->id;
		$this->layout = false;
		
		$qry = "select * from test where id = $id";
		$testObj = $db->createCommand($qry)->queryRow();
		
		if(!$testObj){
			Yii::app()->end();
		}

		$test = unserialize($testObj['data']);
		
		$minMaxMap = $this->FindMinMaxMap($test);
		
		$qry = "select * from test_result_stats where test_id = $id order by score";
		$statsRows = $db->createCommand($qry)->queryAll();
		
		//$chartDatas = array();
		//if($statsRows && count($statsRows) > 0){
		//	foreach($statsRows as $row){
		//		$key = $row['score'];
		//		if(!isset($chartDatas[$key])){
		//			$chartDatas[$key] = array();
		//		}
		//		$chartDatas[$key][] = array('x'=> $row['scorevalue'], 'y' => $row['nouser']);
		//	}
		//}
		
		if($statsRows && count($statsRows) > 0){
			foreach($statsRows as $row){
				$key = $row['score'];
				if(isset($minMaxMap[$key])){
					if(!isset($minMaxMap[$key]['samplePoints'])){
						$minMaxMap[$key]['samplePoints'] = array();
					}
					$minMaxMap[$key]['samplePoints'][] = array('x'=> intval($row['scorevalue']), 'y' => intval($row['nouser']));
				}
			}
		}
		
		$this->render('statistic', array('chartDatas' => $minMaxMap));
		
	}
	
	public function actionStats(){
		$id = CHelperQuizz::GetValueFromKey($_GET,'id', null);
		$id = intval($id);
		$db = Yii::app()->dbquizz;		
		$userid = Yii::app()->user->id;
		
		/*
		Chart
		24h taken and rank
		All time taken and rank
		*/
		
		$qry = "select * from test_result_stats where test_id = $id order by score";
		$statsRows = $db->createCommand($qry)->queryAll();
		
		$chartDatas = array();
		if($statsRows && count($statsRows) > 0){
			foreach($statsRows as $row){
				$key = $row['score'];
				if(!isset($chartDatas[$key])){
					$chartDatas[$key] = array();
				}
				$chartDatas[$key][] = array('x'=> $row['scorevalue'], 'y' => $row['nouser']);
			}
		}
		
		$qry = "select * from test where id = $id";
		$testObj = $db->createCommand($qry)->queryRow();
		
		if(!$testObj){
			$this->error("The test does not exist or has been deleted");
		}
		else{
			$taken = $testObj['taken'];
			$taken1 = $testObj['last_date_taken'];
			$taken2 = $testObj['two_date_before_taken'];			
			
			$qry = "select count(*) from test where id <> $id and taken < $taken";
			$rank = $db->createCommand($qry)->queryScalar();
			
			$qry = "select count(*) from test where id <> $id and last_date_taken < $taken1";
			$rank1 = $db->createCommand($qry)->queryScalar();
			
			$qry = "select count(*) from test where id <> $id and two_date_before_taken < $taken2";
			$rank2 = $db->createCommand($qry)->queryScalar();
			
			$this->render('stats', array('chartDatas' => $chartDatas, 
				'taken' => $taken, 'rank' => $rank, 
				'taken1' => $taken1, 'rank1' =>  $rank1, 
				'taken2' =>  $taken2, 'rank2' =>  $rank2));		
		}
	}
	
	private function error($error){
		$this->render("error", array('error' => $error));
		Yii::app()->end();
	}
	
	private function findItems($userid, $page, $search = '', $order = 3, $mytest = false, $myCreated = false, $perpage = 50){
		if($page == null)$page = 1;

		if($search == null) $search = '';
		if($search != ''){
			$search = " and (u.username like '%$search%' or q.name like '%$search%')";
		}

		if($order == null){
			$order = 3;
		}

		switch($order){
			case '1': $order = 'last_date_taken'; break;
			case '2': $order = 'last_date_rank'; break;
			case '4': $order = 'rank'; break;
			default : $order = 'taken'; break;
		}

		if($mytest){
			$mytest = ' and r.quizzid is not null';
		}
		
		if($mytest){
			$totalQry = "select count(*)
					from test as q
					inner join (select test_id, max(id) as id , max(regdate) as regdate
								from taken 
								where user_id = $userid 
								group by test_id) as t on t.test_id = q.id -- and t.user_id = $userid
					inner join ".DB_NAME.".users as u on q.author = u.id
					where author <> $userid and q.status = 'completed' $search";
		}
		elseif($myCreated){
			$totalQry = "select count(*)
					from test as q
					inner join ".DB_NAME.".users as u on q.author = u.id and q.status <> 'deleted'
					where author = $userid $search";
		}
		else {
			$totalQry = "select count(*)
					from test as q
					inner join ".DB_NAME.".users as u on q.author = u.id
					where author <> $userid and q.status = 'completed' $search";
		}
		
		$total = Yii::app()->dbquizz->createCommand($totalQry)->queryScalar();
		if(!$total)	$total = 1;


		$start = ($page-1)*$perpage;
		if($mytest){
			$qry = "select q.*, 
							t.regdate as tookdate, t.id as taken_id, t2.result
					from test as q
					inner join (select test_id, max(id) as id , max(regdate) as regdate
								from taken 
								where user_id = $userid 
								group by test_id) as t on t.test_id = q.id -- and t.user_id = $userid
					inner join ".DB_NAME.".users as u on q.author = u.id
					inner join taken as t2 on t2.id = t.id
					where author <> $userid and q.status = 'completed' $search
					order by $order desc
					limit $start, 50";	
		}
		elseif($myCreated){
			$qry = "select q.*
					from test as q
					inner join ".DB_NAME.".users as u on q.author = u.id and q.status <> 'deleted'
					where author = $userid $search
					order by $order desc
					limit $start, 50";
		}
		else {
			$qry = "select q.*
					from test as q
					inner join ".DB_NAME.".users as u on q.author = u.id
					where author <> $userid and q.status = 'completed' $search
					order by $order desc
					limit $start, 50";	
		}
		
		$tests = Yii::app()->dbquizz->createCommand($qry)->queryAll();
		if($tests && count($tests)>0 && !$mytest){
			$testids = array();			
			foreach($tests as $test){
				$testids[] = $test['id'];
			}
			$checkTakenQry =	"select t.test_id, 
									t.regdate as tookdate
								from taken as t
								where t.user_id = $userid and t.test_id in (".implode(',', $testids).")";
			$userTakens = Yii::app()->dbquizz->createCommand($checkTakenQry)->queryAll();
			
			foreach($tests as &$test){
				$taken = $this->findRecordInArray($userTakens, 'test_id', $test['id']);
				if($taken){
					foreach($taken as $key => $value){
						if($key != 'id'){
							$test[$key] = $value;
						}
					}
				}
			}
		}
		if(!$tests) $tests = array();
		
		return array('total' => $total, 'tests' => $tests);
	}
	
	private function findRecordInArray($arr, $fieldName, $value){
		foreach($arr as $item){
			if($item[$fieldName] == $value) return $item;
		}
		return null;
	}
	
	
	public function actionCalculateRank(){
		
		sleep(30); //There are many tasks run at *:*:01. This task run at *:*:31 -> avoid overhead
		
		$db = Yii::app()->dbquizz;
		
		$logId = Yii::app()->helperCron->onStartCron('Quizz','CalculateRank');
		
		$trans = $db->beginTransaction();
		
		$now = date("Y-m-d H:i:s");
		$yesterday = date("Y-m-d H:i:s", time() - 24*3600);
		$twodaybefore = date("Y-m-d H:i:s", time() - 2*24*3600);
		try
		{
			$ok = false;

			$qry1 = "update test as t
				set t.last_date_taken = 0, t.two_date_before_taken = 0
				where status = 'completed'";
			$ok = $db->createCommand($qry1)->execute();
			
			//if($ok){
			$ok = false; //When exception happen -> $ok = false
			$qry2 = "update test as t
					inner join (select test_id, count(*) as count
								from taken
								where regdate >= '$yesterday'
								group by test_id) as v on v.test_id = t.id
					set t.last_date_taken = v.count";
			$ok = $db->createCommand($qry2)->execute();
			//}
			
			//if($ok){
			$ok = false;
			$qry3 = "update test as t
					inner join (select test_id, count(*) as count
								from taken
								where '$twodaybefore' <= regdate and regdate < '$yesterday'
								group by test_id) as v on v.test_id = t.id
					set t.two_date_before_taken = v.count";
			$ok = $db->createCommand($qry3)->execute();
			//}
			$trans->commit();
		}
		catch(Exception $e) // an exception is raised if a query fails
		{
			$trans->rollback();
		}
		
		$trans->commit();		
		
		$data = json_encode(array('ok' => $ok));
		Yii::app()->helperCron->onEndCron($logId, $data);	
	}
}


/*$qry = "select u.username, u.userid,
case when u.defaultimage is null and u.sex = 'M' then 'male-large.png' 
		when u.defaultimage is null then'female-large.png' 
		else u.defaultimage end AS defaultimagepath,
 q.*, r.point1, r.point2, r.point3, r.point4, r.point5, r.regdate as tookdate
from quizztable as q 
inner join users as u on q.author = u.userid
left join quizz_user as r on r.quizzid = q.id and r.userid = '$userid'
where q.status = 'completed' and  author <> '$userid' $search $mytest
order by $order desc
limit $start, 50";*/	