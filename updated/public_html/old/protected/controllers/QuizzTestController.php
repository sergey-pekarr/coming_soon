<?php

class QuizztestController extends Controller
{

	public function init()
	{
		parent::init();
		$this->layout='//layouts/ajax';
	}
	
	public function actionUpdateTest(){
		$finish = CHelperQuizz::GetValueFromKey($_GET, 'finish', false);		
		
		$userid = Yii::app()->user->id;

		$test = new TestObj($_POST);
		$testid = $test->testId;
		$status = ($test->status == 'completed'?'completed':'editing');

		if($testid == null || $testid == ''){
			$testid = -1;
			$test->testId = $testid;
		}

		$desc = '';
		if($test->testBasic->description != null){
			$desc = $test->testBasic->description;
		}
		$category = '';
		if($test->testBasic->category != null){
			$category = $test->testBasic->category;
		}
		$subcategory = '';
		if($test->testBasic->subCategory != null){
			$subcategory = $test->testBasic->subCategory;
		}
		$maturity = '';
		if($test->testBasic->maturity != null){
			$maturity = $test->testBasic->maturity;
		}
		
		//if($desc == '' && count($test->items)>0 && $test->items[0]->itemType == ItemTypeDef::$html){
		//	$desc = addslashes($test->items[0]->html);
		//}
		$title = addslashes($test->title);
		
		$db = Yii::app()->dbquizz;
		$qry = "select * from test where id = '$testid'";
		$row = $db->createCommand($qry)->queryRow();
		
		if(!$row){
			$qry = "insert into test(author, name, status, description, category, subcategory, maturity, createdate, regdate, data) 
					values($userid, '$title', '$status', :desc, :category, :subcategory, :maturity , now(), now(), :data)";
			$insertrow = $db->createCommand($qry)
				->bindValue(":data", serialize($test), PDO::PARAM_STR)
				->bindValue(":desc", $desc, PDO::PARAM_STR)
				->bindValue(":category", $category, PDO::PARAM_STR)
				->bindValue(":subcategory", $subcategory, PDO::PARAM_STR)
				->bindValue(":maturity", $maturity, PDO::PARAM_STR)
				->execute();
			$testid = $db->getLastInsertID();
			$test->testId = $testid;			
		}
		else {
			$oldtest = unserialize($row['data']);
			
			$qry = "update test set status = '$status', name = '$title', 
					description = :desc, category = :category, subcategory = :subcategory, 
					maturity = :maturity, data = :data, regdate = now() 
					where id = $testid";
			$updaterow = $db->createCommand($qry)
				->bindValue(":data", serialize($test), PDO::PARAM_STR)
				->bindValue(":desc", $desc, PDO::PARAM_STR)
				->bindValue(":category", $category, PDO::PARAM_STR)
				->bindValue(":subcategory", $subcategory, PDO::PARAM_STR)
				->bindValue(":maturity", $maturity, PDO::PARAM_STR)
				->execute();
			
			$this->processTestChanged($testid, $oldtest, $test);
			
		}
		//else if($row['status'] != 'completed'){
		//	
		//	$qry = "update test set status = '$status', name = '$title', description = '$desc', data = :data , regdate = now() where id = '$testid'";
		//	$updaterow = $db->createCommand($qry)
		//		->bindValue(":data", serialize($test), PDO::PARAM_STR)
		//		->execute();
		//	
		//} else{
		//	$qry = "select * from test where id = $testid";
		//	$testRow = $db->createCommand($qry)->queryRow();
		//	$test = null;
		//	if($testRow){
		//		$test = unserialize($testRow['data']);
		//		$test->testId = $testRow['id'];
		//	}
		//	if($test == null) {
		//		$test = new Test();
		//		$test->status = "completed";
		//	}
		//}
		echo CJavaScript::jsonEncode($test);
		
		//try{
		//	$this->removeUnusedImages($test, $userid);
		//}
		//catch(exception $ex){
		//}
		
		Yii::app()->end();
	}
	
