<?php

class FanController extends Controller
{
	public function init()
	{
		parent::init();
		$this->layout='//layouts/member';
	}    
	
	public function actionIndex()
	{
		if(Yii::app()->user->Profile->getDataValue('gender') != 'F'){
			$this->redirect('/');
		}
		
		$db = Yii::app()->db;	
		
		$fan = FanProfile::createFanProfile();
		
		$stats = $fan->statistic();
		
		$payouts = $fan->getNextPayouts();
		
		$currentTime = time();		
		$weekday = date('N', $currentTime) -1;		
		$before4Week = date('Y-m-d 00:00:00', $currentTime - ($weekday + 21)* 24*3600);
		
		$userId = Yii::app()->user->id;
		$qry = "select id_to as user_id, id_from, u.username as username_from, date(added) as `date`, count(*) as count
				from users_fan_messages as m
				inner join users as u on u.id = m.id_from and m.id_to = {$userId}
				where added >= '$before4Week'
				group by user_id, id_from, username_from, `date`
				order by `date` desc";
		
		$dailyItems = $db->createCommand($qry)->queryAll();
		if(!$dailyItems) $dailyItems = array();
		
		$qry = "select * from users_fan_payout where user_id = {$userId} order by `week` desc";
		$payoutItems = $db->createCommand($qry)->queryAll();
		if(!$payoutItems) $payoutItems = array();
		
		foreach($payoutItems as &$item){
			$time = strtotime($item['week']);
			$weekday = date('N', $time) -1;
			$startWeek = date('Y-m-d', $time - ($weekday)* 24*3600);
			$endWeek = date('Y-m-d', $time + (6 - $weekday)* 24*3600);
			$item['weekrange'] = "$startWeek ~ $endWeek";
		}
		
		$infor = $fan->getPaymentInfor();
		//if($infor != null){
		//	try{
		//		$infor = unserialize($infor);
		//	}
		//	catch(exception $ex){
		//		$infor == null;
		//	}
		//}
		
		
		$this->render('index', array(
			'status' => $fan->getStatus(),
			'payoutMethod' => $fan->getPaymentMethod(),
			'paymentInfor' => $infor,
			'fanProfile' => $fan,
			'notice' => $fan->getNote(),
			'stats' => array('Today' => $stats['todayCount'],
						'This week' => $stats['weekCount'],
						'This month' => $stats['monthCount']),
					'payouts' => $payouts,
					'dailyItems' => $dailyItems,
					'payoutItems' => $payoutItems
					));
	}
	
	public function actionCron(){
		
		echo "<script>
				window.setTimeout(function(){ window.location = '/fan/cron'; }, 6000);
			 </script>";
	}
	
	public function actionUpdatePayout(){
		$method = $_POST['method'];
		$fields = $_POST['fields'];
		
		foreach($fields as $value){
		}
		
		$fan = FanProfile::createFanProfile();
		$fan->setPaymentMethod($method, $fields);
	}
	
	public function actionPayoutForm(){
		$method = $_GET['method'];
	}	
}

/*
> -make it on ONL so girls get paid every time they get a man to message them. 
Have a little stats area for each girl that signs up showing her the date/week/month and each time a man messages then it will show up in their stats as a link in their menu. After a girl signs up and she logs into the site, make a link in the menu that says "Flirt and get paid"

> Then make a page when they click on that , that says "You can make .25 every time a man messages you. To apply simply upload a picture of yourself with a fan sign (a piece of paper) that says your username and then "pinkmeets.com" After you upload your picture we will notify you of your approval and signup instructions for you to get paid weekly through red pass, a prepaid visa ATM card that we send to you to load funds on.
Then just have an upload image button right there on that page. 

>Have an area in admin area where I can approve girls who apply to do this. Lets call them fan girls. Put in there somewhere an area where i can set the payout per message.

Questions:
=> Shouldn't send fake message to girls? How about wink? How about view, like?
	If we send fake message to a fan_girl => she think she will ge paid for this message?
	=> Change AutoFlirt, and OnlineFlirt
=> Do we pay 0.25 for every message? Is there any problem if
	- A man send 1000 messages/week to a girl?  => Some one create a fake account to take our money?
	- A man send to 100 girls, each girl 7 messages/week? => We loss 175$/week because of just one man? some girl's account might be fake?

Tasks:
	+ datatable: 
		- users_fan_message(id, id_to, id_from, flirt_type, send_date, amt, valid): Some logic might be added in the future so the table need rather general
			- flirt_type: We might paid not only message, might be some other kind of flirt
			- amt: Might depend of number of message, for e.g. 0.25 for 1st message, 0.15 for second message...
		- users_fan_payout(user_id, week_date, amt, paid_status, paid_date)
			- paid_status: ongoing, pending, paid
		- users_fan_stats(user_id, date, vaid_count)
		- users_fan_settings(user_id, fan_sign, img, payout_method, account)
		- users_fan_trans(user_id, paid_date, amt, payout_method, account,..., admin)
		- site_setting(pay_per_fan_message)
	+ cron
		- run every minute: pickup fan_message, check policy to update users_fan_message and users_fan_stats
		- run every week: pickup fan_message and build users_fan_payout
		- change autoflirt, online flirt
	+ ONL
		- Left menu: 
			- Components/Fan/*.* (menuitem, menugroup)
			- need new icons!
		- UserFanPage
			- Approval status
			- Upload sign picture
			- Message detail
			- Statistic Daily, Weekly, Monthly
		- ImgController (upload and get)
	+ Admin
		- Approval, send message or email
		- Detail fan message for a users, summary daily, weekly, monthly
		- Statistic all fan message for daily, weekly, monthly
*/