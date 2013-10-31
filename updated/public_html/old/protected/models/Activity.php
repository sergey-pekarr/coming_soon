<?php

class Activity
{
	private $id;
	private $cachedata;
	private $mkey;
	private $cache;
	private $hasCached = true;
	//private $cachedirty = false;
	
	static private $_instances = array();
	static public function createActivity($id = 0){		
		if(!$id) $id = Yii::app()->user->id;
		if(isset(self::$_instances[$id])){
			return self::$_instances[$id];
		}
		else {
			self::$_instances[$id] = new Activity($id);
			return self::$_instances[$id];
		}
	}
	
	/**
	* Constructor.
	* @param integer $id
	   * @param boolean $useCache - use cache (for import or other)
	*/
	private function __construct($id=0)
	{
		if(!$id) $id = Yii::app()->user->id;
		$this->id = intval($id);
		$this->mkey = 'activity_'.$this->id;
		$this->cache = Yii::app()->params->cache['lastActivity'];
		
		$this->_cacheGet();
	}
	
	private function _cacheGet()	{
		
		if ($this->id){
			$this->cachedata = Yii::app()->cache->get( $this->mkey );		
		}
		$this->hasCached = ($this->cachedata != null);
		if(!$this->cachedata) {
			$this->cachedata = array();
		}
		return $this->cachedata;
	}
	
	private function _cacheUpdate()	{
		if ($this->id /*&& $this->cachedata*/) {
			Yii::app()->cache->set($this->mkey, $this->cachedata, $this->cache);
		}
	}
	
	private function _cacheDelete()
	{
		if ($this->id )	{
			Yii::app()->cache->delete($this->mkey);
		}
	}
	
	/**
	 * This method is used internally: only support winks, view, favourite, photorequest, block
	 * 
	 *
	 * @param mixed $type This is a description
	 * @param mixed $id_to This is a description
	 * @return mixed This is the return value description
	 *
	 */	
	private function sendActivity($type, $id_to){
		//Do not sent flirt to theirself
		if($this->id == $id_to) return null;
		
		if($type != 'block'){
			$block = $this->wasBlock($id_to, $type);
			if($block) return $block;
		}
		
		$id_to = intval($id_to);
		$tblName = 'profile_'.$type;
		
		$checkqry = "select * from $tblName where id_from = {$this->id} and id_to = $id_to";
		$existing = Yii::app()->db
			->createCommand($checkqry)
			->queryRow();
		
		$result = false;
		if($existing){
			$updateqry = "update $tblName set added = now(), count = count + 1, `read` = '0' where id_from = {$this->id} and id_to = $id_to";
			$result = Yii::app()->db
				->createCommand($updateqry)
				->execute();
			
			$count = 1+$existing['count'];//oleg, 2012-07-14
		}
		else {
			$insertqry = "insert into $tblName(id_from, id_to, added, `read`, count) values({$this->id}, $id_to, now(), '0', 1)";
			$result = Yii::app()->db
				->createCommand($insertqry)
				->execute();
			
			$count = 1;		//oleg, 2012-07-14	
		}
		
		
		//$obj = array('type'=>$type, 'id_from' => $this->id, 'id_to' => $id_to, 'added' => date('Y-m-d H:i:s'), 'read' => 0, 'count' => 1  );
		//oleg, 2012-07-14, replced with
		$obj = array('type'=>$type, 'id_from' => $this->id, 'id_to' => $id_to, 'added' => date('Y-m-d H:i:s'), 'read' => 0, 'count' => $count   );
		
		$this->updateSentActivityCache($type, $obj);
		
		$act_to = self::createActivity($id_to);		
		$act_to->updateActivityCache($type, $obj);
		
		return $result?$obj:false;
	}
	
	private function deleteActivity($type, $id_to){

		$id_to = intval($id_to);
		$tblName = 'profile_'.$type;
		
		$delqry = "delete from $tblName where id_from = {$this->id} and id_to = $id_to";
		$result = Yii::app()->db
			->createCommand($delqry)
			->execute();
		
		$obj = array('type'=>$type, 'id_from' => $this->id, 'id_to' => $id_to, 'added' => date('Y-m-d H:i:s'), 'read' => 0, 'count' => 1  );
		
		return $result?$obj:false;
	}
	
	private function getActivity($type,  $start = 0, $limit = 16, $orderby = ' added desc ', $customfilter = ''){
		$tblName = 'profile_'.$type;
		
		$qry = "select '$type' as `type`, $tblName.* from $tblName where id_to = {$this->id} ";
		if($customfilter && $customfilter != '') $qry .= " and  $customfilter ";
		
		if($orderby) $qry .= " order by $orderby ";
		if($start !== null && $limit) $qry .= " limit $start, $limit ";
		
		$data = Yii::app()->db
			->createCommand($qry)
			->queryAll();
		
		return $data;
	}
	
