<?php 

namespace OceanWT\Database\Mysql;

use PDO;

class Connect extends PDO
{
 use \OceanWT\Traits\Macro;
 /**
  * @var \PDO
  */
 public $db;

 /**
  * @param array $data
  */
 public function __construct(array $data=[])
 {
  try {
   $options=array_merge($data['options'],[
   PDO::ATTR_PERSISTENT=>true,
   ]);
  $this->db=parent::__construct("mysql:host=".$data["host"].";".(isset($data['port']) ? 'port='.$data['port'].';' : '')."dbname=".$data['database'].";charset=".(isset($data['charset']) ? $data['charset'] : 'utf8').';',$data['user'],$data['password'],$options);
 } catch (PDOException $e) {
              
  }
 }
 
 public function get($sql)
 {
  $sql=$this->prepare($sql);
  $sql->execute([]);
  return $sql->fetchAll(PDO::FETCH_OBJ);
 }

 /**
  * @return void
  */
 public function close()
 {
  $this->db=null;
 }

}
