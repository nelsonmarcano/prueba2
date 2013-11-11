<?php
abstract class Controller
{
    private $_registry;
    protected $_view;
    protected $_acl;
    protected $_request;

    public function __construct() 
    {
        $this->_registry = Registry::getInstancia();
        $this->_acl = $this->_registry->_acl;
        $this->_request = $this->_registry->_request;
        $this->_view = new View($this->_request, $this->_acl);
    }
    abstract public function index();
    //Llamamos al modelo
    protected function loadModel($modelo, $modulo = false)
    {
        $modelo = $modelo.'Model';
        $rutaModelo = ROOT.'models'.DS.$modelo.'.php';
        
        if (!$modulo) {
            $modulo = $this->_request->getModulo();
        }
        if ($modulo) {
            if ($modulo != 'default') {
                $rutaModelo = ROOT.'modules'.DS.$modulo.DS.'models'.DS.$modelo.'.php';
            }
        }
        
        if (is_readable($rutaModelo)) {
            require_once $rutaModelo;
            $modelo = new $modelo;
            return $modelo;
        } else {
            throw new Exception('Error de modelo');
        }
    }
    // llamado de librerias fpdf
    protected function getLibrary($libreria)
    {
        $rutaLibreria = ROOT.'libs'.DS.$libreria.'.php';
        if (is_readable($rutaLibreria)) {
            require_once $rutaLibreria;
        } else {
            throw new Exception('Error de libreria');
        }
    }
    
    protected function getTexto($clave)
    {
        if (isset($_POST[$clave]) && !empty($_POST[$clave])) {
            $_POST[$clave] = htmlspecialchars($_POST[$clave], ENT_QUOTES);
            return $_POST[$clave];
        }
        return '';
    }
    
    protected function getInt($clave)
    {
        if (isset($_POST[$clave]) && !empty($_POST[$clave])) {
            $_POST[$clave] = filter_input(INPUT_POST, $clave, FILTER_VALIDATE_INT);
            return $_POST[$clave];
        }
        return 0;
    }
    
    protected function redireccionar($ruta = false)
    {
        if ($ruta) {
            header('Location:'.BASE_URL.$ruta);
            exit;
        } else {
            header('Location:'.BASE_URL);
            exit;
        }
    }
    
    protected function filtraInt($int)
    {
        $int = (int) $int;
        if (is_int($int)) {
            return $int;
        } else {
            return 0;
        }
    }
    
    protected function getPostParam($clave)
    {
        if (isset($_POST[$clave])) {
            return $_POST[$clave];
        }
    }
    
    protected function getSql($clave)
    {
        if(isset($_POST[$clave]) && !empty($_POST[$clave])){
            $_POST[$clave] = strip_tags($_POST[$clave]);
            
            if(!get_magic_quotes_gpc()){
                @$_POST[$clave] = mysql_escape_string($_POST[$clave]);
            }
            
            return trim($_POST[$clave]);
        }
    }
    
    protected function getAlphaNum($clave)
    {
        if(isset($_POST[$clave]) && !empty($_POST[$clave])){
            $_POST[$clave] = (string) preg_replace('/[^A-Z0-9_]/i', '', $_POST[$clave]);
            return trim($_POST[$clave]);
        }
        
    }
    
    public function validarEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return true;
    }
}
?>
