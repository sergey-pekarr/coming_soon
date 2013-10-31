<?php

///define( "GEO_IP_CITY_DATABASE_PATH", dirname(__FILE__).'/../../../../GeoIPCity.dat' );

class CLocation extends CApplicationComponent
{
    public $geoFilePath = '/usr/share/GeoIP/GeoIPCity.dat';
    ///public $geoFilePath = GEO_IP_CITY_DATABASE_PATH;
    public $testIP="";
    public $defaultLocationId;
    
    private $gi;
    
    private $GEOIP_COUNTRY_CODES;//array from vendors/GeoIP/geoip.inc
    private $GEOIP_COUNTRY_CODES3;//array from vendors/GeoIP/geoip.inc
    private $GEOIP_COUNTRY_NAMES;//array from vendors/GeoIP/geoip.inc
    private $GEOIP_REGION_NAME;//array from /vendors/GeoIP/geoipregionvars.php

    private $zip_table = 'location_zip';
    //private $zip_table = 'location_zip_new';

    
    public function init()
	{
		parent::init();

        include_once( Yii::app()->basePath."/vendors/GeoIP/geoipcity.inc" );
        
        $GeoIP = new GeoIP;
        $this->GEOIP_COUNTRY_CODES  = $GeoIP->GEOIP_COUNTRY_CODES;
        $this->GEOIP_COUNTRY_CODES3  = $GeoIP->GEOIP_COUNTRY_CODES3;
        $this->GEOIP_COUNTRY_NAMES  = $GeoIP->GEOIP_COUNTRY_NAMES;
        $this->GEOIP_REGION_NAME    = $GEOIP_REGION_NAME;
	}
    
    
    public function getIPReal()
    {
        if ($this->testIP)
        {
            return $this->testIP;
        }
        
        /*$ip = '';
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
        {
          $ips = @explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
          $ip = trim($ips[0]);
        }
                
        if (!$ip)
        {
          $ip = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
        }
        return $ip;*/
        
        return CHelperLocation::getIPReal();
    }    
    
    
    public function getGeoIPRecord($ip='')
    {
        $ip = ($ip) ? $ip : $this->getIPReal();
        
        $res = array('IP'=>$ip, 'GEOIP_COUNTRY_CODE'=>"", 'GEOIP_COUNTRY_NAME'=>"", 'GEOIP_REGION'=>"", 'GEOIP_CITY'=>"", 'GEOIP_ZIP'=>"", 'GEOIP_LATITUDE'=>0, 'GEOIP_LONGITUDE'=>0, 'success'=>0);

        $mkey = "GeoIPRecord_{$ip}_";
        $resCache = Yii::app()->cache->get($mkey);
        if ( $resCache === false )
        {
            /*if 
            ( 
				isset($_SERVER['GEOIP_COUNTRY_CODE']) && isset( $_SERVER['GEOIP_REGION'] ) && isset($_SERVER['GEOIP_CITY'])            
				&& 
				$_SERVER['GEOIP_COUNTRY_CODE'] && $_SERVER['GEOIP_REGION'] && $_SERVER['GEOIP_CITY'] 
            )
            {
                $res['GEOIP_COUNTRY_CODE'] = $_SERVER['GEOIP_COUNTRY_CODE'];
                $res['GEOIP_COUNTRY_NAME'] = $_SERVER['GEOIP_COUNTRY_NAME'];
                $res['GEOIP_REGION'] = $_SERVER['GEOIP_REGION'];
                //???$res['GEOIP_REGION_NAME'] = $_SERVER['GEOIP_REGION_NAME'];
                $res['GEOIP_CITY'] = $_SERVER['GEOIP_CITY'];
                $res['GEOIP_ZIP'] = ""; 
                $res['GEOIP_LATITUDE'] = $_SERVER['GEOIP_LATITUDE'];
                $res['GEOIP_LONGITUDE'] = $_SERVER['GEOIP_LONGITUDE'];
                $res['success'] = 1;
            }
            else*/
            {
                $this->gi = geoip_open($this->geoFilePath,GEOIP_STANDARD);
                               
                $record = GeoIP_record_by_addr($this->gi, $ip);
FB::error($record);
//CHelperSite::vd($record, 0);                  
                if ( isset($record->country_code) && $record->country_code )
                {
                    $res['GEOIP_COUNTRY_CODE'] = $record->country_code;
                    $res['GEOIP_COUNTRY_NAME'] = $record->country_name;
                    $res['GEOIP_REGION'] = $record->region;
                    $res['GEOIP_REGION_NAME'] = $this->getRegionName($record->country_code, $record->region);//$GEOIP_REGION_NAME[$record->country_code][$record->region];
                    $res['GEOIP_CITY'] = $record->city;
                    $res['GEOIP_ZIP'] = $record->postal_code;
                    $res['GEOIP_LATITUDE'] = $record->latitude;
                    $res['GEOIP_LONGITUDE'] = $record->longitude;
                    $res['success'] = 1;
                }
                geoip_close($this->gi);          
            }
            
            //���� ������ �� ��� ������ 
            /*if ( $res['GEOIP_COUNTRY_CODE'] && !$res['GEOIP_CITY'] )
            {
                $idTmp = $this->findCityInCountryWithBiggestPopulation($res['GEOIP_COUNTRY_CODE']);
                $locTmp = $this->getLocation($idTmp);
                
//country, city, state, state as stateName, latitude, longitude                
                //$res['GEOIP_COUNTRY_CODE'] = ;
                //$res['GEOIP_COUNTRY_NAME'] = ;
                $res['GEOIP_REGION'] = $locTmp['state'];
                $res['GEOIP_REGION_NAME'] = $this->getRegionName($res['GEOIP_COUNTRY_CODE'], $res['GEOIP_REGION']);
                $res['GEOIP_CITY'] = $locTmp['city'];// Pentae skype 2011-10-14 'New York';
                $res['GEOIP_ZIP'] = '';
                $res['GEOIP_LATITUDE'] = $locTmp['latitude'];
                $res['GEOIP_LONGITUDE'] = $locTmp['longitude'];
                $res['success'] = 1;
            }
            
            if ( !$res['GEOIP_COUNTRY_CODE'] || !$res['GEOIP_CITY'] || !$res['GEOIP_LATITUDE'] || !$res['GEOIP_LONGITUDE'] )
            {
                $res['GEOIP_COUNTRY_CODE'] = 'US';
                $res['GEOIP_COUNTRY_NAME'] = 'USA';
                $res['GEOIP_REGION'] = 'NY';
                $res['GEOIP_REGION_NAME'] = $this->getRegionName($res['GEOIP_COUNTRY_CODE'], $res['GEOIP_REGION']);
                $res['GEOIP_CITY'] = 'Your area';// Pentae skype 2011-10-14 'New York';
                $res['GEOIP_ZIP'] = '10079';
                $res['GEOIP_LATITUDE'] = 0;
                $res['GEOIP_LONGITUDE'] = 0;
                $res['success'] = 0;
            }*/
            
            $res['IP'] = $ip;
FB::error($res);            
            Yii::app()->cache->set($mkey, $res);    
        }
        else
        	$res = $resCache;
        
        return $res;
    }

