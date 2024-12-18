<?php
require_once 'model/User.php';
require_once 'model/Tricount.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';


class ControllerUser extends Controller
{

    public function index(): void
    {

        $this->tricounts();

    }

    public function tricounts() : void {
        $user = $this->get_user_or_redirect();
        $tricounts = Tricount::get_tricounts_by_mail($user->mail);
        (new View("ListTricounts"))->show(["tricounts"=>$tricounts]);
    }
    public function changepassword() : void{

        $passwordd='';
        $new_password='';
        $confirm_password='';
        $errors =[];
        $user=$this->get_user_or_redirect();
        if (isset($_POST["new_password"]) && isset( $_POST["confirm_password"]) && isset($_POST["passwordd"]))
        {
            $passwordd=$_POST["passwordd"];

            $new_password=$_POST["new_password"];
            $confirm_password=$_POST["confirm_password"];
            $errors=array_merge($errors,User::validate_passwordds(Tools::my_hash($passwordd),$user->hashed_password));
            $errors=array_merge($errors,User::validate_passwor(Tools::my_hash($new_password),$user->hashed_password));
            $errors= array_merge($errors,User::validate_passwords($new_password, $confirm_password)) ;


            if (count($errors) == 0) {
                $user->hashed_password=Tools::my_hash($new_password);
                $user->persist();
                $this->log_user($user);
            }

        }
        (new View("changepassword"))->show(["new_password"=>$new_password,"passwordd"=>$passwordd,"confirm_password"=>$confirm_password,"errors"=>$errors]);
    }
    public function changepassword_verifer() : void {
        $res = "false";
        $user = $this->get_user_or_false();
        if(isset($_POST["passwordd"]) && $_POST["passwordd"] !== ""){
            $tricount = User::validate_pass(Tools::my_hash($_POST["passwordd"]),$user->hashed_password);
            if($tricount)
                $res =  "true";
        }
        echo $res;
    }
    public function email_available_service() : void {
        $res = "true";

        if(isset($_POST["param1"]) && $_POST["param1"] !== ""){
            $email = User::get_user_by_mail($_POST["param1"]);
            if($email){
                $res = "false";
            }
        }

        echo $res;
    }
    public function editprofil(): void{

        $errors=[];
        $user=$this->get_user_or_redirect();
        if(isset($_POST["name"]) && isset($_POST["email"]) && isset($_POST["Iban"])){
            $name=$_POST["name"];
            $email=$_POST["email"];
            $Iban=$_POST["Iban"];
            $errors=array_merge($errors,User::validet($user->id,$name));
            $errors=array_merge($errors,User::isValidemail($user->id,$email));
            $errors=array_merge($errors,User::isValidIban($Iban));
            if(count($errors)==0){
                $user->mail= $email;
                $user->iban= $Iban;
                $user->full_name= $name;
                $user->persist();
                $this->log_user($user);
            }
        }
        (new View("editprofil"))->show([
            "name" => $user->full_name,
            "email" => $user->mail,
            "Iban" => $user->iban,
            "errors" => $errors
        ]);
    }

    public function changepassword_verifers() : void {
        $res = "false";
        $user = $this->get_user_or_false();
        if(isset($_POST["new_password"]) && $_POST["new_password"] !== ""){
            $op = User::validate_passs(Tools::my_hash($_POST["new_password"]),$user->hashed_password);
            if($op)
                $res =  "true";
        }
        echo $res;
    }





}