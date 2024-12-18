<?php

require_once "framework/Model.php";
require_once "Tricount.php";
require_once  "Operation.php";

class Tricount extends Model
{



    public function __construct(public string $title,public string $created_at,public int $creator,public ?string $description="",public ?int $id=null ) {}

    public static function get_operation_by_tricount(int $id):array
    {
        $query = self::execute("SELECT * from operations WHERE operations.tricount=:idTricount order by operations.operation_date desc",["idTricount"=>$id]);
        return $query->fetchAll();
    }
    public static function get_operation_by_tricount2(int $id):array
    {
        $query = self::execute("SELECT * from operations WHERE operations.tricount=:idTricount ",["idTricount"=>$id]);
        $data =$query->fetchAll();
        $res = [];
        foreach ($data as $row){
            $res [] = new Operation($row["title"],$row["tricount"],$row["amount"],$row["operation_date"],$row["initiator"],$row["created_at"],$row["id"]);
        }
        return $res;
    }
    public static function get_repartitionTemplates_by_tricount(int $id):array
    {

        $query = self::execute("SELECT * from repartition_templates WHERE repartition_templates.tricount=:idTricount",["idTricount"=>$id]);
        return $query->fetchAll();
    }

    public static function expenses_by_tricount(int $id):bool
    {
        $query=self::execute("select * from operations where operations.tricount =:idTricount ",["idTricount"=>$id]);
        if ($query->rowCount()==0)
            return false;
        else
            return true;
    }
    public static function operationamount(int $id):array
    {
        $query= self::execute("SELECT tricounts.* FROM tricounts,operations 
                                                   WHERE tricounts.id=operations.tricount and operations.id=:id",["id"=>$id]);
        return $query->fetchAll();
    }

    public static function participation_by_tricount(int $id):bool
    {
        $query=self::execute("select * from subscriptions where subscriptions.tricount =:idTricount ",["idTricount"=>$id]);
        $query->fetchAll();
        if ($query->rowCount()==1)
            return false;
        else
            return true;
    }
    public static function checkCreator(int $id,int $user):bool
    {
        $query = self::execute("SELECT * FROM tricounts,subscriptions WHERE tricounts.id =:idTricount AND subscriptions.tricount=tricounts.id AND subscriptions.user=:idUser",["idTricount"=>$id,"idUser"=>$user]);
        $query->fetch();
        if ($query->rowCount()!=0)
            return true;
        return false;

    }
    public static function  get_Participants_In_Operations(int $id,string $name) : bool
    {
        $query = self::execute("SELECT DISTINCT users.full_name FROM repartitions,users,operations,tricounts WHERE repartitions.user=users.id AND repartitions.operation = operations.id AND operations.tricount =tricounts.id AND tricounts.id = :id and users.full_name=:name",["id"=>$id,"name"=>$name]);
        $query->fetchAll();
        if ($query->rowCount() == 0) {
            return true;
        } else {
            return false;
        }

    }

    public static function  get_tricounts_by_mail(string $mail) : array {
        $query = self::execute("SELECT tricounts.* FROM tricounts, users WHERE tricounts.creator = users.id AND users.mail = :mail ORDER by created_at DESC",
            ["mail"=> $mail]);
        $query1 = self::execute("SELECT tricounts.* FROM tricounts, users ,subscriptions WHERE subscriptions.user = users.id AND tricounts.creator != subscriptions.user AND users.mail =:mail AND tricounts.id = subscriptions.tricount ORDER by created_at DESC",
            ["mail"=> $mail]);
       $res =  array_merge($query->fetchAll(),$query1->fetchAll());
       return $res;
    }
    public static function  get_participants_by_tricount(int $id) : int {
        $query = self::execute("SELECT COUNT(user)-1 FROM subscriptions WHERE subscriptions.tricount=:id ",["id"=>$id]);
        $data= $query->fetch();
        return $data[0];
    }
    public static function  last_id () : int {
        $query = self::execute("SELECT MAX(tricounts.id) FROM tricounts;",[]);
        $data= $query->fetch();
        return $data[0];
    }

    public static function  get_tricount_by_id(int $id) : Tricount|false {
        $query = self::execute("SELECT tricounts.* FROM tricounts where tricounts.id =:id",["id"=>$id]);
       $data=$query->fetch();;
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Tricount($data["title"],$data["created_at"],$data["creator"],$data["description"],$data["id"]);
        }
    }
    public static function  get_tricount_by_Title(string $title) : Tricount|false {

        $query = self::execute("SELECT tricounts.* FROM tricounts where tricounts.title =:title",["title"=>$title]);
        $data=$query->fetch();;
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Tricount($data["title"],$data["created_at"],$data["creator"],$data["description"],$data["id"]);
        }
    }
    public static function  get_tricount_by_Title_And_His_Creaotr(string $title,int $IdUser) : Tricount|false {

        $query = self::execute("SELECT tricounts.* FROM tricounts where tricounts.title =:title and tricounts.creator=:idUser",["title"=>$title,"idUser"=>$IdUser]);
        $data=$query->fetch();;
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Tricount($data["title"],$data["created_at"],$data["creator"],$data["description"],$data["id"]);
        }
    }
       public function getParticipantsByTricount():array|false
       {
           $query=self::execute("select users.*,tricounts.creator from subscriptions ,tricounts ,users WHERE subscriptions.tricount=tricounts.id AND users.id=subscriptions.user AND tricounts.id=:id ORDER by users.full_name",["id"=>$this->id]);
          return $query->fetchAll();
       }
    public  function get_creator():array|false
    {
        $query = self::execute("select users.*from tricounts ,users WHERE users.id = tricounts.creator and tricounts.id=:id;",["id"=>$this->id]);
        return $query->fetch();
    }
    public static function get_Total_Operations(int $id):float|null
    {
        $query = self::execute("SELECT ROUND(SUM(operations.amount),2) from operations WHERE operations.tricount =:id GROUP by operations.tricount",["id"=>$id]);
        $data= $query->fetch();
        if ($data==false)
            return null;

        return $data[0];

    }
    public static function  Check_exist_tricounts(int $idUser,string $title) : bool {
        $query = self::execute("SELECT tricounts.* FROM tricounts, users WHERE tricounts.creator = users.id AND tricounts.title = :title AND users.id = :id ORDER by created_at DESC",
            ["id"=> $idUser,"title"=>$title]);
        $query1 = self::execute("SELECT tricounts.* FROM tricounts, users ,subscriptions WHERE subscriptions.user = users.id and tricounts.title = :title AND tricounts.creator != subscriptions.user AND users.id =:id AND tricounts.id = subscriptions.tricount ORDER by created_at DESC",
            ["id"=> $idUser,"title"=>$title]);
        $res =  array_merge($query->fetchAll(),$query1->fetchAll());
        if ($query->rowCount()==0 && $query1->rowCount()==0)
            return false;
        else
            return true;
    }
    public static function  Check_exist_tricounts_for_Edit(int $idUser,string $title,int $idTricount) : bool {
        $query = self::execute("SELECT tricounts.* FROM tricounts, users WHERE tricounts.creator = users.id AND tricounts.id!=:TricountId AND tricounts.title = :title AND users.id = :id ORDER by created_at DESC",
            ["id"=> $idUser,"title"=>$title,"TricountId"=>$idTricount]);
        $query1 = self::execute("SELECT tricounts.* FROM tricounts, users ,subscriptions WHERE subscriptions.user = users.id and tricounts.title = :title AND tricounts.creator != subscriptions.user AND users.id =:id AND tricounts.id = subscriptions.tricount ORDER by created_at DESC",
            ["id"=> $idUser,"title"=>$title]);
        $res =  array_merge($query->fetchAll(),$query1->fetchAll());
        if ($query->rowCount()==0 && $query1->rowCount()==0)
            return false;
        else
            return true;
    }

    public static function CheckInsertTricount(string $title,string $description,int $idUser,string $oldDescription,string $newDescription) : array {
        $errors = [];

        if (self::Check_exist_tricounts($idUser,$title) && $oldDescription==$newDescription)
            $errors[] = "existe déja";
        if ($title == null || $title=="") {
                $errors[] = "Entrer un titre  ";
            }
        else if (strlen($title)<3)
        {
            $errors[] = "minimum 3 caractères title ";
        }
         if ($description!=null && strlen($description)<3)
        {
            $errors[] = "minimum 3 caractères description ";
        }
        return $errors;
    }

    public static function  Check_Edit_tricounts(int $idUser,string $title) : bool {

        $tricount = self::get_tricount_by_Title_And_His_Creaotr($title,$idUser);
        $res=false;
        if ($tricount!=false)
        {
            $query = self::execute("SELECT tricounts.* FROM tricounts, users WHERE tricounts.creator = users.id AND tricounts.id!=:CurrentId And tricounts.title = :title AND users.id = :id ORDER by created_at DESC",
                ["id"=> $idUser,"title"=>$title,"CurrentId"=>$tricount->id]);
            $query1 = self::execute("SELECT tricounts.* FROM tricounts, users ,subscriptions WHERE subscriptions.user = users.id and tricounts.title = :title AND tricounts.creator != subscriptions.user AND users.id =:id AND tricounts.id!=:CurrentId AND tricounts.id = subscriptions.tricount ORDER by created_at DESC",
                ["id"=> $idUser,"title"=>$title,"CurrentId"=>$tricount->id]);
            if ($query->rowCount()==0 && $query1->rowCount()==0)
                $res= false;
            else
                $res= true;
        }
        return $res;
    }

    public static function CheckCreatorTricount(string $title,int $creator):array
    {
        $errors = [];
        $res=true;
        $query = self::execute("SELECT * FROM `tricounts` WHERE tricounts.title =:Title AND tricounts.creator = :idCreator",["Title"=>$title,"idCreator"=>$creator]);
        $query->fetchAll();
        if ($query->rowCount()==0)
            $errors[] = "le créateur seul peut modifier le nom du tricount";

        return $errors;
    }
    public static function CheckCreatorTricountService(string $title,int $creator):bool
    {
        $res = true;
        $query = self::execute("SELECT * FROM `tricounts` WHERE tricounts.title =:Title AND tricounts.creator = :idCreator",["Title"=>$title,"idCreator"=>$creator]);
        $query->fetchAll();
        if ($query->rowCount()==0)
           $res=false;

        return $res;
    }

    public static function CheckEditTricount(string $title,string $description,int $idUser,int $idTricount) : array {
        $errors = [];

            if (self::Check_Edit_tricounts($idUser,$title) || self::Check_exist_tricounts_for_Edit($idUser,$title,$idTricount))
                $errors[] = "nom du tricount existe déja";
            if ($title == null || $title=="") {
                $errors[] = "Entrer un titre  ";
            }
            else if (strlen($title)<3)
            {
                $errors[] = "minimum 3 caractères title ";
            }
             if ($description!=null && strlen($description)<3)
            {
                $errors[] = "minimum 3 caractères description ";
            }

        return $errors;
    }
   /* public static function get_tricount_by_mail(string $mail) : Tricount|false {
        $query = self::execute("SELECT tricounts.* FROM tricounts, users WHERE tricounts.creator = users.id AND users.mail = :mail", ["mail"=>$mail]);
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Tricount($data["title"],$data["created_at"],$data["creator"],$data["description"]);
        }
    }*/

    /*private static function add_tricount(string $title, string $description,int $creator) : bool {
        return true;
    }*/
    public function persist() : Tricount {

        if(self::Check_exist_tricounts($this->creator,$this->title)||self::get_tricount_by_id($this->id))
            self::execute("UPDATE tricounts SET title=:title,description=:description WHERE id=:id",["title"=>$this->title,"description"=>$this->description,"id"=>$this->id]);
        else
            self::execute("INSERT INTO tricounts(title,description,creator) VALUES (:title,:description,:creator)", ["title"=>$this->title, "description"=>$this->description,"creator"=>$this->creator]);
        return $this;
    }


    public function delete(int $creator) : Tricount|false {
        if ($this->creator == $creator) {
            $operations=self::get_operation_by_tricount($this->id);
            foreach ($operations as $operation)
            {
                self::execute("DELETE FROM `repartitions` WHERE repartitions.operation=:id",["id"=>$operation["id"]]);
            }
            $repartitions = self::get_repartitionTemplates_by_tricount($this->id);
            foreach ($repartitions as $repartition)
            {
                self::execute("DELETE FROM `repartition_template_items` WHERE repartition_template_items.repartition_template=:id",["id"=>$repartition["id"]]);
            }
            self::execute('DELETE FROM repartition_templates WHERE repartition_templates.tricount=:id', ['id' => $this->id]);
            self::execute('DELETE FROM subscriptions WHERE subscriptions.tricount=:id', ['id' => $this->id]);
            self::execute('DELETE FROM subscriptions WHERE subscriptions.tricount=:id', ['id' => $this->id]);
            self::execute("DELETE FROM operations WHERE operations.tricount=:id", ['id' => $this->id]);
            self::execute("DELETE FROM tricounts WHERE tricounts.id=:id", ['id' => $this->id]);
            return $this;
       }
        return false;
    }
    public static function  get_tricounts_by_mail2(string $mail) : array {
        $query = self::execute("SELECT tricounts.* FROM tricounts, users WHERE tricounts.creator = users.id AND users.mail = :mail ORDER by created_at DESC",
            ["mail"=> $mail]);
        $query1 = self::execute("SELECT tricounts.* FROM tricounts, users ,subscriptions WHERE subscriptions.user = users.id AND tricounts.creator != subscriptions.user AND users.mail =:mail AND tricounts.id = subscriptions.tricount ORDER by created_at DESC",
            ["mail"=> $mail]);
        $res = [];
        $data =  $query->fetchAll();
        $date = $query1->fetchAll();
        foreach ($data as $row)
        {
            $res[] = new Tricount($row["title"],$row["created_at"],$row["creator"],$row["description"],$row["id"]);
        }
        foreach ($date as $row)
        {
            $res[] = new Tricount($row["title"],$row["created_at"],$row["creator"],$row["description"],$row["id"]);
        }
        return $res;
    }
}
