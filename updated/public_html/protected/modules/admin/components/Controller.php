<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController {

    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout = '/layouts/admin'; //   '//'... - ��� ���������          '/'... - ��� � ������
    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu = array();

    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs = array();

    /**
     * This method is invoked right before an action is to be executed (after all possible filters.)
     * You may override this method to do last-minute preparation for the action.
     * @param CAction $action the action to be executed.
     * @return boolean whether the action should be executed.
     */
    public function beforeAction($action) {
        if (parent::beforeAction($action)) {
            $controllerName = Yii::app()->controller->getId();
            $actionName = $action->id;
            FB::info($controllerName, '****************** controller');
            FB::info($actionName, '****************** action');

            if ($controllerName == 'site' && $actionName == 'error') {
                $allowed = true;
            } else {
                $allowed = Yii::app()->controller->module->admin->isAllowed($controllerName, $actionName);
            }
            ///FB::error($allowed, 'ALLOWED');			


            if (!$allowed/* && !$controllerName!='site' && $actionName!='error' */) {
                header("HTTP/1.0 403 You have not permission");
                echo '<h1>You have not permission</h1>';
                Yii::app()->end();
                //throw new CHttpException(403, 'You have not permission');
            }

            if (isset(Yii::app()->controller->module->admin->id))

            //logging every admin action
                CHelperLog::adminActionsLog(Yii::app()->controller->module->admin->id, $controllerName, $actionName);


            return true;
        }
        else
            return false;
    }

    public function init() {
        FB::info("FirePHP ADMIN enabled");

        /*
          //http://a. ...
          if ( !preg_match('/^(a.)/', $_SERVER['HTTP_HOST']) && !preg_match('/^(adev.)/', $_SERVER['HTTP_HOST']) )
          {
          echo 'Access denied';
          Yii::app()->end();
          }
         */
    }

}