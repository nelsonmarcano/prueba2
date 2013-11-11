<?php
class aclController extends Controller
{
    private $_aclm;
    
    public function __construct() {
        parent::__construct();
        $this->_aclm = $this->loadModel('acl');
    }
    
    public function index()
    {
        $this->_view->assign('titulo', 'Listas de acceso');
        $this->_view->renderizar('index', 'inicio');
    }
    
    public function roles()
    {
        $this->_view->assign('titulo', 'Administración de roles');
        $this->_view->assign('roles', $this->_aclm->getRoles());
        $this->_view->renderizar('roles');
    }
    
    public function permisos_role($roleID)
    {
        $id = $this->filtraInt($roleID);
        
        if (!$id) {
            $this->redireccionar('acl/roles');
        }
        
        $row = $this->_aclm->getRole($id);
        
        if (!$row) {
            $this->redireccionar('acl/roles');
        }
        
        $this->_view->assign('titulo', 'Administración de permisos de role');
        
        if ($this->getInt('guardar') == 1) {
            $values = array_keys($_POST);
            $replace = array();
            $eliminar = array();
            
            for ($i = 0; $i < count($values); $i++) {
                if (substr($values[$i], 0, 5) == 'perm_') {
                    $permiso = (strlen($values[$i]) - 5);
                    if ($_POST[$values[$i]] == 'x') {
                        $eliminar[] = array(
                            'role' => $id,
                            'permiso' => substr($values[$i], - $permiso)
                        );
                    } else {
                        if ($_POST[$values[$i]] == 1) {
                            $v = 1;
                        } else {
                            $v = 0;
                        }
                        $replace[] = array(
                            'role' => $id,
                            'permiso' => substr($values[$i], - $permiso),
                            'valor' => $v
                        );
                    }
                }
            }
            
            for ($i = 0; $i < count($eliminar); $i++) {
                $this->_aclm->eliminarPermisosRole(
                        $eliminar[$i]['role'], 
                        $eliminar[$i]['permiso']);
            }
            
            for ($i = 0; $i < count($replace); $i++) {
                $this->_aclm->editarPermisosRole(
                        $replace[$i]['role'], 
                        $replace[$i]['permiso'], 
                        $replace[$i]['valor']);
            }
        }
         
        $this->_view->assign('roles', $row);
        $this->_view->assign('permisos', $this->_aclm->getPermisosRole($id));
        $this->_view->renderizar('permisos_role');
    }
    
    public function nuevo_role()
    {
        $this->_view->assign('titulo', 'Nuevo role');
        $this->_view->setJs(array('nuevo_role'));
        
        if ($this->getInt('guardar') == 1) {
           
            $this->_view->assign('datos', $_POST);
            
            if (!$this->getSql('role')) {
                $this->_view->assign('_error', 'El campo es requerido');
                $this->_view->renderizar('nuevo_role');
                exit;
            }
            
            $this->_aclm->nuevoRole($this->getSql('role'));
            $this->redireccionar('acl'.DS.'roles');
        }
        
        $this->_view->renderizar('nuevo_role');
    }
    
    public function editar_role($id)
    {
        if (!$this->filtraInt($id)) {
            $this->redireccionar('roles');
        }
        
        if (!$this->_aclm->getRole($this->filtraInt($id))) {
            $this->redireccionar('roles');
        }
        
        $this->_view->assign('titulo', 'Editar Role');
        $this->_view->setJs(array('nuevo_role'));
        
        if ($this->getInt('guardar') == 1) {
            
            $this->_view->assign('datos', $_POST);
            
            if (!$this->getSql('role')) {
                $this->_view->assign('_error', 'Este campo es requerido');
                $this->_view->renderizar('editar_role');
                exit;
            }
            
            $this->_aclm->editarRole(
                    $this->filtraInt($id),
                    $this->getSql('role')
                    );
            $this->redireccionar('acl'.DS.'roles');
        }
        
        $this->_view->assign('datos', $this->_aclm->getRole($this->filtraInt($id)));
        $this->_view->renderizar('editar_role');
    }
    
    public function permisos()
    {
        $this->_view->assign('titulo', 'Permisos');
        $this->_view->assign('permisos', $this->_aclm->getPermisos());
        $this->_view->renderizar('permisos');
    }
    
    public function nuevo_permiso()
    {
        $this->_view->assign('titulo', 'Agregar Permiso');
        $this->_view->setJs(array('nuevo_permiso'));
        
        if ($this->getInt('guardar') == 1) {
            
            $this->_view->assign('datos', $_POST);
            
            if (!$this->getSql('permiso')) {
                $this->_view->assign('_error', 'Este campo es requerido');
                $this->_view->renderizar('nuevo_permiso');
                exit;
            }
            
            if (!$this->getAlphaNum('llave')) {
                $this->_view->assign('_error', 'Este campo es requerido');
                $this->_view->renderizar('nuevo_permiso');
                exit;
            }
            
            $this->_aclm->nuevoPermiso($this->getSql('permiso'), $this->getAlphaNum('llave'));
            $this->redireccionar('acl'.DS.'permisos');
        }
        
        $this->_view->renderizar('nuevo_permiso');
    }
    
    public function editar_permiso($id)
    {
        if (!$this->filtraInt($id)) {
            $this->redireccionar('acl/permisos');
        }
        
        if (!$this->_aclm->getUnPermiso($this->filtraInt($id))) {
            
            $this->redireccionar('acl/permisos');
        }
        
        $this->_view->assign('titulo', 'Editar Permisos');
        $this->_view->setJs(array('nuevo_permiso'));
        
        if ($this->getInt('guardar') == 1) {
            
            $this->_view->assign('datos', $_POST);
            
            if (!$this->getSql('permiso')) {
                $this->_view->assign('_error', 'Este campo es requerido');
                $this->_view->renderizar('editar_permiso');
                exit;
            }
            
            if (!$this->getAlphaNum('llave')) {
                $this->_view->assign('_error', 'Este campo es requerido');
                $this->_view->renderizar('editar_permiso');
                exit;
            }
            
            $this->_aclm->editarPermiso(
                        $this->filtraInt($id),
                        $this->getSql('permiso'),
                        $this->getAlphaNum('llave')
                    );
            $this->redireccionar('acl/permisos');
        }
        
        $this->_view->assign('datos', $this->_aclm->getUnPermiso($this->filtraInt($id)));
        $this->_view->renderizar('editar_permiso');
    }
}
?>