	private function getSentActivity($type,  $start = 0, $limit = 16, $orderby = ' added desc ', $customfilter = ''){
		$tblName = 'profile_'.$type;
		
		$qry = "select '$type' as `type`, $tblName.* from $tblName where id_from = {$this->id} ";
		if($customfilter && $customfilter != '') $qry .= " and  $customfilter ";
		
		if($orderby) $qry .= " order by $orderby ";
		if($start !== null && $limit) $qry .= " limit $start, $limit ";
		
		$data = Yii::app()->db
			->createCommand($qry)
			->queryAll();
		
		return $data;
	}
	
	/**
	 * $read = 0: Unread
	 *		   1: Read
	 *	   other: both
	 * 
	 * @param mixed $type This is a description
	 * @param mixed $read This is a description
	 * @return mixed This is the return value description
	 *
	 */		
	private function countActivity($type, $read = 2){
		
		$subkey = "count_{$type}_$read";
		if(isset($this->cachedata[$subkey])){
			return $this->cachedata[$subkey];
		}
		
		$tblName = 'profile_'.$type;
		
		$qry = "select count(*) from $tblName where id_to = {$this->id} ";
		if($read == 0 || $read == 1) $qry .= " and `read` = '$read'";
		
		$count = Yii::app()->db
			->createCommand($qry)
			->queryScalar();
		
		//Only cache countUnread
		if($read == 0){
			$this->cachedata[$subkey] = $count;
			$this->_cacheUpdate();
		}
		
		return $count;
	}
	
	private function countSentActivity($type){
		$tblName = 'profile_'.$type;
		
		$qry = "select count(*) from $tblName where id_from = {$this->id} ";
		
		$count = Yii::app()->db
			->createCommand($qry)
			->queryScalar();
		
		return $count;
	}
	
	private function setRead($type, $limit = -1){
		$tblName = 'profile_'.$type;
		$qry = "update $tblName set `read` = '1' where id_to = {$this->id} ";
		if($limit>0) $qry .= " limit $limit ";
		
		$result = Yii::app()->db
			->createCommand($qry)
			->execute();
		
		//Clear read in cache
		$subkey = "count_{$type}_0";		
		if(isset($this->cachedata[$subkey])){
			$this->cachedata[$subkey] = 0;
			$this->_cacheUpdate();
		}		
		
		return $result;
	}
	
	private function addUniqueToCache($arrKey, $obj){
		if(!isset($this->cachedata[$arrKey])) return;
		for($i=0;$i<count($this->cachedata[$arrKey]);$i++){
			$item = &$this->cachedata[$arrKey][$i];
			if($obj['id_from'] == $item['id_from'] && $obj['id_to'] == $item['id_to']){
				$this->cachedata[$arrKey][$i] = $obj;
				return;
			}
		}
		$this->cachedata[$arrKey][] = $obj;
	}
	
	/**
	 * Tell $id_fom that new flirt has sent to him. He can set cache dirty to update all items, or choose to update specific fields
	 *
	 * @param mixed $type This is a description
	 * @param mixed $id_fom This is a description
	 * @param mixed $obj This is a description
	 * @return mixed This is the return value description
	 *
	 */	
	public function updateActivityCache($type, $obj){
		
		/*
		Cache Structure
			[$mkey][count_xxx_1]
			[$mkey][RecentItems]
			[$mkey][viewitems]
			[$mkey][youviewitems]
			[$mkey][lastactivity]
		Init cache data the first time user need data: countXXX, getRecent, getViews, getYouView/sendView
		Update cache data whenever related items changed: setReadActivity, setReadMessage, SendActivity, SendMessage, SendLike
				
		*/
		
		//n: 2012-07-25: This method is used when some one want to notify user about just sent activity.
		// => we can ignore when cache has not been created! -> prevent useless data in cache
		
		if(!$this->hasCached) return;
		
		//correct object
		$obj['type'] = $type;
		
		$subkey = "count_{$type}_0";
		if(isset($this->cachedata[$subkey]) && $type=='messages'){
			
			//Only message can be increased by 1
			//$this->cachedata[$subkey]++;	
			
			//Change 2012-07-17: Change, reply message will cause master message become unread
			unset($this->cachedata[$subkey]);			
		}
		else {			
			//Note: the action might duplicate, we can't simple increase the value. Clear cache so we will calculate value again
			unset($this->cachedata[$subkey]);
		}
		
		
		if(isset($this->cachedata['recentitems']) && in_array($type, array('messages'))){
			$this->cachedata['recentitems'][] = $obj;
		}
		
		if(in_array($type, array('winks','photorequest'))){
			$this->addUniqueToCache('recentitems',$obj);
		}
		if(in_array($type, array('view'))){
			$this->addUniqueToCache('viewitems',$obj);
		}	
		
		if(isset($this->cachedata['wasblock']) && in_array($type, array('block'))){
			$this->cachedata['wasblock'][$obj['id_from']] = true;
		}
		if(isset($this->cachedata['wasblock']) && in_array($type, array('unblock'))){
			$this->cachedata['wasblock'][$obj['id_from']] = null;
		}
		
		
		//if(isset($this->cachedata['recentitems']) && in_array($type, array('winks','photorequest','messages'))){
		//	//Remove duplicate?
		//	$this->cachedata['recentitems'][] = $obj;
		//}
		//
		//if(isset($this->cachedata['viewitems']) && in_array($type, array('view'))){
		//	//Remove duplicate?
		//	$this->cachedata['viewitems'][] = $obj;
		//}
		
		//LastItems does not belong to old value in database. 
		
		//Note: Only designed activity will be record. Other activity will not allow to override
		if(isset($this->cachedata['lastitems']) && in_array($type, array('winks','photorequest', 'messages', 'view'))){
			$this->cachedata['lastitems'] = $obj;
		}
		
		if(in_array($type, array('gift'))){
			unset($this->cachedata['gift']);
		}
		
		$this->_cacheUpdate();				
	}
	
