<?php
class RiskDetection{
	
	static private $config = null;
	
	static public function saveConfig($config){
		$file = dirname(dirname(__FILE__)).'/runtime/config.cng';
		
		//$config = array_merge_recursive(self::getConfig(), $config);
		
		$fh = fopen($file, 'w');
		fwrite($fh, serialize($config));
		fclose($fh);
	}
	
	static public function getConfig(){
		if(self::$config) return self::$config;
		
		$file = dirname(dirname(__FILE__)).'/runtime/config.cng';
		$readConfig = array();
		if(file_exists($file)) {
			$fh = fopen($file,'r');
			$data = fread($fh, filesize($file));
			$readConfig = unserialize($data);			
		}
		
		$configs = array('location_range' => 0.1235, //200meters
			'figure_print' => array('threshold' => 0.85, 
					'ranks' => array(
						'screen' => 4,
						'availscreen' => 4,
						'browser' => 10,
						'agent' => 10,
						'platform' => 10,
						'fonts' => 10,
						'plugins' => 6,
						'screen_nor' => 6,
						'availscreen_nor' => 6,
						'plugins_pur' => 4,
						)));
		if($readConfig){
			$configs = $readConfig; //array_merge_recursive($configs, $readConfig);
		}
		
		$figure_print = &$configs['figure_print'];
		
		$total = 0;
		foreach($figure_print['ranks'] as $rank => $value){
			$total += $value;
		}
		$levels = array();
		foreach($figure_print['ranks'] as $rank => $value){
			$levels[$rank] = $value/$total;
		}
		$figure_print['levels'] = $levels;
		
		self::$config = $configs;
		
		return $configs;
	}
	
	static private function getLocationConfig(){
		$config = self::getConfig();
		if(isset($config['location_range'])) return $config['location_range'];
		return 0.06; //~200m
	}
	
	static private function getFigurePrintConfig(){
		$config = self::getConfig();
		if(isset($config['figure_print'])) return $config['figure_print'];
		return array('threshold' => 0.5,'levels' => array());
	}
	