    public function showGeoIPRecordTEST($ip='')
    {
        $ip = ($ip) ? $ip : $this->getIPReal();
        
        $res = array('IP'=>$ip, 'GEOIP_COUNTRY_CODE'=>"", 'GEOIP_COUNTRY_NAME'=>"", 'GEOIP_REGION'=>"", 'GEOIP_CITY'=>"", 'GEOIP_ZIP'=>"", 'GEOIP_LATITUDE'=>0, 'GEOIP_LONGITUDE'=>0, 'success'=>0);

        //$mkey = "GeoIPRecord_{$ip}_TEST_";
        //if ( !$res = Yii::app()->cache->get($mkey) )
        {
            CHelperSite::vd("IP: ".$ip, 0);
            
            CHelperSite::vd("SERVER['GEOIP_COUNTRY_CODE']: ".$_SERVER['GEOIP_COUNTRY_CODE'], 0);
            CHelperSite::vd("SERVER['GEOIP_DMA_CODE']: ".$_SERVER['GEOIP_DMA_CODE'], 0);
            CHelperSite::vd("SERVER['GEOIP_AREA_CODE']: ".$_SERVER['GEOIP_AREA_CODE'], 0);
            CHelperSite::vd("SERVER['GEOIP_REGION']: ".$_SERVER['GEOIP_REGION'], 0);
            CHelperSite::vd("SERVER['GEOIP_CITY']: ".$_SERVER['GEOIP_CITY'], 0);
            CHelperSite::vd("SERVER['GEOIP_LATITUDE']: ".$_SERVER['GEOIP_LATITUDE'], 0);
            CHelperSite::vd("SERVER['GEOIP_LONGITUDE']: ".$_SERVER['GEOIP_LONGITUDE'], 0);

            CHelperSite::vd('*******************************************************************', 0);
            
            /*if ( $_SERVER['GEOIP_COUNTRY_CODE'] && $_SERVER['GEOIP_REGION'] && $_SERVER['GEOIP_CITY'] )
            {
                $res['GEOIP_COUNTRY_CODE'] = $_SERVER['GEOIP_COUNTRY_CODE'];
                $res['GEOIP_COUNTRY_NAME'] = $_SERVER['GEOIP_COUNTRY_NAME'];
                $res['GEOIP_REGION'] = $_SERVER['GEOIP_REGION'];
                //???$res['GEOIP_REGION_NAME'] = $_SERVER['GEOIP_REGION_NAME'];
                $res['GEOIP_CITY'] = $_SERVER['GEOIP_CITY'];
                $res['GEOIP_ZIP'] = ???; 
                $res['GEOIP_LATITUDE'] = $_SERVER['GEOIP_LATITUDE'];
                $res['GEOIP_LONGITUDE'] = $_SERVER['GEOIP_LONGITUDE'];
                $res['success'] = 1;
            }
            else*/
            {
                $this->gi = geoip_open($this->geoFilePath,GEOIP_STANDARD);
                               
                $record = GeoIP_record_by_addr($this->gi, $ip);
                
                CHelperSite::vd($record,0);
                 
                if ($record->country_code)
                {
                    $res['GEOIP_COUNTRY_CODE'] = $record->country_code;
                    $res['GEOIP_COUNTRY_NAME'] = $record->country_name;
                    $res['GEOIP_REGION'] = $record->region;
                    $res['GEOIP_REGION_NAME'] = $this->getRegionName($record->country_code, $record->region);//$GEOIP_REGION_NAME[$record->country_code][$record->region];
                    $res['GEOIP_CITY'] = $record->city;
                    $res['GEOIP_ZIP'] = $record->postal_code;
                    $res['GEOIP_LATITUDE'] = $record->latitude;
                    $res['GEOIP_LONGITUDE'] = $record->longitude;
                    $res['success'] = 1;
                }
                geoip_close($this->gi);          
            }
            
            //���� ������ �� ��� ������ 
            if ( $res['GEOIP_COUNTRY_CODE'] && !$res['GEOIP_CITY'] )
            {
                $idTmp = $this->findCityInCountryWithBiggestPopulation($res['GEOIP_COUNTRY_CODE']);
                $locTmp = $this->getLocation($idTmp);
                
//country, city, state, state as stateName, latitude, longitude                
                //$res['GEOIP_COUNTRY_CODE'] = ;
                //$res['GEOIP_COUNTRY_NAME'] = ;
                $res['GEOIP_REGION'] = $locTmp['state'];
                $res['GEOIP_REGION_NAME'] = $this->getRegionName($res['GEOIP_COUNTRY_CODE'], $res['GEOIP_REGION']);
                $res['GEOIP_CITY'] = $locTmp['city'];// Pentae skype 2011-10-14 'New York';
                $res['GEOIP_ZIP'] = '';
                $res['GEOIP_LATITUDE'] = $locTmp['latitude'];
                $res['GEOIP_LONGITUDE'] = $locTmp['longitude'];
                $res['success'] = 1;
            }
            
            if ( !$res['GEOIP_COUNTRY_CODE'] || !$res['GEOIP_CITY'] || !$res['GEOIP_LATITUDE'] || !$res['GEOIP_LONGITUDE'] )
            {
                $res['GEOIP_COUNTRY_CODE'] = 'US';
                $res['GEOIP_COUNTRY_NAME'] = 'USA';
                $res['GEOIP_REGION'] = 'NY';
                $res['GEOIP_REGION_NAME'] = $this->getRegionName($res['GEOIP_COUNTRY_CODE'], $res['GEOIP_REGION']);
                $res['GEOIP_CITY'] = 'Your area';// Pentae skype 2011-10-14 'New York';
                $res['GEOIP_ZIP'] = '10079';
                $res['GEOIP_LATITUDE'] = 0;
                $res['GEOIP_LONGITUDE'] = 0;
                $res['success'] = 0;
            }
            
            $res['IP'] = $ip;
            
            //Yii::app()->cache->set($mkey, $res);    
        }
        CHelperSite::vd($res, 0);
    }

    
    /**
     * return array('code'=>name) of countries from DB table location_countries
     */
    public function getCountriesList($skipBlack=false)
    {
        $blackListCountries = CHelperSite::getBlockedCountry();
    	
    	foreach ($this->GEOIP_COUNTRY_CODES as $k=>$v)
        {
            if ($skipBlack || ($skipBlack==false && !in_array($v, $blackListCountries) ))
        		$res[$v] = $this->GEOIP_COUNTRY_NAMES[$k];
        }
        
        unset($res['']);
        unset($res['UM']);
        
        unset($res['A1']);//Anonymous Proxy
        unset($res['AQ']);//Antarctica
        unset($res['AP']);//Asia/Pacific Region
        unset($res['EU']);//Europe
        unset($res['FX']);//France, Metropolitan
        unset($res['TF']);//French Southern Territories
        
        
        
        asort($res);
        
        return $res;
    }
    
    /**
     * return array('code'=>name) of countries from DB table location_countries
     */
    public function getCountryName($code)
    {
        $countries = self::getCountriesList();
        
        return $countries[$code];
    }        
    
    /**
     * return states/regions of the country
     * @param country code
     */
    public function getStatesList($countryCode)
    {
        return (isset($this->GEOIP_REGION_NAME[$countryCode])) ? $this->GEOIP_REGION_NAME[$countryCode] : array();
    }
    
    /**
     * return id/city/state (limit 100)
     * @param city like (min length is 3)
     * @param country code
     */
    public function findCityLike($city, $country)
    {
        if (strlen($city)>=3)
        {
            $sql = "SELECT id, city, state, latitude, longitude FROM location_geoip_cities WHERE country=:country AND city like :city ORDER BY population DESC, city ASC LIMIT 100";// GROUP BY latitude,longitude
            
            $res = Yii::app()->dbGEO->createCommand($sql)
                        ->bindValue(':country', $country, PDO::PARAM_STR)
                        ->bindValue(':city', $city.'%', PDO::PARAM_STR)                        
                        ->queryAll();
            if ($res)
            {
                foreach ($res as $k=>$v)
                {
                    $res[$k]['state'] = $this->getRegionName($country, $v['state']);                   
                }             
            }            
        }        
            
        return $res;
    }    
    
