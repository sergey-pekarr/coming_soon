<?php


class LogsEmailsForm extends CFormModel
{
    public $perPage;
    public $sort;
    
    public $date1;
    public $date2;
    
    public $template;
    public $templates;
    public $templatesTitle;
    
    public $showAll;//shows empty/deleted too
    
    public function init()
    {
        parent::init();
        
        $this->perPage = 100;
        $this->sort = "idDESC";
        
        $this->date1=date("Y-m-d");
        $this->date2=date("Y-m-d");
        
        $this->templates = array(
        	'all', 
        	'welcome',
        	'verifyemail',
        	'passwordReset',        	
        	'email', 
        	'messages',
        	'photorequest', 
        	'autoemail', 
        	'automessages',
        	'autophotorequest', 
        	'declineimage'
        );
        $this->templatesTitle = array(
        	''=>'all', 
        	'welcome'=>'welcome',
        	'verifyemail'=>'verifyemail',
        	'passwordReset'=>'passwordReset',
        	'email'=>'email', 
        	'messages'=>'messages',
        	'photorequest'=>'photorequest', 
        	'autoemail'=>'autoemail', 
        	'automessages'=>'automessages',
        	'autophotorequest'=>'autophotorequest', 
        	'declineimage'=>'declineimage'
        );
        
        $this->showAll = 1;
        
    }

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
            array('perPage', 'in', 'range'=>array('10','50','100','200','500','1000') ),
            array('sort', 'in', 'range'=>array('idASC','idDESC') ),
            array('showAll', 'in', 'range'=>array(0,1) ),
            array('date1, date2', 'date', 'allowEmpty'=>'false', 'format'=>'yyyy-MM-dd'),
            array('template', 'in', 'range'=>$this->templates ),
		);
	}
    
	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
            'perPage'   =>'Per Page',
            'sort'      =>'Sort By',
            'date1'     => 'From',
            'date2'     => 'To',
		);
	}

}