	public function actionLoad(){
		$id = $_GET['id'];
		$id = intval($id);
		$db = Yii::app()->dbquizz;
		$qry = "select * from test where id = $id";
		$testObj = $db->createCommand($qry)->queryRow();
		$test = unserialize($testObj['data']);
		$test->testId = $testObj['id'];
		$test->status = $testObj['status'];
		echo CJavaScript::jsonEncode($test);
		Yii::app()->end();		
	}
	
	
	private function processTestChanged($testid, $oldTest, $newTest){
		
		//As research in Hello Quizz, when user change variables (change name, new variable, or delete variable) => cause reset statistic
		//Other change dont cause reset statistic -> It is not fair to taken test before change, but acceptable.
		//Correct solution must be: Any change on question, answer, answer score -> need to re-process for all taken test.
		//	Milion of taken test might cause long time to process: Asume few seconds
		
		//Solution as HelloQuizz:
		
		$variablesChanged = false;
		$variablesChanged = ($oldTest == null || $newTest == null || $oldTest->testBasic == null || $newTest->testBasic == null
			|| $oldTest->testBasic->variables == null || $newTest->testBasic->variables == null
			|| count($oldTest->testBasic->variables) != count($newTest->testBasic->variables));
		
		if(!$variablesChanged){
			for($i=0;$i<count($oldTest->testBasic->variables);$i++){
				$variablesChanged |= $oldTest->testBasic->variables[$i] != $newTest->testBasic->variables[$i];
			}
		}
		
		if($variablesChanged){
			$qry = "delete from test_result_stats where test_id = $testid";
			$db = Yii::app()->dbquizz;
			$ok = $db->createCommand($qry)->execute();
		}
		
	}
	
	/**
	 * Create testObj include description and question (Do not send Score, Basic...)
	 *
	 * @return mixed This is the return value description
	 *
	 */
	public function actionTakeTest(){
		$testid = CHelperQuizz::GetValueFromKey($_GET, 'id', 0);
		$testid = intval($testid);

		$userid =Yii::app()->user->id;
		
		//$qry = "select * from taken where test_id=$testid and user_id=$userid";
		//$testObj = Yii::app()->dbquizz->createCommand($qry)->queryRow();
		//if($testObj){
		//	$test = unserialize($testObj['data']);
		//	echo CJavaScript::jsonEncode($test);
		//	Yii::app()->end();	
		//}
		
		//Allow retest
		$mkey = "retest_{$userid}_{$testid}";
		if(!isset(Yii::app()->session[$mkey]) || Yii::app()->session[$mkey]!== true){
			$this->CheckHasBeenTested($testid, $userid);
		}

		//$qry = "select * from test where id = '$testid' and author <> $userid";
		
		//Allow author preview test
		$qry = "select * from test where id = '$testid'";
		
		//Note: QuizzController.test also check author. Double check to prevent hacking
		$testObj = Yii::app()->dbquizz->createCommand($qry)->queryRow();
		if($testObj){
			$test = unserialize($testObj['data']);
			$test->testId = $testObj['id'];
			
			$test->testScore = null;
			
			//Preview
			if($testObj['author'] == $userid){
				$test->title = "Preview: ".$test->title;
			}
			
			echo CJavaScript::jsonEncode($test);
			Yii::app()->end();
		}
	}
	
	private function GetPurseAndSetSelection($test, $questionid, $value){
		$result = array();
		foreach($test->items as $item){
			if($item->questionId == $questionid){
				if($item->itemType = "MultiChoice"){
					foreach($item->answers as $ans){
						if($ans->answerId == $value){
							$ans->selected = true;
							$result[] = $ans->pures;
						} else{
							$ans->selected = false;
						}
						
					}
				}
				break;
			}		
		}
		if(count($result)==1) return $result[0];
		return $result;
	}

