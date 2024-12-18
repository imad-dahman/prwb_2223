<?php

require_once "framework/Model.php";
require_once "Repartition.php";

class Repartition extends Model
{


    public function __construct(public int $operation,public int $user,public int $weight){}

    /* public static function getoperation(int  $operation) : Repartition|false {
         $query = self::execute("SELECT * FROM repartitions WHERE repartitions.operation=:id", ["id"=>$operation]);
         $data = $query->fetch(); // un seul résultat au maximum
         if ($query->rowCount() == 0) {
             return false;
         } else {
             return new Repartition(Operation::get_member_by_title($data["operation"]),
                 User::get_user_by_mail($data["user"]) , $data["weight"]);
         }
     }*/
    public static function getoperation(int  $operation,int $user) : Repartition|false {
        $query = self::execute("SELECT * FROM repartitions WHERE repartitions.operation=:id and repartitions.user=:idd", ["id"=>$operation,"idd"=>$user]);
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Repartition($data["operation"],
                $data["user"] , $data["weight"]);
        }
    }
    public function addrepartition():Repartition {
        if(self::getoperation($this->operation,$this->user))
            self::execute("UPDATE repartitions SET weight=:weight WHERE operation=:operation and user=:user",
                ["weight"=>$this->weight,
                    "operation"=>$this->operation,
                    "user"=>$this->user]);

        else
            self::execute("INSERT INTO repartitions(operation,user,weight)
                 values (:operation,:user,:weight)",
                ["operation"=>$this->operation,
                    "user"=>$this->user,
                    "weight"=>$this->weight]);
        $this->operation=Model::lastInsertId();
        return $this;
    }
    public static function adderepartition(int $operation,int $user,int $weight):void {
        if(self::getoperation($operation,$user))
            self::execute("UPDATE `repartitions` SET weight=:weight WHERE repartitions.operation=:operation and repartitions.user=:user",
                ["weight"=>$weight,
                    "operation"=>$operation,
                    "user"=>$user]);
        else
            self::execute("INSERT INTO repartitions(operation,user,weight)
                 values (:operation,:user,:weight)",
                ["operation"=>$operation,
                    "user"=>$user,
                    "weight"=>$weight]);
    }
    public static function validateWeight($weight): array {
        $errors = [];

        if(!is_numeric($weight)) {
            $errors[]= "Le poids doit être numérique";
        }
       if(empty($weight)) {
            $errors[]= "Le poids ne peut pas être vide";
        }
        else if($weight <= 0) {
            $errors[]= "Le poids doit être supérieur à zéro";
        }
        return $errors;
    }
    public static function getid(int $id):array{
        $query = self::execute("SELECT * FROM repartitions WHERE repartitions.operation=:id ", ["id"=>$id]);

        return $query->fetchAll();
    }


    public static    function deleteRes(int $idre):void{
        self::execute("DELETE FROM `repartitions` WHERE repartitions.operation=:id",["id"=>$idre]);
    }


    public static    function deleteRe(int $idre):void{
        self::execute("DELETE FROM `repartitions` WHERE repartitions.operation=:id",["id"=>$idre]);
        self::execute("DELETE FROM `operations` WHERE operations.id=:id",["id"=>$idre]);


    }
}