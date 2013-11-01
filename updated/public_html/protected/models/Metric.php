<?php
class Metric
{
    protected $id=0;
    protected $visitId=0;
    protected $userId=0;

    /**
     * 
     */
	public function __construct($userId=0)
	{
return;		
        if (ADMIN)
            return;
        
        FB::warn($userId, 'METRIC CONSTRUCT start, userId');
        
        $userId = intval($userId);
        
        if (FACEBOOK && !$userId)
            return;
        
        
        $this->userId = $userId;

        $key = (isset(Yii::app()->request->cookies['mkey'])) ? Yii::app()->request->cookies['mkey']->value : '';            
        if ($key)
            $this->id = $this->getIdFromKey($key);
        
        if ( $this->id )
        {
            $data = $this->getData();
            
            //���� ���� ���� �������
            if (!$data)//if (!Yii::app()->db->createCommand("SELECT COUNT(id) FROM metric WHERE id={$this->id} LIMIT 1")->queryScalar())
                $this->refresh();
            
            //���� ���� ����� ��� ������ ����...
            if ($this->userId!=$data['user_id'])
                $this->refresh();
        }
//FB::warn(Yii::app()->session['metricVisit'].'---');

        if (!$this->id)
            $this->Create();
        
        
        
        if ($this->userId)
        {
            $idFromUser = $this->getIdFromUserId();
   
            if ($idFromUser)
            {
                if ($this->id && $this->id!=$idFromUser)
                {
                    $sql = "UPDATE metric_visits SET metric_id={$idFromUser} WHERE metric_id={$this->id} ;";
                    $sql.= "DELETE FROM metric WHERE id={$this->id} ;"; 
                    Yii::app()->db->createCommand($sql)->execute();
                }
                $this->id = $idFromUser;
            }
            else
            {
                if ($this->id)
                {
                    $data = $this->getData();

                    if (!$data['user_id'])
                    {
                        $sql = "UPDATE metric SET user_id=:user_id WHERE id=:id LIMIT 1";
                        Yii::app()->db->createCommand($sql)
                            ->bindParam(":user_id", $this->userId, PDO::PARAM_INT)
                            ->bindParam(":id", $this->id, PDO::PARAM_INT)
                            ->execute();
                    }
                }                
            }
        }

                   
        if ($this->id)
        {
            $key = Yii::app()->secur->encryptByYii( $this->id ); 
            $cookie = new CHttpCookie('mkey', $key);
            $cookie->expire = time()+60*60*24*360; 
            Yii::app()->request->cookies['mkey'] = $cookie;
            
            
            $this->visitId = Yii::app()->session['metricVisit'];
            if ($this->visitId)
            {
                //��������� ����������� visitId to metric_id ��� ���� ���� ���� �������
                $sql = "SELECT metric_id FROM metric_visits WHERE id={$this->visitId} LIMIT 1";
                $tmpId = Yii::app()->db->createCommand($sql)->queryScalar();
                
                if ( $tmpId!=$this->id )
                    $this->visitId = 0;
            }
            
            if (!$this->visitId)
            {
                $this->CreateVisit();
                Yii::app()->session['metricVisit'] = $this->visitId;
            }
            else
            {
                //update last activity
                $this->UpdateLastActivity();
            }
            
        }
        
        FB::warn($this->id, 'METRIC CONSTRUCT END, metricID');
        
	} 

    
    public function refresh()
    {
        $this->id = 0;
        Yii::app()->session['metricVisit'] = $this->visitId = 0;
    }
    
    public function getData()
    {
        if ($this->id)
        {
            $sql = "SELECT * FROM metric WHERE id=:id LIMIT 1";
            $data = Yii::app()->db->createCommand($sql)
                ->bindParam(":id", $this->id, PDO::PARAM_INT)
                ->queryRow();
        }        
        return $data;
    }
    
    /**
     * 
     */    
    public function Create()
    {
        $sql = "INSERT INTO metric (user_id, added) VALUES (:user_id, NOW())";
        Yii::app()->db->createCommand($sql)
            ->bindParam(":user_id", $this->userId, PDO::PARAM_INT)
            ->execute();        
        
        $this->id = Yii::app()->db->lastInsertId;
    }
    