	private function CheckRequirements($total, $totalpercent, $scaling, $requirements, $minValue, $maxValue){
		foreach($requirements as $req){
			if($req->variable == null || $req->condition == null) continue;
			switch($req->condition){
				case 'GreaterThanValue':
					if($scaling == 'Raw' && $total[$req->variable] <= $req->value){
						return false;
					}
					if($scaling == 'Percentage' && $totalpercent[$req->variable] <= $req->value){
						return false;
					}
					break;
				case 'LessThanValue':
					if($scaling == 'Raw' && $total[$req->variable] >= $total[$req->value]){
						return false;
					}
					if($scaling == 'Percentage' && $totalpercent[$req->variable] >= $req->value){
						return false;
					}
					break;
				case 'GreaterThanVar':
					if($total[$req->variable] <= $total[$req->objVariable]){
						return false;
					}
					break;
				case 'LessThanVar':
					if($total[$req->variable] >= $total[$req->objVariable]){
						return false;
					}
					break;
				case 'GreatestVar':
					if($total[$req->variable] != $maxValue) return false;
					break;
				case 'LeastVar':
					if($total[$req->variable] != $minValue) return false;				
					break;
				default:
					return false;
					break;
			}
		}
		return true;
	}

	private function CheckRequirements2($total, $totalpercent, $scaling, $requirements, $minMaxMap){
		foreach($requirements as $req){
			if($req->variable == null || $req->condition == null) continue;
			switch($req->condition){
				case 'GreaterThanValue':
					if($scaling == 'Raw' && $total[$req->variable] <= $req->value){
						return false;
					}
					if($scaling == 'Percentage' && $totalpercent[$req->variable] * 100 <= $req->value){
						return false;
					}
					break;
				case 'LessThanValue':
					if($scaling == 'Raw' && $total[$req->variable] >= $total[$req->value]){
						return false;
					}
					if($scaling == 'Percentage' && $totalpercent[$req->variable] * 100 >= $req->value){
						return false;
					}
					break;
				case 'GreaterThanVar':
					if($total[$req->variable] <= $total[$req->objVariable]){
						return false;
					}
					break;
				case 'LessThanVar':
					if($total[$req->variable] >= $total[$req->objVariable]){
						return false;
					}
					break;
				case 'GreatestVar':
					if($total[$req->variable] < $minMaxMap[$req->variable]['max']) return false;
					break;
				case 'LeastVar':
					if($total[$req->variable] > $minMaxMap[$req->variable]['min']) return false;				
					break;
				default:
					return false;
					break;
			}
		}
		return true;
	}
	
	/**
	 * This method will be replaced by ReplaceVariable2
	 *
	 * @param mixed $total This is a description
	 * @param mixed $text This is a description
	 * @param mixed $scaling This is a description
	 * @return mixed This is the return value description
	 *
	 */	
	private function ReplaceVariable($total, $text, $scaling){
		if($scaling == 'Percentage'){
			foreach($total as $key => $value){
				$text = str_replace("$($key)",round($value[1]*100,0),$text);
			}
		}
		else{
			foreach($total as $key => $value){
				$text = str_replace("$($key)",$value,$text);
			}
		}
		return $text;
	}
	
	private function ReplaceVariable2($total, $text, $scaling){
		if($scaling == 'Percentage'){
			foreach($total as $key => $value){
				$text = str_replace("$($key)",round($value*100,0),$text);
			}
		}
		else{
			foreach($total as $key => $value){
				$text = str_replace("$($key)",$value,$text);
			}
		}
		return $text;
	}
	
	private function CheckHasBeenTested($testid, $userid){
		$qry = "select * 
				from taken 
				where test_id = $testid and user_id = $userid
				order by id desc
				limit 0,1";
		$db = Yii::app()->dbquizz;
		$testResultObj = $db->createCommand($qry)->queryRow();
		if($testResultObj){
			$obj = unserialize($testResultObj['data']);
			$this->ReturnTestResult2(
				CHelperQuizz::GetValueFromKey($obj, 'test', array()), 
				CHelperQuizz::GetValueFromKey($obj, 'total', array()));
		}
	}
	
	private function ParseSubmitResult($test, &$result, &$total, &$answers = null){
		$result = array();
		$total = array();
		$answers = array();
		foreach($_POST as $key => $value){
			$list = explode('_',$key);
			if(count($list)==2){
				$questionid = $list[1];
				$pures = $this->GetPurseAndSetSelection($test, $questionid, $value);
				
				//n Aug21:
				$answers[$questionid] = $value;
				
				if($pures == null) $pures = array();
				$detail = array('questionId'=>$questionid);
				foreach($pures  as $pur => $purvalue){
					if(!isset($total[$pur])){
						$total[$pur] = $purvalue;
					} else {
						$total[$pur] += $purvalue;					
					}
					$detail[$pur] = $value;			
				}
				$result[] = $detail;
			}
		}
	}
	