    /**
     * return REGION_NAME
     * @param country code
     * @param region code
     */
    public function getRegionName($countryCode, $regionCode)
    {
        if ($countryCode=='US' || $countryCode=='CA')//if (!preg_match('/[0-9]{1,2}$/', $regionCode))
            return $regionCode;
        else       
            return (isset($this->GEOIP_REGION_NAME[$countryCode][$regionCode])) ? $this->GEOIP_REGION_NAME[$countryCode][$regionCode] : '';
    } 
    
    /**
     * get location info from DB
     * add ZIP
     * @param id
     */
    public function getLocation($id)
    {
    	
    	$mkey = "getLocationId_" . $id;
        $res = Yii::app()->cache->get($mkey);        
        if ( $res === false )
        {
            if ($id)
            {
                $res = Yii::app()->dbGEO
                        ->createCommand("SELECT country, city, state, state as stateName, latitude, longitude FROM location_geoip_cities WHERE id=:id LIMIT 1")
                        ->bindValue(':id', $id, PDO::PARAM_INT)
                        ->queryRow();
                
                $res['stateName'] = $this->getRegionName($res['country'], $res['state']);
             
                $res['zip'] = $this->findZipNearest($res['latitude'], $res['longitude']);
                
                Yii::app()->cache->set($mkey, $res);
            }
        }
		
        if (!$res || empty($res))
        	$res = $this->getLocationDefault();
        	
        return $res;         
    }
    
    /**
     * get DEFAULT location info from DB
     * @param id
     */
    public function getLocationDefault()
    {
        return $this->getLocation( $this->defaultLocationId );         
    }  
      
    /**
     * return one nearest id
     */
    public function findLocationIdByIP($ip='')
    {
        $ip = ($ip) ? $ip : $this->getIPReal();
    
        $mkey = "LocationIdByIP_{$ip}";
        if ( !$id = Yii::app()->cache->get($mkey) )
        {
            $record = $this->getGeoIPRecord($ip);
    
            /*if ( !$record['success'] )
            {
                $id = $this->defaultLocationId;
            }
            else*/
            {
                //$id = $this->findLocationIdByLatLon($record['GEOIP_LATITUDE'], $record['GEOIP_LONGITUDE']);
                $id = $this->findNearestIDFromGeoIpRecord($record);
            }
            
            Yii::app()->cache->set($mkey, $id);        
        }
                           
        return $id;         
    }    
    
    /**
     * find nearest location ID
     * @param latitude 
     * @param longitude
     */ 
    public function findLocationIdByLatLon($lat, $lon)
    {
        if ( !$id = $this->findNearestID($lat,$lon) )
        {
            $id = $this->defaultLocationId;
        }
        return $id;         
    }    


    
    /**
     * find nearest point(s)
     */
    public function findNearestID($lat,$lon, $range_from=0, $range_to=1, $limit=1)
    {
/*$lat = 48.917221;
$lon = 24.707222;
$lat = 43.349998;
$lon = -7.466667;*/
        
        if ($range_to>30)
        {
            $range_to = 30;
        }
        
        //changed code from http://www.micahcarrick.com/php-zip-code-range-and-distance-calculation.html
      
        // Find Max - Min Lat / Long for Radius and zero point and query
        $lat_range = $range_to/69.172;
        $lon_range = abs($range_to/(cos($lat) * 69.172));
        $min_lat = number_format($lat - $lat_range, "4", ".", "");
        $max_lat = number_format($lat + $lat_range, "4", ".", "");
        $min_lon = number_format($lon - $lon_range, "4", ".", "");
        $max_lon = number_format($lon + $lon_range, "4", ".", "");

        $sql = "SELECT z.id FROM location_geoip_cities as z "
              ."WHERE " 
              ."z.latitude BETWEEN '$min_lat' AND '$max_lat' " 
              ."AND z.longitude BETWEEN '$min_lon' AND '$max_lon' "
              ."ORDER BY population DESC "
              ."LIMIT {$limit}";
//FB::info($sql);
        return Yii::app()->dbGEO->createCommand($sql)->queryScalar();

        
        /*$sql = "SELECT z.*, 1292 * 2 * ATAN2(SQRT(POW(SIN((RADIANS({$lat}) - "
              .'RADIANS(z.latitude)) / 2), 2) + COS(RADIANS(z.latitude)) * '
              ."COS(RADIANS({$lat})) * POW(SIN((RADIANS({$lon}) - "
              ."RADIANS(z.longitude)) / 2), 2)), SQRT(1 - POW(SIN((RADIANS({$lat}) - "
              ."RADIANS(z.latitude)) / 2), 2) + COS(RADIANS(z.latitude)) * "
              ."COS(RADIANS({$lat})) * POW(SIN((RADIANS({$lon}) - "
              ."RADIANS(z.longitude)) / 2), 2))) AS \"miles\" FROM location_geoip_cities as z "
              ."WHERE " 
              ."z.latitude BETWEEN '$min_lat' AND '$max_lat' " 
              ."AND z.longitude BETWEEN '$min_lon' AND '$max_lon' "
              ."GROUP BY latitude, longitude "
              //�������� !!! ."ORDER BY miles " 
              ."LIMIT {$limit}";

        $dbres = Yii::app()->dbGEO->createCommand($sql)->queryAll();
        
        $milesres = array();
        
        if ($dbres)
        {
            foreach ($dbres as $v)
            {
                $milesres[$v['miles']] = $v;                
            }
            
            ksort($milesres);
            
            foreach ($milesres as $k=>$v)
            {
                $v['stateName'] = $this->getRegionName($v['country'], $v['state']);
                $res[] = $v;
            }            
        }
        
        //FB::info($res, 'nearest');
        return $res;  */                    
              

      
      
        //changed code from http://www.micahcarrick.com/php5-zip-code-range-and-distance.html
        /*
        other variant from SQL (very long)
        $sql = "SELECT 3956 * 2 * ATAN2(SQRT(POW(SIN((RADIANS({$lat}) - "
              .'RADIANS(z.latitude)) / 2), 2) + COS(RADIANS(z.latitude)) * '
              ."COS(RADIANS({$lat})) * POW(SIN((RADIANS({$lon}) - "
              ."RADIANS(z.longitude)) / 2), 2)), SQRT(1 - POW(SIN((RADIANS({$lat}) - "
              ."RADIANS(z.latitude)) / 2), 2) + COS(RADIANS(z.latitude)) * "
              ."COS(RADIANS({$lat})) * POW(SIN((RADIANS({$lon}) - "
              ."RADIANS(z.longitude)) / 2), 2))) AS \"miles\", z.* FROM location_geoip_cities z "
              ."WHERE latitude BETWEEN ROUND({$lat} - (25 / 69.172), 4) "
              ."AND ROUND({$lat} + (25 / 69.172), 4) "
              ."AND longitude BETWEEN ROUND({$lon} - ABS(25 / COS({$lat}) * 69.172)) "
              ."AND ROUND({$lon} + ABS(25 / COS({$lat}) * 69.172)) "
              ."AND 3956 * 2 * ATAN2(SQRT(POW(SIN((RADIANS({$lat}) - "
              ."RADIANS(z.latitude)) / 2), 2) + COS(RADIANS(z.latitude)) * "
              ."COS(RADIANS({$lat})) * POW(SIN((RADIANS({$lon}) - "
              ."RADIANS(z.longitude)) / 2), 2)), SQRT(1 - POW(SIN((RADIANS({$lat}) - "
              ."RADIANS(z.latitude)) / 2), 2) + COS(RADIANS(z.latitude)) * "
              ."COS(RADIANS({$lat})) * POW(SIN((RADIANS({$lon}) - "
              ."RADIANS(z.longitude)) / 2), 2))) <= $range_to "
              ."AND 3956 * 2 * ATAN2(SQRT(POW(SIN((RADIANS({$lat}) - "
              ."RADIANS(z.latitude)) / 2), 2) + COS(RADIANS(z.latitude)) * "
              ."COS(RADIANS({$lat})) * POW(SIN((RADIANS({$lon}) - "
              ."RADIANS(z.longitude)) / 2), 2)), SQRT(1 - POW(SIN((RADIANS({$lat}) - "
              ."RADIANS(z.latitude)) / 2), 2) + COS(RADIANS(z.latitude)) * "
              ."COS(RADIANS({$lat})) * POW(SIN((RADIANS({$lon}) - "
              ."RADIANS(z.longitude)) / 2), 2))) >= $range_from "
              ."ORDER BY miles ASC LIMIT 100";
        echo $sql;
        die();*/      
    }

