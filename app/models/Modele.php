<?php
namespace app\models;

use Flight;
use PDO;

class Modele {

    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function getPrixEssence(): float {
        $sql = "SELECT prix_essence FROM parametre LIMIT 1";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result && isset($result['prix_essence'])){
            return floatval($result['prix_essence']);
        }
        return 5000; 
    }

}