	private function SaveTestResult($test, $userid, $result, $total, $answers = ''){
		$testid = $test->testId;
		$points = array();
		foreach($total as $point){
			$points[] = $point;
		}
		
		//Note: We only support maximum 10 score: point1 -> point10
		for($i=count($points);$i<10;$i++){
			$points[] = 'null';	
		}
		
		$data = serialize(array('result' => $result, 'total' => $total, 'test' => $test));
		
		$db = Yii::app()->dbquizz;
		
		$qry = "insert into taken(test_id, user_id, scoredata, answers,
						regdate, data)
				values($testid, $userid, :result, :answers,
						now(), :data)";
		$ok = $db->createCommand($qry)
			->bindValue(":data", $data, PDO::PARAM_STR)
			->bindValue(":result", serialize($result), PDO::PARAM_STR)
			->bindValue(":answers", serialize($answers), PDO::PARAM_STR)
			->execute();
		
		//CHelperQuizz::UpdateTestTaken($testid);
		
		//Calculate value: start, taken, daytaken....
		//		$yesterday = date('Y-m-d H:i:s' ,time() - 3600*24);
		//		$ok = $updateqry = "Update test 
		//					  set taken = (select count(*) from taken where test_id=$testid),
		//						  last_date_taken = (select count(*) from taken where test_id=$testid and regdate >= '$yesterday')
		//					  where id = $testid";