	private function updateSentActivityCache($type, $obj){
		$obj['type'] = $type;		
		
		if(isset($this->cachedata['recentitems']) && in_array($type, array('messages'))){
			$this->cachedata['recentitems'][] = $obj;
			//$this->_cacheUpdate();	
		}
		
		if(in_array($type, array('winks','photorequest'))){
			$this->addUniqueToCache('recentitems',$obj);
			//$this->_cacheUpdate();	
		}
		if(in_array($type, array('view'))){
			$this->addUniqueToCache('youviewitems',$obj);
			//$this->_cacheUpdate();	
		}
		
		//$counts = array('messages','winks', 'favourite', 'like');
		//foreach($counts as $key){
		//	$mkey = "sent_{$key}_count";
		//	//For levelup usage, we dont care if user has sent over 10 activity
		//	if(isset($this->cachedata[$mkey]) && $this->cachedata[$mkey] < 10){
		//		unset($this->cachedata[$mkey]);
		//	}
		//}
		$mkey = "sent_{$type}_count";
		//For levelup usage, we dont care if user has sent over 10 activity
		if(isset($this->cachedata[$mkey])){
			//if(isset($this->cachedata[$mkey]) && $this->cachedata[$mkey] < 10){
			unset($this->cachedata[$mkey]);
		}
		$this->_cacheUpdate();	
	}
	
	/**
	 * Check if user is in other's block list
	 *
	 * @return mixed This is the return value description
	 *
	 */
	private function wasBlock($id_to, $blockaction = ''){
		if(!isset($this->cachedata['wasblock'])){
			$tblName = 'profile_block';
			$qry = "select id_from from $tblName where id_to = {$this->id} ";			
			$data = Yii::app()->db
				->createCommand($qry)
				->queryAll();
			$blocklist = array();
			foreach($data as $item){
				$blocklist[$item['id_from']] = true;
			}
			$this->cachedata['wasblock'] = $blocklist;
			$this->_cacheUpdate();
		}
		if(isset($this->cachedata['wasblock'][$id_to])){
			return array('type' => 'wasblock', 'id_from' => $id_to, 'id_to' => $this->id, 'blockaction' => $blockaction);
		}
		return false;
	}	
	/* -- Winks -- */
	
	public function sendWinks($id_to){
		return $this->sendActivity('winks',$id_to);
	}
	
	public function getWinks( $start = 0, $limit = 16, $orderby = ' added desc '){
		return $this->getActivity('winks',  $start, $limit, $orderby);
	}
	
	public function getSentWinks( $start = 0, $limit = 16, $orderby = ' added desc '){
		return $this->getSentActivity('winks',  $start, $limit, $orderby);
	}
	
	public function countUnreadWinks(){
		return $this->countActivity('winks', 0);
	}
	
	public function countAllWinks(){
		return $this->countActivity('winks', 2);
	}
	
	public function countSentWinks(){
		return $this->countSentActivity('winks');
	}
	
	public function setReadWinks($start = 0, $limit = -1){
		return $this->setRead('winks',$start, $limit);
	}
	
	//Do not need public method. This is called interally
	//public function updateStatusWinks($id_fom, $obj = null){
	//	return $this->updateStatus('winks', $id_fom, $obj);
	//}
	
	/* -- View -- */
	
