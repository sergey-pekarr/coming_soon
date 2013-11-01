<?php

class SearchCondition
{
	private $userid;
	public function __construct(){
		$this->userid = Yii::app()->user->id;
	}
	
	private function &explodeMultiValue($text){
		$pItems = explode(',', $text);
		$result = array();
		foreach($pItems as $pItem){
			if($pItem != null) $pItem = trim($pItem);
			if($pItem != '' && $pItem != 0) $result[] = $pItem;
		}
		return $result;
	}
	
	private function &buildPerfect(){
		$profile = new Profile($this->userid);
		
		//Option 1: As old version
		//$profile->getDating();
		//if(!$profile) return array();
		//$result = array();
		//foreach($dating as $key => $value){
		//	$result[] = array('name'=>$key, 'value'=>$value);
		//}
		
		//Option 2: new onl
		$dating = $profile->getDating();
		
		//Changed: Client can accept both string or array
		//foreach($dating as $key => &$value){
		//	$value = $this->explodeMultiValue($value);
		//}		
		
		$dating['age'] = array($profile->getSettingsValue('ageMin'));
		$dating['maxage'] = array($profile->getSettingsValue('ageMax'));
		
		return $dating;		
	}
	
	public function &listCondition(){		
		$perfect = $this->buildPerfect();
		
		$list = array('Perfect Match' => json_encode($perfect));
		
		$qry = "select * from users_search where user_id = :user_id";
		$search = Yii::app()->db
			->createCommand($qry)
			->bindValue(":user_id", $this->userid, PDO::PARAM_INT)
			->queryAll();
		if($search){
			foreach($search as $item){
				$list[$item['name']] = $item['data'];
			}
		}
		return $list;
	}
	
	public function remove($post){
		$name = $post['name'];
		if(!$name || $name =='') return;
		
		$qry = "delete from users_search where user_id = :user_id and name=:name ";
		$result = Yii::app()->db
			->createCommand($qry)
			->bindValue(":user_id", $this->userid, PDO::PARAM_INT)
			->bindValue(":name", $name, PDO::PARAM_STR)
			->execute();
	}
	
	public function save($post){
		$name = $post['name'];
		$data = $post['data'];
		unset($data['profile_id']);
		//unset($data['']);
		$data = json_encode($data);
		if(!$name || $name =='' || !$data) return;
		
		
		$qry = "select * from  users_search where user_id = :user_id and name=:name";
		$result = Yii::app()->db
			->createCommand($qry)
			->bindValue(":user_id", $this->userid, PDO::PARAM_INT)
			->bindValue(":name", $name, PDO::PARAM_STR)
			->queryRow();
		
		if(!$result){
			$qry = "insert into users_search(user_id, name, data) values(:user_id, :name, :data)";
			$result = Yii::app()->db
				->createCommand($qry)
				->bindValue(":user_id", $this->userid, PDO::PARAM_INT)
				->bindValue(":name", $name, PDO::PARAM_STR)
				->bindValue(":data", $data, PDO::PARAM_STR)
				->execute();
		}
		else{			
			$qry = "update users_search set data = :data where user_id = :user_id and name=:name";
			$result = Yii::app()->db
				->createCommand($qry)
				->bindValue(":user_id", $this->userid, PDO::PARAM_INT)
				->bindValue(":name", $name, PDO::PARAM_STR)
				->bindValue(":data", $data, PDO::PARAM_STR)
				->execute();
		}
		
	}
	
	// */
	
}