		$ok = $updateqry = "Update test 
						set taken = taken + 1
						where id = $testid";
		$db->createCommand($updateqry)->Execute();
	}
	
	private function SaveStatistic($testid, $total){
		$db = Yii::app()->dbquizz;
		foreach($total as $key => $value){
			$insqry = "insert into test_result_stats(test_id, score, scorevalue, nouser) 
													values($testid, '$key', $value, 1)
						on duplicate key update nouser = nouser + 1";
			$ins = $db->createCommand($insqry)->execute();
		}
	}
	
	/**
	 * This method is not correct. Will be replace by ReturnTestResult
	 *
	 * @param mixed $test This is a description
	 * @param mixed $result This is a description
	 * @param mixed $total This is a description
	 * @return mixed This is the return value description
	 *
	 */	
	private function ReturnTestResult($test, $result, $total){
		$testid = $test->testId;
		$db = Yii::app()->dbquizz;
		
		$minValue = null; //Note: Some variables might have same value => will have the same role
		$maxValue = null;	
		foreach($total as $key => $value){
			if($minValue == null || $minValue > $value) $minValue = $value;
			if($maxValue == null || $maxValue < $value) $maxValue = $value;
		}
		
		$totalpercent = array();
		foreach($total as $key => $value){			
			$perqry = "select sum(case when scorevalue > $value then nouser else 0 end) as greater, 
							sum(case when scorevalue < $value then nouser else 0 end) as lesser, 
							sum(case when scorevalue = $value then nouser else 0 end) as equal 
					from test_result_stats where test_id = $testid and score = '$key'";
			$stats = $db->createCommand($perqry)->queryRow();
			
			if(!$stats) $stats = array('greater'=>0,'lesser'=>0,'equal'=>0);
			$sum = $stats['greater'] +$stats['equal'] +$stats['lesser'] ;
			if($sum==0) $sum = 1;	
			
			$totalpercent[$key] = array(($stats['equal'] +$stats['lesser'])/$sum, ($stats['greater'] +$stats['equal'])/$sum);
		}
		
		if($test->testScore != null && $test->testScore->scoreItems != null){
			$scoreItems = $test->testScore->scoreItems;
			$scaling = $test->testScore->scaling;
		} else {
			$scoreItems = array();
			$scaling = "Percentage";
		}
		
		$test->testScore = null;
		$test->testBasic = null;
		$testresult = new TestResult($test, $total, $totalpercent, date('M d, Y',time()));
		
		foreach($scoreItems as $item){
			if($item->requirements == null || count($item->requirements) == 0){			
				$testresult->scoreItemResults[] = new ScoreItemResult(
					$this->ReplaceVariable($scaling =="Percentage"?$totalpercent:$total , $item->title, $scaling),
					$this->ReplaceVariable($scaling =="Percentage"?$totalpercent:$total , $item->subTitle, $scaling), 
					$this->ReplaceVariable($scaling =="Percentage"?$totalpercent:$total , $item->description, $scaling),
					$item->imageUrl);
			} else if($this->CheckRequirements($total, $totalpercent, $scaling, $item->requirements, $minValue, $maxValue)){
				$testresult->scoreItemResults[] = new ScoreItemResult(
					$this->ReplaceVariable($scaling =="Percentage"?$totalpercent:$total , $item->title, $scaling),
					$this->ReplaceVariable($scaling =="Percentage"?$totalpercent:$total , $item->subTitle, $scaling), 
					$this->ReplaceVariable($scaling =="Percentage"?$totalpercent:$total , $item->description, $scaling),
					$item->imageUrl);
				break; //Display first match resulttype
			}			
		}
		
		//n Aug 21st: Do not need to render answer detail
		$testresult->detail->items = null;
		
		echo CJavaScript::jsonEncode($testresult);
		Yii::app()->end();
	}

	private function GetChartDatas($testid, $total, $totalPercent, $minMaxMap, $scaling){
		$qry = "select * from test_result_stats where test_id = $testid";
		$db = Yii::app()->dbquizz;
		$rows = $db->createCommand($qry)->queryAll();
		
		function findScoreItem($rows, $score){
			$data = array();
			foreach($rows as $row){
				if($row['score'] == $score){
					$data[] = new StatsPoint(intval($row['scorevalue']), intval($row['nouser']));
				}
			}
			return $data;
		}
		
		$chartDatas = array();
		foreach($total as $key => $value){
			$minX = intval($minMaxMap[$key]['min']);
			$maxX = intval($minMaxMap[$key]['max']);
			$percent = floatval($totalPercent[$key]); 
			$chartDatas[] = new ResultStats($key, $value, $percent, findScoreItem($rows, $key), $minX, $maxX, $scaling);
			//$chartDatas[] = new ResultStats($key, -1, 0.1, findScoreItem($rows, $key), $minX, $maxX, $scaling);
		}
		return $chartDatas;
	}
	
	private function FindMinMaxMap($test){
		$minMaxMap = array();
		
		//Find maxValue and minValue for each variable. 
		//maxValue($var) = sumAllQuestion(max($var of all answer))
		foreach($test->items as $qItem){
			$qMinMax = array();
			if($qItem->itemType != ItemTypeDef::$multiChoice){
				continue;
			}
			foreach($qItem->answers as $answer){
				foreach($answer->pures as $pure => $value){
					if(!isset($qMinMax[$pure])){
						$qMinMax[$pure] = array('min' => $value, 'max' => $value);
					}
					else{
						if($qMinMax[$pure]['min'] > $value){
							$qMinMax[$pure]['min'] = $value;
						}
						if($qMinMax[$pure]['max'] < $value){
							$qMinMax[$pure]['max'] = $value;
						}								
					}
				}
			}
			
			foreach($qMinMax as $pure=>$qValue){
				if(!isset($minMaxMap[$pure])){
					$minMaxMap[$pure] = $qValue;
				}
				else{
					//Note: User can chose only one answer
					$minMaxMap[$pure]['min'] += $qValue['min'];
					$minMaxMap[$pure]['max'] += $qValue['max'];
				}
			}
		}
		return $minMaxMap;
	}
	
	private function ReturnTestResult2($test, $total){
		$testid = $test->testId;
		
		/*
		$minMaxMap = array();
				
		//Find maxValue and minValue for each variable. 
		//maxValue($var) = sumAllQuestion(max($var of all answer))
		foreach($test->items as $qItem){
			$qMinMax = array();
			if($qItem->itemType != ItemTypeDef::$multiChoice){
				continue;
			}
			foreach($qItem->answers as $answer){
				foreach($answer->pures as $pure => $value){
					if(!isset($qMinMax[$pure])){
						$qMinMax[$pure] = array('min' => $value, 'max' => $value);
					}
					else{
						if($qMinMax[$pure]['min'] > $value){
							$qMinMax[$pure]['min'] = $value;
						}
						if($qMinMax[$pure]['max'] < $value){
							$qMinMax[$pure]['max'] = $value;
						}								
					}
				}
			}
			
			foreach($qMinMax as $pure=>$qValue){
				if(!isset($minMaxMap[$pure])){
					$minMaxMap[$pure] = $qValue;
				}
				else{
					//Note: User can chose only one answer
					$minMaxMap[$pure]['min'] += $qValue['min'];
					$minMaxMap[$pure]['max'] += $qValue['max'];
				}
			}
		}
		*/
		$minMaxMap = $this->FindMinMaxMap($test);
		
		//Correct missing variables
		foreach($test->testBasic->variables as $var){
			if(!isset($minMaxMap[$var])){
				$minMaxMap[$var] = array('min' => 0, 'max' => 0);
			}	
			
			//When user does not select any answer has $var => set default value is 0
			if(!isset($total)) $total[$var] = 0;
		}
		
		foreach($total as $var => $value){
			if(!in_array($var, $test->testBasic->variables)){
				unset($total[$var]);
			}
		}
		
		foreach($minMaxMap as $var => $value){
			if(!in_array($var, $test->testBasic->variables)){
				unset($minMaxMap[$var]);
			}
		}
		
		$totalPercent = array();
		foreach($test->testBasic->variables as $var){
			$max = $minMaxMap[$var]['max'];
			$min = $minMaxMap[$var]['min'];
			if($max -$min == 0){
				$totalPercent[$var] = 0;
			}
			else{
				$totalPercent[$var] = ($total[$var] - $min)/($max - $min);
			}
		}
		
		if($test->testScore != null && $test->testScore->scoreItems != null){
			$scoreItems = $test->testScore->scoreItems;
			$scaling = $test->testScore->scaling;
		} else {
			$scoreItems = array();
			$scaling = "Percentage";
		}
		
		$test->testScore = null;
		$test->testBasic = null;
		$testresult = new TestResult($test, $total, $totalPercent, date('M d, Y',time()), $this->GetChartDatas($testid, $total, $totalPercent, $minMaxMap, $scaling));
		
		foreach($scoreItems as $item){
			if($item->requirements == null || count($item->requirements) == 0){			
				$testresult->scoreItemResults[] = new ScoreItemResult(
					$this->ReplaceVariable2($scaling =="Percentage"?$totalPercent:$total , $item->title, $scaling),
					$this->ReplaceVariable2($scaling =="Percentage"?$totalPercent:$total , $item->subTitle, $scaling), 
					$this->ReplaceVariable2($scaling =="Percentage"?$totalPercent:$total , $item->description, $scaling),
					$item->imageUrl);
			} else if($this->CheckRequirements2($total, $totalPercent, $scaling, $item->requirements, $minMaxMap)){
				$testresult->scoreItemResults[] = new ScoreItemResult(
					$this->ReplaceVariable2($scaling =="Percentage"?$totalPercent:$total , $item->title, $scaling),
					$this->ReplaceVariable2($scaling =="Percentage"?$totalPercent:$total , $item->subTitle, $scaling), 
					$this->ReplaceVariable2($scaling =="Percentage"?$totalPercent:$total , $item->description, $scaling),
					$item->imageUrl);
				break; //Display first match resulttype
			}			
		}
		
		//n Aug 21st: Do not need to render answer detail
		$testresult->detail->items = null;
		
		//n Aug 28: Update test result in database.
		// => When original test is changed, and user view test result again -> update
		$testResultTitle = $testresult->scoreItemResults[0]->title;
		
		$db = Yii::app()->dbquizz;
		$userid =Yii::app()->user->id;
		$qry = "select id from taken where  test_id = $testid and user_id = $userid order by id desc limit 0,1";
		$id = $db->createCommand($qry)->queryScalar();
		
		if($id){			
			$qry = "Update taken set result = :result where id = $id";
			$ok = $db->createCommand($qry)
				->bindValue(":result", $testResultTitle, PDO::PARAM_STR)
				->execute();
		}
		
		echo CJavaScript::jsonEncode($testresult);
		Yii::app()->end();
	}
	
	private function CheckPreviewSubmit($testid){
		$userid =Yii::app()->user->id;
		$db = Yii::app()->dbquizz;
		
		$qry = "select * from test where author = $userid and id = $testid"; 
		$row = $db->createCommand($qry)->queryRow();
		
		if($row){
			$qry = "select * from test where id = '$testid'";
			$testObj = $db->createCommand($qry)->queryRow();

			$test = unserialize($testObj['data']);
			
			$test->status = 'tested';
			
			$test->title = "Preview: " . $test->title;
			
			$this->ParseSubmitResult($test, $result, $total, $answers);
			
			$this->ReturnTestResult2($test, $total);
			
			Yii::app()->end();
		}
	}
	
	/**
	 * If use has taken the test -> Return old result with new evaluation
	 * Store in taken: current test, result, total score map
	 * if test is changed -> we will evaluate based on store test data
	 *
	 * @return mixed This is the return value description
	 *
	 */
	public function actionSubmit(){
		$testid = CHelperQuizz::GetValueFromKey($_GET, 'id', 0);
		$testid = intval($testid);

		$userid =Yii::app()->user->id;
		
		$this->CheckPreviewSubmit($testid);
		
		$mkey = "retest_{$userid}_{$testid}";
		
		if(!isset(Yii::app()->session[$mkey]) || Yii::app()->session[$mkey]!== true){
			$this->CheckHasBeenTested($testid, $userid);
		}
		
		$db = Yii::app()->dbquizz;
		
		$qry = "select * from test where id = '$testid'";
		$testObj = $db->createCommand($qry)->queryRow();
		if(!$testObj){
			die('The test does not exist anymore on our system');
		}

		$test = unserialize($testObj['data']);
		$test->status = 'tested';
		
		$this->ParseSubmitResult($test, $result, $total, $answers);
		
		$this->SaveTestResult($test, $userid, $result, $total, $answers);
		
		$this->SaveStatistic($testid, $total);		
		
		//Turnoff retest
		Yii::app()->session[$mkey] = null;
		
		$this->ReturnTestResult2($test, $total);
	}
	
	public function actionLoadResult(){
	}
	
	private function removeUnusedImages($test, $userid){
		$imgs = array();
		if($test->testBasic != null && $test->testBasic->thumnailUrl != null){
			$imgs[] = $test->testBasic->thumnailUrl;
		}
		if($test->items != null){
			foreach($test->items as $item){
				if($item->itemType == ItemTypeDef::$photo && $item->imagePath != null){
					$imgs[] = $item->imagePath;
				}
			}
		}
		if($test->testScore != null && $test->testScore->scoreItems != null){
			foreach($test->testScore->scoreItems as $scoreItem){
				if($scoreItem->imageUrl != null){
					$imgs[] = $scoreItem->imageUrl;
				}
			}
		}
		
		foreach($imgs as &$img){
			try{
				$img = basename($img);
			}
			catch(exception $ex){
				$img = null;
			}
		}
		
		$dir = Yii::app()->helperProfile->getUserQuizzImgDir($userid);
		$files = scandir($dir);
		for($i = count($files) - 1;$i>=0; $i--){
			$file = $files[$i];
			
			if(in_array($file, $imgs) || $file == '.' || $file == '..'){
				unset($files[$i]);
			}						
		}
		
		foreach($files as $file){
			unlink("$dir/$file");
		}
	}
	
	public function actionRate(){
		$testid = CHelperQuizz::GetValueFromKey($_GET, 'id', 0);
		$testid = intval($testid);
		$userid =Yii::app()->user->id;
		
		$mkey = "quizz_rate_{$userid}_{$testid}";
		$rate = isset(Yii::app()->session[$mkey])?Yii::app()->session[$mkey]:null;
		
		//User can only rate onetime for a session
		if($rate != null || !isset($_GET['rate'])) return;
		
		$rate = $_GET['rate'];
		Yii::app()->session[$mkey] = $rate;
		
		$db = Yii::app()->dbquizz;
		$qry = "update test 
				set star = (star*norate + $rate)/(norate + 1), norate = norate + 1
				where id = $testid";
		$ok = $db->createCommand($qry)->execute();
		
		echo CJavaScript::jsonEncode(array('success'=>$ok));
		
	}
}