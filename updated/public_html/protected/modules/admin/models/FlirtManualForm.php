<?php


class FlirtManualForm extends CFormModel
{
    public $date1;
    public $date2;
	
	public $paymod;
	/*public $paymods;
	public $paymodsForSelect;*/
    
	public $status;
	
	public $form;
	
    public $perPage;
    public $sort;
    
    public function init()
    {
        parent::init();
        
        $this->perPage = 50;
        $this->sort = "idDESC";
        
        
        /*$this->paymods = CHelperPayment::getPaymods();
        $this->paymods[] = '';
        
        asort($this->paymods);
        
        foreach ($this->paymods as $p)
        	if ($p=='')
        		$this->paymodsForSelect[$p] = 'All';
        	else 
        		$this->paymodsForSelect[$p] = $p;*/

        if (!$this->date1)
        	$this->date1=date("Y-m-d");
        if (!$this->date2)
        	$this->date2=date("Y-m-d");
    }
    
	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		$rules = array(
			array('date1, date2', 'date', 'allowEmpty'=>'false', 'format'=>'yyyy-MM-dd'),
			
            
            array('perPage', 'in', 'range'=>array('10','50','100','200','500','1000') ),
            array('sort', 'in', 'range'=>array('idASC','idDESC') ),
		);
		
		return $rules;
	}

	
	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
            'date1'     => 'From',
            'date2'     => 'To',
		
            'perPage'  =>'Per Page',
            'sort'  =>'Sort By',
		);
	}
   
	
	
	public function getMessages($page=0)
	{
	    $res = array();
		$msgs = false;
		$res['count'] = 0;
		
		$post = $this->attributes;
		
	    //$order = "m.id_from, m.id";
	    $order = "id, id_from";
	    
		$where[] = "m.autoflirt_answered='0'";
		$where[] = "m.added>='{$post['date1']} 00:00:00'";
	    $where[] = "m.added<='{$post['date2']} 23:59:59'";
	    $where[] = "u.id=m.id_to";
	    $where[] = "u.promo='1'";
	    
	    $where = implode(" AND ", $where);
	    
	    $sql = "SELECT MAX( m.id )  FROM `profile_messages` as m, users as u WHERE {$where} GROUP BY m.id_from, m.id_to";
	    
	    $ids = Yii::app()->db->createCommand($sql)->queryColumn();
//if (DEBUG_IP) CHelperSite::vd($ids);
	    
	    if ($ids)
	    {	    
		    $res['count'] = count($ids);
		        
		    $sql = "SELECT * FROM `profile_messages` WHERE id IN (".implode(',',$ids).")";
		    //$sql.= " ORDER BY {$order}";
		    $sql.= " ORDER BY {$order}";		
	
	        $sql.= " LIMIT ".($page * $post['perPage']).", " . $post['perPage'];
	      
	        $msgs = Yii::app()->db->createCommand($sql)->queryAll();
	        
	        if ($msgs)
	        {
	        	foreach($msgs as $k=>$m)
	        	{
					$sql = "SELECT * FROM `profile_messages` WHERE ((id_from={$m['id_from']} AND id_to={$m['id_to']}) OR (id_from={$m['id_to']} AND id_to={$m['id_from']})) AND id<>{$m['id']} ORDER BY id";
				    $msgs[$k]['allMessages'] = Yii::app()->db->createCommand($sql)->queryAll();
				    
				    $msgs[$k]['replies'] = count($msgs[$k]['allMessages']);
	        	}
	        }
	    }
//CHelperSite::vd($sql);	    
        $res['list'] = $msgs;
		
        return $res;         
	}
	
}
