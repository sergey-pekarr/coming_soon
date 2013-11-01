<?php
class ProfileClientInfo{
	
	static function updateClientInfo($items){
		/*
		      var clientinfor = { 'screen': screenSize, 'availscreen': availscreen,
		          'screen_nor': screenSize_nor, 'availscreen_nor': availscreen_nor,
		          'browser': browser, 'agent': agent, 'platform': platform,
		          'fonts': fonts, 'plugins': plugins, 'plugins_pur': plugins_pur,
		          'timezone' : (new Date()).getTimezoneOffset(),
		          'http_accept': '',
		          'cookies': are_cookies_enabled(),
		          'supper_cookies': test_dom_storage(),
		      };
		*/
		
		$userid = Yii::app()->user->id;
		$db = Yii::app()->db;
		
		if(!isset($items['screen'])) $items['screen'] = '';
		if(!isset($items['screen_nor'])) $items['screen_nor'] = '';
		
		if(!isset($items['availscreen'])) $items['availscreen'] = '';
		if(!isset($items['availscreen_nor'])) $items['availscreen_nor'] = '';
		
		if(!isset($items['browser'])) $items['browser'] = '';
		if(!isset($items['agent'])) $items['agent'] = '';
		if(!isset($items['platform'])) $items['platform'] = '';
		if(!isset($items['fonts'])) $items['fonts'] = '';
		
		if(!isset($items['plugins'])) $items['plugins'] = '';
		if(!isset($items['plugins_pur'])) $items['plugins_pur'] = '';
		
		if(!isset($items['timezone'])) $items['timezone'] = '';
		if(!isset($items['cookies'])) $items['cookies'] = '';
		if(!isset($items['supper_cookies'])) $items['supper_cookies'] = '';
		if(!isset($items['http_accept'])) $items['http_accept'] = '';
		
		$records = array(
			'screen' => array('value' => $items['screen'], 'hash' => 's '.$items['screen']), 
			'screen_nor' => array('value' => $items['screen_nor'], 'hash' => 'sn '.$items['screen_nor']), 
			
			'availscreen' => array('value' => $items['availscreen'], 'hash' => 'a '.$items['availscreen']), 
			'availscreen_nor' => array('value' => $items['availscreen_nor'], 'hash' => 'an '.$items['availscreen_nor']), 
			
			'browser' => array('value' => $items['browser'], 'hash' => 'b '.$items['browser']), 
			'agent' => array('value' =>$items['agent'], 'hash' => 'ag '.md5($items['agent'])), 
			'platform' => array('value' => $items['platform'], 'hash' => 'p '.$items['platform']), 
			'fonts' => array('value' => $items['fonts'], 'hash' => 'f '.md5($items['fonts'])), 
			
			'plugins' => array('value' => $items['plugins'], 'hash' => 'pl '.md5($items['plugins'])),
			'plugins_pur' => array('value' => $items['plugins_pur'], 'hash' => 'plp '.md5($items['plugins_pur'])),
						
			'timezone' => array('value' => $items['timezone'], 'hash' => 'tz '.$items['timezone']), 
			'cookies' => array('value' => $items['cookies'], 'hash' => 'ck '.$items['cookies']), 
			'supper_cookies' => array('value' => $items['supper_cookies'], 'hash' => 'sck '.$items['supper_cookies']), 
			'http_accept' => array('value' => $items['http_accept'], 'hash' => 'hat '.md5($items['http_accept'])),
		);
		
		$ip = '127.0.0.1';
		$latitude = '0';
		$longitude = '0';
		
		$ip = CHelperLocation::getIPReal();//if(isset($_SERVER['REMOTE_ADDR'])) $ip = $_SERVER['REMOTE_ADDR'];
		if(isset($_SERVER['GEOIP_LATITUDE'])) $latitude = $_SERVER['GEOIP_LATITUDE'];
		if(isset($_SERVER['GEOIP_LONGITUDE'])) $longitude = $_SERVER['GEOIP_LONGITUDE'];
		
		$curQry = "select * from users_info_ex where user_id = $userid";
		$curValues = $db->createCommand($curQry)->queryRow();
		
		if(!$curValues){						
			
			$insQry = "insert into users_info_ex (user_id, screen, availscreen, browser, agent, platform, fonts, `plugins`, ip, latitude, longitude, 
													screen_nor, availscreen_nor, plugins_pur, timezone, cookies, supper_cookies, http_accept) 
						values($userid, '{$records['screen']['hash']}', '{$records['availscreen']['hash']}', '{$records['browser']['hash']}',
						'{$records['agent']['hash']}', '{$records['platform']['hash']}', '{$records['fonts']['hash']}', '{$records['plugins']['hash']}',
						'$ip', $latitude, $longitude,
						'{$records['screen_nor']['hash']}', '{$records['availscreen_nor']['hash']}', '{$records['plugins_pur']['hash']}',
						'{$records['timezone']['hash']}', '{$records['cookies']['hash']}', '{$records['supper_cookies']['hash']}', '{$records['http_accept']['hash']}')";
			$ok = $db->createCommand($insQry)->execute();
			
			self::buildUserInfoEx(array($userid));
		}
		else{
			$setFields = '';
			foreach($records as $key => $record){
				if($record['hash'] != $curValues[$key]){
					$setFields .= ($setFields == ''? '':', '). $key."='{$record['hash']}'";
				}
			}
			
			if($curValues['ip'] != $ip) $setFields .= ($setFields == ''? '':', ')."ip='$ip'";
			if($curValues['latitude'] != $latitude) $setFields .= ($setFields == ''? '':', ')."latitude=$latitude";
			if($curValues['longitude'] != $longitude) $setFields .= ($setFields == ''? '':', ')."longitude=$longitude";
			
			if($setFields != ''){
				$updQry = "update users_info_ex set $setFields where user_id = $userid";
				$ok = $db->createCommand($updQry)->execute();
			}
		}
		