	static function whereLocation($lat, $lon, $range_to=30)
	{
		if($lat == null || $lon == null) return  "1";
		$lat_range = $range_to/69.172;
		$lon_range = abs($range_to/(cos($lat * pi()/180) * 69.172));
		$min_lat = number_format($lat - $lat_range, "4", ".", "");
		$max_lat = number_format($lat + $lat_range, "4", ".", "");
		$min_lon = number_format($lon - $lon_range, "4", ".", "");
		$max_lon = number_format($lon + $lon_range, "4", ".", "");
		
		$res = "(
					(i.latitude is null or i.longitude is null) 
					or (i.latitude BETWEEN '$min_lat' AND '$max_lat' 
        				 AND 
        				 i.longitude BETWEEN '$min_lon' AND '$max_lon')
				)
				AND
				(
					(i.reg_latitude is null or i.reg_longitude is null) 
					or (i.reg_latitude BETWEEN '$min_lat' AND '$max_lat' 
        				 AND 
        				 i.reg_longitude BETWEEN '$min_lon' AND '$max_lon')
				)
				AND
				(
					(i.sig_latitude is null or i.sig_longitude is null) 
					or (i.sig_latitude BETWEEN '$min_lat' AND '$max_lat' 
        				 AND 
        				 i.sig_longitude BETWEEN '$min_lon' AND '$max_lon')
				)
				AND
				(
					(i.pay_latitude is null or i.pay_longitude is null) 
					or (i.pay_latitude BETWEEN '$min_lat' AND '$max_lat' 
        				 AND 
        				 i.pay_longitude BETWEEN '$min_lon' AND '$max_lon')
				)
				";        
		return $res;
	}  
	
	static function checkLocationExp($lat, $lon, $range_to=0.03, $prefix = 'i', $base = 1)
	{
		if($lat == null || $lon == null) return  "0";
		$lat_range = $range_to/69.172;
		$lon_range = abs($range_to/(cos($lat * pi()/180) * 69.172));
		$min_lat = number_format($lat - $lat_range, "6", ".", "");
		$max_lat = number_format($lat + $lat_range, "6", ".", "");
		$min_lon = number_format($lon - $lon_range, "6", ".", "");
		$max_lon = number_format($lon + $lon_range, "6", ".", "");
		
		$res = "(
					($prefix.latitude is not null and $prefix.longitude is not null) 
					and ($prefix.latitude BETWEEN '$min_lat' AND '$max_lat' 
        				 AND 
        				 $prefix.longitude BETWEEN '$min_lon' AND '$max_lon')
				) * 1 * $base
				+
				(
					($prefix.pay_latitude is not null and $prefix.pay_longitude is not null) 
					and ($prefix.pay_latitude BETWEEN '$min_lat' AND '$max_lat' 
        				 AND 
        				 $prefix.pay_longitude BETWEEN '$min_lon' AND '$max_lon')
				) * 8 * $base
				"; 
		/*
		$res = "(
					($prefix.latitude is not null and $prefix.longitude is not null) 
					and ($prefix.latitude BETWEEN '$min_lat' AND '$max_lat' 
		      				 AND 
		      				 $prefix.longitude BETWEEN '$min_lon' AND '$max_lon')
				) * 1 * $base
				+
				(
					($prefix.reg_latitude is not null and $prefix.reg_longitude is not null) 
					and ($prefix.reg_latitude BETWEEN '$min_lat' AND '$max_lat' 
		      				 AND 
		      				 $prefix.reg_longitude BETWEEN '$min_lon' AND '$max_lon')
				) * 2 * $base
				+
				(
					($prefix.sig_latitude is not null and $prefix.sig_longitude is not null) 
					and ($prefix.sig_latitude BETWEEN '$min_lat' AND '$max_lat' 
		      				 AND 
		      				 $prefix.sig_longitude BETWEEN '$min_lon' AND '$max_lon')
				) * 4 * $base
				+
				(
					($prefix.pay_latitude is not null and $prefix.pay_longitude is not null) 
					and ($prefix.pay_latitude BETWEEN '$min_lat' AND '$max_lat' 
		      				 AND 
		      				 $prefix.pay_longitude BETWEEN '$min_lon' AND '$max_lon')
				) * 8 * $base
				";  
		*/      
		return $res;
	} 
	
	static function detectRisk(){
		$db = Yii::app()->db;
		$qry1 = "select max(id) from pm_today_risk";
		$maxRiskId = $db->createCommand($qry1)->queryScalar();
		if(!$maxRiskId) $maxRiskId = 0;
		
		$qry = "select id, user_id, manager_id, date from pm_today_gold where id > $maxRiskId order by id";
		$items = $db->createCommand($qry)->queryAll();
		if(!$items) $items = array();
		
		foreach($items as $item){
			ProfileClientInfo::updatePaymentInfo($item['user_id']);
			$qry = "insert ignore into pm_today_risk(id, user_id, reg_date) 
					values({$item['id']},{$item['user_id']}, now())";
			$ok = $db->createCommand($qry)->execute();;
			//self::detectRisk_Email($item['id'], $item['user_id']);
			//self::detectRisk_Location($item['id'], $item['user_id']);
			self::detectRisk_FigurePrint2($item['id'], $item['user_id'], $item['manager_id'], $item['date']);
		}
		return count($items);
	}
	
	static public function detectRisk_Email($id, $userid){
		
		$db = Yii::app()->db;
		
		$qry2 = "select u.email, p.email as pay_email 
				from users as u 
				inner join pm_transactions as p on u.id = p.user_id
				where u.id = $userid 
				order by p.id limit 0,1";
		$pay_email = $db->createCommand($qry2)->queryRow();
	}
	
	static public function detectRisk_Location($id, $userid){
		
		$db = Yii::app()->db;
		
		$qry = "select i.user_id, 
				i.latitude as lastlat, i.longitude as lastlon, 
				-- reg_latitude, reg_longitude, 
				-- sig_latitude, sig_longitude, 
				pay_latitude, pay_longitude
				from users_info_ex as i
				where i.user_id = $userid";
		
		$item = $db->createCommand($qry)->queryRow();
		
		if(!$item){
			return array('error' => 'User does not exists');
		}
		
		$range_to = self::getLocationConfig();
		
		$checkExp = self::checkLocationExp($item['lastlat'],$item['lastlon'], $range_to, 'i', 1)
			//. ' + ' . self::checkLocationExp($item['reg_latitude'],$item['reg_longitude'], $range_to, 'i', 16)
			//. ' + ' . self::checkLocationExp($item['sig_latitude'],$item['sig_longitude'], $range_to, 'i', 256)
			. ' + ' . self::checkLocationExp($item['pay_latitude'],$item['pay_longitude'], $range_to, 'i', 4096);
		
		$limitDate = date('Y-m-d H:i:s', time() - 182 * 24 * 3600);
		$qry = "select $checkExp as count, r.id, r.user_id, i.latitude, i.longitude, i.pay_latitude, i.pay_longitude
				from users_info_ex as i
				inner join pm_today_risk as r on i.user_id = r.user_id and r.id < $id
				where $checkExp > 0 and r.reg_date > '$limitDate' and i.user_id <> $userid
				order by r.id desc
				limit 0, 100
				";
		
		$riskItems = $db->createCommand($qry)->queryAll();
		if(!$riskItems) $riskItems = array();
		
		$idArr = array();
		$min = $range_to;
		$detailItems = array();
		if($item['lastlat'] || $item['lastlon'] || $item['pay_latitude'] || $item['pay_longitude']){
			$locationSv = Yii::app()->location;
			$curItem = array('latitude' => round($item['lastlat'],3), 'longitude' => round($item['lastlon'],3));
			if($item['lastlat'] != $item['pay_latitude'] || $item['lastlon'] != $item['pay_longitude']) {
				$curItem['pay_latitude'] = round($item['pay_latitude'],3);
				$curItem['pay_longitude'] = round($item['pay_longitude'],3);
			}
			$detailItems[] = $curItem;
			foreach($riskItems as $riskItem){
				
				$idArr[] = $riskItem['id'];
				$item['duplicate'] = $riskItem['count'];
				
				$res = $riskItem['count'];
				if($res & 1){
					$detailItems[] = "{$riskItem['id']} ".round($locationSv->calculateMileage($item['lastlat'], $riskItem['latitude'],$item['lastlon'], $riskItem['longitude']),2);
				}
				else if($res & 8){
					$detailItems[] = "{$riskItem['id']} ".round($locationSv->calculateMileage($item['lastlat'], $riskItem['pay_latitude'],$item['lastlon'], $riskItem['pay_longitude']),2);
				}
				else if($res & (1*4096)){
					$detailItems[] = "{$riskItem['id']} ".round($locationSv->calculateMileage($item['pay_latitude'], $riskItem['latitude'],$item['pay_longitude'], $riskItem['longitude']),2);
				}
				else if($res & (8*4096)){
					$detailItems[] = "{$riskItem['id']} ".round($locationSv->calculateMileage($item['pay_latitude'], $riskItem['pay_latitude'],$item['pay_longitude'], $riskItem['pay_longitude']),2);
				}
				else{
					$detailItems[] = "{$riskItem['id']} ?";
				}
			}
		}
		
		$duplicate = 'null';
		if(count($idArr)>0) $duplicate = "'".addslashes(implode(',', $idArr))."'";
		
		//$detail = addslashes(json_encode($item));
		$detail = addslashes(json_encode($detailItems));
		$insQry = "insert into pm_today_risk(id, user_id, duplicate_by_location, location_detail)
				values($id, $userid, $duplicate, '$detail')
				on duplicate key update duplicate_by_location = values(duplicate_by_location)
				, location_detail = values(location_detail)
				, reg_date = now()";
		$ok = $db->createCommand($insQry)->execute();		
	}
	
	/*
		
		if($item['lastlat'] != null) $lat = $item['lastlat'];
		else if($item['pay_latitude'] != null) $lat = $item['pay_latitude'];
		else if($item['reg_latitude'] != null) $lat = $item['reg_latitude'];
		else if($item['sig_latitude'] != null) $lat = $item['sig_latitude'];
		else return array('error' => 'Position does not exists');
		
		if($item['lastlon'] != null) $lon = $item['lastlon'];
		else if($item['pay_longitude'] != null) $lon = $item['pay_longitude'];
		else if($item['reg_longitude'] != null) $lon = $item['reg_longitude'];
		else if($item['sig_longitude'] != null) $lon = $item['sig_longitude'];
		else return array('error' => 'Position does not exists');
		
		$range_to = self::getLocationConfig();
		
		$lat_range = $range_to/69.172;
		$lon_range = abs($range_to/(cos($lat * pi()/180) * 69.172));
		$min_lat = number_format($lat - $lat_range, "4", ".", "");
		$max_lat = number_format($lat + $lat_range, "4", ".", "");
		$min_lon = number_format($lon - $lon_range, "4", ".", "");
		$max_lon = number_format($lon + $lon_range, "4", ".", "");
	*/
	
	/*
	-- FIX ERROR FOR availscreen_nor

	update users_info_ex set availscreen_nor = 
	-- select availscreen, locate('x', availscreen), substring(availscreen, 3, locate('x', availscreen) -1 - 2), 
	case when cast(substring(availscreen, 3, locate('x', availscreen) -1 - 2) as SIGNED) >= cast(substring(availscreen, locate('x', availscreen) + 1) as SIGNED) 
	   then concat('an ', substring(availscreen, 3, locate('x', availscreen) -1 - 2), 'x', substring(availscreen, locate('x', availscreen) + 1))
	   else concat('an ', substring(availscreen, locate('x', availscreen) + 1), 'x', substring(availscreen, 3, locate('x', availscreen) -1 - 2))
	end
	-- from users_info_ex
	where availscreen is not null and availscreen <> '';

	select availscreen, availscreen_nor
	from users_info_ex
	where availscreen is not null and availscreen <> '';


	delete from client_info_stats where `type` = 'availscreen_nor';

	insert into client_info_stats(hash, `type`, count, unique_count)
	(
	select i.availscreen_nor as availscreen_nor, 'availscreen_nor',  count(*) as count,  count(*) as count
	from users_info_ex as i
	where availscreen is not null and availscreen <> ''
	group by i.availscreen_nor
	);

	select * from client_info_stats where `type` = 'availscreen_nor' order by count desc;
	select * from client_info_stats where `type` = 'screen_nor' order by count desc;
	-- Test
	select availscreen
	from users_info_ex
	where availscreen is not null and availscreen <> ''
	And cast(substring(availscreen_nor, 3, locate('x', availscreen) -1 - 2) as SIGNED) < cast(substring(availscreen_nor, locate('x', availscreen) + 1) as SIGNED);
	*/
	static public function detectRisk_FigurePrint($id, $userid){
		$db = Yii::app()->db;
		
		$qry = "select i.screen, i.availscreen, i.browser, i.agent, i.platform, i.fonts, 
						i.plugins, i.screen_nor, i.availscreen_nor, i.plugins_pur
				from users_info_ex as i
				where i.user_id = $userid";
		$item = $db->createCommand($qry)->queryRow();
		
		if(!$item){
			return array('error' => 'User does not exists');
		}		
		
		$limitDate = date('Y-m-d H:i:s', time() - 182 * 24 * 3600);
		$qry = "select r.id, r.user_id, i.screen, i.availscreen, i.browser, i.agent, i.platform, i.fonts, 
						i.plugins, i.screen_nor, i.availscreen_nor, i.plugins_pur
				from users_info_ex as i
				inner join pm_today_risk as r on i.user_id = r.user_id and r.id < $id
				where r.reg_date > '$limitDate' and i.user_id <> $userid
				and (i.screen = '{$item['screen']}' or  i.availscreen = '{$item['availscreen']}' or  i.browser = '{$item['browser']}'  or 
					i.agent = '{$item['agent']}' or  i.platform = '{$item['platform']}' or  i.fonts = '{$item['fonts']}' or  
					i.plugins = '{$item['plugins']}' or  i.screen_nor = '{$item['screen_nor']}' or  i.availscreen_nor = '{$item['availscreen_nor']}' or  
					i.plugins_pur = '{$item['plugins_pur']}')
				order by r.id desc
				limit 0,100";
		
		$riskItems = $db->createCommand($qry)->queryAll();
		if(!$riskItems) $riskItems = array();
		
		$idArr = array();
		$detailRisk = array();
		$list = array();
		$max = 0;
		foreach($riskItems as $riskItem){
			$explain = null;
			$res = self::checkRisk(
				$riskItem['screen'] == $item['screen']?$item['screen']:'',
				$riskItem['availscreen'] == $item['availscreen']?$item['availscreen']:'',
				$riskItem['browser'] == $item['browser']?$item['browser']:'',
				$riskItem['agent'] == $item['agent']?$item['agent']:'',
				$riskItem['platform'] == $item['platform']?$item['platform']:'',
				$riskItem['fonts'] == $item['fonts']?$item['fonts']:'',
				$riskItem['plugins'] == $item['plugins']?$item['plugins']:'',
				$riskItem['screen_nor'] == $item['screen_nor']?$item['screen_nor']:'',
				$riskItem['availscreen_nor'] == $item['availscreen_nor']?$item['availscreen_nor']:'',
				$riskItem['plugins_pur'] == $item['plugins_pur']?$item['plugins_pur']:'', $explain
				);
			
			if($res){
				$idArr[] = $riskItem['id'];
				$riskItem['risk'] = $res;
				$detailRisk[] = $riskItem;
				$list[$riskItem['id']] = array($res, $explain);
				if($res>$max) $max = $res;
			}		
		}
		
		$duplicate = 'null';
		if(count($idArr)>0) $duplicate = "'".addslashes(implode(',', $idArr))."'";
		
		$ls = 'null';
		$ls = "'".addslashes(json_encode($list))."'";
		
		$item['riskDetail'] = $detailRisk;
		$detail = addslashes(json_encode($item));
		$insQry = "insert into pm_today_risk(id, user_id, duplicate_by_figure_print, figure_print_detail, figure_print_risk_detail, max_risk)
				values($id, $userid, $duplicate, '$detail', $ls, $max)
				on duplicate key update duplicate_by_figure_print = values(duplicate_by_figure_print)
				, figure_print_detail = values(figure_print_detail), figure_print_risk_detail = values(figure_print_risk_detail)
				, reg_date = now(), max_risk = values(max_risk)";
		$ok = $db->createCommand($insQry)->execute();	
	}
	
	static private function getRealHashValue($orgValue){
		if(($f = strpos($orgValue, ' ')) && $f + 1 < strlen($orgValue)) $orgValue = substr($orgValue, $f+1, strlen($orgValue));
		return $orgValue;
	}
	
	static public function checkBrowserCompartible($b1, $b2){
		$ba1 = explode(' ', $b1);
		$ba2 = explode(' ', $b2);
		
		if(count($ba1) < 3 || count($ba2) < 3) return false;
		
		if($ba1[1] != $ba2[1]) return false;
		
		$sa1 = strpos($ba1[2], ',') === false?explode('.', $ba1[2]): explode(',', $ba1[2]);
		$sa2 = strpos($ba2[2], ',') === false?explode('.', $ba2[2]): explode(',', $ba2[2]);
		
		$i = 0;
		for($i=0;$i<count($sa1);$i++){
			if($i < count($sa2) && intval($sa1[$i]) > intval($sa2[$i])) return false;
			if($i >= count($sa2)) return false;
		}
		
		if(count($sa1) > count($sa2)) return false;
		
		//if(intval($ba1[2]) > intval($ba2[2])) return false;
		
		return true;
	}
	
	static private function removeAgentVersion($agent){
		return preg_replace(array('/(\W+)\d+((\.\d+)*)/', '/(_\d+)+/', '/\/\w+/'),array('\1', '',''),$agent);
	}
	
	static private function checkAgentCompartible($a1, $a2){
		return self::removeAgentVersion($a1) == self::removeAgentVersion($a2);
	}	
	
	static private function showDistinct($v1, $v2, $sep = 'vs.'){
		if($v1 != $v2) return "$v1 $sep $v2";
		return $v1;
	}
	
	static public function detectRisk_FigurePrint2($id, $userid, $managerid, $regdate = null){
		$db = Yii::app()->db;
		
		$qry = "select i.screen, i.availscreen, i.browser, i.agent, i.platform, i.fonts, 
						i.plugins, i.screen_nor, i.availscreen_nor, i.plugins_pur, 
						timezone, cookies, supper_cookies, http_accept, s.value as agent_pur,
						$managerid as manager_id
				from users_info_ex as i
				inner join pm_today_risk as r on i.user_id = r.user_id
				inner join client_info_stats as s on s.hash = i.agent
				where i.user_id = $userid";
		$item = $db->createCommand($qry)->queryRow();
		
		if(!$item){
			return array('error' => 'User does not exists');
		}		
		
		if($regdate == null){
			$limitDate = date('Y-m-d H:i:s', time() - 30 * 24 * 3600);
		}
		else{
			$limitDate = date('Y-m-d H:i:s', strtotime($regdate) - 30 * 24 * 3600);
		}
		
		$whereEx = '';
		if($limitDate >= '2012-10-01 00:00:00'){
			$whereEx = "and ifnull(i.timezone,'') = '{$item['timezone']}'";
		}
		$qry = "select r.id, r.user_id, i.screen, i.availscreen, i.browser, i.agent, i.platform, i.fonts, 
						i.plugins, i.screen_nor, i.availscreen_nor, i.plugins_pur, 
						timezone, cookies, supper_cookies, http_accept, s.value as agent_pur,
						g.agent_name, g.username
				from users_info_ex as i
				inner join pm_today_risk as r on i.user_id = r.user_id and r.id < $id
				inner join pm_today_gold as g on g.user_id = i.user_id
				inner join client_info_stats as s on s.hash = i.agent
				where g.date > '$limitDate' and i.user_id <> $userid and g.manager_id = {$item['manager_id']}
				and (i.screen = '{$item['screen']}'and i.platform = '{$item['platform']}' and  i.fonts = '{$item['fonts']}' 
					-- and  i.plugins = '{$item['plugins']}' and  i.browser = '{$item['browser']}'  and i.agent = '{$item['agent']}' 
					and i.fonts <> 'f d41d8cd98f00b204e9800998ecf8427'
					and i.plugins <> 'pl d41d8cd98f00b204e9800998ecf8427e'
					and i.browser <> 'b '
					and i.platform <> 'iPhone' and i.platform <> 'iPad' and i.platform <> 'iPod' and i.platform <> 'Android'
					and cast(substring(i.screen_nor, 4, locate('x', i.screen_nor) - 4) as SIGNED) >= 1024
					-- and cast(substring(i.screen_nor, 4, locate('x', i.screen_nor) - 4) as SIGNED) <> 1366
					$whereEx
					)
				order by r.id desc
				limit 0,100";
		//if($id == 9594){
		//	echo $qry;
		//	echo date('Y-m-d H:i:s', strtotime($regdate)).'   '.$regdate;
		//	echo "\r\n";
		//}
		//  timezone, cookies, supper_cookies, http_accept
		$riskItems = $db->createCommand($qry)->queryAll();
		if(!$riskItems) $riskItems = array();
		
		$idArr = array();
		$idSubArr = array();
		$detailArr = array(array("screen" => self::getRealHashValue($item['screen']),
			"platform" => self::getRealHashValue($item['platform']),
			"timezone" => self::getRealHashValue($item['timezone']),
			"cookies" => self::getRealHashValue($item['cookies']),
			"supper_cookies" => self::getRealHashValue($item['supper_cookies']),
			"http_accept" => "same", //self::getRealHashValue($item['http_accept']),
			"agent" => self::removeAgentVersion($item['agent_pur']),
			"plugins" => "same",
			"fonts" => "same",
			"browser" => self::getRealHashValue($item['browser']),
			"availscreen" => self::getRealHashValue($item['availscreen']),
			"plugins-hash" => substr(self::getRealHashValue($item['plugins']),0,10).'...',
			"fonts-hash" => substr(self::getRealHashValue($item['fonts']),0,10).'...',
			));
		$diffvalues = array(
			'availscreen' => array(self::getRealHashValue($item['availscreen'])), 
			'browser' => array(self::getRealHashValue($item['browser']))
			);
		foreach($riskItems as $riskItem){
			$idArr[] = $riskItem['id'];
			
			//$detailItem = "{$riskItem['id']}-{$riskItem['username']}-{$riskItem['agent_name']}".
			//	"; fonts ".self::getRealHashValue($riskItem['timezone'])
			//;
			
			if( self::checkBrowserCompartible($riskItem['browser'],$item['browser']) 
				&& self::checkAgentCompartible($riskItem['agent_pur'],$item['agent_pur'])
				&& $riskItem['plugins_pur'] == $item['plugins_pur']
				&& ($whereEx == '' || (
							$riskItem['cookies'] == $item['cookies'] && $riskItem['supper_cookies'] == $item['supper_cookies'] 
							&& $riskItem['http_accept'] == $item['http_accept']
							))
				){
				$idSubArr[] = $riskItem['id'];
				//$detailItem .= "; browser ".self::getRealHashValue($item['browser']).
				//	"; cookies ".self::getRealHashValue($item['cookies']).
				//	"; supper_cookies ".self::getRealHashValue($item['supper_cookies']).
				//	"; agent ".self::removeAgentVersion($item['agent_pur']).
				//	"; plugins "."; http_accept ";
				
				if($riskItem['browser'] != $item['browser']) $diffvalues['browser'][] = self::getRealHashValue($riskItem['browser']);
				if($riskItem['availscreen'] != $item['availscreen']) $diffvalues['availscreen'][] = self::getRealHashValue($riskItem['availscreen']);
				
			}
			
			//$detailArr[] = $detailItem;	
		}
		//$tmp = $detailArr[0];
		//$tmp['browser'] = implode(' vs. ', $diffvalues['browser']);
		//$tmp['availscreen'] = implode(' vs. ', $diffvalues['availscreen']);
		
		$detailArr[0]['browser'] = implode(' vs. ', $diffvalues['browser']);
		$detailArr[0]['availscreen'] = implode(' vs. ', $diffvalues['availscreen']);
		
		
		$duplicate = 'null';
		$sub = 'null';
		$detail = 'null';
		//if(count($idArr)>0) $duplicate = "'".addslashes(implode(',', $idArr))."'";	
		//if(count($idSubArr)>0) $sub = "'".addslashes(implode(',', $idSubArr))."'";	
		
		if(count($idSubArr)>0 ){
			//n 2012 Oct 01: Only check if browser is same! Check cross browser confuse Chris			
			
			if(count($idSubArr)>0) $duplicate = "'".addslashes(implode(',', $idSubArr))."'";
			
			//Show location infor (show nearest location between user) X-Y: 0.1
			$detailArr[] = self::checkLocation($id, $idSubArr);
			
			//Show credit card infor
			//$detailArr[] = self::checkCreditCard($id, $idSubArr);
			
			if(count($detailArr)>0) $detail = "'".addslashes(json_encode($detailArr))."'";
		}
		
		$insQry = "insert into pm_today_risk(id, user_id, duplicate_by_figure_print, figure_print_detail, figure_print_risk_detail, max_risk)
				values($id, $userid, $duplicate, $detail, $sub, 1)
				on duplicate key update duplicate_by_figure_print = values(duplicate_by_figure_print)
				, figure_print_detail = values(figure_print_detail), figure_print_risk_detail = values(figure_print_risk_detail)
				, reg_date = now(), max_risk = values(max_risk)";
		$ok = $db->createCommand($insQry)->execute();	
	}
	
	static private function checkLocation($id, $idSubArr){
		$result = array();
		if(count($idSubArr) ==0) return $result;
		$sql = "select g.user_id, g.id, e.pay_latitude, e.pay_longitude
				from pm_today_gold as g
				inner join users_info_ex as e on e.user_id = g.user_id
				where g.id in ($id, " . implode(',', $idSubArr) . ")";
		$items = Yii::app()->db->createCommand($sql)->queryAll();
		
		if(!$items || count($items) <= 1) return $result;
		$item0 = null;
		foreach($items as $item){
			if($item['id'] == $id){
				$item0 = $item;
				break;
			}
		}
		
		if(!$item0) return $result;
		$cLocation = Yii::app()->location;
		foreach($items as $item){
			if($item['id'] == $id){
				continue;
			}
			$result["{$item0['id']}-{$item['id']}"] = round($cLocation->calculateMileage($item0['pay_latitude'], $item['pay_latitude'],
				$item0['pay_longitude'], $item['pay_longitude']), 1);
		}
		return $result;
	}
	
	static private function checkCreditCard($id, $idSubArr){
		$result = array();
		if(count($idSubArr) ==0) return $result;
		$sql = "select -- g.user_id, g.user_name, g.email, 
						t.ccname, t.ccnum, t.firstname, t.lastname, t.address, t.country, t.state, t.city, t.email 
				from pm_today_gold as g
				inner join pm_transactions as t on t.user_id = g.user_id and t.date = g.date and t.status = 'completed' and g.progress = 'done'
				where g.id in ($id, " . implode(',', $idSubArr) . ")";
		$items = Yii::app()->db->createCommand($sql)->queryAll();		
	}
	
	static private $totalItem = null;
	
	static public function getTotalItems(){
		if(self::$totalItem) return self::$totalItem;
		
		$db = Yii::app()->db;
		$totalQry = "select `type`, sum(unique_count) as count from client_info_stats group by `type`";
		$items = $db->createCommand($totalQry)->queryAll();
		$res = array();
		foreach($items as $item){
			$res[$item['type']] = $item['count'];
		}
		self::$totalItem = $res;
		return $res;
	}
	
	/**
	 * This is method checkR
	 *
	 * @return true / false
	 *
	 */	
	static public function checkRisk($screen, $availscreen, $browser, $agent, $platform, $fonts, $plugins, $screen_nor, $availscreen_nor, $plugins_pur, &$explain = null){
		
		$db = Yii::app()->db;
		
		$qry = "select hash, `type`, unique_count
				from client_info_stats
				where hash in ('$screen', '$availscreen', '$browser', '$agent', '$platform', '$fonts', 
				'$plugins', '$screen_nor', '$availscreen_nor', '$plugins_pur')";
		$items = $db->createCommand($qry)->queryAll();
		
		//$totalQry = "select sum(unique_count) from client_info_stats group by `type`";
		//$totalItem = $db->createCommand($totalQry)->queryScalar();
		
		$totalItems = self::getTotalItems();
		
		$config = self::getFigurePrintConfig();		
		$levels = $config['levels'];
		$threshold = $config['threshold'];
		
		if(!$items || !$totalItems) return 0;
		
		$totalField = 10;
		$totalIndepent = 7;
		$defaultLevel = 1/$totalIndepent;
		//$totalItem = $totalItem/$totalField;
		
		$s = 1;
		$explain = array();
		foreach($items as $item){
			$key = $item['type'];
			$value = $item['unique_count'];
			$level = isset($levels[$key])?$levels[$key]:$defaultLevel;
			if(!isset($totalItems[$key])) continue;
			$totalItem = $totalItems[$key];
			$s = $s * pow($value/$totalItem, $level);
			
			$orgValue = $item['hash'];
			if(($f = strpos($orgValue, ' ')) && $f + 1 < strlen($orgValue)) $orgValue = substr($orgValue, $f+1, strlen($orgValue));
			$explain[] = "{$item['type']} $orgValue $value/$totalItem";
		}
		
		$s = round(1 - $s,2);
		
		if($s < $threshold) return 0;
		
		return $s;
	}
	
	static public function checkAndSendMail(){
		if(date('H', time()) != '5') return;
		
		$qry = "select max(reported)
				from pm_today_risk
				where reported is not null and duplicate_by_figure_print is not null";
		$lastReported = Yii::app()->db->createCommand($qry)->queryScalar();
		
		if(!$lastReported || strtotime($lastReported) + 24 * 3600 - 15 <= time()){
			$limitTime = date("Y-m-d H:i:s", time() - 24 * 3600 - 60);
			$qry = "select r.*, g.manager_id
					from pm_today_risk as r
					inner join pm_today_gold as g on g.id = r.id
					where duplicate_by_figure_print is not null and reported is null
					and reg_date >= '$limitTime'
					order by id";
			$items = Yii::app()->db->createCommand($qry)->queryAll();
			
			if(count($items) > 0){
				$ids = '';
				$cardIds = '';
				foreach($items as $item){
					$ids .= ($ids == ''?'':',').$item['id'];
					$cardIds .= ($cardIds == ''?'':',').$item['id'];
					$cardIds .= ($cardIds == ''?'':',').$item['duplicate_by_figure_print'];
				}	
				$from = "admin@".SITE;
				$to = 'admin@pinkmeets.com';
				$subject = 'Today fraud: '.date('Y-m-d', time());
				$link = SITE_URL."/admin/risk?ids=$ids";
				$cardLink = SITE_URL."/admin/risk/affcardinfo?AffCardInfoForm%5Bids%5D=$cardIds&AffCardInfoForm%5Baff%5D={$items[0]['manager_id']}";
				$body = "<h3>Fauds on ".date('Y-m-d', time()).". Please click the links below: </h3>
						<ul>
						<li>Possible frauds: <a href='$link'>Possible frauds</a></li>
						<li>Card information: <a href='$cardLink'>Card information</a></li>
						</ul>";
				//$message->ReplyTo = $row['from'];					
				//Yii::app()->mail->sendSimple($from, $to, $subject, $body);
				
				$message = new YiiMailMessage;			
				$message->setTo($to);
				$message->from = $from;        
				$message->setBody($body, 'text/html', 'utf-8');
				$message->subject = $subject;
				//$message->ReplyTo = $from;
				Yii::app()->mail->send($message);
				//mail($to, $subject, $body, array('from', $from));
			}
			
			$qry = "update pm_today_risk
					set reported = now()
					where reported is null";
			$items = Yii::app()->db->createCommand($qry)->execute();			
		}
	}
}
