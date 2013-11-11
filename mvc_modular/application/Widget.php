<?php
abstract class Widget
{
    protected function loadModel($model)
    {
        if (is_readable(ROOT . 'widgets' . DS . 'models' . DS . $model . '.php')) {
            include_once ROOT . 'widgets' . DS . 'models' . DS . $model . '.php';
            $modelClass = $model . 'ModelWidgets';
            if (class_exists($modelClass)) {
                return new $modelClass;
            }
        }
        throw new Exception('Error de modelo de widget');
    }
    
    protected function render($view, $data = array(), $ext = '.phtml')
    {
        if (is_readable(ROOT . 'widgets' . DS . 'views' . DS . $view . $ext)) {
            ob_start();
            extract($data);
            include_once ROOT . 'widgets' . DS . 'views' . DS . $view . $ext;
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }
        
        throw new Exception('Error de vista de widget');
    }
}