	public function sendView($id_to){
		$result = $this->sendActivity('view',$id_to);
		
		//if(isset($this->cachedata[$subkey])){
		//	//Remove duplicate?
		//	$this->cachedata[$subkey][] = $result;
		//	//else $this->cachedata[$subkey] = $this->getSentView(0,5);	
		//}
		
		//Move to updateSentActivityCache
		//$this->addUniqueToCache('youviewitems', $result);
		//$this->_cacheUpdate();	
		
		//Insert into viewed
		$sql = "Insert into profile_viewed (user_id, count) values ({$id_to}, 1)
  				On Duplicate Key Update count=count+1;";
		$ok = Yii::app()->db->createCommand($sql)->execute(); 
		
		return $result;
	}
	
	public function getView( $start = 0, $limit = 16, $orderby = ' added desc '){
		return $this->getActivity('view',  $start, $limit, $orderby);
	}
	
	public function getSentView( $start = 0, $limit = 16, $orderby = ' added desc '){
		return $this->getSentActivity('view',  $start, $limit, $orderby);
	}
	
	public function countUnreadView(){
		return $this->countActivity('view', 0);
	}
	
	public function countAllView(){
		return $this->countActivity('view', 2);
	}
	
	public function countSentView(){
		return $this->countSentActivity('view');
	}
	
	public function setReadView($start = 0, $limit = -1){
		return $this->setRead('view',$start, $limit);
	}
	
	//Do not need public method. This is called interally
	//public function updateStatusView($id_fom, $obj = null){
	//	return $this->updateStatus('view', $id_fom, $obj);
	//}
	
	/* -- Favourite -- */
	
	public function sendFavourite($id_to){
		return $this->sendActivity('favourite',$id_to);
	}
	
	public function getFavourite( $start = 0, $limit = 16, $orderby = ' added desc '){
		return $this->getActivity('favourite',  $start, $limit, $orderby);
	}
	
	public function getMyFavourite( $start = 0, $limit = 16, $orderby = ' added desc '){
		return $this->getSentActivity('favourite',  $start, $limit, $orderby);
	}
	
	public function countUnreadFavourite(){
		return $this->countActivity('favourite', 0);
	}
	
	public function countAllFavourite(){
		return $this->countActivity('favourite', 2);
	}
	
	public function countMyFavourite(){
		return $this->countSentActivity('favourite');
	}
	
	public function setReadFavourite($start = 0, $limit = -1){
		return $this->setRead('favourite',$start, $limit);
	}
	
	//Do not need public method. This is called interally
	//public function updateStatusFavourite($id_fom, $obj = null){
	//	return $this->updateStatus('favourite', $id_fom, $obj);
	//}
	
	/* -- PhotoRequest -- */
	
	public function sendPhotoRequest($id_to){
		return $this->sendActivity('photorequest',$id_to);
	}
	
	public function getPhotoRequest( $start = 0, $limit = 16, $orderby = ' added desc '){
		return $this->getActivity('photorequest',  $start, $limit, $orderby);
	}
	
	public function getSentPhotoRequest( $start = 0, $limit = 16, $orderby = ' added desc '){
		return $this->getSentActivity('photorequest',  $start, $limit, $orderby);
	}
	
	public function countUnreadPhotoRequest(){
		return $this->countActivity('photorequest', 0);
	}
	
	public function countAllPhotoRequest(){
		return $this->countActivity('photorequest', 2);
	}
	
	public function setReadPhotoRequest($start = 0, $limit = -1){
		return $this->setRead('photorequest',$start, $limit);
	}
	
	//Do not need public method. This is called interally
	//public function updateStatusPhotoRequest($id_fom, $obj = null){
	//	return $this->updateStatus('photorequest', $id_fom, $obj);
	//}
	
	/* -- Like -- */
	
	public function sendLike($id_to, $pics){
		//Do not sent flirt to theirself
		if($this->id == $id_to) return null;
		
		$block = $this->wasBlock($id_to, 'like');
		if($block) return $block;
		
		$tblName = 'profile_like';
		
		$checkqry = "select * from $tblName where id_from = {$this->id} and id_to = $id_to";
		$existing = Yii::app()->db
			->createCommand($checkqry)
			->queryRow();
		
		$result = false;
		if($existing){
			if($existing['pics'] && $existing['pics'] != '') $pics = " {$existing['pics']}, $pics";
			$updateqry = "update $tblName set added = now(), count = count + 1, `read` = '0', pics = '$pics' 
						where id_from = {$this->id} and id_to = $id_to";
			$result = Yii::app()->db
				->createCommand($updateqry)
				->execute();
		}
		else {
			$insertqry = "insert into $tblName(id_from, id_to, added, `read`, count, pics) 
						values({$this->id}, $id_to, now(), '0', 1, '$pics')";
			$result = Yii::app()->db
				->createCommand($insertqry)
				->execute();			
		}
		
		$obj = array('type'=> 'like','id_from' => $this->id, 'id_to' => $id_to, 'added' => date('Y-m-d H:i:s'), 
			'read' => 0, 'count' => 1, 'pics' => $pics  );
		
		$this->updateSentActivityCache('like', $obj);
		
		$act_to = self::createActivity($id_to);		
		$act_to->updateActivityCache('like', $obj);
		
		return $result?$obj:false;
	}
	
