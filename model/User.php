<?php

require_once "framework/Model.php";
require_once "User.php";

enum Role{
    case user;
    case admin;

}

class User extends Model
{

    public function __construct(public int $id,public string $mail, public string $hashed_password, public string $full_name , public string $role="",public ?string $iban=NULL) {
    }



    public static function validate_login(string $pseudo, string $password) : array {
        $errors = [];
        $user = User::get_user_by_mail($pseudo);
        if ($user) {
            if (!self::check_password($password, $user->hashed_password)) {
                $errors[] = "Wrong password. Please try again.";
            }
        } else {
            $errors[] = "Can't find a member with the pseudo '$pseudo'. Please sign up.";
        }
        return $errors;
    }

    private static function check_password(string $clear_password, string $hash) : bool {
        return $hash === Tools::my_hash($clear_password);
    }

    public static function get_users(int $idTricount):array|false
    {
        $query = self::execute("SELECT users.* from users,tricounts WHERE users.id != tricounts.creator AND tricounts.id=:idTricount AND users.id NOT IN ( SELECT subscriptions.user from users,subscriptions,tricounts WHERE subscriptions.user != users.id AND tricounts.creator!= users.id AND subscriptions.tricount=:idTricount AND tricounts.id =:idTricount) order by users.full_name asc "
            ,["idTricount"=>$idTricount]);
        return $query->fetchAll();
    }