    /**
     * find nearest point with big population
     */
    public function findCityInCountryWithBiggestPopulation($country)
    {
        $sql = "SELECT id FROM location_geoip_cities WHERE country='{$country}' ORDER BY population DESC ";
        return Yii::app()->dbGEO->createCommand($sql)->queryScalar();
    }

    /**
     * find nearest point(s)
     */
    public function findNearestUsers($lat,$lon, $range_to=30, $limit=10000, $where="")
    {
return false;    	
        $res = array();
        
        if (!$lat || !$lon) return $res;//!
        
        //changed code from http://www.micahcarrick.com/php-zip-code-range-and-distance-calculation.html
      
        // Find Max - Min Lat / Long for Radius and zero point and query
        $lat_range = $range_to/69.172;
        $lon_range = abs($range_to/(cos($lat) * 69.172));
        $min_lat = number_format($lat - $lat_range, "4", ".", "");
        $max_lat = number_format($lat + $lat_range, "4", ".", "");
        $min_lon = number_format($lon - $lon_range, "4", ".", "");
        $max_lon = number_format($lon + $lon_range, "4", ".", "");

        $sql = "SELECT z.user_id FROM users_location as z"
              ." WHERE "
              ." z.latitude BETWEEN '$min_lat' AND '$max_lat'" 
              ." AND z.longitude BETWEEN '$min_lon' AND '$max_lon'"
              //.($where) ? $where : ""
              ." LIMIT ".$limit; 
		
//FB::info($sql,'findNearestUsers');
              
		return Yii::app()->db->createCommand($sql)->queryColumn();


    	/*$res = array();
        
        if (!$lat || !$lon) return $res;//!
        
        $userId = Yii::app()->user->id;
        
        $genderWhere = Yii::app()->helperProfile->whereLookGender($userId);

        //changed code from http://www.micahcarrick.com/php-zip-code-range-and-distance-calculation.html
      
        // Find Max - Min Lat / Long for Radius and zero point and query
        $lat_range = $range_to/69.172;
        $lon_range = abs($range_to/(cos($lat) * 69.172));
        $min_lat = number_format($lat - $lat_range, "4", ".", "");
        $max_lat = number_format($lat + $lat_range, "4", ".", "");
        $min_lon = number_format($lon - $lon_range, "4", ".", "");
        $max_lon = number_format($lon + $lon_range, "4", ".", "");

        $sql = "SELECT 1292 * 2 * ATAN2(SQRT(POW(SIN((RADIANS({$lat}) - "
              .'RADIANS(z.latitude)) / 2), 2) + COS(RADIANS(z.latitude)) * '
              ."COS(RADIANS({$lat})) * POW(SIN((RADIANS({$lon}) - "
              ."RADIANS(z.longitude)) / 2), 2)), SQRT(1 - POW(SIN((RADIANS({$lat}) - "
              ."RADIANS(z.latitude)) / 2), 2) + COS(RADIANS(z.latitude)) * "
              ."COS(RADIANS({$lat})) * POW(SIN((RADIANS({$lon}) - "
              ."RADIANS(z.longitude)) / 2), 2))) AS \"miles\", z.user_id FROM users_location as z, users as u "
              ."WHERE "
              ." z.latitude BETWEEN '$min_lat' AND '$max_lat'" 
              ." AND z.longitude BETWEEN '$min_lon' AND '$max_lon'"
              ." AND z.user_id=u.id"
              ." AND u.{$genderWhere}"
              ." AND u.".Yii::app()->params['user']['rolesWhere'];
              //�������� !!! ."ORDER BY miles " 
              ;

        if ( $userId )
        {
            $sql .= " AND z.user_id<>{$userId}";
        }

//FB::info($sql,'findNearestUsers');
        
        if ($dbres = Yii::app()->db->createCommand($sql)->queryAll())
        {
            foreach ($dbres as $v)
            {
                $milesres[$v['miles']] = $v;                
            }
          
            shuffle($milesres); ///ksort($milesres);
                        
            foreach ($milesres as $k=>$v)
            {
                $res[]['id'] = $v['user_id'];
                
                if (count($res)>=$limit)//if (!--$limit)
                {
                    break;
                }                
            }
        }
//FB::warn($res,'**********************************');     
        if ( count($res)<$limit )
        {
            if ($userId)
            {
                $promos = new Promos;
                $promos->createPromosLocationsForUser($userId);
                
                if ($promosIds = $this->findLocationPromosExisted($lat,$lon,0.1,$userId))//if ($promosIds = $promos->getPromosIdsForUser($userId, $limit-count($res)))
                {
                    shuffle($promosIds);
                    foreach ($promosIds as $fid)
                        if (count($res)<$limit)
                            $res[]['id'] = $fid;
                }                
            }
            else
            {
                $promos = Yii::app()->user->Profile->getDataValue('promos');
                if ($promos)
                    foreach($promos as $p)
                    {
                        $res[] = $p;
                    }
            }
        }
//FB::warn($res,'**********************************');              
        return $res;*/
    }
    /**
     * find nearest promos of user
     */
    /*public function findNearestPromos($userId, $limit, $lat, $lon)
    {
        $res = array();
        $range_to = 500;
        // Find Max - Min Lat / Long for Radius and zero point and query
        $lat_range = $range_to/69.172;
        $lon_range = abs($range_to/(cos($lat) * 69.172));
        $min_lat = number_format($lat - $lat_range, "4", ".", "");
        $max_lat = number_format($lat + $lat_range, "4", ".", "");
        $min_lon = number_format($lon - $lon_range, "4", ".", "");
        $max_lon = number_format($lon + $lon_range, "4", ".", "");

        $sql = "SELECT promo_id as id FROM promo_locations AS z"
              ." WHERE "
              ." z.user_id={$userId}"
              ." AND z.latitude BETWEEN '$min_lat' AND '$max_lat'" 
              ." AND z.longitude BETWEEN '$min_lon' AND '$max_lon'"
              ." LIMIT {$limit}"
              ;
FB::error($sql);              
        return Yii::app()->db->createCommand($sql)->queryColumn();
    }
    */
    
    
    
