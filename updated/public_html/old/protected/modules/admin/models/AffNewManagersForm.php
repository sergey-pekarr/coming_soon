<?php


class AffNewManagersForm extends CFormModel
{
    public $date1;
    public $date2;
	
	
        
    public $perPage;
    public $sort;
    
    
    public function init()
    {
        parent::init();
        
        $this->perPage = 10;
        $this->sort = "idDESC";
        
        
        $this->date1=date("Y-m-d");
        $this->date2=date("Y-m-d");

    }

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
            array('date1, date2', 'date', 'allowEmpty'=>'false', 'format'=>'yyyy-MM-dd'),
            
            array('perPage', 'in', 'range'=>array('10','50','100','200','500','1000') ),
            array('sort', 'in', 'range'=>array('idASC','idDESC') ),
		);
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
   
}
