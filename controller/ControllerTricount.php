<?php
require_once 'model/User.php';
require_once 'model/Tricount.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'model/Operation.php';
require_once 'model/Participation.php';

class ControllerTricount extends Controller
{
    const UPLOAD_ERR_OK = 0;

    public function index(): void
    {
        $this->tricounts();
    }


    public function tricounts(): void
    {

        $user = $this->get_user_or_redirect();
        $tricounts = Tricount::get_tricounts_by_mail($user->mail);

        (new View("ListTricounts"))->show(["tricounts" => $tricounts]);

    }

    public function tri(): void
    {
        $user=$this->get_user_or_redirect();
        $mytotal=0;
        $totalExpenses=0;
/*     Tricount::participation_by_tricount($_GET["param1"]);*/
        $user=$this->get_user_or_false();
        $id = $_GET["param1"];
        if (Tricount::checkCreator($id,$user->id))
        {
            $tricount = Tricount::get_tricount_by_id($id);
            $operations=Tricount::get_operation_by_tricount($id);
            $resltas=Tricount::get_operation_by_tricount($id);
            $res =Tricount::participation_by_tricount($id);
            $ok = Tricount::expenses_by_tricount($id);
            $mytotal = User::get_Total($user->id,$operations);
            $totalExpenses=Tricount::get_Total_Operations($id);
            (new View("tricount"))->show(["mytotal"=>$mytotal,"totalExpenses"=>$totalExpenses,"operations"=>$operations,"res"=>$res,"ok"=>$ok,"mytotal"=>$mytotal,"totalExpenses"=>$totalExpenses,"id"=>$tricount->id,"titreTricount"=>$tricount->title]);

        }
        else
            $this->redirect("Main","index");


    }

    public function balance(): void
    {
        $user=$this->get_user_or_redirect();
        $id = $_GET["param1"];
        $tricount = Tricount::get_tricount_by_id($id);
        $operations=Tricount::get_operation_by_tricount($id);
        $participants=$tricount->getParticipantsByTricount();
        $creator=$tricount->get_creator();
        (new View("Balance"))->show(["participants"=>$participants,"creator"=>$creator,"operations"=>$operations,"id"=>$id,"nameTricount"=>$tricount->title]);

    }

    public function editTricount(): void
    {
        $user=$this->get_user_or_redirect();
        $errors=[];
        $users=[];
        $id = $_GET["param1"];
        $tricount = Tricount::get_tricount_by_id($id);
        $participants=$tricount->getParticipantsByTricount();
        $users= User::get_users($id);
        $creator=$tricount->get_creator();
        (new View("EditTricount"))->show(["errors"=>$errors,"titreTricount"=>$tricount->title,"title"=>$tricount->title ,"description"=>$tricount->description,"participants"=>$participants,"users"=>$users,"creator"=>$creator,"tricount"=>$tricount->id,"id"=>$id]);
    }

    public function delete(): void
    {
        $user=$this->get_user_or_redirect();
        $tricount = $this->remove();
        if ($tricount) {
            $user = $tricount->creator;
            $this->redirect("user", "index");
        } else {
            $this->redirect("Tricount", "index");
        }
    }

    private function remove(): Tricount|false
    {
        $user=$this->get_user_or_redirect();
        $title = $_GET['param1'];
        $user = $this->get_user_or_redirect();
        $tricount = Tricount::get_tricount_by_id($title);
        if ($tricount) {
            return $tricount->delete($user->id);
        }
        return false;
    }

    public function removeTricount(): void
    {
        $user=$this->get_user_or_redirect();
        if (isset($_GET["param1"])) {
            $user = $this->get_user_or_redirect();
            $id = $_GET["param1"];
            $tricount = Tricount::get_tricount_by_id($id);
        }
        (new View("DeleteTricount"))->show(["tricount" => $tricount->title, "tricountid" => $tricount->id]);
    }

    public function editTricount2(): void
    {
        $user=$this->get_user_or_redirect();
        $user = $this->get_user_or_false();
        $tricount = Tricount::get_tricount_by_id($_POST['tricount']);
        $errors1 = Tricount::CheckCreatorTricount($tricount->title,$user->id);
        $title = $_POST['title'];
        $description = $_POST['description'];
        $old = $tricount->description;
        $tricount->title = $title;
        $tricount->description = $description;
        $errors = Tricount::CheckEditTricount($title,$description,$user->id,$tricount->id);
        if (empty($errors) && empty($errors1))
        {
            $tricount->persist();
            $tricounts= Tricount::get_tricounts_by_mail($user->mail);
            (new View("ListTricounts"))->show(["tricounts"=>$tricounts]);
        }
        else
        {
            $tricount = Tricount::get_tricount_by_id($_POST['tricount']);
            $participants=$tricount->getParticipantsByTricount();
            $users= User::get_users($_POST['tricount']);
            $creator=$tricount->get_creator();
            (new View("EditTricount"))->show(["errors"=>array_merge($errors,$errors1) ,"titreTricount"=>$tricount->title,"title"=>$_POST['title'] ,"description"=>$_POST['description'],"participants"=>$participants,"users"=>$users,"creator"=>$creator,"tricount"=>$tricount->id,"id"=>$_POST['tricount']]);
        }

    }

