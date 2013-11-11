<?php
class Bootstrap
{
    public static function run(Request $peticion)
    {
        $modulo = $peticion->getModulo();
        $controller = $peticion->getControlador().'Controller';
        $metodo = $peticion->getMetodo();
        $argumentos = $peticion->getArgs();
        
        if ($modulo) {
            $rutaModulo = ROOT.'controllers'.DS.$modulo.'Controller.php';
            
            if (is_readable($rutaModulo)) {
                require_once $rutaModulo;
                $rutaControlador = ROOT.'modules'.DS.$modulo.DS.'controllers'.DS.$controller.'.php';
            } else {
                throw new Exception('Error de base módulo');
            }
        } else {
            $rutaControlador = ROOT.'controllers'.DS.$controller.'.php';
        }
        
        
        //echo $rutaControlador;exit;
        if (is_readable($rutaControlador)) {
            require_once $rutaControlador;
            $controller = new $controller;
            
            if (is_callable(array($controller, $metodo))) {
                $metodo = $peticion->getMetodo();
            } else {
                $metodo = 'index';
            }
            
            if (isset($argumentos)) {
                call_user_func_array(array($controller, $metodo), $argumentos);
            } else {
                call_user_func(array($controller, $metodo));
            }
        } else {
            throw new Exception('No encontrado');
        }
    }
}
?>
