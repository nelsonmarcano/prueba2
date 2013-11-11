<?php
class ajaxModel extends Model
{
    public function __construct() {
        parent::__construct();
    }
    
    public function getPaises()
    {
        $paises = $this->_db->query("SELECT * FROM paises");
        return $paises->fetchAll();
    }
    
    public function getCiudades($pais)
    {
        $pais = (int) $pais;
        $ciudades = $this->_db->query("SELECT * FROM ciudades WHERE pais = {$pais}");
        $ciudades->setFetchMode(PDO::FETCH_ASSOC);
        return $ciudades->fetchAll();
    }
    
    public function insertarCiudad($ciudad, $pais)
    {
        $this->_db->query("INSERT INTO ciudades VALUES (null, '{$ciudad}', '{$pais}')");
    }
}
?>
