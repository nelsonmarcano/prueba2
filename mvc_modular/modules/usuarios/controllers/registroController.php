<?php
class registroController extends Controller
{
    private $_registro;

    public function __construct() {
        parent::__construct();
        $this->_registro = $this->loadModel('registro');
//        $this->_view->setTemplate('default');
    }
    
    public function index()
    {
        if (Session::get('autenticado')) {
            $this->redireccionar();
        }
        
        $this->_view->assign('titulo', 'Registro');
        
        if ($this->getInt('enviar') == 1) {
            $this->_view->assign('datos', $_POST);
            
            if (!$this->getSql('nombre')) {
                $this->_view->assign('_error', 'Debe introducir un nombre');
                $this->_view->renderizar('index', 'registro');
                exit;
            }
            
            if (!$this->getAlphaNum('usuario')) {
                $this->_view->assign('_error', 'Debe introducir un usuario');
                $this->_view->renderizar('index', 'registro');
                exit;
            }
            
            if ($this->_registro->verificarUsuario($this->getAlphaNum('usuario'))) {
                $this->_view->assign('_error', 'El usuario '.$this->getAlphaNum('usuario').' ya existe!');
                $this->_view->renderizar('index', 'registro');
                exit;
            }
            
            if (!$this->validarEmail($this->getPostParam('email'))) {
                $this->_view->assign('_error', 'La direccion de email es inválida');
                $this->_view->renderizar('index', 'registro');
                exit;
            }
            
            if ($this->_registro->verificarEmail($this->getPostParam('email'))) {
                $this->_view->assign('_error', 'Esta dirección de correo ya está registrada');
                $this->_view->renderizar('index', 'registro');
                exit;
            }
            
            if (!$this->getSql('pass')) {
                $this->_view->assign('_error', 'Debe introducir su password');
                $this->_view->renderizar('index', 'registro');
                exit;
            }
            
            if ($this->getSql('pass') != $this->getPostParam('confirmar')) {
                $this->_view->assign('_error', 'Los passwords no coinciden');
                $this->_view->renderizar('index', 'registro');
                exit;
            }
            
           $this->getLibrary('class.phpmailer');
           $mail = new PHPMailer();
           $mail->IsSMTP();
           $mail->SMTPAuth = true;
           $mail->SMTPSecure = 'ssl';
           $mail->Host = 'smtp.gmail.com';
           $mail->Port = 465;
           $mail->Username = 'tsu.nelsonmarcano@gmail.com';
           $mail->Password = 'NayeskaMarcano123';
           
           $this->_registro->registrarUsuario(
                    $this->getSql('nombre'),
                    $this->getAlphaNum('usuario'),
                    $this->getSql('pass'),
                    $this->getPostParam('email')
                    );
            $usuario = $this->_registro->verificarUsuario($this->getAlphaNum('usuario'));

            if (!$usuario) {
                $this->_view->assign('_error', 'Error al registrar el usuario');
                $this->_view->renderizar('index', 'registro');
                exit;
            } 

            
            $mail->From = 'www.nelsonmr.byethost32.com/'; // Aqui va la direccion de la pagina
            $mail->FromName = 'Nelson Marcano';
            $mail->Subject = 'Activacion de cuenta de usuario';
            $mail->Body = 'Hola <strong>'.$this->getSql('nombre').'</strong>'.
                    '<p>Se ha registrado en www.nelsonmr.byethost32.com para activar su cuenta</p>'.
                    ' haga clic sobre el siguiente enlace:<br>'.
                    '<a href="'.BASE_URL.'usuarios/registro/activar/'.
                    $usuario['id'].'/'.$usuario['codigo'].'">'.
                    BASE_URL.'usuarios/registro/activar/'.
                    $usuario['id'].'/'.$usuario['codigo'].'</a>';

            $mail->AltBody = 'Su servidor de correo no soporta hmtl';
            $mail->AddAddress($this->getPostParam('email'));
            $mail->Send();
           
            $this->_view->assign('datos', false);
            $this->_view->assign('_mensaje', 'Registro completado, revise su email para activar su cuenta');
            
        }
        
        $this->_view->renderizar('index', 'registro');
    }
    
    public function activar($id, $codigo)
    {
        if (!$this->filtraInt($id) || !$this->filtraInt($codigo)) {
            $this->_view->assign('_error', 'Esta cuenta no existe');
            $this->_view->renderizar('activar', 'registro');
            exit;
        }
        
        $row = $this->_registro->getUsuario($this->filtraInt($id), $this->filtraInt($codigo));
        
        if (!$row) {
            $this->_view->assign('_error', 'Esta cuenta no existe');
            $this->_view->renderizar('activar', 'registro');
            exit;
        }
        
        if ($row['estado'] == 1) {
            $this->_view->assign('_error', 'Esta cuenta ya ha sido activada');
            $this->_view->renderizar('activar', 'registro');
            exit;
        }
        
        $this->_registro->activarUsuario($this->filtraInt($id), $this->filtraInt($codigo));
        
        $row = $this->_registro->getUsuario($this->filtraInt($id), $this->filtraInt($codigo));
        
        if ($row['estado'] == 0) {
            $this->_view->assign('_error', 'Error al activar la cuenta, por favor intente mas tarde');
            $this->_view->renderizar('activar', 'registro');
            exit;
        }
        
        $this->_view->assign('_mensaje', 'Su cuenta ha sido activada');
        $this->_view->renderizar('activar', 'registro');
    }
}
?>
