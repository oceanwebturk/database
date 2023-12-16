<?php 
namespace OceanWT\Database;

use OceanWT\Config;

class DB 
{
 
 use \OceanWT\Traits\Macro;

 /**
  * @var object
  */
 public static $connect;
  
 /**
  * @var array
  */
 public static $defaultConnect=[],$connections=[],$wheres=[];
 
 /**
  * @var string
  */
 public static $table,$sql,$select="*";
 
 public function __construct()
 {
  self::$connections=array_merge(self::$connections,Config::get("database")->connections);
  self::$defaultConnect=self::$connections[Config::get("database")->default];
  self::$connect=self::initDriver(self::$defaultConnect['driver'],'Connect',self::$defaultConnect);
 }

 /**
  * @param array  $config
  * @param string $name 
  */
 public static function addConnection(array $config,string $name='default'){
  self::$connections[$name]=$config;
  return new self;
 }
 
 /**
  * @param  string $name
  */
 public function connect(string $name)
 {
  if(isset(self::$connections[$name])){
    self::$defaultConnect=self::$connections[$name];
  }
  return new self;
 }

 /**
  * @param  string $driver
  * @param  string $class
  * @param  array  $params
  */
 public static function initDriver(string $driver,string $class,array $params)
 {
  $className=__NAMESPACE__.'\\'.ucfirst($driver).'\\'.$class;
  if(class_exists($className)){
   $class=new $className($params);
   return $class;   
  }
 }
 
 /**
  * @param  string $name
  */
 public static function table(string $name)
 {
  self::$table=self::$defaultConnect['prefix'].$name;
  return new self;
 }
 
 /**
  * @param  string $name
  */
 public static function select(string $name)
 {
  self::$select=self::$defaultConnect['prefix'].$name;
  return new self;
 }

 public static function where($column,$value,string $operation = '=')
 {
  self::$wheres[]=' '.$column." ".$operation." ".$value;
  return new self;
 }


 /**
  * @return string
  */
 private static function prepareSql(){
  self::$sql=sprintf("SELECT %s FROM %s ",self::$select,self::$table);
  if(self::$wheres){
   self::$sql.='WHERE '.implode(" && ",self::$wheres);
  }
 }
 
 public static function get()
 {
  self::prepareSql();
  return self::$connect->get(self::$sql);
 }
 
 public static function first()
 {
  return self::get()[0];
 }

 public function __destruct(){
  if(method_exists(self::$connect,'close')){
   self::$connect->close();
  } 
 }
}
