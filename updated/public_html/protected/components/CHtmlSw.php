<?php
/**
 * CHtml class file.
 *
 * @author 
 * @link 
 * @copyright 
 * @license 
 */


/**
 * CHtml is a static class that provides a collection of helper methods for creating HTML views.
 *
 * @author 
 * @version 
 * @package 
 * @since
 */
class CHtmlSw extends CHtml
{


	/**
	 * Displays the first validation error for a model attribute.
	 * @param CModel $model the data model
	 * @param string $attribute the attribute name
	 * @param array $htmlOptions additional HTML attributes to be rendered in the container div tag.
	 * This parameter has been available since version 1.0.7.
	 * @return string the error display. Empty if no errors are found.
	 * @see CModel::getErrors
	 * @see errorMessageCss
	 */
	public static function error($model,$attribute,$htmlOptions=array())
	{
		self::resolveName($model,$attribute); // turn [a][b]attr into attr
		$error=$model->getError($attribute);
		if($error!='')
		{
			if(!isset($htmlOptions['class']))
				$htmlOptions['class']=self::$errorMessageCss;
            $htmlOptions['title']=$error;
			return self::tag('dd',$htmlOptions,$error);
		}
		else
			return '';
	}
}
