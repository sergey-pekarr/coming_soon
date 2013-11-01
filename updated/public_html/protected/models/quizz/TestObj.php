<?php

class ItemTypeDef
{
	static public $multiChoice = "MultiChoice";
	static public $html = "Html";
	static public $photo = "Photo";
	static public $pageBreak = "PageBreak";
	static public $shortAnswer = "ShortAnswer";
	static public $multiSelect = "MultiSelect";
}

class AnswerItem
{
	var $answerId = "";
	var $answer = "";
	var $pures = array();
	var $selected;
	
	public function __construct($array, $index){
		$this->answerId = CHelperQuizz::GetValueFromKey($array,'answerId',$index);
		$this->answer = CHelperQuizz::GetValueFromKey($array,'answer','');		
		$this->pures = CHelperQuizz::GetValueFromKey($array,'pures',array());	
		
	}
}

class QuestionItem
{
	var $questionId = "";
	var $itemType = "MultiChoice";
	var $question;
	var $answers;
	var $html;
	var $buttonNext;
	var $imagePath;
	public function __construct($array, $index)
	{
		$this->questionId = CHelperQuizz::GetValueFromKey($array,'questionId',$index);		
		$this->itemType = CHelperQuizz::GetValueFromKey($array,'itemType','');		
		$this->question = CHelperQuizz::GetValueFromKey($array,'question','');
		
		if($this->itemType == ItemTypeDef::$multiChoice){
			$this->answers = array();
			$ans = CHelperQuizz::GetValueFromKey($array,'answers', array());
			if(!is_array($ans)){
				$ans = array();
			}
			foreach($ans as $an){
				$this->answers[] = new AnswerItem($an, count($this->answers));
			}
		}
		
		$this->html = CHelperQuizz::GetValueFromKey($array,'html','');
		$this->buttonNext = CHelperQuizz::GetValueFromKey($array,'buttonNext','');
		$this->imagePath = str_replace("http://", "",  str_replace("https://", "",  str_replace($_SERVER["HTTP_HOST"] ,"", CHelperQuizz::GetValueFromKey($array,'imagePath',''))));
	}
}

class TestObj
{
	var $title;
	var $items = array();
	var $testId;
	var $totalPage;
	var $page;
	var $parseError = array();
	var $status = 'editing';
	var $testBasic;
	var $testScore;
	
	public function __construct($post){
		$this->title = CHelperQuizz::GetValueFromKey($post,'title','');
		
		$items = CHelperQuizz::GetValueFromKey($post,'items', array());
		$this->items = array();
		foreach($items as $item){
			$this->items[] = new QuestionItem($item, count($this->items));
		}
		
		//$this->testId = CHelperQuizz::GetValueFromKey($post,'testId',CreateUniqueId());
		$this->testId = CHelperQuizz::GetValueFromKey($post,'testId');	
		
		$this->totalPage = CHelperQuizz::GetValueFromKey($post,'totalPage',1);
		settype($this->totalPage, 'int');
		$this->page = CHelperQuizz::GetValueFromKey($post,'totalPage',1);
		settype($this->page, 'int');
		$this->parseError = CHelperQuizz::GetValueFromKey($post,'parseError',array());
		$this->status = CHelperQuizz::GetValueFromKey($post,'status','editing');
		if($this->status == "") $this->status = "editing";
		
		$this->testBasic = new TestBasic(CHelperQuizz::GetValueFromKey($post, 'testBasic'));
		$this->testScore = new TestScore(CHelperQuizz::GetValueFromKey($post, 'testScore'));
		
	}
}

class UserAnswer
{
	public function __construct($name,  $value)
	{
		$this->name = name;
		$this->value = value;
	}
	var $mame;
	var $value;
}

class StatsPoint
{
	public $x;
	public $y;
	public function __construct($x, $y){
		$this->x = $x;
		$this->y = $y;
	}
}

class ResultStats
{
	public $scoreName;
	public $percent;
	public $score;
    public $minX;
    public $maxX;
    public $samplePoints;
	public $scaling;
	
