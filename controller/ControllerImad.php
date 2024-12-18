<?php
require_once 'model/User.php';
require_once 'model/Operation.php';
require_once 'model/Repartition.php';
require_once 'model/Participation.php';
require_once 'model/Tricount.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';
class ControllerImad extends Controller{

    public function index(): void
    {
        $user = $this->get_user_or_redirect();
        $users = User::get_userse();
        $participants="";
        $noparticipants="";
        if($user->role=="admin")
        {
            if (isset($_GET["param1"]) && is_numeric($_GET["param1"]))
            {
                $idUser = $_GET["param1"];
                $user=User::get_member_id($idUser);
                if ($user!=false)
                {
                    $users = User::get_userse();
                    $participants=Tricount::get_tricounts_by_mail($user->mail);
                    $noparticipants=User::get_NotParticipTricounts_by_mail($user->mail);
                    (new View("imad"))->show(["users"=>$users,"participants"=>$participants,"noparticipants"=>$noparticipants]);

                }
                else
                    (new View("imad"))->show(["users"=>$users,"participants"=>$participants,"noparticipants"=>$noparticipants]);
            }
            else
            {
                if(isset($_POST["users"]) && is_numeric($_POST["users"]))
                {
                    $idUser = $_POST["users"];
                    $this->redirect("Imad","index",$idUser);

                }
                else
                {
                    (new View("imad"))->show(["users"=>$users,"participants"=>$participants,"noparticipants"=>$noparticipants]);
                }
            }


        }
        else
            $this->redirect("main","logout");
    }

    public function add_services():void{
        $op=new Participation($_POST["idtr"],$_POST["idus"]);
        $op->persist();
        echo "true";
    }
}