<?php

class FanController extends Controller
{
	private function doAutoLogin(&$items, $idKey = 'user_id', $userKey = 'username'){
		foreach($items as &$item){
			if(isset($item[$idKey]) && isset($item[$userKey])){
				$url = CHelperProfile::getAutoLoginUrl($item[$idKey]);
				$item['loginAnchor'] = "<a href='$url'>{$item[$userKey]}</a>";
			}
		}
	}
		
	public function actionIndex(){
		
		$qry = "select s.*, u.username from users_fan_settings as s
				inner join users as u on u.id = s.user_id
				where status = 'pending'";
		$reqs = Yii::app()->db->createCommand($qry)->queryAll();
		if(!$reqs) $reqs = array();
		
		$this->doAutoLogin($reqs);
		$this->render('index', array('reqs' => $reqs));
	}
	
	public function actionAll(){
		$page = (isset($_REQUEST['page'])) ? intval($_REQUEST['page']) : 0;
		$page = ($page) ? $page : 1;
		$page--;
		
		$perpage = 15;
		
		$model = $this->getFanGirl($page, $perpage);
		$fangirls = $model['fangirls'];

		$pages=new CPagination($model['count']);
		$pages->pageSize = $perpage;
		$pages->setCurrentPage($page);
		
		$this->doAutoLogin($fangirls);
		
		$this->render('all', array('model'=>$model, 'fangirls'=>$fangirls, 'pages'=>$pages, 'count' => $model['count']));
	}
	
	public function actionSetting(){
		$qry = "select `value` from user_fan_configs where `name` = 'pay_per_message' limit 0,1";
		$db = Yii::app()->db;
		$pay_per_message = Yii::app()->db->createCommand($qry)->queryScalar();
		if(!$pay_per_message){
			$pay_per_message = 0.25;
		}	
		
		$this->render('setting', array('value' => $pay_per_message));
	}
	
	public function actionReport(){
		$db = Yii::app()->db;
		$qry = "select week, 
					sum(case when pay_status = 'going' then amt else 0 end) as going, 
					sum(case when pay_status = 'pending' then amt else 0 end) as pending, 
					sum(case when pay_status = 'paid' then amt else 0 end) as paid, 
					sum(case when pay_status = 'rejected' then amt else 0 end) as rejected
				from users_fan_payout
				group by week
				order by week desc";
		$payouts = Yii::app()->db->createCommand($qry)->queryAll();
		if(!$payouts) $payouts = array();
		foreach($payouts as &$item){
			$time = strtotime($item['week']);
			$weekday = date('N', $time) -1;
			$startWeek = date('Y-m-d', $time - ($weekday)* 24*3600);
			$endWeek = date('Y-m-d', $time + (6 - $weekday)* 24*3600);
			$item['weekrange'] = "$startWeek ~ $endWeek";
		}
		$this->render('report', array('payouts' => $payouts));
	}
	
	public function actionPayout(){
		
		//$time = time();
		//$weekday = date('N', $time) -1;
		//$fourWeeks = date('Y-m-d', $time - ($weekday)* 24*3600);
		
		$db = Yii::app()->db;
		$qry = "select u.username, p.user_id, p.week, p.valid_count, p.amt, p.pay_status, p.pay_date, s.payment_method, s.payment_infor
				from users_fan_payout  as p
				inner join users as u on u.id = p.user_id
				inner join users_fan_settings as s on s.user_id = p.user_id
				where pay_status = 'pending'
				order by p.`week`";
		//or (pay_status in ('paid', 'rejected') and pay_date > '$fourWeeks')
		$payouts = Yii::app()->db->createCommand($qry)->queryAll();
		if(!$payouts) $payouts = array();
		foreach($payouts as &$item){
			$time = strtotime($item['week']);
			$weekday = date('N', $time) -1;
			$startWeek = date('Y-m-d', $time - ($weekday)* 24*3600);
			$endWeek = date('Y-m-d', $time + (6 - $weekday)* 24*3600);
			$item['weekrange'] = "$startWeek ~ $endWeek";
			
			$infor = $item['payment_infor'];
			if($infor != null || $infor != ''){
				try{
					$infor = unserialize($infor);
				}
				catch(exception $ex){
					$infor = null;
				}
			}
			$item['payment_infor'] = $infor;
		}
		
		$this->doAutoLogin($payouts);
		$this->render('payout', array('payouts' => $payouts));
	}
	
	public function actionApproveImg(){
		$id = $_GET['id'];
		$userid = Yii::app()->secur->decryptID($id);
		$fan = FanProfile::createFanProfile($userid);
		
		$fan->setStatus('approved');
	}
	
	public function actionDeclineImg(){
		$id = $_GET['id'];
		$userid = Yii::app()->secur->decryptID($id);
		$fan = FanProfile::createFanProfile($userid);
		
		$fan->setStatus('rejected');
	}
	
	public function actionUpdateSettings(){
		$value = $_POST['value'];
		$value = floatval($value);
		$qry = "insert into user_fan_configs values('pay_per_message', 'float', :value)
				on duplicate key update `type` = 'float', `value` = :value";
		$db = Yii::app()->db;
		$ok = Yii::app()->db->createCommand($qry)
			->bindValue(':value', $value, PDO::PARAM_STR)
			->execute();
		echo json_encode(array());
	}
	
	public function actionPayoutPaid(){
		$note = $_POST['note'];
		$payment_method = $_POST['payment_method'];
		$payment_infor = $_POST['payment_infor'];
		$user_id = intval($_POST['user_id']);
		$week= $_POST['week'];
		$now = date('Y-m-d H:i:s');
		$db = Yii::app()->db;
		
		$qry = "update users_fan_payout
				set note = :note, payment_method = :method, payment_infor = :infor, pay_status = 'paid', pay_date = '$now'
				where user_id = $user_id and week = :week and pay_status in ('pending','rejected')";
		$ok = $db->createCommand($qry)
			->bindValue(':method', $payment_method, PDO::PARAM_STR)
			->bindValue(':infor', serialize($payment_infor), PDO::PARAM_STR)
			->bindValue(':note', $note, PDO::PARAM_STR)
			->bindValue(':week', $week, PDO::PARAM_STR)
			->execute();
	}
	
	public function actionPayoutReject(){
		$note = $_POST['note'];
		//$payment_method = $_POST['payment_method'];
		//$payment_infor = $_POST['payment_infor'];
		$user_id = intval($_POST['user_id']);
		$week= $_POST['week'];
		$now = date('Y-m-d H:i:s');
		$db = Yii::app()->db;
		
		$qry = "update users_fan_payout
				set note = :note, payment_method = null, payment_infor = null, pay_status = 'rejected', pay_date = '$now'
				where user_id = $user_id and week = :week and pay_status in ('pending')";
		$ok = $db->createCommand($qry)
			->bindValue(':note', $note, PDO::PARAM_STR)
			->bindValue(':week', $week, PDO::PARAM_STR)
			->execute();
	}
	
	private function getFanGirl($page, $perpage){
		$result = array('count' => 0, 'fangirls' => array());
		
		$start = $page*$perpage;
		
		$qry = "select f.*, u.username from users_fan_settings as f inner join users as u on u.id = f.user_id limit $start, $perpage";
		$result['fangirls'] = Yii::app()->db->createCommand($qry)->queryAll();
		
		$qry = "select count(*) from users_fan_settings";
		$result['count'] = Yii::app()->db->createCommand($qry)->queryScalar();
		
		return $result;
	}
}