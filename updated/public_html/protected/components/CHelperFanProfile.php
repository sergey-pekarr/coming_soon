<?php

class CHelperFanProfile
{
	static public function getAllPaymentMethodsInfor(){
		//return array(
		//	'Paypal' => array('method' => 'Paypal', 'Paypal email' => ''),
		//	'Check' => array('method' => 'Check', 'Name on check' => '', 'Check address' => ''),
		//	'Wire transfer' => array('method' => 'Wire transfe', 'Account name' => '', 
		//			'Account number' => '', 'Bank name' => '', 'SWIFT code' => '', 'Additional information' => ''),
		//		'Webmoney' => array('method' => 'Webmoney', 'Webmoney WMZ' => '' ),
		//		);
		return array(
			'Paypal' => array(array('field' => 'Paypal email', 'length' => 100, 'required' => true)),
			'Check' => array(array('field' => 'Name on check', 'length' => 100, 'required' => true), 
					array('field' => 'Check address', 'length' => 255, 'required' => true)),
				'Wire transfer' => array(array('field' => 'Account name', 'length' => 100, 'required' => true),
					array('field' => 'Account number', 'length' => 20, 'required' => true),
					array('field' => 'Bank name', 'length' => 100, 'required' => true),
					array('field' => 'SWIFT code', 'length' => 20, 'required' => true),
					array('field' => 'Additional information', 'length' => 500, 'required' => false),),
				'Webmoney' => array(array('field' => 'Webmoney WMZ', 'length' => 100, 'required' => true)),
				);
	}
	
	static public function getPaymentInfor($method){
		$allInfors = self::getAllPaymentMethodsInfor();
		foreach($allInfors as $key => $value){
			if($key == $method) return $value;
		}
		return null;
	}
		
	static public function getFanSettings(){
		return array(
			'pay_per_message' => 0.25,			
			);
	}
	
	/**
	 * This method will be run every minute to check new messages
	 *
	 * @return mixed This is the return value description
	 *
	 */	
	static public function checkNewFanMessages(){
		$db = Yii::app()->db;
		$currentTime = time();
		$now = date('Y-m-d H:i:s', $currentTime);
		$today = date('Y-m-d', $currentTime);
		
		$settings = self::getFanSettings();
		$pay_per_message = $settings['pay_per_message'];
		
		$qry = "select max(id) from users_fan_messages";
		$maxId = $db->createCommand($qry)->queryScalar();
		if(!$maxId) $maxId = 1;
		
		$qry = "select m.id, m.id_to, m.id_from, 'messages' as flirt_type, m.added, 0, 'justadded', null
				from profile_messages as m
				inner join users as u on u.id = m.id_to and u.gender = 'F' and u.promo = '0' and m.id > $maxId
				inner join users_fan_settings as s on s.user_id = u.id and s.status in ('pending', 'approved') and m.added > s.joined";
		
		/*
			Only insert messages sent after user join fan-feature
		*/
		$insQry = "insert ignore into users_fan_messages
				select m.id, m.id_to, m.id_from, 'messages' as flirt_type, m.added, 0, 'justadded', null, '$now'
				from profile_messages as m
				inner join users as u on u.id = m.id_to and u.gender = 'F' and u.promo = '0' and m.id > $maxId
				inner join users_fan_settings as s on s.user_id = u.id and s.status in ('pending', 'approved') and m.added > s.joined";
		
		$ok = $db->createCommand($insQry)->execute();
		
		//More logic will do later
		$updQry = "update users_fan_messages set amt = $pay_per_message, valid = 'valid' where id > $maxId";
		$ok = $db->createCommand($updQry)->execute();
	}
	
