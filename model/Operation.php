<?php

require_once "framework/Model.php";
require_once "Operation.php";
class Operation extends Model
{

    public function __construct(public string $title ,public int $tricount , public float $amount,public string $operation_date,public int $initiator,public string $created_at,public ?int $id=null) {}

    public static function get_operation_by_tricount(int $id):array
    {
        $query = self::execute("SELECT * from operations WHERE operations.tricount=:idTricount",["idTricount"=>$id]);
        return $query->fetchAll();
    }
    public function persist():Operation {
        if(self::get_id($this->id))
            self::execute("UPDATE Operations SET title=:title, amount=:amount,initiator=:initiator,operation_date=:operation_date WHERE id=:id",
                ["id"=>$this->id,
                    "title"=>$this->title,
                    "initiator"=>$this->initiator,
                    "amount"=>$this->amount,
                    "operation_date"=>$this->operation_date]);

        else
            self::execute("INSERT INTO operations(title,tricount,amount,operation_date,initiator,created_at)
                 values (:title,:tricount,:amount,:operation_date,:initiator,:created_at)",
                ["title"=>$this->title,
                    "tricount"=>$this->tricount,
                    "amount"=>$this->amount,
                    "operation_date"=>$this->operation_date,
                    "initiator"=>$this->initiator,"created_at"=>$this->created_at]);


        $this->id=Model::lastInsertId();
        return $this;
    }
    public function persist2():Operation {
        if(self::get_id($this->id))
            self::execute("UPDATE Operations SET amount=:amount WHERE id=:id",
                ["id"=>$this->id,
                    "amount"=>$this->amount]);
        return $this;
    }



    public static function CheckInsertOperation(string $title) : array {
        $errors = [];
        if ($title == null || $title=="") {
            $errors[] = "Please try again";
        }
        return $errors;
    }

