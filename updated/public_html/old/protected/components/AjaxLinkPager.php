<?php

class AjaxLinkPager extends CLinkPager
{
    public $panel;
    public $all;
    
    
	/**
	 * @var integer maximum number of page buttons that can be displayed. Defaults to 10.
	 */
	public $maxButtonCount=5;
    
	/**
	 * @var string the text shown before page buttons. Defaults to 'Go to page: '.
	 */
	public $header='';

	/**
	 * @var string the text label for the next page button. Defaults to 'Next &gt;'.
	 */
	public $nextPageLabel = '>';
	/**
	 * @var string the text label for the previous page button. Defaults to '&lt; Previous'.
	 */
	public $prevPageLabel = '<';
	/**
	 * @var string the text label for the first page button. Defaults to '&lt;&lt; First'.
	 */
	public $firstPageLabel = '<<';
	/**
	 * @var string the text label for the last page button. Defaults to 'Last &gt;&gt;'.
	 */
	public $lastPageLabel = '>>';    



	/**
	 * Initializes the pager by setting some default property values.
	 */
	public function init()
	{
		if(!isset($this->htmlOptions['class']))
			$this->htmlOptions['class']='pagination';

        parent::init();
	}

    
        
    /**
     * Creates a page button.
     * You may override this method to customize the page buttons.
     * @param string the text label for the button
     * @param integer the page number
     * @param string the CSS class for the page button. This could be 'page', 'first', 'last', 'next' or 'previous'.
     * @param boolean whether this page button is visible
     * @param boolean whether this page button is selected
     * @return string the generated button
     */
    protected function createPageButton($label,$page,$class,$hidden,$selected)
    {
        if ($class=='page')
            $pageClass = 'p'.$page;
        else
        	$pageClass = "";
        
        if($hidden || $selected)
            $class.=' '.($hidden ? self::CSS_HIDDEN_PAGE : self::CSS_SELECTED_PAGE);

        $link = CHtml::link($label,'#',array(
                'onclick'=>"loadPanel('".$this->panel."',".$page.",".$this->all."); return false;")); 
        
        return '<li class="'.$class.' '.$pageClass.'">'.$link.'</li>';
    }



}