    /**
     * find nearest point(s) for Search use
     */
    public function findNearestUsersAdv($lat,$lon, $range_to=100, $where=array(), $start=0, $limit=20)
    {
        $res = array();
        
        //changed code from http://www.micahcarrick.com/php-zip-code-range-and-distance-calculation.html
      
        // Find Max - Min Lat / Long for Radius and zero point and query
        $lat_range = $range_to/69.172;
        $lon_range = abs($range_to/(cos($lat) * 69.172));
        $min_lat = number_format($lat - $lat_range, "4", ".", "");
        $max_lat = number_format($lat + $lat_range, "4", ".", "");
        $min_lon = number_format($lon - $lon_range, "4", ".", "");
        $max_lon = number_format($lon + $lon_range, "4", ".", "");

        $sql = "SELECT 1292 * 2 * ATAN2(SQRT(POW(SIN((RADIANS({$lat}) - "
              .'RADIANS(z.latitude)) / 2), 2) + COS(RADIANS(z.latitude)) * '
              ."COS(RADIANS({$lat})) * POW(SIN((RADIANS({$lon}) - "
              ."RADIANS(z.longitude)) / 2), 2)), SQRT(1 - POW(SIN((RADIANS({$lat}) - "
              ."RADIANS(z.latitude)) / 2), 2) + COS(RADIANS(z.latitude)) * "
              ."COS(RADIANS({$lat})) * POW(SIN((RADIANS({$lon}) - "
              ."RADIANS(z.longitude)) / 2), 2))) AS \"miles\", z.user_id FROM users_location as z, users as u, users_activity as a "
              ."WHERE "
              ."z.latitude BETWEEN '$min_lat' AND '$max_lat' " 
              ."AND z.longitude BETWEEN '$min_lon' AND '$max_lon' "
              ."AND z.user_id=u.id AND a.user_id=u.id"
              ."AND u.".Yii::app()->params['user']['rolesWhere'];
              //�������� !!! ."ORDER BY miles " 
              ;
        
        if ($where)
        {
            $sql .= " AND ".implode(' AND ', $where);
        }
/*$sql = "SELECT *, id as user_id FROM users ORDER BY id DESC LIMIT 20";
FB::info($sql,'------------------------------------------------------');
return Yii::app()->db->createCommand($sql)->queryAll();*/
        
        if ($dbres = Yii::app()->db->createCommand($sql)->queryAll())
        {
            foreach ($dbres as $v)
            {
                $milesres[$v['miles']] = $v;                
            }
            
            ksort($milesres);
                        
            foreach ($milesres as $k=>$v)
            {
                $res[]['id'] = $v['user_id'];
                
                if (count($res)>=$limit)//if (!--$limit)
                {
                    break;
                }
            }
        }
        
        return $res;
    }

//    /**
//     * Calculate distance in miles between 2 points
//     * @params lat1, lat2, lon1, lon2
//     */  
//    private function _calculateMileage($lat1, $lat2, $lon1, $lon2) 
//    {
//      // Convert lattitude/longitude (degrees) to radians for calculations
//      $lat1 = deg2rad($lat1);
//      $lon1 = deg2rad($lon1);
//      $lat2 = deg2rad($lat2);
//      $lon2 = deg2rad($lon2);
//      
//      // Find the deltas
//      $delta_lat = $lat2 - $lat1;
//      $delta_lon = $lon2 - $lon1;
//	
//      // Find the Great Circle distance 
//      $temp = pow(sin($delta_lat/2.0),2) + cos($lat1) * cos($lat2) * pow(sin($delta_lon/2.0),2);
//      $distance = 3956 * 2 * atan2(sqrt($temp),sqrt(1-$temp));
//
//      return $distance;
//   }

	//n 2012-07-06: this function should be public or van be static, because it does not access any member variable!
	/**
	 * Calculate distance in miles between 2 points
	 * @params lat1, lat2, lon1, lon2
	 */  
	public function calculateMileage($lat1, $lat2, $lon1, $lon2) 
	{
		// Convert lattitude/longitude (degrees) to radians for calculations
		$lat1 = deg2rad($lat1);
		$lon1 = deg2rad($lon1);
		$lat2 = deg2rad($lat2);
		$lon2 = deg2rad($lon2);
		
		// Find the deltas
		$delta_lat = $lat2 - $lat1;
		$delta_lon = $lon2 - $lon1;
		
		// Find the Great Circle distance 
		$temp = pow(sin($delta_lat/2.0),2) + cos($lat1) * cos($lat2) * pow(sin($delta_lon/2.0),2);
		$distance = 3956 * 2 * atan2(sqrt($temp),sqrt(1-$temp));

		return $distance;
	}
    
    /**
     * find nearest one ZIP
     */
    public function findZipNearest($lat, $lon, $range_to=10)
    {
        $res = '';
    	
    	///$mkey = "LocationZip_{$lat}_{$lon}_{$range_to}";
        
        ///������...if ( !$rez = Yii::app()->cache->get($mkey) )
        {
            // Find Max - Min Lat / Long for Radius and zero point and query
            $lat_range = $range_to/69.172;
            $lon_range = abs($range_to/(cos($lat) * 69.172));
            $min_lat = number_format($lat - $lat_range, "4", ".", "");
            $max_lat = number_format($lat + $lat_range, "4", ".", "");
            $min_lon = number_format($lon - $lon_range, "4", ".", "");
            $max_lon = number_format($lon + $lon_range, "4", ".", "");
    
            $sql = "SELECT 1292 * 2 * ATAN2(SQRT(POW(SIN((RADIANS({$lat}) - "
                  .'RADIANS(latitude)) / 2), 2) + COS(RADIANS(latitude)) * '
                  ."COS(RADIANS({$lat})) * POW(SIN((RADIANS({$lon}) - "
                  ."RADIANS(longitude)) / 2), 2)), SQRT(1 - POW(SIN((RADIANS({$lat}) - "
                  ."RADIANS(latitude)) / 2), 2) + COS(RADIANS(latitude)) * "
                  ."COS(RADIANS({$lat})) * POW(SIN((RADIANS({$lon}) - "
                  ."RADIANS(longitude)) / 2), 2))) AS \"miles\", zip_code FROM {$this->zip_table} "
                  ."WHERE "
                  ."latitude BETWEEN '$min_lat' AND '$max_lat' " 
                  ."AND longitude BETWEEN '$min_lon' AND '$max_lon' "
                  ."LIMIT 1000 "
                  //�������� !!! ."ORDER BY miles " 
                  ;
      
    
//FB::info($sql);
            
            if ($dbres = Yii::app()->dbGEO->createCommand($sql)->queryAll())
            {
                foreach ($dbres as $v)
                {
                    $milesres[$v['miles']] = $v;                
                }
                
                ksort($milesres);
                
                $firstRecord = reset($milesres);
                //FB::error(reset($milesres));
                
                $res = $firstRecord['zip_code'];
            }
            
            ///Yii::app()->cache->set($mkey, $res);
        }
        
        return $res;
    }
    
    /**
     * find nearest one ZIP from LocationId
     */
    public function findZipNearestLocationId($id, $range_to=10)
    {
        $location = $this->getLocation($id);
        return $location['zip'];//$this->findZipNearest($location['latitude'], $location['longitude'], $range_to);
    }
    
    
    
