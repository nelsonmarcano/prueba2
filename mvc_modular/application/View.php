<?php
require_once ROOT.'libs'.DS.'smarty'.DS.'libs'.DS.'Smarty.class.php';

class View extends Smarty
{
    private $_request;
    private $_js;
    private $_acl;
    private $_rutas;
    private $_jsPlugin;
    private $_template;
    private $_item;
    private $_widget;

    public function __construct(Request $peticion, Acl $_acl) {
        parent::__construct();
        $this->_request = $peticion;
        $this->_js = array();
        $this->_acl = $_acl;
        $this->_rutas = array();
        $this->_jsPlugin = array();
        $this->_template = DEFAULT_LAYOUT;
        $this->_item = '';
        
        $modulo = $this->_request->getModulo();
        $controlador = $this->_request->getControlador();
        
        if ($modulo) {
            $this->_rutas['view'] = ROOT . 'modules' . DS . $modulo . DS . 'views' . DS . $controlador . DS;
            $this->_rutas['js'] = BASE_URL . 'modules/' . $modulo .'/views/' . $controlador . '/js/';
        } else {
            $this->_rutas['view'] = ROOT . 'views' . DS . $controlador . DS;
            $this->_rutas['js'] = BASE_URL . 'views/' . $controlador . '/js/';
        }
    }
    
    public function renderizar($vista, $item = false, $noLayout = false)
    {
        if ($item) {
            $this->_item = $item;
        }
        
        $this->template_dir = ROOT . 'views' . DS . 'layout' . DS . $this->_template . DS;
        $this->config_dir = ROOT . 'views' . DS . 'layout' . DS . $this->_template . DS . 'configs' . DS;
        $this->cache_dir = ROOT . 'tmp' . DS . 'cache' . DS;
        $this->compile_dir = ROOT . 'tmp' . DS . 'template' . DS;
        
        $menu = array(
            array(
                'id' => 'inicio',
                'titulo' => 'Inicio',
                'enlace' => BASE_URL,
                'imagen' => 'icon-home'
            ),
            array(
                'id' => 'post',
                'titulo' => 'Post',
                'enlace' => BASE_URL.'post',
                'imagen' => 'icon-flag'
            )
        );
        
        if(!Session::get('autenticado')){
            $menu[] = array(
                'id' => 'registro',
                'titulo' => 'Registro',
                'enlace' => BASE_URL . 'usuarios/registro',
                'imagen' => 'icon-book'
                );
        }
        
        $_params = array(
            'ruta_css' => BASE_URL . 'views/layout/' . $this->_template . '/css/',
            'ruta_img' => BASE_URL . 'views/layout/' . $this->_template . '/img/',
            'ruta_js' => BASE_URL . 'views/layout/' . $this->_template . '/js/',
            'menu' => $menu,
            'item' => $this->_item,
            'js' => $this->_js,
            'js_plugin' => $this->_jsPlugin,
            'root' => BASE_URL,
            'configs' => array(
                'app_name' => APP_COMPANY,
                'app_slogan' => APP_SLOGAN,
                'app_company' => APP_COMPANY
            )
        );
        
        if (is_readable($this->_rutas['view'] . $vista . '.tpl')) {
            
            if($noLayout){
                $this->template_dir = $this->_rutas['view'];
                $this->display($this->_rutas['view'] . $vista . '.tpl');
                exit;
            }
            
            $this->assign('_contenido', $this->_rutas['view'] . $vista . '.tpl');
            
        } else {
            throw new Exception('Error de vista');
        }
        
        $this->assign('widgets', $this->getWidgets());
        $this->assign('_acl', $this->_acl);
        $this->assign('_layoutParams', $_params);
        $this->display('template.tpl');
    }
    
    public function setJs(array $js)
    {
        if (is_array($js) && count($js)) {
            for ($i = 0; $i < count($js); $i++) {
                $this->_js[] = $this->_rutas['js'] . $js[$i] . '.js';
            }
        } else {
            throw new Exception('Error de js');
        }
    }
    
    public function setJsPlugin(array $js)
    {
        if(is_array($js) && count($js)){
            for($i=0; $i < count($js); $i++){
                $this->_jsPlugin[] = BASE_URL . 'public/js/' . $js[$i] . '.js';
            }
        } else {
            throw new Exception('Error de js plugin');
        }
    }
    
    public function setTemplate($template)
    {
        $this->_template = (string) $template;
    }
    
    public function widget($widget, $method, $options = array())
    {
        if (!is_array($options)) {
            $options = array($options);
        }
        
        if (is_readable(ROOT . 'widgets' . DS . $widget . '.php')) {
            include_once ROOT . 'widgets' . DS . $widget . '.php';
            $widgetClass = $widget . 'Widgets';
            
            if (!class_exists($widgetClass)) {
                throw new Exception('Error de clase de widget');
            }
            
            if (is_callable($widgetClass, $method)) {
                if (count($options)) {
                    return call_user_func_array(array(new $widgetClass, $method), $options);
                } else {
                    return call_user_func(array(new $widgetClass, $method));
                }
            }
            throw new Exception('Error de metodo widget');
        }
        
        throw new Exception('Error de widget');
    }
    
    public function getLayoutPositions()
    {
        if (is_readable(ROOT . 'views' . DS . 'layout' . DS . $this->_template . DS . 'configs.php')) {
            include_once ROOT . 'views' . DS . 'layout' . DS . $this->_template . DS . 'configs.php';
            return get_layout_positions();
        }
        throw new Exception('Error de configuracion layout');
    }
    
    public function setWidgetOptions($key, $options)
    {
        if (!isset($this->_widget[$key])) {
            $this->_widget[$key] = $options;
        }
    }
    
    public function getWidgets()
    {
        $widget = array(
            'menu-sidebar' => array(
                'config' => $this->widget('menu', 'getConfig'),
                'content' => array('menu', 'getMenu')
            )
        );
        
        $position = $this->getLayoutPositions();
        $keys = array_keys($widget);
        
        foreach ($keys as $k) {
            if (isset($position[$widget[$k]['config']['position']])) {
                if (!isset($widget[$k]['config']['hide']) || !in_array($this->_item, $widget[$k]['config']['hide'])) {
                    if ($widget[$k]['config']['show'] === 'all' || in_array($this->_item, $widget[$k]['config']['show'])) {
                        if (isset($this->_widget[$k])) {
                            $widget[$k]['content'][2] = $this->_widget[$k];
                        }
                        $position[$widget[$k]['config']['position']][] = $this->getWidgetsContent($widget[$k]['content']);
                    }
                }
            }
        }
        
        return $position;
    }
    
    
    
    public function getWidgetsContent(array $content)
    {
        if (!isset($content[0]) || !isset($content[1])) {
            throw new Exception('error contenido widget');
            return;
        }
        
        if (!isset($content[2])) {
            $content[2] = array();
        }
        
        return $this->widget($content[0], $content[1], $content[2]);
    }
}
?>
