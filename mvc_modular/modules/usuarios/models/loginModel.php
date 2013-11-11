<?php
class loginModel extends Model
{
    public function __construct() {
        parent::__construct();
    }
    
    public function getUsuario($usuario, $password)
    {
        $datos = $this->_db->query(
                "SELECT * FROM usuarios ".
                "WHERE usuario = '$usuario' ".
                "AND pass = '".Hash::getHash('sha1', $password, HASH_KEY)."';");
        return $datos->fetch();
    }
}
?>