	public function getLike( $start = 0, $limit = 16, $orderby = ' added desc '){
		return $this->getActivity('like',  $start, $limit, $orderby);
	}
	
	public function countUnreadLike(){
		return $this->countActivity('like', 0);
	}
	
	public function countAllLike(){
		return $this->countActivity('like', 2);
	}
	
	public function setReadLike($start = 0, $limit = -1){
		return $this->setRead('like',$start, $limit);
	}
	
	//Do not need public method. This is called interally
	//public function updateStatusLike($id_fom, $obj = null){
	//	return $this->updateStatus('like', $id_fom, $obj);
	//}
	
	/* -- Block -- */
	
	public function block($id_to){
		return $this->sendActivity('block',$id_to);
	}
	
	public function unBlock($id_to){		
		$res = $this->deleteActivity('block',$id_to);	
		$act = self::createActivity($id_to);
		$act->updateActivityCache('unblock', array('type' => 'unblock', 'id_from' => $this->id, 'id_to' => $id_to));
		return $res;	
	}
	
	/**
	 * Return list of members are blocked by user
	 *
	 * @param mixed $orderby This is a description
	 * @param mixed $start This is a description
	 * @param mixed $limit This is a description
	 * @return mixed This is the return value description
	 *
	 */	
	public function getBlock( $start = 0, $limit = 16, $orderby = ' added desc '){
		return $this->getSentActivity('block',  $start, $limit, $orderby);
	}
	
	public function countBlock(){
		return $this->countSentActivity('block');
	}
	
	//Do not need countUnreadBlock, setReadBlock, updateStatusBlock
	
	
	/* -- Messages -- */
	
	public function sendMessages($id_to, $subject, $text, $parent = null){
		//Do not sent flirt to theirself
		if($this->id == $id_to || !$id_to) return null;
		
		$block = $this->wasBlock($id_to, 'messages');
		if($block) return $block;
		
		$tblName = 'profile_messages';
		$id_to = intval($id_to);
		
		if($parent){
			$parent = intval($parent);
			
			//					where ((`parent` is null && id = $parent))
			$checkparentqry = "select id, parent from $tblName 
								where (id = $parent)
								and (  (id_from = $id_to and id_to={$this->id}) 
										or (id_from = {$this->id} and id_to=$id_to))";
			$parentRow = Yii::app()->db
				->createCommand($checkparentqry)
				->queryRow();
			if(!$parentRow){
				$parent = null;
				
				//input is not correct (parent) -> should return instead of use default
				return null;
			}
			
			
			/* No need anymore. we will get last item	
			else {
				$udtparentqry = "update $tblName set `read` = '0' 								
								where id = $parent
								and (  (id_from = $id_to and id_to={$this->id}) 
										or (id_from = {$this->id} and id_to=$id_to))";
				$parentRow = Yii::app()->db
					->createCommand($udtparentqry)
					->execute();
			}*/		
		}
		
		if($parent) $parentText = $parent;
		else $parentText = 'null';
		
		/* n: 2012-07-18: need to verify this logic: Will be show all messages or just show last items in thread
		if($parent){
			$updatelast = "update $tblName set `last` = '0'
						   where id_from = {$this->id} and `parent` = $parentText";
			Yii::app()->db
				->createCommand($updatelast)
				->execute();
		}*/
		
		$insertqry = "insert into $tblName(id_from, id_to, added, `read`, `subject`, `text`, parent, `last`) 
						values({$this->id}, $id_to, now(), '0', :subject, :text, $parentText, '1')";
		
		$result = Yii::app()->db
			->createCommand($insertqry)
			->bindValue(":subject", $subject, PDO::PARAM_STR)
			->bindValue(":text", $text, PDO::PARAM_STR)
			->execute();			
		
		$obj = array('type' => 'messages', 'id' => Yii::app()->db->getLastInsertId($tblName),'id_from' => $this->id, 'id_to' => $id_to, 
			'added' => date('Y-m-d H:i:s'), 'read' => 0, 'subject' => $subject, 'text' => $text, 'parent' => $parent  );
		
		$this->updateSentActivityCache('messages',$obj);
		
		$act_to = self::createActivity($id_to);		
		$act_to->updateActivityCache('messages', $obj);
		
		return $result?$obj:false;
		
	}
	
	public function getMessages( $start = 0, $limit = 16, $orderby = ' added desc ', $customfilter = ''){
		return $this->getActivity('messages',  $start, $limit, $orderby, $customfilter);
	}
	
	public function getSentMessages( $start = 0, $limit = 16, $orderby = ' added desc ', $customfilter = ''){
		return $this->getSentActivity('messages',  $start, $limit, $orderby, $customfilter);
		return array();
	}
	
