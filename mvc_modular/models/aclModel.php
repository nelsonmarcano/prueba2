<?php
class aclModel extends Model
{
    public function __construct() {
        parent::__construct();
    }
    
    public function getRole($roleID)
    {
        $role = $this->_db->query(
                "SELECT * FROM roles WHERE ".
                "id_role = $roleID"
                );
        return $role->fetch();
    }
    
    public function getRoles()
    {
        $role = $this->_db->query("SELECT * FROM roles");
        return $role->fetchAll();
    }
    
    public function getPermisosAll()
    {
        $permisos = $this->_db->query(
                    "SELECT * FROM permisos"
                );
        $permisos = $permisos->fetchAll(PDO::FETCH_ASSOC);
        
        for ($i = 0; $i < count($permisos); $i++) {
            if ($permisos[$i]['key'] == '') { continue; }
            $data[$permisos[$i]['key']] = array(
                'key' => $permisos[$i]['key'],
                'valor' => 'x',
                'nombre' => $permisos[$i]['permiso'],
                'id' => $permisos[$i]['id_permiso']
            );
        }
        
        return $data;
    }
    
    public function getPermisos()
    {
        $permisos = $this->_db->query(
                    "SELECT * FROM permisos"
                );
        return $permisos->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getUnPermiso($id_permiso)
    {
        $permisos = $this->_db->query(
                    "SELECT * FROM permisos WHERE id_permiso = $id_permiso"
                );
        return $permisos->fetch();
    }
    
    public function nuevoPermiso($permiso, $key)
    {
        
        $this->_db->prepare(
                "INSERT INTO permisos VALUES (null, :permiso, :key)"
                )
        ->execute(
                 array(
                     ':permiso' => $permiso,
                     ':key' => $key
                 ));
    }
    
    public function editarPermiso($id_permiso, $permiso, $key)
    {
       
        $id_permiso = (int) $id_permiso;
        $this->_db->prepare("UPDATE permisos SET permiso = :permiso, `key` = :key WHERE id_permiso = :id_permiso")
                ->execute(
                        array(
                            ':id_permiso' => $id_permiso,
                            ':permiso' => $permiso,
                            ':key' => $key
                        ));
    }
    
    public function getPermisosRole($roleID)
    {
        $data = array();
        
        $permisos = $this->_db->query(
                    "SELECT * FROM permisos_role WHERE ".
                    "role = $roleID"
                );
        $permisos = $permisos->fetchAll(PDO::FETCH_ASSOC);
        
        for ($i = 0; $i < count($permisos); $i++) {
            
            $key = $this->getPermisoKey($permisos[$i]['permiso']);
            
            
            if ($key == '') { continue; }
            
            if ($permisos[$i]['valor'] == 1) {
                $v = 1;
            } else {
                $v = 0;
            }
            
            $data[$key] = array(
                'key' => $key,
                'valor' => $v,
                'nombre' => $this->getPermisoNombre($permisos[$i]['permiso']),
                'id' => $permisos[$i]['permiso']
            );
        }
        
        $data = array_merge($this->getPermisosAll(), $data);
        
        return $data;
        
    }
    
    public function eliminarPermisosRole($roleID, $permisoID)
    {
        $this->_db->query(
                "DELETE FROM permisos_role WHERE ".
                "role = $roleID AND permiso = $permisoID"
                );
    }
    
    public function editarPermisosRole($roleID, $permisoID, $valor)
    {
        $this->_db->query(
                "REPLACE INTO permisos_role ".
                "SET role = $roleID, permiso = $permisoID, valor = '$valor'"
                );
    }
    
    public function getPermisoKey($permisoId)
    {
        $permisoId = (int) $permisoId;
        
        $key = $this->_db->query(
                "SELECT `key` FROM permisos WHERE id_permiso = {$permisoId}"
                );
                
        $key = $key->fetch();
        return $key['key'];
    }
    
    public function getPermisoNombre($permisoId)
    {
        $permisoId = (int) $permisoId;
        
        $key = $this->_db->query(
                "SELECT permiso FROM permisos WHERE id_permiso = {$permisoId}"
                );
                
        $key = $key->fetch();
        return $key['permiso'];
    }
    
    public function nuevoRole($role)
    {
        
        $this->_db->prepare(
                "INSERT INTO roles VALUES (null, :role)"
                )
        ->execute(
                 array(
                     ':role' => $role
                 ));
    }
    
    public function editarRole($id_role, $role)
    {
       
        $id = (int) $id;
        $this->_db->prepare("UPDATE roles SET role = :role WHERE id_role = :id_role")
                ->execute(
                        array(
                            ':id_role' => $id_role,
                            ':role' => $role
                        ));
    }
}
?>