    public static function get_member_by_title(string $title) : Operation|false {
        $query = self::execute("SELECT * FROM `operations` WHERE operations.title=:title;", ["title"=>$title]);
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Operation($data["title"], $data["tricount"], $data["amount"],$data["operation_date"], $data["initiator"], $data["created_at"],$data["id"]);
        }
    }
    public static function get_id(int $id) : Operation|false {
        $query = self::execute("SELECT * FROM `operations` WHERE operations.id=:id;", ["id"=>$id]);
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Operation($data["title"], $data["tricount"], $data["amount"],$data["operation_date"], $data["initiator"], $data["created_at"],$data["id"]);
        }
    }

    public static function getname():array{
        $operations=[];
        $query=self::execute("Select full_name From users");
        $data=$query->fetch($query);
        while ($row=$data){
            $operations[]=$row;
        }
        return $operations;
    }
    public static function operationamount(int $id):array{
        $query= self::execute("SELECT operations.amount,operations.operation_date,operations.* FROM tricounts,operations 
                                                   WHERE tricounts.id=operations.tricount and operations.id=:id",["id"=>$id]);
        return $query->fetchAll();
    }

    public static function getoperations(int $id):array{
        $query= self::execute("SELECT operations.* FROM operations 
                                                   WHERE  operations.tricount=:id ORDER BY operations.operation_date ASC, operations.id desc",["id"=>$id]);
        return $query->fetchAll();
    }
    public static function getcurrentindex(array $operations,Operation $operation):int {
        $result=0;
        for($i=0;$i<sizeof($operations);++$i){
            if($operations[$i]['id'] == $operation->id){
                $result =  $i;

            }
        }
        return $result;
    }
    public static function getid(int $id) :array{
        $query = self::execute("SELECT * FROM `operations` WHERE operations.id=:id;", ["id"=>$id]);
        return $query->fetchAll();
    }
    public static function opertricount(int $id): int{
        $query=self::execute("SELECT count(operations.tricount) from operations WHERE operations.tricount=:id",["id"=>$id]);
        $date= $query->fetch();
        return $date[0];
    }
    public static function idinititor(int $id):int {
        $query=self::execute("SELECT operations.initiator FROM operations WHERE operations.id=:id",["id"=>$id]);
        $date=$query->fetch();
        return $date[0];
    }
    public static function getuseroperation(int $id): array|false
    {
        $query=self::execute("SELECT users.* from operations,users 
                       WHERE operations.initiator=users.id AND operations.id=:id",["id"=>$id]);

        return $query->fetchAll();;
    }
    public static function operationini(int $id):int  {
        $query=self::execute("SELECT COUNT(repartitions.operation) from repartitions,operations 
                                     WHERE repartitions.operation=operations.id and operations.id=:id
                                     group by repartitions.operation",["id"=>$id]);
        $data= $query->fetch();
        if(empty($data[0]))
            return false;
        else
            return $data[0];
    }
    public static function creator(int $id):string{
        $query=self::execute("SELECT users.full_name FROM tricounts,users 
                       WHERE tricounts.creator=users.id
                         and tricounts.id=:id",["id"=>$id]);
        $date=$query->fetch();
        return $date["full_name"];
    }
    public static function ititator(int $id):int{
        $query=self::execute("SELECT operations.tricount FROM operations 
                           WHERE operations.id=:id",["id"=>$id]);
        $data= $query->fetch();
        return $data[0];
    }
    public static function idtricount(int $id):int{
        $query=self::execute("SELECT tricounts.id FROM tricounts,operations WHERE tricounts.id=operations.tricount and operations.id=:id",["id"=>$id]);
        $data= $query->fetch();
        return $data[0];
    }


    public static function getrepartions(int $id): array
    {
        $query=self::execute("SELECT users.full_name,repartitions.weight FROM repartitions,users 
                                                                  WHERE repartitions.user=users.id and repartitions.operation=:id",["id"=>$id]);
        return $query->fetchAll();
    }


    public static function get_operation(string $name): int
    {
        $query=self::execute("SELECT operations.id FROM `operations`WHERE operations.title=:name",["name"=>$name]);
        $data=$query->fetch();
        return $data["id"];
    }


    public static function operti(int $id): array
    {
        $query=self::execute("SELECT  sum(repartitions.weight) FROM operations,repartitions 
                                                                              WHERE repartitions.operation=operations.id 
                                                                                and operations.id=:id",["id"=>$id]);
        return $query->fetchAll();

    }
    //////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function validateAmountt($amount): array {
        $errors = [];
        if(empty($amount) || $amount < 0) {
            $errors[] = "Le montant ne peut pas être vide ou négatif.";
        }
        return $errors;
    }

    public function validateTitle($id,$title,$x): array {
        $errors = [];
        if(self::Check_Edit_operations($id,$title,$x))
            $errors []= "existe déja";
        if (!strlen($title) > 0) {
            $errors[] = "Le titre est requis.";
        } elseif (!(strlen($title) >= 3 && strlen($title) <= 30)) {
            $errors[] = "La longueur du titre doit être comprise entre 3 et 30.";
        }
        elseif (!(preg_match("/^[a-zA-ZÀ-ÖØ-öø-ÿ][a-zA-ZÀ-ÖØ-öø-ÿ0-9 ]*$/", $this->title))) {
            $errors[] = "title must start by a letter and must contain only letters, numbers, and spaces.";
        }

        return $errors;
    }


    public function validate(int $id,string $title) : array {
        $errors = [];
        if (self::Check_exist_operations($id,$title) )
            $errors[] = "existe déja";
        if (!strlen($this->title) > 0) {
            $errors[] = "title is required.";
        }elseif(!(strlen($this->title) >= 3 && strlen($this->title) <= 16)) {
            $errors[] = "title length must be between 3 and 16.";
        } elseif (!(preg_match("/^[a-zA-Z][a-zA-Z0-9 ]*$/", $this->title))) {
            $errors[] = "title must start by a letter and must contain only letters and numbers.";
        }
        return $errors;
    }
    public function validatee(string $title) : array {
        $errors = [];
        if (!strlen($this->title) > 0) {
            $errors[] = "title is required.";
        }elseif(!(strlen($this->title) >= 3 )) {
            $errors[] = "title length must be between";
        } elseif (!(preg_match("/^[\p{L}0-9 ]+$/u", $this->title))) {

            $errors[] = "title must start by a letter and must contain only letters and numbers.";
        }
        return $errors;
    }
    public function validateoperation_date($operation_date)
    {
        $errors = [];
        if(empty($operation_date)) {
            $errors[] = "Date of operation is required";
        }
        return $errors;
    }

    public static function validationcheckbox(int $x){
        $errors = [];
        if($x == 0){
            $errors [] ="At least one person must be selected.";
        }
        return $errors;
    }




    public static  function validateamount(float $amount) : array {
        $errors=[];
        if(empty($amount)){
            $errors[] = "entrer un montant valide";
        }
        elseif ($amount < 0) {
            $errors[] = "amount negatif.";
        }
        elseif ($amount == 0) {
            $errors[] = "entrer un montant";

        }
        return $errors;
    }

    public static function getnbrrepation(int $id):array{
        $query=self::execute("SELECT * FROM repartitions WHERE repartitions.operation=:id",["id"=>$id]);
        return $query->fetchAll();
    }
    public static function  Check_exist_operations(int $id,string $title) : bool {
             $query = self::execute("SELECT operations.* FROM tricounts,operations 
                    WHERE operations.tricount= tricounts.id and tricounts.id=:id and operations.title=:title",
                 ["id"=> $id,"title"=>$title]);
             $query->fetchAll();
             if ($query->rowCount()==0 ){
                 return false;
             }

             else{
                 return true;
             }

    }

 public static function  Check_Edit_operations(int $idtricount,string $title,int $id) : bool {
           $operations = self::get_member_by_title($title);
           $res=false;
           if ($operations!=false)
           {
               $query = self::execute("SELECT operations.* FROM tricounts,operations
                    WHERE operations.tricount= tricounts.id and tricounts.id=:idtricount and operations.title =:title and operations.id!=:id",
                   ["idtricount"=> $idtricount,"title"=>$title,"id"=>$id]);
               $query->fetchAll();
               if ($query->rowCount()==0)
                   $res= false;
               else
                   $res= true;
           }
           return $res;
    }
    public static function getoperations2(int $id):array{
        $query= self::execute("SELECT operations.* FROM operations 
                                                   WHERE  operations.tricount=:id ORDER BY operations.operation_date ASC, operations.id desc",["id"=>$id]);
        $res = [];
        $data= $query->fetchAll();
        foreach ($data as $row){
            $res[] = new Operation($row["title"],$row["tricount"],$row["amount"],$row["operation_date"],$row["initiator"],$row["created_at"],$row["id"]);
        }
        return $res;
    }
    public static function updateAmount(int $newAmount, int $id): void {
        $amount = $newAmount;

        self::execute("UPDATE Operations SET amount = :amount WHERE id = :id",
            [
                "amount" => $amount,
                "id" => $id
            ]
        );
    }


}