		$insItems = array();
		foreach($records as $key => $record){
			$value = addslashes($record['value']);
			$unique_count = (!isset($curValues[$key]) || $curValues[$key] != $record['hash'])?1:0;
			$insItems[] = "('{$record['hash']}', '{$key}', '$value', 1, $unique_count)";
		}
		
		$insStatsQry = "insert into client_info_stats(hash, `type`, `value`, `count`, `unique_count`) values " .
				implode(',', $insItems) .
				"on duplicate key update `count` = `count` + 1, `unique_count` = `unique_count` + values(`unique_count`)";
		$ok = $db->createCommand($insStatsQry)->execute();
		
	}
	
	static private function findRecord(&$records, $fieldName, $value){
		foreach($records as $record){
			if($record[$fieldName] == $value) return $record;
		}
		return null;
	}
	
	static private function findLocationByIp($ip, &$lat = 'null', &$lon = 'null'){
		$lat = 'null';
		$lon = 'null';
		if($ip == null || $ip == '') return null;
		$record = Yii::app()->location->getGeoIPRecord($ip);
		if(!$record) return null;
		$lat = $record['GEOIP_LATITUDE'];
		$lon = $record['GEOIP_LONGITUDE'];
		return array('latitude' => $record['GEOIP_LATITUDE'], 'longitude' => $record['GEOIP_LONGITUDE']);
	}
	
	static public function updatePaymentInfo($userid){
		$db = Yii::app()->db;
		
		$payQry = "select zip, email, ip
					   from pm_transactions as p 
						where `status` = 'completed' and p.user_id = $userid";
		$pay = $db->createCommand($payQry)->queryRow();
		
		if($pay){
			self::findLocationByIp(isset($pay['ip'])?$pay['ip']:null, $pay_lat, $pay_lon);
			$pay_ip = isset($pay['ip'])?"'{$pay['ip']}'":'null';	
			$pay_zip = isset($pay['zip'])?"'{$pay['zip']}'":'null';	
			
			$insQry = "insert into users_info_ex(user_id, 
					pay_ip, pay_latitude, pay_longitude, pay_zip)
					values ($userid, $pay_ip, $pay_lat, $pay_lon, $pay_zip)
					on duplicate key update 
					pay_ip = values(pay_ip), pay_latitude = values(pay_latitude), 
					pay_longitude = values(pay_longitude), pay_zip = values(pay_zip)";
			$ok = $db->createCommand($insQry)->execute();
		}
	}
	
	static public function buildUserInfoEx($userids){
		$db = Yii::app()->db;
		
		if(count($userids) >0){
			$qry = "select l.user_id, l.latitude, l.longitude, i.ip_signup
					from users_location as l 
					inner join users_info as i on i.user_id = l.user_id
					where l.user_id in (". implode(',', $userids).")";
			$items = $db->createCommand($qry)->queryAll();
			
			$payQry = "select user_id, zip, email, ip
					   from pm_transactions as p 
						where `status` = 'completed' and p.user_id in (". implode(',', $userids).")";
			$paymentItems = $db->createCommand($payQry)->queryAll();
			
			$insItems = array();
			foreach($items as $item){
				
				$pay = self::findRecord($paymentItems, 'user_id', $item['user_id']);
				self::findLocationByIp(isset($pay['ip'])?$pay['ip']:null, $pay_lat, $pay_lon);
				$pay_ip = isset($pay['ip'])?"'{$pay['ip']}'":'null';	
				$pay_zip = isset($pay['zip'])?"'{$pay['zip']}'":'null';
				
				if($pay){
					$pay_ip = isset($pay['ip'])?"'{$pay['ip']}'":'null';	
					$pay_zip = isset($pay['zip'])?"'{$pay['zip']}'":'null';
				}		
				
				self::findLocationByIp($item['ip_signup'], $sig_lat, $sig_lon);
								
				if(!isset($item['latitude'])) $item['latitude'] = 'null';
				if(!isset($item['longitude'])) $item['longitude'] = 'null';
				
				$lat = ($pay_lat != 'null')? $pay_lat: $item['latitude'];
				$lon = ($pay_lon != 'null')? $pay_lon: $item['longitude'];				
				
				$insItems[] = "({$item['user_id']},
								 '{$item['ip_signup']}', $sig_lat, $sig_lon,
								{$item['latitude']}, {$item['longitude']},
								$pay_ip, $pay_lat, $pay_lon, $pay_zip,
								$lat, $lon
								)";
				
			}
			if(count($insItems)){
				$insQry = "insert into users_info_ex(user_id, 
					reg_ip, reg_latitude, reg_longitude, 
					sig_latitude, sig_longitude, 
					pay_ip, pay_latitude, pay_longitude, pay_zip, 
					latitude, longitude)
					values". implode(',',$insItems).
					"on duplicate key update reg_ip = values(reg_ip), reg_latitude = values(reg_latitude), reg_longitude = values(reg_longitude)
					, sig_latitude = values(sig_latitude), sig_longitude = values(sig_longitude)
					, pay_ip = values(pay_ip), pay_latitude = values(pay_latitude), pay_longitude = values(pay_longitude), pay_zip = values(pay_zip)";
				
				//Note do not update latitude and longitude. We insert them here to be compatible with old profile
				$ok = $db->createCommand($insQry)->execute();
			}
		}
	}
}