    public function result():void
    {
        $user=$this->get_user_or_redirect();
        $errors=[];
        $tricount = Tricount::get_tricount_by_id($_GET["param1"]);
        $participants=$tricount->getParticipantsByTricount();
        $users= User::get_users($_GET["param1"]);
        $creator=$tricount->get_creator();
        (new View("EditTricount"))->show(["errors"=>$errors,"titreTricount"=>$tricount->title,"title"=>$tricount->title ,"description"=>$tricount->description,"participants"=>$participants,"users"=>$users,"creator"=>$creator,"tricount"=>$tricount->id,"id"=>$_GET["param1"]]);
    }

    public function addparticipant(): void
    {
        if ($_POST['participants']!="default" && !empty($_POST['participants']))
        {
            $errors=[];
            $part = new Participation($_POST['tricount'],$_POST['participants']);
            $part->persist();
            $this->redirect("Tricount","result",$_POST['tricount']);
        }
        else
        {
            $errors=[];
            $tricount = Tricount::get_tricount_by_id($_POST['tricount']);
            $participants=$tricount->getParticipantsByTricount();
            $users= User::get_users($_POST['tricount']);
            $creator=$tricount->get_creator();
            (new View("EditTricount"))->show(["errors"=>$errors,"titreTricount"=>$tricount->title,"title"=>$tricount->title ,"description"=>$tricount->description,"participants"=>$participants,"users"=>$users,"creator"=>$creator,"tricount"=>$tricount->id,"id"=>$_POST['tricount']]);

        }

    }

    public function deleteparticipant():void
    {
        if (isset($_POST["delete"]) && isset($_POST["full_name"]))
        {
            $id = $_POST["delete"];
            $user=User::get_user_by_name($_POST["full_name"]);
            Participation::Delete_Participant($user->id,$id);
        }

        $errors=[];
        $tricount = Tricount::get_tricount_by_id($id);
        $participants=$tricount->getParticipantsByTricount();
        $users= User::get_users($id);
        $creator=$tricount->get_creator();
        (new View("EditTricount"))->show(["errors"=>$errors,"titreTricount"=>$tricount->title,"title"=>$tricount->title ,"description"=>$tricount->description,"participants"=>$participants,"users"=>$users,"creator"=>$creator,"tricount"=>$tricount->id,"id"=>$id]);


    }
    public function tricount_exists_service() : void {
        $res = "false";
        $user = $this->get_user_or_false();
        if(isset($_POST["title"]) && $_POST["title"] !== ""){
            $tricount = Tricount::Check_exist_tricounts($user->id,$_POST["title"]);
            if($tricount)
                $res =  "true";
        }
        echo $res;
    }
    public function tricount_creator_service() : void {
        $res = "true";
        $user = $this->get_user_or_false();
        if(isset($_POST["param1"]) && $_POST["param1"] !== ""){
            $tricount = Tricount::CheckCreatorTricountService($_POST["param1"],$user->id);
            if(!$tricount)
                $res =  "false";
        }
        echo $res;
    }
    public function AddTricounts(): void
    {
        $user=$this->get_user_or_redirect();
        $title = '';
        $description = '';
        $user = $this->get_user_or_false();
        $errors = [];
        if (isset($_POST['title']) && isset($_POST['description'])) {
            $title = $_POST['title'];
            $description = $_POST['description'];
            $tricount = new Tricount($title, "", $user->id, $description);
            $errors = Tricount::CheckInsertTricount($title,$description,$user->id,"","");
            if (empty($errors)) {
                $tricount->persist();
           }

            }
        (new View("addTricount"))->show(["title" => $title, "description" => $description, "errors" => $errors]);

    }



    public function saveTricount(): void
    {
        $user=$this->get_user_or_redirect();
        $user = $this->get_user_or_false();
        if (isset($_POST['title']) && isset($_POST['description'])) {
            $title = $_POST['title'];
            $description = $_POST['description'];
            $tricount = new Tricount($title, "", $user->id, $description, 0);
            $errors = Tricount::CheckInsertTricount($title,$description,$user->id,"","");
            if (empty($errors)) {
                $tricount->persist();
                $id = Tricount::last_id();
                $participation = new Participation($id,$user->id);
                $participation->persist();
                $operations=Tricount::get_operation_by_tricount($id);
                $res =Tricount::participation_by_tricount($id);
                $ok = Tricount::expenses_by_tricount($id);
                $mytotal = User::get_Total($user->id,$operations);
                $totalExpenses=Tricount::get_Total_Operations($id);
                (new View("tricount"))->show(["mytotal"=>$mytotal,"totalExpenses"=>$totalExpenses,"operations"=>$operations,"res"=>$res,"ok"=>$ok,"mytotal"=>$mytotal,"totalExpenses"=>$totalExpenses,"id"=>$id,"titreTricount"=>$tricount->title]);

            }else{
                (new View("addTricount"))->show(["title"=>$_POST['title'],"description"=>$_POST['description'],"errors" => $errors]);
            }
        }
    }
}