	/**
	* Count for Primary Message (not include reply)
	*
	* @return mixed This is the return value description
	*
	*/	
	public function countUnreadMessages(){
		
		//return $this->countActivity('messages', 0);
		$type = 'messages';
		$read = 0;
		
		$subkey = "count_{$type}_$read";
		if(isset($this->cachedata[$subkey])){
			return $this->cachedata[$subkey];
		}
		
		$tblName = 'profile_'.$type;
		
		//$qry = "select count(*) from $tblName where id_to = {$this->id} and `parent` is null";
		
		//Was wrong. what we need is to display last message in thread
		$qry = "select count(*) from $tblName where id_to = {$this->id} and `last` = '1'";
		if($read == 0 || $read == 1) $qry .= " and `read` = '$read'";
		
		$count = Yii::app()->db
			->createCommand($qry)
			->queryScalar();
		
		//Only cache countUnread
		if($read == 0){
			$this->cachedata[$subkey] = $count;
			$this->_cacheUpdate();
		}
		
		return $count;
		
	}
	
	// seem not be used
	//public function countAllMessages(){
	//	return $this->countActivity('messages', 2);
	//}
	
	public function setReadMessagesByThread($contactId, $parentid){
		$contactId = intval($contactId);
		$parentid = intval($parentid);
		$tblName = 'profile_messages';
		$qry = "update $tblName set `read` = '1' where id_from = $contactId and id_to={$this->id}";
		$result = Yii::app()->db
			->createCommand($qry)
			->execute();
		
		//-> set dirty to recalculate (when needed)
		$subkey = "count_messages_0";		
		unset($this->cachedata[$subkey]);
		$this->_cacheUpdate();	
		
		return $result;	
	}
	
	/*public function setReadMessage($id){
		$tblName = 'profile_messages';
		$qry = "update $tblName set read = 1 where id = :id";
		$result = Yii::app()->db
			->createCommand($qry)
			->bindValue(":id", $id, PDO::PARAM_INT)
			->execute();
		
		//Decrese by 1
		$subkey = "count_messages_0";		
		if(isset($this->cachedata[$subkey]) && $this->cachedata[$subkey]>0){
			$this->cachedata[$subkey]--;
			$this->_cacheUpdate();
		}
		return $result;	
	}*/
	
	//Do not need public method. This is called interally
	//public function updateStatusMessages($id_fom, $obj = null){
	//	//Add code here
	//}
	
	/*public function getMessage($id){
		$tblName = 'profile_messages';
		$qry = "select * from $tblName where id = :id";
		$result = Yii::app()->db
			->createCommand($qry)
			->bindValue(":id", $id, PDO::PARAM_INT)
			->queryRow();
		return $result;
	}*/
	
	public function getThreads($contactId, $parentid, $start = 0, $limit = 50){
		$contactId = intval($contactId);
		$parentid = intval($parentid);
		$tblName = 'profile_messages';
		
		//Get last 50 messages
		$qry = "select 'messages' as `type`, m.* from  $tblName as m 
				where ((id_from = $contactId and id_to={$this->id}) or (id_from = {$this->id} and id_to=$contactId))
					   and (`parent` = $parentid or id = $parentid)
					   and ((id_to={$this->id} && hided_to = '0') or id_from = {$this->id}) 
				order by added desc
				limit $start, $limit";
		$result = Yii::app()->db
			->createCommand($qry)
			->queryAll();
		
		return $result;
	}
	
	/* -- Gift -- */
	
	public function sendGift($id_to, $gift){
		//Do not sent flirt to theirself
		if($this->id == $id_to) return null;
		
		$block = $this->wasBlock($id_to, 'gift');
		if($block) return $block;
		
		$tblName = 'profile_gift';
		
		$checkqry = "select * from $tblName where id_from = {$this->id} and id_to = $id_to and gift = :gift";
		$existing = Yii::app()->db
			->createCommand($checkqry)
			->bindValue(":gift", $gift , PDO::PARAM_STR) //safe
			->queryRow();
		
		$result = false;
		if($existing){
			$updateqry = "update $tblName set added = now(), count = count + 1, `read` = '0' 
						where id_from = {$this->id} and id_to = $id_to and gift = :gift";
			$result = Yii::app()->db
				->createCommand($updateqry)
				->bindValue(":gift", $gift , PDO::PARAM_STR) //safe
				->execute();
		}
		else {
			$insertqry = "insert into $tblName(id_from, id_to, added, `read`, count, gift) 
						values({$this->id}, $id_to, now(), '0', 1, :gift)";
			$result = Yii::app()->db
				->createCommand($insertqry)
				->bindValue(":gift", $gift , PDO::PARAM_STR) //safe
				->execute();			
		}
		
		$obj = array('type'=> 'gift','id_from' => $this->id, 'id_to' => $id_to, 'added' => date('Y-m-d H:i:s'), 
			'read' => 0, 'count' => 1, 'gift' => $gift  );
		
		//No need to update ourself
		//$this->updateSentActivityCache('gift', $obj);	
		
		$act_to = self::createActivity($id_to);		
		$act_to->updateActivityCache('gift', $obj);
		
		return $result?$obj:false;
	}
	