	static public function checkDailyStatistic(){
		$db = Yii::app()->db;
		
		$currentTime = time();
		$now = date('Y-m-d H:i:s', $currentTime);
		$today = date('Y-m-d', $currentTime);
		
		$qry = "select max(reg_date) from users_fan_daily_stats";
		$maxDate = $db->createCommand($qry)->queryScalar();
		
		if(!$maxDate){
			$maxDate = '2012-07-01 00:00:00';
		}
		
		$qry = "select m.id_to, date(m.added) as `date`, 
				sum(case when m.valid = 'valid' then 1 else 0 end) as valid_count,
				sum(case when m.valid = 'justadded' then 1 else 0 end) as pending_count,
				sum(case when m.valid = 'invalid' then 1 else 0 end) as invalid_count,
				sum(m.amt) as estimate_amt
				from users_fan_messages as m
				group by m.id_to, date(m.added)
				order by m.id_to, date(m.added)";
		
		$insQry = "insert into users_fan_daily_stats
				select m.id_to, date(m.added) as `date`, 
				sum(case when m.valid = 'valid' then 1 else 0 end),
				sum(case when m.valid = 'justadded' then 1 else 0 end),
				sum(case when m.valid = 'invalid' then 1 else 0 end),
				sum(m.amt), '$now'               
				from users_fan_messages as m
				where m.reg_date > '$maxDate' and m.reg_date <= '$now'
				group by m.id_to, date(m.added)
				on duplicate key 
				update valid_count = valid_count + values(valid_count), 
					   pending_count = pending_count + values(pending_count), 
						invalid_count = invalid_count + values(invalid_count), 
						estimate_amt = estimate_amt + values(estimate_amt), 
						reg_date = values(reg_date)";
		
		$ok = $db->createCommand($insQry)->execute();
		
	}
	
	static public function checkWeeklyPayout(){
		$db = Yii::app()->db;
		
		$currentTime = time();
		$now = date('Y-m-d H:i:s', $currentTime);
		$today = date('Y-m-d', $currentTime);
		
		$weekday = date('N', $currentTime); //1~Mon -> 7~Sun
		$weekday -= 1; //Like mySQL
		
		$thisweek = date('Y-m-d 00:00:00', $currentTime - $weekday * 24*3600);
		
		//echo $thisweek;
		
		$minGoingDateQry = "select min(week) from users_fan_payout where pay_status = 'going'";
		$minGoingDate = $db->createCommand($minGoingDateQry)->queryScalar();
		if(!$minGoingDate){
			$minGoingDateQry = "select max(week) from users_fan_payout";
			$minGoingDate = $db->createCommand($minGoingDateQry)->queryScalar();
			if(!$minGoingDate){
				$minGoingDate = '2012-07-01 00:00:00';
			}
		}
		
		$pendingQry = "insert into users_fan_payout
						select m.id_to, 
							adddate(date(m.added), interval -WEEKDAY(m.added) day) as `week`,
							sum(case when m.valid = 'valid' then 1 else 0 end) as valid_count,
							sum(m.amt) as amt,
							'pending',
							'0000-00-00 00:00:00',
							null,
							null,
							null,
							'$now'
						from users_fan_messages as m
						where m.added >= '$minGoingDate' and m.added < '$thisweek'
						group by m.id_to, `week`
						on duplicate key update 
							valid_count = case when pay_status = 'going' or pay_status = 'pending' then values(valid_count) else valid_count end,
							amt = case when pay_status = 'going' or pay_status = 'pending' then values(amt) else amt end,
							pay_status = case when pay_status = 'going' or pay_status = 'pending' then values(pay_status) else pay_status end,
							reg_date = case when pay_status = 'going' or pay_status = 'pending' then values(reg_date) else reg_date end";
		$ok = $db->createCommand($pendingQry)->execute();
		
		$goingQry = "insert into users_fan_payout
					select m.id_to, 
						adddate(date(m.added), interval -WEEKDAY(m.added) day) as `week`,
						sum(case when m.valid = 'valid' then 1 else 0 end) as valid_count,
						sum(m.amt) as amt,
						'going',
						'0000-00-00 00:00:00',
						null,
						null,
						null,
						'$now'
					from users_fan_messages as m
					where m.added >= '$thisweek'
					group by m.id_to, `week`
					on duplicate key update 
						valid_count = case when pay_status = 'going' or pay_status = 'pending' then values(valid_count) else valid_count end,
						amt = case when pay_status = 'going' or pay_status = 'pending' then values(amt) else amt end,
						pay_status = case when pay_status = 'going' or pay_status = 'pending' then values(pay_status) else pay_status end,
						reg_date = case when pay_status = 'going' or pay_status = 'pending' then values(reg_date) else reg_date end";
		$ok = $db->createCommand($goingQry)->execute();
		
		$pendingQry = "";
		
	}
}