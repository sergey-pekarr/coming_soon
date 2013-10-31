<?php

class PaymentsController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}
	
	
	public function actionTodayGold()
	{
		$model = new PaymentTodayGoldForm;
		$model->attributes = (isset($_REQUEST['PaymentTodayGoldForm'])) ? $_REQUEST['PaymentTodayGoldForm'] : '';
		
		$page = (isset($_REQUEST['page'])) ? intval($_REQUEST['page']) : 0;
		$page = ($page) ? $page : 1;
		$page--;        
		
		$rows = array('count'=>0, 'countWaiting'=>0, 'list'=>false);
		if ($model->validate())
			$rows = Payments::getTodayGold($model->attributes, $page);
		
		$pages=new CPagination($rows['count']);
		$pages->pageSize = $model->perPage;
		$pages->setCurrentPage($page);
		
		$this->render(
			'todayGold',
			array(
					'model'=>$model,
					'rows'=>$rows,
					'pages'=>$pages,
					/*'bil'=>$bil, 
					'sales_done'=>$sales_done, 
					'nextBilling'=>$nextBilling,
					'isToday'=>$isToday,
					'date'=>$date,
					'prev'=>date("Y-m-d", strtotime("-1 day", strtotime($date))),
					'next'=>date("Y-m-d", strtotime("+1 day", strtotime($date))),*/
					)
				);		
	}
	
	
	public function actionBillingControll()
	{
		$date = (isset($_GET['date'])) ? $_GET['date'] : date("Y-m-d");
		$form = (isset($_GET['form'])) ? $_GET['form'] : "93";
		$isToday = ($date==date("Y-m-d"));    
		
		if ($_POST)
		{
			if (isset($_POST['id']))
			{
				foreach($_POST['id'] as $k=>$id)
				{
					$sql = "UPDATE pm_billing_control SET `order`={$_POST['order'][$k]}, `paymod`='{$_POST['paymod'][$k]}', sales={$_POST['sales'][$k]} WHERE id={$id} LIMIT 1";
					Yii::app()->db->createCommand($sql)->execute();
					
					if ( isset($_POST['del'][$k]))
					{
						$sql = "DELETE FROM pm_billing_control WHERE id={$_POST['del'][$k]} LIMIT 1";
						Yii::app()->db->createCommand($sql)->execute();
					}
				}
			}
			
			
			if ($_POST['add_paymod'])
			{
				$sql = "INSERT INTO pm_billing_control (`order`, `paymod`, `sales`, `form`) VALUES ({$_POST['add_order']}, '{$_POST['add_paymod']}', {$_POST['add_sales']}, '{$form}' )";
				Yii::app()->db->createCommand($sql)->execute();
			}
		}


		$sql = "SELECT * FROM pm_billing_control WHERE `form`='{$form}' ORDER BY `order`";
		$bil = Yii::app()->db->createCommand($sql)->queryAll();
		
		if (!$isToday)
		{
			$sql = "SELECT DISTINCT(paymod) FROM user_payment WHERE lastpay='{$date}' AND firstpay=lastpay AND `form`='{$form}'";
			$bil_tmp = Yii::app()->db->createCommand($sql)->queryAll();
			if ($bil_tmp)
				foreach($bil_tmp as $b_tmp)
				{
					$exists = false;
					foreach($bil as $b)
						if ($b['paymod']==$b_tmp['paymod'])
							$exists = true;
					
					if (!$exists)
					{
						$bil[] = array(
							'paymod' => $b_tmp['paymod']
							);
					}
				}
		}

		$sales_done = array();
		
		if ($bil)
			foreach ($bil as $p)
				$sales_done[$p['paymod']] = 0;    

		
		//if ($isToday)
		{
			$sql = "SELECT paymod FROM user_payment WHERE firstpay='{$date}' AND firstpay=lastpay AND `form`='{$form}'";
			$dbres = Yii::app()->db->createCommand($sql)->queryAll();
			
			if ($dbres)
				foreach ($dbres as $v)
					if (isset($sales_done[$v['paymod']]))
						$sales_done[$v['paymod']]++;	        	
					else
						$sales_done[$v['paymod']] = 1;
			
		}   

		$nextBilling = CHelperPayment::getAllinNextBilling($form);


		//$t->assign("info", '<br/><br/><br/>Time on server: '.date("Y-m-d H:i:s").'<br/> Next billing: <b>'.PaymodGetByBillingControl().'</b>');
		
		$paymods = array(
			'93'=>array('zombaio', 'rg', 'segpay'),
			'932'=>array('zombaio2'),
		);
		
		$formText = ($form=='93') ? "allin1 (form 93)" : "allin9 (form 932)";
		
		$this->render(
			'billingControll', 
			array(
					'bil'=>$bil, 
					'sales_done'=>$sales_done, 
					'nextBilling'=>$nextBilling,
					'isToday'=>$isToday,
					'date'=>$date,
					'prev'=>date("Y-m-d", strtotime("-1 day", strtotime($date))),
					'next'=>date("Y-m-d", strtotime("+1 day", strtotime($date))),
					'form'=>$form,
					'formText'=>$formText,
					'paymods'=>$paymods,
					)
				);
	}

	
	
	
	
	
	
	public function actionReversalForm()
	{
		$success = 0;
		$users = array();
		
		if (! isset( $_REQUEST['step2'] ) )
		{
			Yii::app()->session['userAndAffForReversals'] = array();
			
			if (isset($_GET['user'])  && $_GET['user']/*&& strlen($_GET['user'])>3*/)
			{
				$user = $_GET['user'];
				$sql = "SELECT id, affid FROM users WHERE ";
				if (is_numeric($user))
					$sql.= "id='{$user}'";
				else
					$sql.= "username='{$user}' OR email='{$user}'";
				$sql.= " LIMIT 1";
				$res = Yii::app()->db->createCommand($sql)->queryRow();
				if ($res) $users[] = array('id'=>$res['id'], 'affid'=>$res['affid']);
			}
			if ( isset($_GET['netbilling_member_id']) && $_GET['netbilling_member_id'])
			{
				$sql = "SELECT user_id, aff_id FROM `pm_nb_trn` WHERE member_id='".intval($_GET['netbilling_member_id'])."' LIMIT 1";
				$res = Yii::app()->db->createCommand($sql)->queryRow();
				if ($res) $users[] = array('id'=>$res['user_id'], 'affid'=>$res['aff_id']);
			}    
			if ( isset($_GET['netbilling_2_member_id']) && $_GET['netbilling_2_member_id'])
			{
				$sql = "SELECT user_id, aff_id FROM `pm_nb_2_trn` WHERE member_id='".intval($_GET['netbilling_2_member_id'])."' LIMIT 1";
				$res = Yii::app()->db->createCommand($sql)->queryRow();
				if ($res) $users[] = array('id'=>$res['user_id'], 'affid'=>$res['aff_id']);
			}
			if ( isset($_GET['zombaio_sub_id']) && $_GET['zombaio_sub_id'])
			{
				$sql = "SELECT user_id, affid FROM `pm_zombaio_postback` WHERE sub_id='".intval($_GET['zombaio_sub_id'])."' AND action='user.add' LIMIT 1";
				$res = Yii::app()->db->createCommand($sql)->queryRow();
				if ($res) $users[] = array('id'=>$res['user_id'], 'affid'=>$res['affid']);
			}

			if ( isset($_GET['vendo_trans_id']) && $_GET['vendo_trans_id'])
			{
				$sql = "SELECT user_id FROM `pm_vendo_postback` WHERE `trans_transid` =".intval($_GET['vendo_trans_id'])." LIMIT 1";
				$res = Yii::app()->db->createCommand($sql)->queryScalar();
				if ($res) 
				{
					$res['affid'] = Yii::app()->db->createCommand("SELECT affid FROM users WHERE id={$res['id']} LIMIT 1")->queryScalar();
					$users[] = array('id'=>$res['user_id'], 'affid'=>$res['affid']);
				}
			}
			
			if ( $users)
			{
				/*foreach($users as $k=>$u)
				{
				    $sql = "SELECT * FROM users WHERE id=".$u['id']." LIMIT 1";
				    $res = $db->getRow($sql);

				    if ($res)
				    {
				        $users[$k]['username'] = $res['username'];
				        $users[$k]['country'] = $res['country'];
				        $users[$k]['state_province'] = $res['state_province'];
				        $users[$k]['level'] = $res['level'];
				        $users[$k]['active'] = $res['active'];
				        $users[$k]['joined2'] = strtotime($res['joined']);//2011-08-08
				    }              
				}*/
				//CHelperSite::vd($users);    
				Yii::app()->session['userAndAffForReversals'] = $users;
				//$t->assign("users", $users);
			}
			
			
			//$t->assign("today", date("Y-m-d"));
		}
		else
		{
			$errors = array();
			
			if ( !isset(Yii::app()->session['userAndAffForReversals']) || empty(Yii::app()->session['userAndAffForReversals']) )
				$errors[] = 'error...';
			
			if (!$user_id = $_POST['user_id'][0])
				$errors[] = 'bad user';
			
			
			$date_real = $_POST['date_real'];
			if ( !strtotime($date_real) )
				$errors[] = 'bad date';
			else
				$date_real = date("Y-m-d", strtotime($date_real));
			
			if (!$type = $_POST['type'])
				$errors[] = 'select type';
			
			$amount = $_POST['amount'];
			if (!$amount || $amount<0 || !is_numeric($amount))
				$errors[] = 'enter right amount';
			

			if (!$errors)
			{
				foreach(Yii::app()->session['userAndAffForReversals'] as $v)
				{
					if ($v['id'] == $user_id)
					{
						$user = $v;
						break;
					}
				}

				//FB::error($user);
				
				if ($user)
				{
					//warn aff            
					$aff_banned = '0';

					/*i dont want any reversals to show up in that reversal column until sunday of each week
					[14:32:47] Chris: so all reversals that nat enters into the system, can they all show up on sunday of that same week for that weeks sales?
					[14:33:27] Chris: becuase i dont want chatters to see their reversals until the end of the week
					[14:33:29] Chris: and managers*/
					$date = date('Y-m-d', strtotime("Next Sunday", strtotime($date_real)));//$date = date('Y-m-d', strtotime("Next Monday", strtotime($date_real)));
					
					//save
					$sql = "INSERT INTO reversals (
				                aff_id,
				                user_id,
				                `date`,
				                `date_real`,
				                `type`,
				                `amount`,
				                `aff_banned`
				            ) VALUES (
				                {$user['affid']},
				                {$user_id},
				                '{$date}',
				                '{$date_real}',
				                '{$type}',
				                {$amount},
				                '{$aff_banned}'           
				            )";
					Yii::app()->dbSTATS->createCommand($sql)->execute();
					
					$reversalId = Yii::app()->db->lastInsertId; 

					$sql = "UPDATE pm_today_gold SET stats_reversals_id={$reversalId} WHERE user_id='{$user_id}'";
					Yii::app()->db->createCommand($sql)->execute();
					
					
					
					//clear aff cache
					$sql = "DELETE FROM daily_stats WHERE date='{$date}' AND affid={$user['affid']}";
					Yii::app()->dbSTATS->createCommand($sql)->execute();
					
					//make user free        
					$profile = new Profile($user_id);
					$mods = HelperReversals::getMods();
					$reason = (isset($mods[$type][2])) ? $mods[$type][2] : '';
					
					$profile->makeFree($reason);
					
					$success=1;//$t->assign("success", 1);
					
					//streamate part
					if ($profile->getDataValue('form')=='cams')
					{
						$cams = new Cams();
						$cams->setFraud($user_id);					
					}
					
					
				}
			}
			else
			{
				foreach($errors as $e)
					echo $e.'<br />';
				
				die();
			}
			
		}		
		
		
		
		
		
		
		
		$this->render(
			'reversalForm',
			array(
					'users'=>$users,
					'today'=>date("Y-m-d"),
					'success'=>$success,
					)
				);	
	}
	

	public function actionReversalReport()
	{
		$model = new PaymentReversalsForm;
		$model->attributes = (isset($_REQUEST['PaymentReversalsForm'])) ? $_REQUEST['PaymentReversalsForm'] : '';
		
		$page = (isset($_REQUEST['page'])) ? intval($_REQUEST['page']) : 0;
		$page = ($page) ? $page : 1;
		$page--;
		
		$res = array('count'=>0);
		if ($model->validate())
			$res = HelperReversals::getReversals($model->attributes, $page);
		
		$pages=new CPagination($res['count']);
		$pages->pageSize = $model->perPage;
		$pages->setCurrentPage($page);
		
		$this->render(
			'reversalReport',
			array(
				'model'=>$model,
				'res'=>$res,
				'pages'=>$pages,
			)
		);
	}
	
	public function actionReversalReportFix()
	{
		if (!isset($_POST['action'])) exit(); 
		
		$mods = HelperReversals::getMods();
		
		if ($_POST['action']=='changeReason')
		{
			FB::error($_POST);
		
			$newType = $_POST['newReason'];
		
			$sql = "UPDATE reversals SET `type`=:type WHERE id=:id LIMIT 1";
			FB::error($sql);
			$res = Yii::app()->dbSTATS->createCommand($sql)
						->bindValue(":id", $_POST['id'], PDO::PARAM_INT)
						->bindValue(":type", $newType, PDO::PARAM_STR)
						->execute();
			if ($res)
			{
				echo json_encode( array("success"=>"Yes", "newReasonText"=>$mods[$newType][0], "bgcolor"=>$mods[$newType][1]) );
			}
		
			Yii::app()->end();
		}
		
		
		if ($_POST['action']=='delete')
		{
			$sql = "DELETE FROM reversals WHERE id=:id LIMIT 1";
			FB::error($sql);
			$res = Yii::app()->dbSTATS->createCommand($sql)
						->bindValue(":id", $_POST['id'], PDO::PARAM_INT)
						->execute();
			if ($res)
			{
				echo json_encode(array("success"=>"Yes"));
			}
		
			Yii::app()->end();
		}		
		
	}
	
	
	
	public function actionTransactionsReport()
	{	
		$model = new PaymentTransactionsForm;
		$model->attributes = (isset($_REQUEST['PaymentTransactionsForm'])) ? $_REQUEST['PaymentTransactionsForm'] : '';
		
		$page = (isset($_REQUEST['page'])) ? intval($_REQUEST['page']) : 0;
		$page = ($page) ? $page : 1;
		$page--;        
		
		$rows = false;
		if ($model->validate())
			$rows = Payments::getTransactions($model->attributes, $page);
		
		$pages=new CPagination($rows['count']);
		$pages->pageSize = $model->perPage;
		$pages->setCurrentPage($page);
		
		$this->render(
			'transactions',
			array(
				'model'=>$model,
				'rows'=>$rows,
				'pages'=>$pages,
			)
		);
		
	}
	
	
	
	
	
	
	
	/**
	 * Report Refund and Chargeback
	 *
	 * @return mixed This is the return value description
	 *
	 */	
	public function actionRfdNChbkReport(){
		
		$model = new RefundNChargebackForm;
		$model->attributes = (isset($_REQUEST['RefundNChargebackForm'])) ? $_REQUEST['RefundNChargebackForm'] : '';
		
		//$page = (isset($_REQUEST['page'])) ? intval($_REQUEST['page']) : 0;
		//$page = ($page) ? $page : 1;
		//$page--;        
		
		$rows = false;
		if ($model->validate()){
			$items = Payments::getRfdNChbkItems($model->date1, $model->date2);
		}
		else{
			$items = array();
		}
		
		//$pages=new CPagination($rows['count']);
		//$pages->pageSize = $model->perPage;
		//$pages->setCurrentPage($page);
		
		$this->render(
			'refund',
			array(
					'model'=>$model,
					'items'=>$items,
					//'pages'=>$pages,
					)
				);		
	}
	

	/*
	 * FAKES for new MID
	 * skype 2013-03-27
	 */
	public function actionLastYear(){
		
		$trns = array(
			'2012-03' => array(71989.9, 64079.8, 3887.45, 2585.32),				
			'2012-04' => array(69552.95, 67435.6, 4451.39, 2465.79),
			'2012-05' => array(80059.8, 64639.1, 3922.93, 4196.27),
			'2012-06' => array(62601.65, 75385.65, 3443.09, 4415.59),
			'2012-07' => array(71390.65, 67835.1, 4711.78, 3619.87),
			'2012-08' => array(93522.95, 71350.7, 5349.51, 5440.83),
			'2012-09' => array(67555.45, 77343.2, 4181.68, 4202.06),
			'2012-10' => array(71390.65, 70631.6, 4219.19, 3550.56),
			'2012-11' => array(69513, 68793.9, 4101.27, 4010.9),		
		);
		$this->render(
			'lastyear',
			array(
				'trns'=>$trns,
			)
		);		
	}
	
	
}