	private function mergeFakeGifts($fakeProfile, $result){
		$id = $fakeProfile->getId();
		$qry = "select gifts from profile_gift_promos where user_id = $id";
		$gifts = Yii::app()->db
			->createCommand($qry)
			->queryScalar();
		if(!$gifts){
			return $result;
		}
		else {
			$giftarr = explode(',', $gifts);
			foreach($giftarr as $item){
				if($item != null && trim($item) != ''){
					$item = trim($item);
					$existing = false;
					foreach($result as $record){
						if($item == $record['gift']){
							$existing = true;
							break;
						}
					}
					if(!$existing){
						$result[] = array('gift' => $item);
					}
				}
			}
		}
		return $result;
	}
	
	public function getGifts($fakeProfile = null){
		if(isset($this->cachedata['gift'])){
			return $this->cachedata['gift'];
		}
		else {
			$tblName = 'profile_gift';
			$qry = "select gift from $tblName 
						where id_to = {$this->id} 
						group by gift";
			$result = Yii::app()->db
				->createCommand($qry)
				->queryAll();
			if(!$result){
				$result = array();
			}
			
			if($fakeProfile){
				$result = $this->mergeFakeGifts($fakeProfile, $result);
			}				
			
			$this->cachedata['gift'] = $result;
			$this->_cacheUpdate();
			return $result;
		}
		return false;
	}
	
	public function getGiftsByType(){
		//Will support later
	}
	
	
	/* -- Sidebar -- */
	
	public function getCountUnreadAll(){
		
		//Note: countUnreadXXX has done or used cache.
		
		return array('Email'=>$this->countUnreadMessages(), 'Wink'=>$this->countUnreadWinks(),
			'View' => $this->countUnreadView(), 'Favorite' => $this->countUnreadFavourite() , 
			'PhotoRequest' => $this->countUnreadPhotoRequest(), 'Like' => $this->countUnreadLike(),);
	}
	
	public function getDashboardRecent(){		
		$subkey = "recentitems";		
		$result = array();
		
		if(isset($this->cachedata[$subkey])){
			$result = $this->cachedata[$subkey];
		}
		else{
			
			$winks = $this->getWinks(0,10);
			if(!$winks) $wink = array();
			$photo= $this->getPhotoRequest(0,10);
			if(!$photo)  $photo= array();
			$messages = $this->getMessages(0,10);
			
			$result = array_merge($winks, array_merge($photo,$messages));
			
			//Get both sent and receive items
			$winks = $this->getSentWinks(0,10);
			if(!$winks) $wink = array();
			$photo= $this->getSentPhotoRequest(0,10);
			if(!$photo)  $photo= array();
			$messages = $this->getSentMessages(0,10);
			
			$result = array_merge($result, array_merge($winks, array_merge($photo,$messages)));
		}
		
		//Only get 10 most recent items.
		
		//$addeds = array();
		//foreach($result as $item){
		//	$addeds[] = $item['added'];
		//}
		//array_multisort($addeds, SORT_DESC, $result);		
		//$result = array_slice($result,0,10);
		
		$result = $this->getMostRecentActivity($result,10);
		
		$this->cachedata[$subkey] = $result;
		$this->_cacheUpdate();		
		return $result;
	}
	
	/**
	 * Return current status for sidebard: getCountUnreadAll + Viewing + Viewed + YouView + MostRecentActivity (Alert)
	 *
	 * @return mixed This is the return value description
	 *
	 */	
	public function getSidebarData(){
		
		$result = array(
			'activity' => $this->getCountUnreadAll(),
			'viewed' => $this->getRecentViewItems(),
			'youviewed' => $this->getRecentYouViewItems(),
			'viewing' => false, //Will be extract from viewed
			'alert' => isset($this->cachedata['lastitems'])?$this->cachedata['lastitems']:false,
			);
		
		$this->cachedata['lastitems'] = false; //Return for one time
		$this->_cacheUpdate();
		
		return $result;		
		//The result will be rendered to html in view
	}
	
	private function getMostRecentActivity($result, $count = 5){
		$addeds = array();
		foreach($result as $item){
			$addeds[] = $item['added'];
		}
		array_multisort($addeds, SORT_DESC, $result);
		
		return array_slice($result,0,$count);
	}
	
