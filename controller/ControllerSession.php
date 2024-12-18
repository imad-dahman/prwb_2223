<?php
require_once 'model/User.php';
require_once 'model/Tricount.php';
require_once 'framework/View.php';
require_once 'model/Participation.php';
require_once 'framework/Controller.php';
require_once 'model/Operation.php';
require_once 'model/Participation.php';
class ControllerSession extends Controller{

    public function index(): void
    {
        $user=$this->get_user_or_redirect();
        $users=User::get_userse();
        $participants=Tricount::get_tricounts_by_mail($user->mail);
        (new View("session"))->show(["users"=>$users,"participants"=>$participants]);
    }
    public function show():void
    {
        $users=User::get_userse();
        $participants="";
        if(isset($_POST["users"]) && is_numeric($_POST["users"])){
            $this->redirect("Session","result",$_POST["users"]);
        }
        else
            (new View("Session"))->show(["users"=>$users,"participants"=>$participants]);

    }
    public function result() : void{
        $users=User::get_userse();
        $participants="";
        $NOparticipants="";
        if(is_numeric($_GET["param1"])){
            $id=$_GET["param1"];
            $user=User::get_member_id($id);
            if($user!==false){
                $participants=Tricount::get_tricounts_by_mail($user->mail);
                $NOparticipants=User::get_NotParticipTricounts_by_mail($user->mail);
                (new View("imad"))->show(["users"=>$users,"participants"=>$participants,"NOparticipants"=>$NOparticipants]);
            }
            else
                (new View("Session"))->show(["users"=>$users,"participants"=>$participants,"NOparticipants"=>$NOparticipants]);

        }
        else
            (new View("Session"))->show(["users"=>$users,"participants"=>$participants,"NOparticipants"=>$NOparticipants]);

    }
    public function add_services() : void {

        $op = new Participation($_POST["idtricount1"],$_POST["idusers"]);
        $op->persist();
        echo "true";
    }
}