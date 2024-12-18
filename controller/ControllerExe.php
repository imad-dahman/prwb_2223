<?php
require_once 'model/User.php';
require_once 'model/Tricount.php';
require_once 'framework/View.php';
require_once 'model/Participation.php';
require_once 'framework/Controller.php';
require_once 'model/Operation.php';
require_once 'model/Participation.php';
class ControllerExe extends Controller{

    public function index(): void
    {

        $users=User::get_userse();
        $tricounts="";
        (new View("exe"))->show(["users"=>$users,"tricounts"=>$tricounts]);
    }
    public function show(): void{

        if(isset($_POST["user"]) && is_numeric($_POST["user"])){
            $this->redirect("Exe","result",$_POST["user"]);
        }
        else
        (new View("exe"))->show([]);
    }
    public function result(): void{

        $tricounts="false";
        $users=User::get_userse();
        if(isset($_GET["param1"]) && is_numeric($_GET["param1"])){
            $id=$_GET["param1"];
            $user=User::get_member_id($id);
            if($user!==false){
                $tricounts=Tricount::get_tricounts_by_mail2($user->mail);

                (new View("exe"))->show(["users"=>$users,"tricounts"=>$tricounts]);
            }

        else
            (new View("exe"))->show(["users"=>$users,"tricounts"=>$tricounts]);
        }
        else{
            (new View("exe"))->show(["users"=>$users,"tricounts"=>$tricounts]);
        }
    }
    public function add_services() : void {

        $pres=Tricount::expenses_by_tricount($_POST["id"]);
    if($pres===false){
    echo "false";
        }
    else
        echo "true";
    }
    public  function get_operation_services1(int $id): string{
        $operations=Tricount::get_operation_by_tricount2($id);
        $table = [];
        foreach ($operations as $operation){
            $row = [];
            $row["title"]=$operation->title;
            $row["tricount"]=$operation->tricount;
            $row["amount"]=$operation->amount;
            $row["operation_date"]=$operation->operation_date;
            $row["initiator"]=$operation->initiator;
            $row["created_at"]=$operation->created_at;
            $row["id"]=$operation->id;
            $table[]=$row;
        }
        return json_encode($table);
    }

    public function get_operation_services(): void{
        $operations = ControllerExe::get_operation_services1($_GET["param1"]);
        echo $operations;
    }
    public function update_amount_services(): void{
        $operations = Operation::get_id($_POST["op"]);
        $amount= $operations->amount;
        $operations->amount+=$amount*0.1;
        $operations->persist();
        echo $operations->amount;

    }

}