    /**
     * find nearest point(s) for promos with forced full location
     */
    public function findPromosForcedFullForFront($lat, $lon, $range_to=0.1/*500*/, $limit=9)
    {    
        // Find Max - Min Lat / Long for Radius and zero point and query
        $lat_range = $range_to/69.172;
        $lon_range = abs($range_to/(cos($lat) * 69.172));
        $min_lat = number_format($lat - $lat_range, "4", ".", "");
        $max_lat = number_format($lat + $lat_range, "4", ".", "");
        $min_lon = number_format($lon - $lon_range, "4", ".", "");
        $max_lon = number_format($lon + $lon_range, "4", ".", "");
        
        $sql = "SELECT r.user_id, r.force_lat, force_lon FROM promo_roles as r, users as u"
              ." WHERE "
              ." r.force_lat BETWEEN '$min_lat' AND '$max_lat' AND r.force_lon BETWEEN '$min_lon' AND '$max_lon' "
              ." AND r.front='1'"
              ." AND r.user_id=u.id"
              ." AND u.role='approved'"//.Yii::app()->params['user']['rolesWhere']
              ." LIMIT {$limit} " 
              ;
        
        return Yii::app()->db->createCommand($sql)->queryAll();        
    }
    /**
     * find nearest point(s) for promos with forced full location
     */
    public function findPromosForcedFullForMembers($userId, $promosExisted, $lat, $lon, $range_to=0.1/*500*/, $limit=9)
    {    
        
        // Find Max - Min Lat / Long for Radius and zero point and query
        $lat_range = $range_to/69.172;
        $lon_range = abs($range_to/(cos($lat) * 69.172));
        $min_lat = number_format($lat - $lat_range, "4", ".", "");
        $max_lat = number_format($lat + $lat_range, "4", ".", "");
        $min_lon = number_format($lon - $lon_range, "4", ".", "");
        $max_lon = number_format($lon + $lon_range, "4", ".", "");
        
        $where[] = "r.force_lat BETWEEN '$min_lat' AND '$max_lat' AND r.force_lon BETWEEN '$min_lon' AND '$max_lon'";
        $where[] = "r.members='1'";
        $where[] = "r.user_id=u.id";
        ///$where[] = "u.".CHelperProfile::whereLookGender($userId);
        $where[] = "u.role='approved'";//.Yii::app()->params['user']['rolesWhere'];
        if ($promosExisted) 
            $where[] = " r.user_id NOT IN (".implode(',',$promosExisted).")";

        $sql = "SELECT r.user_id, r.force_lat, force_lon FROM promo_roles as r, users as u"
              ." WHERE ".implode(' AND ', $where)
              ." LIMIT {$limit} " 
              ;
              
//FB::error($sql,'222222222222222222222');              
              
        return Yii::app()->db->createCommand($sql)->queryAll();        
    }
 
    
    
    /**
     * find nearest point(s) for promos
     */
    public function findLocationForPromos($lat, $lon, $range_to=0.1/*500*/, $limit=9, $force_country='')
    {
        
        // Find Max - Min Lat / Long for Radius and zero point and query
        $lat_range = $range_to/69.172;
        $lon_range = abs($range_to/(cos($lat) * 69.172));
        $min_lat = number_format($lat - $lat_range, "4", ".", "");
        $max_lat = number_format($lat + $lat_range, "4", ".", "");
        $min_lon = number_format($lon - $lon_range, "4", ".", "");
        $max_lon = number_format($lon + $lon_range, "4", ".", "");
        
        $sql_0 = "SELECT id, latitude, longitude, population FROM location_geoip_cities as z WHERE ";
        
        if ($force_country) $where[] = "z.country='{$force_country}'";
        $where[] = "z.latitude BETWEEN '$min_lat' AND '$max_lat'"; 
        $where[] = "z.longitude BETWEEN '$min_lon' AND '$max_lon'";
        //$where[] = "z.population>100000";//."ORDER BY z.population DESC "
        $sql = $sql_0 . implode(" AND ", $where) . " LIMIT 1000"; //.(3*$limit);
FB::info($sql,'*** findLocationForPromos ***');
        $dbres = Yii::app()->dbGEO->createCommand($sql)->queryAll();

        if ($dbres)
        {
            foreach ($dbres as $v)
            {
                $res[$v['population']] = $v;                
            }
            
            asort($res);
            
            array_slice($res,0,3*$limit);
            
            shuffle($res);
            
            array_slice($res,0,$limit);
        }
   
        return $res;        
        
        
        /*// Find Max - Min Lat / Long for Radius and zero point and query
        $lat_range = $range_to/69.172;
        $lon_range = abs($range_to/(cos($lat) * 69.172));
        $min_lat = number_format($lat - $lat_range, "4", ".", "");
        $max_lat = number_format($lat + $lat_range, "4", ".", "");
        $min_lon = number_format($lon - $lon_range, "4", ".", "");
        $max_lon = number_format($lon + $lon_range, "4", ".", "");
        
        $sql_0 = "SELECT id, latitude, longitude FROM location_geoip_cities as z WHERE ";
        
        if ($force_country) $where[] = "z.country='{$force_country}'";
        $where[] = "z.latitude BETWEEN '$min_lat' AND '$max_lat'"; 
        $where[] = "z.longitude BETWEEN '$min_lon' AND '$max_lon'";
        $where[] = "z.population>100000";//."ORDER BY z.population DESC "
        $sql = $sql_0 . implode(" AND ", $where) . " LIMIT ".(3*$limit);
//FB::info($sql,'$sql$sql$sql');
        $res1 = Yii::app()->db->createCommand($sql)->queryAll();

        if (count($res1)<$limit)
        {
            $where = array();
            if ($force_country) $where[] = "z.country='{$force_country}'";
            $where[] = "z.latitude BETWEEN '$min_lat' AND '$max_lat'"; 
            $where[] = "z.longitude BETWEEN '$min_lon' AND '$max_lon'";
            $where[] = "z.population>10000";//."ORDER BY z.population DESC "
            $sql = $sql_0 . implode(" AND ", $where) . " LIMIT ".(2*($limit-count($res1)));
//FB::info($sql,'$sql$sql$sql');
            $res2 = Yii::app()->db->createCommand($sql)->queryAll();
            
            if ((count($res1)+count($res2))<$limit)
            {
                $where = array();
                if ($force_country) $where[] = "z.country='{$force_country}'";
                $where[] = "z.latitude BETWEEN '$min_lat' AND '$max_lat'"; 
                $where[] = "z.longitude BETWEEN '$min_lon' AND '$max_lon'";
                //$where[] = "z.population<>0";//."ORDER BY z.population DESC "
                $sql = $sql_0 . implode(" AND ", $where) . " LIMIT ".($limit-count($res1)-count($res2));
//FB::info($sql,'$sql$sql$sql');
                $res3 = Yii::app()->db->createCommand($sql)->queryAll();
            }
        }
//FB::info($sql, 'findLocationForPromos');
        
        if ($res1)
            foreach($res1 as $r)
                $res[] = $r;

        if ($res2)
            foreach($res2 as $r)
                $res[] = $r;

        if ($res3)
            foreach($res3 as $r)
                $res[] = $r;
//FB::info($res,'$sql$sql$sql');        
        return $res;*/
    }
    