    public static function get_userForOperation(string $name,int $idTricount): string
    {
        $query = self::execute("select u.* from operations o ,users u WHERE o.initiator = u.id AND o.tricount=:idTricount AND o.title=:nom",["nom"=>$name,"idTricount"=>$idTricount]);
        $data= $query->fetch();
        return $data["full_name"];
    }
    public static function get_userforid(string $name): int
    {
        $query=self::execute("SELECT users.id FROM users WHERE users.full_name=:name",["name"=>$name]);
        $data=$query->fetch();
        return $data["id"];
    }
    public static function get_user_by_mail(string $mail) : User|false {
        $query=self::execute("SELECT * FROM users where users.mail=:mail", ["mail"=>$mail]);
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["id"],$data["mail"], $data["hashed_password"], $data["full_name"],$data["role"], $data["iban"]);
        }
    }
    public static function get_user_by_name(string $name) : User|false {
        $query = self::execute("SELECT * FROM users where full_name=:name", ["name"=>$name]);
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["id"],$data["mail"], $data["hashed_password"], $data["full_name"],$data["role"], $data["iban"]);
        }
    }
    public static function getuseroperation(int $id): string
    {
        $query=self::execute("SELECT users.full_name from operations,users 
                       WHERE operations.initiator=users.id AND operations.id=:id",["id"=>$id]);
        $date=$query->fetch();
        return $date["full_name"];
    }


    public static function get_member_by_mail(string $mail) : User|false {
        $query = self::execute("SELECT * FROM users where mail=:mail", ["mail"=>$mail]);
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["id"],$data["mail"], $data["hashed_password"], $data["full_name"],$data["role"], $data["iban"]);
        }
    }
    public static function get_member_id(int $id) : User|false {
        $query = self::execute("SELECT * FROM users where id=:id", ["id"=>$id]);
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["id"],$data["mail"], $data["hashed_password"], $data["full_name"],$data["role"], $data["iban"]);
        }
    }
    private static function validate_password(string $hashed_password) : array {
        $errors = [];
        if (strlen($hashed_password) < 8 || strlen($hashed_password) > 16) {
            $errors[] = "Password length must be between 8 and 16.";
        } if (!((preg_match("/[A-Z]/", $hashed_password)) && preg_match("/\d/", $hashed_password) && preg_match("/['\";:,.\/?!\\-]/", $hashed_password))) {
            $errors[] = "Password must contain one uppercase letter, one number and one punctuation mark.";
        }
        return $errors;
    }
    public static function validate_passwords(string $hashed_password, string $password_confirm) : array {
        $errors = User::validate_password($hashed_password);
        if ($hashed_password != $password_confirm) {
            $errors[] = "You have to enter twice the same password.";
        }
        return $errors;
    }
    public static function validate_passwordds(string $password, string $hashed_password) : array {
        $errors = [];
        if ($password!=$hashed_password) {
            $errors[] = "Your password is incorrect";
        }
        return $errors;
    }
    public static function validate_pass(string $password, string $hashed_password) : bool {
        $res=false;
        if ($password!=$hashed_password) {
            $res=true;
        }
        return $res;
    }





    public function persist():User {
        if(self::get_member_id($this->id))
            self::execute("UPDATE Users SET mail=:mail,iban=:iban, hashed_password=:hashed_password,full_name=:full_name WHERE id=:id",
                ["mail"=>$this->mail,
                    "iban"=>$this->iban,
                    "hashed_password"=>$this->hashed_password,
                    //"hashed_password"=>Tools::my_hash($this->hashed_password),
                    "full_name"=>$this->full_name,
                    "id"=>$this->id]);

        else
            self::execute("INSERT INTO Users(mail,hashed_password,full_name,role,iban)
                 values (:mail,:hashed_password,:full_name,:role,:iban)",
                ["mail"=>$this->mail, "hashed_password"=>$this->hashed_password,"full_name"=>$this->full_name,"role"=>$this->role,"iban"=>$this->iban]);
        return $this;
    }
    public static function get_total_paid_by_me(int $idParticipant,int $idTricount):float
    {
        $query = self::execute("SELECT sum(operations.amount)  FROM operations WHERE operations.initiator = :idParticipant 
                                                 AND operations.tricount=:idTricount",["idParticipant"=>$idParticipant,"idTricount"=>$idTricount]);
        $data = $query->fetch();
        if (is_null($data[0]))
            return 0;
        return $data[0];
    }
    public static function get_Total(int $id,array $operations):float|null
    {
        $somme=0;
        foreach ($operations as $operation)
        {
            $query=self::execute("select sum(repartitions.weight) from operations,repartitions where repartitions.operation=operations.id and operations.id=:idOp",["idOp"=>$operation["id"]]);
            $sommeWeight = $query->fetch();
            $query1 = self::execute("SELECT repartitions.weight FROM repartitions,users
        WHERE repartitions.user=users.id and repartitions.operation=:IdOp and repartitions.user=:idUser",["IdOp"=>$operation["id"],"idUser"=>$id]);
            $weight=$query1->fetch();
            $amount = $operation["amount"];
            if (isset($sommeWeight[0])&&isset($weight[0]))
            {
                $resultat=$amount*$weight[0]/$sommeWeight[0];
                $somme=$somme+$resultat;
            }

        }

        return $somme;
    }
    public static function existemail(int $id,string $mail):bool
    {
        $query = self::execute("SELECT * FROM users where users.mail=:mail and users.id!=:id",["id"=>$id,"mail"=>$mail]);
        $query->fetch();
        if ($query->rowCount()!=0)
            return true;
        return false;

    }
    public static function existname(int $id,string $full_name):bool
    {
        $query = self::execute("SELECT * FROM users where users.full_name=:full_name and users.id!=:id",["id"=>$id,"full_name"=>$full_name]);
        $query->fetch();
        if ($query->rowCount()!=0)
            return true;
        return false;

    }





    public static function get_userse():array|false
    {
        $query=self::execute("select * from users ORDER BY `users`.`full_name` ASC ",[]);
        return $query->fetchAll();
    }

    public static function getparti(int $id):array|false{
        $query=self::execute("SELECT users.full_name FROM users,tricounts,subscriptions WHERE 
                                                              tricounts.id=subscriptions.tricount AND 
                                                              users.id=subscriptions.user AND 
                                                              tricounts.id=:id ORDER BY `users`.`full_name` ASC",["id"=>$id]);
        return $query->fetchAll();
    }

    ////////////////////////////////////////////////////////////////
    public static function getname(int $id,int $idd):string{
        $query=self::execute("SELECT users.full_name FROM operations,users 
                       WHERE operations.initiator=users.id 
                         and operations.id=:id 
                         and operations.tricount=:idd",["id"=>$id,"idd"=>$idd]);
        $date=$query->fetch();
        return $date["full_name"];
    }
    public static function validate_passs(string $password, string $hashed_password) : bool {
        $res=false;
        if ($password==$hashed_password) {
            $res=true;
        }
        return $res;
    }

    public static function validate_passwor(string $password, string $hashed_password) : array {
        $errors = [];
        if ($password==$hashed_password) {
            $errors[] = "le nouveau mdp doit être différent du précédent";
        }
        return $errors;
    }

    public static function validate_unicity(string $mail) : array {
        $errors = [];
        $user = self::get_member_by_mail($mail);
        if ($user) {
            $errors[] = "This user already exists.";
        }
        return $errors;
    }

    public static function validet(int $id,string $full_name):array{
        $errors = [];

        if (!strlen($full_name)>0) {
            $errors[]="name is required";
        }
        if (!(strlen($full_name)>=3 && strlen($full_name)<=20)) {
            $errors[] = "Name length must be between 3 and 16.";
        }
        if (!(preg_match("/^[a-zA-ZÀ-ÖØ-öø-ÿ'][a-zA-ZÀ-ÖØ-öø-ÿ0-9 ']*$/", $full_name))) {
            $errors[] = "title must start by a letter and must contain only letters, numbers, and spaces.";
        }
        return $errors;
    }
    public static  function isValidIban(String $iban):array
    {
        $errors=[];
        $Countries = array(
            'al'=>28,'ad'=>24,'at'=>20,'az'=>28,'bh'=>22,'be'=>16,'ba'=>20,'br'=>29,'bg'=>22,'cr'=>21,'hr'=>21,'cy'=>28,'cz'=>24,
            'dk'=>18,'do'=>28,'ee'=>20,'fo'=>18,'fi'=>18,'fr'=>27,'ge'=>22,'de'=>22,'gi'=>23,'gr'=>27,'gl'=>18,'gt'=>28,'hu'=>28,
            'is'=>26,'ie'=>22,'il'=>23,'it'=>27,'jo'=>30,'kz'=>20,'kw'=>30,'lv'=>21,'lb'=>28,'li'=>21,'lt'=>20,'lu'=>20,'mk'=>19,
            'mt'=>31,'mr'=>27,'mu'=>30,'mc'=>27,'md'=>24,'me'=>22,'nl'=>18,'no'=>15,'pk'=>24,'ps'=>29,'pl'=>28,'pt'=>25,'qa'=>29,
            'ro'=>24,'sm'=>27,'sa'=>24,'rs'=>22,'sk'=>24,'si'=>19,'es'=>24,'se'=>24,'ch'=>21,'tn'=>24,'tr'=>26,'ae'=>23,'gb'=>22,'vg'=>24
        );
        $iban=preg_replace('/[^a-zA-Z0-9]/','',$iban);
        $pays=substr($iban,0,2);
        if(!ctype_alpha($pays) && strlen($iban)!=0){
            $errors[]="2 first characters are not letters";
        }
        if (strlen($iban)!=0){
            if(array_key_exists(strtolower($pays),$Countries)){
                if(strlen($iban)!= $Countries[ strtolower(substr($iban,0,2))]){
                    $errors[]= "wrong IBAN size";
                }
            }
            else{
                $errors[]= "unknown country";
            }
        }
        return $errors;
    }
    public static function isValidemail(int $id,string $mail):array{
        $errors= [];
        if(self::existemail($id,$mail)){
            $errors [] ="mail existe deja";
        }
        if(!preg_match("/^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$/",$mail)){
            $errors[]= "this mail is not valide";
        }
        return $errors;
    }
    public static function isValidemaile(string $mail):array{
        $errors= [];

        if(!preg_match("/^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$/",$mail)){
            $errors[]= "this mail is not valide";
        }
        return $errors;
    }



    public function validate():array{
        $errors=[];
        if(!strlen($this->full_name)>0){
            $errors[]="name is required";
        }
        if(!(strlen($this->full_name)>=3 && strlen($this->full_name)<=20)){
            $errors[] = "Name length must be between 3 and 16.";
        }
        if (!(preg_match("/^[a-zA-Z][a-zA-Z0-9 ]*$/", $this->full_name))) {
            $errors[] = "Name must start by a letter and must contain only letters and numbers.";
        }
        return $errors;
    }

    public static function  get_NotParticipTricounts_by_mail(string $mail) : array {
        $query = self::execute("select * 
from tricounts 
where id not in (
    SELECT tricounts.id FROM tricounts, users WHERE tricounts.creator = users.id AND users.mail = :mail
) AND id not IN (
SELECT tricounts.id FROM tricounts, users ,subscriptions WHERE subscriptions.user = users.id AND tricounts.creator != subscriptions.user AND users.mail =:mail AND tricounts.id = subscriptions.tricount
)
order by title",["mail"=>$mail]);
        $res = [];
        $data =  $query->fetchAll();
        foreach ($data as $row)
        {
            $res[] = new Tricount($row["title"],$row["created_at"],$row["creator"],$row["description"],$row["id"]);
        }
        return $res;
    }
}



