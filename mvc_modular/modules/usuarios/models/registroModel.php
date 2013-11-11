<?php
class registroModel extends Model
{
    public function __construct() {
        parent::__construct();
    }
    
    public function verificarUsuario($usuario)
    {
        $id = $this->_db->query("SELECT id, codigo FROM usuarios WHERE usuario = '$usuario';");
        return $id->fetch();
    }
    
    public function verificarEmail($email)
    {
        $id = $this->_db->query("SELECT id FROM usuarios WHERE email = '$email';");
        if ($id->fetch()) {
            return $id->fetch();
        }
        return false;
    }
    
    public function registrarUsuario($nombre, $usuario, $password, $email)
    {
        $ramdom = rand(17654276, 999999999);
        
        $this->_db->prepare("INSERT INTO usuarios VALUES ".
                "(null, :nombre, :usuario, :pass, :email, 'usuario', 0, now(), :codigo);")
                ->execute(array(
                    ':nombre' => $nombre,
                    ':usuario' => $usuario,
                    ':pass' => Hash::getHash('sha1', $password, HASH_KEY),
                    ':email' => $email,
                    ':codigo' => $ramdom
                ));
    }
    
    public function getUsuario($id, $codigo)
    {
        $id = (int) $id;
        $usuario = $this->_db->query("SELECT * FROM usuarios WHERE id = $id and codigo = '$codigo';");
        return $usuario->fetch();
    }
    
    public function activarUsuario($id, $codigo)
    {
        $id = (int) $id;
        $this->_db->query("UPDATE usuarios SET estado = 1 WHERE id = $id and codigo = '$codigo';");
    }
}
?>