    /**
     * find nearest point(s) for promos
     */
    public function findLocationPromosExisted($lat,$lon, $range_to=0.1/*500*/, $user_id)
    {
        // Find Max - Min Lat / Long for Radius and zero point and query
        $lat_range = $range_to/69.172;
        $lon_range = abs($range_to/(cos($lat) * 69.172));
        $min_lat = number_format($lat - $lat_range, "4", ".", "");
        $max_lat = number_format($lat + $lat_range, "4", ".", "");
        $min_lon = number_format($lon - $lon_range, "4", ".", "");
        $max_lon = number_format($lon + $lon_range, "4", ".", "");

        $genderWhere = Yii::app()->helperProfile->whereLookGender($userId);

        $sql = "SELECT promo_id FROM promo_locations as z, users as u, promo_roles as r"
              ." WHERE "
              ." z.user_id={$user_id} "
              ." AND z.promo_id=r.user_id"
              ." AND r.members='1'"
              ." AND z.promo_id=u.id"
              ." AND u.{$genderWhere}"
              ." AND z.latitude BETWEEN '$min_lat' AND '$max_lat' " 
              ." AND z.longitude BETWEEN '$min_lon' AND '$max_lon' "
              ." ORDER BY z.id"//." ORDER BY RAND()"
              ;


//FB::info($sql, 'findLocationPromosExisted');
        
        return Yii::app()->db->createCommand($sql)->queryColumn();
    }    
    
    
    


















































    
    public function importGeoNames($filename)//file from http://download.geonames.org/export/zip/
    {
        /* �� ��������� ��� ��������� �����
        CREATE TABLE IF NOT EXISTS `location_geonames` (
          `id` int(11) NOT NULL auto_increment,
          `country_code` varchar(2) character set latin1 collate latin1_bin NOT NULL COMMENT 'iso country code, 2 characters',
          `postal_code` varchar(20) character set latin1 collate latin1_bin NOT NULL,
          `place_name` varchar(180) collate utf8_unicode_ci NOT NULL,
          `admin_name1` varchar(100) collate utf8_unicode_ci NOT NULL COMMENT '1. order subdivision (state)',
          `admin_code1` varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '1. order subdivision (state)',
          `admin_name2` varchar(100) collate utf8_unicode_ci NOT NULL COMMENT '2. order subdivision (county/province)',
          `admin_code2` varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '2. order subdivision (county/province)',
          `admin_name3` varchar(100) collate utf8_unicode_ci NOT NULL COMMENT '3. order subdivision (community)',
          `admin_code3` varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '3. order subdivision (community)',
          `latitude` float(10,5) NOT NULL COMMENT 'estimated latitude (wgs84)',
          `longitude` float(10,5) NOT NULL COMMENT 'estimated longitude (wgs84)',
          `accuracy` tinyint(4) NOT NULL COMMENT 'accuracy of lat/lng from 1=estimated to 6=centroid',
          PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;        
        */
        
        /*if ($f = fopen($filename, "r"))
        {
            $i=10000;
            while (!feof($f) && $i--)
            {
                $s = fgets($f);
                $a = explode("\t", $s);
                $s = implode(' * ',$a);
                echo $s.'<br/>';
            }
            
            fclose($f);
        }*/
    }
    
    public function importGeoIPCities($filename)//worldcitiespop.txt  file from http://www.maxmind.com/app/worldcities
    {

return;
        /*

        DATABASE Fields
        Field 	                         Data Type 	         Field Description
        Country Code 	                 char(2) 	         ISO 3166 Country Code,
        ///ASCII City Name 	             varchar(100) 	     Name of city or town in ASCII encoding
        City Name 	                     varchar(255) 	     Name of city or town in ISO-8859-1 encoding. A list of cities contained in GeoIP City is available.
        State/Region 	                 char(2) 	         For US, ISO-3166-2 code for the state/province name. Outside of the US, FIPS 10-4 code
        ///Population 	                 unsigned int 	     Population of city (available for over 33,000 major cities only)
        Latitude 	                     numeric (float) 	 Latitude of city where IP is located
        Longitude 	                     numeric (float) 	 Longitude of city where IP is located        


        CREATE TABLE `location_geoip_cities` (
        `country` VARCHAR( 2 ) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL ,
        `city_ascii` VARCHAR( 100 ) CHARACTER SET ascii COLLATE ascii_bin NOT NULL ,
        `city` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
        `state` VARCHAR( 2 ) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL ,
        `latitude` FLOAT( 10, 5 ) NOT NULL ,
        `longitude` FLOAT( 10, 5 ) NOT NULL
        ) ENGINE = MYISAM ;
        
        
        
        */                
        if ($f = fopen($filename, "r"))
        {
            set_time_limit(0);
            
            $i=0;
            while (!feof($f))
            {
                $s = fgets($f);                
                if ($i++==0) continue;
                //if ($i>100) break;
                $s = utf8_encode($s);
                $a = explode(",", $s);
                $a[0] = strtoupper($a[0]);
                $sql = "INSERT INTO location_geoip_cities VALUES (NULL,'{$a[0]}',:city,'{$a[3]}','{$a[5]}','{$a[6]}')";              
                Yii::app()->dbGEO
                    ->createCommand($sql)
                    ->bindValue(':city', $a[2], PDO::PARAM_STR)
                    ->query();
            }
            
            fclose($f);
            echo 'FINISHED!!! '.$i; 
        }
    }    


    public function importGeoIPCitiesUpdatePopulation($filename)//worldcitiespop.txt  file from http://www.maxmind.com/app/worldcities
    {
return;        
        /*
        ALTER TABLE `location_geoip_cities` ADD `population` INT UNSIGNED NOT NULL AFTER `state` 
        */                
        if ($f = fopen($filename, "r"))
        {
            set_time_limit(0);
            
            $i=0;
            while (!feof($f))
            {
                $s = fgets($f);                
                if ($i++==0) continue;

                $s = utf8_encode($s);
                $a = explode(",", $s);
                $a[0] = strtoupper($a[0]);
                
                if ($a[4]!='') 
                {
                    $id = $i-1;
                    //echo $id. ' - '.$a[4].' - '.$s.'<br />';
                    $sql = "UPDATE location_geoip_cities SET population={$a[4]} WHERE id={$id}";              
                    Yii::app()->dbGEO->createCommand($sql)->query();
                }
                

            }
            
            fclose($f);
            echo 'FINISHED!!! '.$i; 
        }
    }    
    
    
        
    /*public function getStateCode($country, $state)
    {
        $code = $state;
        switch ($country)
        {
            case 'CA':
                switch ($state)
                {
                    case '01': $code="AB"; break;
                    case '02': $code="BC"; break;
                    case '03': $code="MB"; break;
                    case '04': $code="NB"; break;
                    case '05': $code="NF"; break;
                    case '06': $code="NS"; break;
                    case '07': $code="NU"; break;
                    case '08': $code="ON"; break;
                    case '09': $code="PE"; break;
                    case '10': $code="QC"; break;
                    case '11': $code="SK"; break;
                    case '13': $code="NT"; break;
                    case '12': $code="YT"; break;
                }
                break;
            
            default: break;
        }
        return $code;
    }*/
    
    
    public function flagUrl($country)
    {
        return '/images/flags/'.strtolower($country).'.gif';
    }








    private function _getWhereRange($lat, $lon, $range_to=0.1, $t="")
    {
        // Find Max - Min Lat / Long for Radius and zero point and query
        $lat_range = $range_to/69.172;
        $lon_range = abs($range_to/(cos($lat) * 69.172));
        $min_lat = number_format($lat - $lat_range, "4", ".", "");
        $max_lat = number_format($lat + $lat_range, "4", ".", "");
        $min_lon = number_format($lon - $lon_range, "4", ".", "");
        $max_lon = number_format($lon + $lon_range, "4", ".", "");
        
        $t = ($t) ? $t."." : "";
        
        return " {$t}latitude BETWEEN '$min_lat' AND '$max_lat' AND {$t}longitude BETWEEN '$min_lon' AND '$max_lon' ";
    }