    /**
     * 
     */    
    private function CreateVisit()
    {
        if (!$this->id)
            return;        

        $ip = Yii::app()->location->getIPReal();
        $location_id = Yii::app()->location->findLocationIdByIP();
        $referer = ( isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : "" ;
        $sql = "INSERT INTO metric_visits (metric_id, ip, location_id, referer, added, last_activity) VALUES (:metric_id, :ip, :location_id, :referer, NOW(), NOW())";
        Yii::app()->db->createCommand($sql)
            ->bindParam(":metric_id", $this->id, PDO::PARAM_INT)
            ->bindParam(":ip", $ip, PDO::PARAM_STR)
            ->bindParam(":location_id", $location_id, PDO::PARAM_INT)
            ->bindParam(":referer", $referer, PDO::PARAM_STR)
            ->execute();        
        
        $this->visitId = Yii::app()->db->lastInsertId;
    }
    
    /**
     * 
     */
    private function UpdateLastActivity()
    {
        if (!$this->visitId)
            return;
        
        $sql = "UPDATE metric_visits SET last_activity=NOW() WHERE id=:id LIMIT 1";
        Yii::app()->db->createCommand($sql)
            ->bindParam(":id", $this->visitId, PDO::PARAM_INT)
            ->execute();
    }
    
    
    /**
     * 
     */
    public function getIdFromUserId($userId=0)
    {
        $userId = ($userId) ? $userId : $this->userId;
        
        if ($userId)
        {
            $mkey = "metric_getIdFromUserId_".$userId;
            $id = Yii::app()->cache->get( $mkey );
            
            if (!$id)
            {
                $sql = "SELECT id FROM metric WHERE user_id=:user_id LIMIT 1";
                $id = Yii::app()->db->createCommand($sql)
                    ->bindParam(":user_id", $userId, PDO::PARAM_INT)
                    ->queryScalar();
                    
                Yii::app()->cache->set($mkey, $id, Yii::app()->params->cache['profile']);                  
            }      
        }
        
        return $id;
    }    
    
    /**
     * 
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * 
     */
    public function getIdFromKey($key)
    {
        if ($key)
            $id = intval( Yii::app()->secur->decryptByYii($key) );
            
        return $id;
    }
    
  
    
    /**
     * 
     */
    public function addEvent_viewVideo($user_id_to)
    {
        if ($user_id_to && !is_numeric($user_id_to))
            $user_id_to = Yii::app()->secur->decryptID($user_id_to);
        
        if ($this->visitId && $user_id_to)
        {
            $sql = "INSERT INTO metric_actions (metric_visit_id, action, user_id_to, added) VALUES (:metric_visit_id, 'viewVideo', :user_id_to, NOW())";
            Yii::app()->db->createCommand($sql)
                ->bindParam(":metric_visit_id", $this->visitId, PDO::PARAM_INT)
                ->bindParam(":user_id_to", $user_id_to, PDO::PARAM_INT)
                ->execute();
            
            $this->UpdateLastActivity();
        }
    }
    
    /**
     * 
     */
    public function addEvent_viewPage($requestUri)
    {
        if ($this->visitId && $requestUri)
        {
            $sql = "INSERT INTO metric_actions (metric_visit_id, action, action_details, added) VALUES (:metric_visit_id, 'viewPage', :action_details, NOW())";
            Yii::app()->db->createCommand($sql)
                ->bindParam(":metric_visit_id", $this->visitId, PDO::PARAM_INT)
                ->bindParam(":action_details", $requestUri, PDO::PARAM_STR)
                ->execute();
            
            $this->UpdateLastActivity();
        }
    }    
    


























    
    
    static function adminGetMetrics($post, $page=0)
    {
        switch  ($post['sort'])
        {
            case 'idASC':    $order = "last_activity ASC"; break;
            case 'idDESC':   $order = "last_activity DESC"; break;
            
            default:   $order = $post['sort']; break;
        }
        
        
        $sql = "SELECT COUNT(id) FROM metric LIMIT 1";
        $res['count'] = Yii::app()->db->createCommand($sql)->queryScalar();

        
        
        
        $sql = "SELECT metric_id FROM metric_visits";//$sql = "SELECT DISTINCT(metric_id) FROM metric_visits";
        $sql.= " ORDER BY {$order}";
        $sql.= " LIMIT ".($page * $post['perPage']).", " . $post['perPage'];
FB::error($sql);
        $metric_ids_tmp = Yii::app()->db->createCommand($sql)->queryColumn();
        $metric_ids = array();
        if ($metric_ids_tmp)
            foreach($metric_ids_tmp as $m)
                if (!in_array($m, $metric_ids))
                {
                    $metric_ids[] = $m;
                }
                
        if ($metric_ids)
        {
            $sql = "SELECT * FROM metric WHERE id IN (".implode(',',$metric_ids).") ORDER BY FIELD(id,".implode(',',$metric_ids).")";
FB::error($sql);
            $res['list'] = Yii::app()->db->createCommand($sql)->queryAll();            
        }

        
        if ($res['list'])
            foreach ($res['list'] as $k=>$v)
            {
                $lastVisit = Yii::app()->db->createCommand("SELECT * FROM metric_visits WHERE metric_id={$v[id]} ORDER BY last_activity DESC LIMIT 1")->queryRow();
                
                $res['list'][$k]['lastVisit'] = $lastVisit;
                $res['list'][$k]['visitsCount'] = self::adminGetVisitsCount($v['id']);
            }
//CHelperSite::vd($res);        
        return $res;
    }

    /**
     * count of visits for user 
     * @id - metric ID
     */
    static function adminGetVisitsCount($id)
    {
        if ($id)
        {
            $sql = "SELECT COUNT(id) FROM metric_visits WHERE metric_id={$id} LIMIT 1";
            $res = Yii::app()->db->createCommand($sql)->queryScalar();
        }
        return $res;
    }
    
    /**
     * all details about user activity
     * current metric and other for this user
     */
    static function adminGetMetricDetails($id)
    {
        $id = intval($id);
        
        $res = array();
        
        if (!$id)
            return $res;
        
        
        $sql = "SELECT * FROM metric WHERE id=:id LIMIT 1";
        $data = Yii::app()->db->createCommand($sql)
                ->bindParam(":id", $id, PDO::PARAM_INT)
                ->queryRow();
        
        
        $sql = "SELECT * FROM metric_visits WHERE metric_id=:id ORDER BY last_activity DESC";
        $visits = Yii::app()->db->createCommand($sql)
                ->bindParam(":id", $id, PDO::PARAM_INT)
                ->queryAll();   
        
        if ($visits)
            foreach ($visits as $k=>$v)
            {
                $visits[$k]['location'] = Yii::app()->location->getLocation($v['location_id']);
            }
        
        $data['visits'] = $visits;
        
        
FB::warn($data);
        
        
        
/*        
        $sql = "SELECT user_id FROM metric WHERE id={$id}";
        $userId = Yii::app()->db->createCommand($sql)->queryScalar();
        
        
        
        if ($userId)
        {
            $sql = "SELECT id FROM metric WHERE user_id={$userId} ORDER BY id DESC";
            $ids = Yii::app()->db->createCommand($sql)->queryColumn();
        }
        else
        {
            $ids[] = $id;
        }
        
        $res['user_id'] = $userId;
        
        if ($ids)
            foreach ($ids as $v)
            {
                //location

                $sql = "SELECT ip, location_id,	added, last_activity FROM metric WHERE id={$v} LIMIT 1";
                
                $m['details'] = Yii::app()->db->createCommand($sql)->queryRow();                
                $m['details']['location'] = Yii::app()->location->getLocation($m['details']['location_id']);
                
                //visits
                $sql = "SELECT * FROM metric_visits WHERE metric_id={$v} ORDER BY id DESC";
                
                $m['details'] = Yii::app()->db->createCommand($sql)->queryRow();                 
                
                
                
                //actions
                //$sql = "SELECT viewed_user_id, added FROM metric_video_play WHERE metric_id={$v} ORDER BY id DESC";
                //$m['details']['videos'] = Yii::app()->db->createCommand($sql)->queryAll();
                
                $res['info'][] = $m;
            }
*/    
        
        return $data;
    }
    
    
    /**
     * all details about user activity
     * current metric and other for this user
     */
    static function adminGetVisitDetails($id)
    {
        $id = intval($id);
        
        $res = array();
        
        if (!$id)
            return $res;
        
        $sql = "SELECT * FROM metric_visits WHERE id=:id LIMIT 1";
        $visit = Yii::app()->db->createCommand($sql)
                ->bindParam(":id", $id, PDO::PARAM_INT)
                ->queryRow();   
        
        $sql = "SELECT * FROM metric_actions WHERE metric_visit_id=:id";
        $details = Yii::app()->db->createCommand($sql)
                ->bindParam(":id", $id, PDO::PARAM_INT)
                ->queryAll();
       
        if ($details)
            foreach ($details as $k=>$v)
            {
                switch ($v['action'])
                {
                    case 'viewVideo':
                        $actions['viewVideo'][$v['user_id_to']][] = $v['added'];
                        break;
                        
                    case 'viewPage':
                        /*if (stristr('/about', $v['action_details']))
                            $actions['viewPage']['About'][] = $v['added'];
                        else*/
                            $actions['viewPage'][] = $v;
                        
                        break;
                }
                
                unset($details[$k]);
            }
        
        $visit['actions'] = $actions;
       
        return $visit;
    }    
    
}