	public function __construct($scoreName, $score, $percent, $samplePoints, $minX, $maxX, $scaling){
		$this->scoreName = $scoreName;
		$this->percent = $percent;
		$this->score = $score;
		$this->samplePoints = $samplePoints;
		$this->minX = $minX;
		$this->maxX = $maxX;
		$this->scaling = $scaling;
	}
}

class TestResult
{
	var $detail;
	var $total;
	var $percentTotal;
	var $takenTime;
	var $chartDatas;
	var $scoreItemResults = array(); 
	
	public function __construct($detail, $total, $percentTotal, $time, $chartDatas = null){
		$this->detail = $detail;
		$this->total = $total;
		$this->percentTotal = $percentTotal;
		$this->takenTime = $time;
		$this->chartDatas = $chartDatas;
	}	
}

class ScoreItemResult
{
	var $title;
	var $subTitle;
	var $description;
	var $imageUrl;
	
	public function __construct($title,$subTitle,$description,$imageUrl){
		$this->title = $title;
		$this->subTitle = $subTitle;
		$this->description = $description;
		$this->imageUrl = $imageUrl;
	}
}

class TestBasic
{
	var $description = "";
	var $maturity = "Teen";
	var $category = "";
	var $subCategory = "";
	var $variables = array();
	var $thumnailUrl = "";
	
	public function __construct($array){
		$this->description = CHelperQuizz::GetValueFromKey($array, 'description');
		$this->maturity = CHelperQuizz::GetValueFromKey($array, 'maturity');
		$this->category = CHelperQuizz::GetValueFromKey($array, 'category');
		$this->subCategory = CHelperQuizz::GetValueFromKey($array, 'subCategory');
		$this->thumnailUrl = str_replace("http://", "", str_replace("https://", "", str_replace($_SERVER["HTTP_HOST"] ,"",CHelperQuizz::GetValueFromKey($array, 'thumnailUrl'))));
		$this->variables = CHelperQuizz::GetValueFromKey($array, 'variables');
	}
}

class TestScore
{
	var $scaling = "Raw"; //"Percentages"
	var $scoreItems = array();
	
	public function __construct($array){
		$this->scaling = CHelperQuizz::GetValueFromKey($array,'scaling');
		foreach(CHelperQuizz::GetValueFromKey($array,'scoreItems',array()) as $item){
			$this->scoreItems[] = new ScoreItem($item);
		}
	}
}

class ScoreItem
{
	var $requirements = array();
	var $title;
	var $subTitle;
	var $description;
	var $imageUrl;
	
	public function __construct($array){
		$this->title = CHelperQuizz::GetValueFromKey($array,'title');
		$this->subTitle = CHelperQuizz::GetValueFromKey($array,'subTitle');
		$this->description = CHelperQuizz::GetValueFromKey($array,'description');
		$this->imageUrl = str_replace("http://", "",  str_replace("https://", "",  str_replace($_SERVER["HTTP_HOST"] ,"",CHelperQuizz::GetValueFromKey($array,'imageUrl'))));
		
		foreach(CHelperQuizz::GetValueFromKey($array,'requirements',array()) as $req){
			$this->requirements[] = new Requirement($req);
		}
	}
}

class Requirement
{
	static $GreaterThanValue = "GreaterThanValue";
	static $LessThanValue = "LessThanValue";
	static $GreaterThanVar = "GreaterThanVar";
	static $LessThanVar = "LessThanVar";
	static $GreatestVar = "GreatestVar";
	static $LeastVar = "LeastVar";

	var $variable;
	var $condition;
	var $value;
	var $objVariable;
	
	public function __construct($array){
		$this->variable = CHelperQuizz::GetValueFromKey($array,'variable');
		$this->condition = CHelperQuizz::GetValueFromKey($array,'condition');
		$this->value = CHelperQuizz::GetValueFromKey($array,'value');
		$this->objVariable = CHelperQuizz::GetValueFromKey($array,'objVariable');
	}
}