    /**
     * find nearest point(s)
     */
    public function findNearestIDFromGeoIpRecord($record)
    {
        $range_to=1;

        $country = $record['GEOIP_COUNTRY_CODE'];//'US';
        $state   = $record['GEOIP_REGION'];// 'NY';
        $city    = $record['GEOIP_CITY'];
        $lat     = $record['GEOIP_LATITUDE'];
        $lon     = $record['GEOIP_LONGITUDE'];

        if (!$lat || !$lon)
            return $this->defaultLocationId;
        
        $range_to = 10;        
        /*if ($country && $state && $city)
        {
            $sql = "SELECT id FROM location_geoip_cities"
                  ." WHERE "
                  ." country='{$country}' AND state='{$state}' AND city='$city'" 
                  ." AND ".$this->_getWhereRange($lat, $lon, $range_to) 
                  ." ORDER BY population DESC "
                  ." LIMIT 1";
            FB::info($sql);
echo $sql;            
            $id = Yii::app()->dbGEO->createCommand($sql)->queryScalar();
            if ($id) return $id;            
        }*/
        if ($country && $city)
        {
            $sql = "SELECT id FROM location_geoip_cities"
                  ." WHERE "
                  ." country='{$country}' AND city=:city" 
                  ." AND ".$this->_getWhereRange($lat, $lon, $range_to) 
                  ." ORDER BY population DESC "
                  ." LIMIT 1";
            FB::info($sql);
//echo $sql;            
            $id = Yii::app()->dbGEO->createCommand($sql)
            		->bindValue(':city', $city, PDO::PARAM_STR)
            		->queryScalar();
            if ($id) return $id;            
        }
        
        /*$range_to = 5;
        if ($country && $state)
        {
            $sql = "SELECT id FROM location_geoip_cities"
                  ." WHERE "
                  ." country='{$country}' AND state='{$state}'" 
                  ." AND ".$this->_getWhereRange($lat, $lon, $range_to) 
                  ." ORDER BY population DESC "
                  ." LIMIT 1";
    //FB::info($sql);
            $id = Yii::app()->dbGEO->createCommand($sql)->queryScalar();
            if ($id) return $id;            
        }*/

        /*$range_to = 5;
        if ($country)
        {
            $sql = "SELECT id FROM location_geoip_cities"
                  ." WHERE "
                  ." country='{$country}'" 
                  ." AND ".$this->_getWhereRange($lat, $lon, $range_to) 
                  ." ORDER BY population DESC "
                  ." LIMIT 1";
    //FB::info($sql);
            $id = Yii::app()->dbGEO->createCommand($sql)->queryScalar();
            if ($id) return $id;            
        }*/

        if ($country)
        {
            $id = $this->findCityInCountryWithBiggestPopulation($country);
            if ($id) return $id;            
        }
        
        return $this->defaultLocationId;
        
        /*$sql = "SELECT id FROM location_geoip_cities "
              ."WHERE " 
              ."latitude BETWEEN '$min_lat' AND '$max_lat' " 
              ."AND longitude BETWEEN '$min_lon' AND '$max_lon' "
              ."ORDER BY population DESC "
              ."LIMIT 1";
//FB::info($sql);
        return Yii::app()->dbGEO->createCommand($sql)->queryScalar();*/

    }
    
    
    
    
    
    
    
    
    
    
    public function getCountryCode3($countryCode)
    {
    	foreach ($this->GEOIP_COUNTRY_CODES as $k=>$code)
    		if ($code==$countryCode)
    			return $this->GEOIP_COUNTRY_CODES3[$k];
    	
    	return "";
    }
    
    
    
    
    
    /*
     * 2013-08-26
     * http://download.geonames.org/export/zip/
     */
    public function importZipNew()
    {
    	$file = DIR_ROOT."/../allCountries.txt";
    	
    	$file_handle = fopen($file, "r");
    	$i=0;
		$sqlStart = "
			   	INSERT INTO location_zip_new 
			   	(country_code,zip_code,place_name,admin_name1,admin_code1,admin_name2,admin_code2,admin_name3,admin_code3,latitude,longitude,accuracy) 
			   	VALUES 
		";		   
    	$sql="";
		while (!feof($file_handle)) 
		{
		   $line = fgets($file_handle);
		   
		   $row = @explode("	", $line);
			
		   if ($row && is_array($row) && count($row)==12)
		   {
				foreach($row as $k=>$v)
					$row[$k] = str_replace("'", "\'", trim($v));

				$sql.= "('".implode("','",$row)."'),";
				
	   		   if ($i && $i%5000==0)
			   {
			   		$sql = $sqlStart . $sql;
					$sql = preg_replace("/,$/", "", $sql);
					Yii::app()->dbGEO->createCommand($sql)->execute();
					
					$sql = "";
					$i=0;
			   }
				

		   }
		   $i++;
		}
		
		if ($sql)
		{
			$sql = $sqlStart . $sql;
			$sql = preg_replace("/,$/", "", $sql);
			Yii::app()->dbGEO->createCommand($sql)->execute();
		}
		
		
		fclose($file_handle);
		echo 'OK';
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    public function getGeoIp2_Record($ip='')
    {
        $ip = ($ip) ? $ip : $this->getIPReal();
        
        $res = array('IP'=>$ip, 'GEOIP_COUNTRY_CODE'=>"", 'GEOIP_COUNTRY_NAME'=>"", 'GEOIP_REGION'=>"", 'GEOIP_CITY'=>"", 'GEOIP_ZIP'=>"", 'GEOIP_LATITUDE'=>0, 'GEOIP_LONGITUDE'=>0, 'success'=>0);

        $mkey = "GeoIp2_Record_{$ip}";
        $resCache = Yii::app()->cache->get($mkey);
        if ( $resCache === false )
        {
			$url = (LOCAL) ? "http://geo.onl.lo" : "http://geo.overnightlover.com";
			$post = array(
				'a'=>'record',
				'ip'=>$ip,
			);
        	$resTmp = CHelperSite::curl_request($url, $post, '', '', 5);
        	$res = @json_decode($resTmp, true);

            if ($resTmp && is_array($resTmp))
            {
            	$res = $resTmp;
            	$res['IP'] = $ip;
            }
            
            Yii::app()->cache->set($mkey, $res, 900);    
        }
        else
        	$res = $resCache;
        
        return $res;
    }    
}















/*
UPDATE `location_geoip_cities` SET `state`='AB' WHERE `country`='CA' AND `state`='01';
UPDATE `location_geoip_cities` SET `state`='BC' WHERE `country`='CA' AND `state`='02';
UPDATE `location_geoip_cities` SET `state`='MB' WHERE `country`='CA' AND `state`='03';
UPDATE `location_geoip_cities` SET `state`='NB' WHERE `country`='CA' AND `state`='04';
UPDATE `location_geoip_cities` SET `state`='NF' WHERE `country`='CA' AND `state`='05';
UPDATE `location_geoip_cities` SET `state`='NS' WHERE `country`='CA' AND `state`='06';
UPDATE `location_geoip_cities` SET `state`='NU' WHERE `country`='CA' AND `state`='07';
UPDATE `location_geoip_cities` SET `state`='ON' WHERE `country`='CA' AND `state`='08';
UPDATE `location_geoip_cities` SET `state`='PE' WHERE `country`='CA' AND `state`='09';
UPDATE `location_geoip_cities` SET `state`='QC' WHERE `country`='CA' AND `state`='10';
UPDATE `location_geoip_cities` SET `state`='SK' WHERE `country`='CA' AND `state`='11';
UPDATE `location_geoip_cities` SET `state`='NT' WHERE `country`='CA' AND `state`='13';
UPDATE `location_geoip_cities` SET `state`='YT' WHERE `country`='CA' AND `state`='12';


UPDATE `location_geoip_cities` SET `state`='NU' WHERE `country`='CA' AND `state`='14'; - ?
*/



