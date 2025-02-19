<?php
class Database
{
    private static ?PDO $BDD = null;
    private function __construct() {}
    private static function getConfig(): array {
        $config = (require "./models/config.php");
        return $config;
    }
    public static function getInstance(){
        if(is_null(self::$BDD)){
            $config = self::getConfig();
            self::$BDD = new PDO($config["dsn"],$config["user"],$config["password"]);
            
        }
        return self::$BDD;
    }
}