	private function getRecentViewItems(){
		$subkey = "viewitems";	
		$result = null;
		
		if(isset($this->cachedata[$subkey])){
			$result = $this->cachedata[$subkey];
		}
		
		if(!$result) {
			$result = $this->getView(0,10);
		}
		
		if(!$result) $result = array();
		
		//Might be viewing: 5 + viewed: 5
		$result = $this->getMostRecentActivity($result,10);
		$this->cachedata[$subkey] = $result;
		$this->_cacheUpdate();
		
		return $result?$result:array();
	}
	
	private function getRecentYouViewItems(){
		$subkey = "youviewitems";
		$result = null;
		
		//if(isset($this->cachedata[$subkey])) { 
		//	$result = $this->cachedata[$subkey];
		//}
		
		if(!$result) {
			$result = $this->getSentView(0,5);
		}
		
		if(!$result) $result = array();
		
		$result = $this->getMostRecentActivity($result,5);
		$this->cachedata[$subkey] = $result;
		$this->_cacheUpdate();
		
		return $result?$result:array();
	}
	
	/**
	 * Is used in Viewprofile. Does not need cache
	 *
	 * @param mixed $contactId This is a description
	 * @param mixed $limit This is a description
	 * @return mixed This is the return value description
	 *
	 */	
	public function getRecentActivities($contactId, $limit = 10){
		$contactId = intval($contactId);
		//$messages = $this->getMessages(0,$limit, ' added desc', " (id_from = $contactId or id_to = $contactId)");
		
		$tblName = 'profile_messages';		
		$qry = "select 'messages' as `type`, m.* from  $tblName as m 
				where (id_from = $contactId and id_to={$this->id}) or (id_from = {$this->id} and id_to=$contactId)
				order by added desc
				limit 0, $limit";
		$messages = Yii::app()->db
			->createCommand($qry)
			->queryAll();			
		if(!$messages) $messages = array();
		
		$tblName = 'profile_winks';		
		$qry = "select 'winks' as `type`, m.* from  $tblName as m 
				where (id_from = $contactId and id_to={$this->id}) or (id_from = {$this->id} and id_to=$contactId)
				order by added desc
				limit 0, $limit";
		$winks = Yii::app()->db
			->createCommand($qry)
			->queryAll();
		if(!$winks) $winks = array();
		
		$tblName = 'profile_photorequest';		
		$qry = "select 'photorequest' as `type`, m.* from  $tblName as m 
				where (id_from = $contactId and id_to={$this->id}) or (id_from = {$this->id} and id_to=$contactId)
				order by added desc
				limit 0, $limit";
		$photorequests = Yii::app()->db
			->createCommand($qry)
			->queryAll();
		if(!$photorequests) $photorequests = array();
		
		$result = array_merge($messages, array_merge($winks, $photorequests));
		
		return $this->getMostRecentActivity($result,$limit);
	}
	
	/**
	 * Is used for level up panel and popup
	 *
	 * @return mixed This is the return value description
	 *
	 */	
	public function getLevelUpCounts(){
		$counts = array('messages' => 0,'winks' => 0, 'favourite' => 0, 'like' => 0);
		FB::info($this->cachedata, 'getLevelUpCounts_cache');
		$needUpdate = false;
		foreach($counts as $key => &$count){
			$mkey = "sent_{$key}_count";
			if(isset($this->cachedata[$mkey])){
				$count = $this->cachedata[$mkey];
			}
			else{
				$qry = "select count(*) from profile_{$key} where id_from={$this->id}";
				$count = Yii::app()->db
					->createCommand($qry)
					->queryScalar();
				if(!$count) $count=0;
				$this->cachedata[$mkey] = $count;
				$needUpdate = true;
			}
		}
		FB::info($this->cachedata, 'getLevelUpCounts_cache2');
		if($needUpdate) $this->_cacheUpdate();
		return $counts;
	}
	
	/* Optimize later
	private function _cacheUpdate()
	{
		if ( $this->useCache && $this->id &&  isset($this->data['id']) && $this->data['id']==$this->id)
		{
			$mkey = "profile_".$this->id;
			Yii::app()->cache->set($mkey, $this->data, Yii::app()->params->cache['profile']);
		}
	}
		
	private function _cacheGet()
	{
		if ( $this->useCache && $this->id )
		{
			$mkey = "profile_".$this->id;
			return Yii::app()->cache->get( $mkey );
		}
		else
			return false;
	}
		
	private function _cacheDelete()
	{
		if ( $this->useCache && $this->id )
		{
			$mkey = "profile_".$this->id;
			Yii::app()->cache->delete($mkey);
		}
	}
		
	public function getData()
	{	
		if ( $this->id )
		{
			$this->data = $this->_cacheGet();
			if ( $this->data === false )
			{
				if ($this->useCache) FB::warn('CACHE IS EMPTY FOR USER '.$this->id);
				
				
				$this->_cacheUpdate();
			}
		}
		
		return $this->data;
	} 
	*/    
}