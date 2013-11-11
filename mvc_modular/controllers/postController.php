<?php
class postController extends Controller
{
    private $_post;
    
    public function __construct() 
    {
        parent::__construct();
        $this->_post = $this->loadModel('post');
    }
    
    public function index($pagina = false)
    {
        
        if (!$this->filtraInt($pagina)) {
            $pagina = false;
        } else {
            $pagina = (int) $pagina;
        }
        
        $paginador = new Paginador();
        
        $this->_view->assign('posts', $paginador->paginar($this->_post->getPost(), $pagina));
        $this->_view->assign('paginacion', $paginador->getView('prueba', 'post/index'));
        $this->_view->assign('titulo', 'Post');
        $this->_view->renderizar('index', 'post');
    }
    
    public function nuevo()
    {
        
        $this->_acl->acceso('nuevo_post');
        
        $this->_view->assign('titulo', 'Nuevo Post');
        $this->_view->setJsPlugin(array('jquery.validate'));
        $this->_view->setJs(array('nuevo'));
        
        if ($this->getInt('guardar') == 1) {
            
            $this->_view->assign('datos', $_POST);
            
            if (!$this->getTexto('titulo')) {
                //$this->_view->_error = 'Debe introducir el titulo del post';
                $this->_view->assign('_error', 'No puede quedar vacío');
                $this->_view->renderizar('nuevo', 'post');
                exit;
            }
            
            if (!$this->getTexto('cuerpo')) {
                $this->_view->assign('_error', 'No puede quedar vacío');
                $this->_view->renderizar('nuevo', 'post');
                exit;
            }
            
            $imagen = '';
            
            if ($_FILES['imagen']['name']) {
                $ruta = ROOT.'public'.DS.'img'.DS.'post'.DS;
                $upload = new upload($_FILES['imagen']);
                $upload->allowed = array('image/*');
                $upload->file_new_name_body = 'upl_'.uniqid();
                $upload->process($ruta);
                
                if ($upload->processed) {
                    $imagen = $upload->file_dst_name;
                    $thumb = new upload($upload->file_dst_pathname);
                    $thumb->image_resize = true;
                    $thumb->image_x = 100;
                    $thumb->image_y = 70;
                    $thumb->file_name_body_pre = 'thumb_';
                    $thumb->process($ruta.'thumb'.DS);
                } else {
                    $this->_view->assign('_error', $upload->error);
                    $this->_view->renderizar('nuevo', 'post');
                    exit;
                }
            }
            
            $this->_post->insertarPost(
                    $this->getPostParam('titulo'),
                    $this->getPostParam('cuerpo'),
                    $imagen
                    );
            Session::setMensaje('Registro exitoso!');
            $this->redireccionar('post');
        }
        
        $this->_view->renderizar('nuevo', 'post');
    }
    
    public function editar($id)
    {
         $this->_acl->acceso('nuevo_post');
        
        if (!$this->filtraInt($id)) {
            $this->redireccionar('post');
        }
        
        if (!$this->_post->getUnPost($this->filtraInt($id))) {
            $this->redireccionar('post');
        }
        
        $this->_view->assign('titulo', 'Editar Post');
        $this->_view->setJs(array('nuevo'));
        
        if ($this->getInt('guardar') == 1) {
            
            $this->_view->assign('datos', $_POST);
            
            if (!$this->getTexto('titulo')) {
                $this->_view->assign('_error', 'No puede quedar vacío');
                $this->_view->renderizar('editar', 'post');
                exit;
            }
            
            if (!$this->getTexto('cuerpo')) {
                 $this->_view->assign('_error', 'No puede quedar vacío');
                $this->_view->renderizar('editar', 'post');
                exit;
            }
            
            
            $this->_post->editarPost(
                    $this->filtraInt($id),
                    $this->getTexto('titulo'),
                    $this->getTexto('cuerpo')
                    );
            Session::setMensaje('Registro modificado!');
            $this->redireccionar('post');
        }
        $this->_view->assign('datos', $this->_post->getUnPost($this->filtraInt($id)));
        $this->_view->renderizar('editar', 'post');
    }
    
    public function eliminar($id)
    {
        $this->_acl->acceso('eliminar_post');
        
        if (!$this->filtraInt($id)) {
            $this->redireccionar('post');
        }
        
        if (!$this->_post->getUnPost($this->filtraInt($id))) {
            $this->redireccionar('post');
        }
        
        $this->_post->eliminarPost($this->filtraInt($id));
        Session::setMensaje('Registro eliminado!');
        $this->redireccionar('post');
    }
    
    public function prueba($pagina = false) 
    {
        $paginador = new Paginador();
        
        $ajaxModel = $this->loadModel('ajax');
        
        $this->_view->setJs(array('prueba'));
        $this->_view->assign('paises', $ajaxModel->getPaises());
        $this->_view->assign('post', $paginador->paginar($this->_post->getPrueba()));
	$this->_view->assign('paginacion', $paginador->getView('paginacion_ajax'));
        $this->_view->assign('titulo', 'Post');
        $this->_view->renderizar('prueba', 'prueba');
    }
    
    public function pruebaAjax() 
    {
        $pagina = $this->getInt('pagina');
        $nombre = $this->getSql('nombre');
        $pais = $this->getInt('pais');
        $ciudad = $this->getInt('ciudad');
        $registros = $this->getInt('registros');
        $condicion = "";
        
        if ($nombre) {
            $condicion .= " AND `nombre` LIKE '$nombre%' ";
        }
        
        if ($pais) {
            $condicion .= " AND `id_pais` = $pais ";
        }
        
        if ($pais) {
            $condicion .= " AND `id_ciudad` = $ciudad ";
        }
        $paginador = new Paginador();
        
        $this->_view->setJs(array('prueba'));
        $this->_view->assign('post', $paginador->paginar($this->_post->getPrueba($condicion), $pagina, $registros));
	$this->_view->assign('paginacion', $paginador->getView('paginacion_ajax'));
        $this->_view->assign('titulo', 'Post');
        $this->_view->renderizar('ajax/prueba', false, true);
    }
}
?>
