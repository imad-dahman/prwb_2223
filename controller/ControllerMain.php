<?php

require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerMain extends Controller
{

    public function index(): void
    {
        $pseudo = '';
        $password = '';
        $errors = [];
        if ($this->user_logged()) {
            $this->redirect("user", "tricounts");
        } else {
            (new View("login"))->show(["mail" => $pseudo, "password" => $password, "errors" => $errors]);
        }
    }

    public function login() : void {
        $pseudo = '';
        $password = '';
        $errors = [];
        if (isset($_POST['mail']) && isset($_POST['password'])) {
            $pseudo = $_POST['mail'];
            $password = $_POST['password'];

            $errors = User::validate_login($pseudo, $password);
            if (empty($errors)) {
                $this->log_user(User::get_user_by_mail($pseudo));
            }
        }
        (new View("login"))->show(["mail" => $pseudo, "password" => $password, "errors" => $errors]);
    }

    public function signup() : void{

        $mail='';
        $full_name='';
        $iban='';
        $hashed_password='';
        $password_confirm = '';
        $errors = [];
        $id=0;
        if(isset($_POST['mail']) && isset($_POST['full_name']) && isset($_POST['iban']) && isset($_POST['hashed_password']) && isset($_POST['password_confirm'])) {
            $mail=trim($_POST['mail']);
            $full_name=$_POST['full_name'];
            $iban=$_POST['iban'];
            $hashed_password=$_POST['hashed_password'];
            $password_confirm=$_POST['password_confirm'];

            $user= new User($id,$mail,Tools::my_hash($hashed_password),$full_name,"user",$iban);
            $errors=User::validate_unicity($mail);
            $errors=array_merge($errors,User::isValidemaile($mail));

            $errors=array_merge($errors,User::isValidIban($iban));
            $errors = array_merge($errors, $user->validate());
            $errors=array_merge($errors, User::validate_passwords($hashed_password, $password_confirm));

            if (count($errors) == 0) {
                $user->persist(); //sauve l'utilisateur
                //$this->redirect("Main","login");
                $this->log_user(User::get_user_by_mail($mail));
            }

            //  (new View("signup"))->show(["mail"=>$mail,"full_name"=>$full_name,"iban"=>$iban,"hashed_password"=>$hashed_password,"password_confirm"=>$password_confirm,"errors"=>$errors]);

        }
        (new View("signup"))->show(["mail"=>$mail,"full_name"=>$full_name,"iban"=>$iban,"hashed_password"=>$hashed_password,"password_confirm"=>$password_confirm,"errors"=>$errors]);
    }
}