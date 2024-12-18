<?php
require_once 'model/User.php';
require_once 'model/Operation.php';
require_once 'model/Repartition.php';
require_once 'model/Tricount.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';
class ControllerOperation extends Controller
{

    public function index(): void
    {

        $this->operation();


    }

    public function title_available_service() : void {
        $res = "true";
        if(isset($_POST["title"]) && $_POST["title"] !== ""){


            $title = Operation::get_member_by_title($_POST["title"]);
            if($title){
                $res = "false";
            }
        }

        echo $res;
    }



    public function title_available_services() : void {
        $res = 0;
        if(isset($_POST["title"]) && $_POST["title"] !== ""){
            $title = Operation::get_member_by_title($_POST["title"]);
            if($title){
                $res = $title->tricount;
            }
        }
        echo $res;
    }
    public function title_availableid_services() : void {
        $res = 0;
        if(isset($_POST["title"]) && $_POST["title"] !== ""){
            $title = Operation::get_member_by_title($_POST["title"]);
            if($title){
                $res = $title->id;
            }
        }
        echo $res;
    }

    public function operation() :void{
        $user=$this->get_user_or_redirect();
        $id=$_GET["param1"];


        $idcr=Operation::ititator($id);


        $creator=Operation::creator($idcr);
        $rems=Operation::operti($id);
        $res=Tricount::operationamount($id);


        $operations=Operation::operationamount($id);
        $ops=Operation::getoperations($idcr);
        $currentindex=Operation::getcurrentindex($ops,Operation::get_id($id));

        $participons=Operation::getrepartions($id);
        $user=$this->get_user_or_false();
        (new View("operation"))->show(["user"=>$user,"operations"=>$operations,"participons"=>$participons,"currentindex"=>$currentindex,"res"=>$res,"rems"=>$rems,"ops"=>$ops,"creator"=>$creator]);
    }
    public function addoperation():void{

        $user=$this->get_user_or_redirect();
        $idtricount=$_GET["param1"];
        $participons=User::getparti($idtricount);
        $users=User::get_userse();
        $idpest='';
        $title='';
        $amount='';
        $operation_date='';
        $pest='';
        $name='';

        $weight='';
        $errors =[];
        $res=[];
        $personneCochee = false;
        if(isset($_POST['title']) && isset($_POST['amount']) && isset($_POST['operation_date'])
            && isset($_POST['pest']) )
        {
            $title=$_POST['title'];
            $amount=(floatval($_POST['amount'])) ;
            $operation_date=$_POST['operation_date'];
            $pest=$_POST['pest'];

            $idpest=User::get_userforid($pest);
            $operations=new Operation($title,$idtricount,$amount,$operation_date,$idpest,  date('Y-m-d H:i:s') ,0);
            $title_errors = $operations->validate($idtricount,$title);
            $amount_errors = $operations->validateamount($amount);
            $date_errors = $operations->validateoperation_date($operation_date);
            $errors = array_merge($title_errors, $amount_errors, $date_errors);
            if (!empty($title_errors)) {
                $errors['title'] = $title_errors;
            }
            if (!empty($amount_errors)) {
                $errors['amount'] = $amount_errors;
            }
            if (!empty($date_errors)) {
                $errors['operation_date'] = $date_errors;
            }

            if(empty($errors)){
                $arrayNames = array();
                $arrayWeight = array();
                foreach ($users as $x=>$user){
                    if(isset($_POST['name'.$x])   && isset($_POST['weight'.$x])){
                        $name=$_POST['name'.$x];
                        $res=$name;
                        $weight=$_POST['weight'.$x];
                        $errors_weight=Repartition::validateWeight($weight);
                        if(!empty($errors_weight)){
                            $errors['weight'.$x] = $errors_weight;
                        }
                        if(empty($errors)){
                            array_push($arrayNames,$name);
                            array_push($arrayWeight,$weight);
                            $personneCochee = true;

                        }
                    }
                }
                if (!$personneCochee ) {
                    $errors['name'] = "Il faut cocher au moins une personne.";
                    (new View("addoperation"))->show(["title" => $title, "amount" => $amount, "res" => $res,
                        "operation_date" => $operation_date,
                        "errors" => $errors, "users" => $users, "idtricount" => $idtricount, "weight" => $weight, "name" => $name, "participons" => $participons]);
                    return;
                }
                if (empty($errors)) {
                    $operations=$operations->persist();
                    $combine = array_combine($arrayNames,$arrayWeight);
                    $i=0;
                    foreach ($combine as $arrayNames=>$arrayWeight )
                    {
                        $idusers=User::get_userforid($arrayNames);
                        $weight=$arrayWeight;
                        $errors=Repartition::validateWeight($weight);
                        if(empty($errors)){
                            $repartitions=new Repartition($operations->id,$idusers,$weight);
                            $repartitions->addrepartition();
                        }
                        $i++;
                    }

                   $this->redirect("Tricount","tri",$idtricount);
                }
            }
            else
            {
                (new View("addoperation"))->show(["title"=>$title,"amount"=>$amount,"res"=>$res,
                    "operation_date"=>$operation_date,
                    "errors" => $errors,"users"=>$users,"idtricount"=>$idtricount,"weight"=>$weight,"name"=>$name,"participons"=>$participons]);
            }

        }
        else
        {
            (new View("addoperation"))->show(["title"=>$title,"amount"=>$amount,
                "operation_date"=>$operation_date,
                "errors" => $errors,"users"=>$users,"idtricount"=>$idtricount,"weight"=>$weight,"name"=>$name,"participons"=>$participons]);

        }

    }



    public function editoperation():void{
        $user=$this->get_user_or_redirect();
        $x=$_GET["param1"];

        $nametitles=Tricount::operationamount($x);



        $idcr=Operation::ititator($x);
        $participons=User::getparti($idcr);


        $rems=Operation::operti($x);


        $fullnames=User::getname($x,$idcr);
        $res=Operation::operationamount($x);
        $users=User::get_userse();
        $idp='';
        $amounts='';
        $titles='';
        $operation_da='';
        $pes='';

        $CheckedParts=Operation::getrepartions($x);

        $weights='';
        $names='';
        $errors= [];
        $operations=Operation::get_id($x);
        $repartitions=Repartition::getid($x);


        if( isset($_POST['amounts']) && isset($_POST['titles']) && isset($_POST['operation_da'])
            && isset($_POST['pes']))
        {

            $amounts=$_POST['amounts'];
            $errors_amount = array_merge($errors, $operations->validateAmountt($amounts));

                $titles=$_POST['titles'];
            $errors_titles = array_merge($errors, $operations->validateTitle($idcr,$titles,$x));

                    $operation_da=$_POST['operation_da'];
            $errors_date = $operations->validateoperation_date($operation_da);
            $errors=array_merge($errors_amount,$errors_titles,$errors_date);

            if (!empty($errors_titles)) {
                $errors['titles'] = $errors_titles;

            }
            if (!empty($errors_amount)) {
                $errors['amounts'] = $errors_amount;
            }
            if (!empty($errors_date)) {
                $errors['operation_da'] = $errors_date;
            }



            $pes=$_POST['pes'];
                    $idp=User::get_userforid($pes);

                    if(count($errors)==0) {
                        $operations->amount = $amounts;
                        $operations->title = $titles;

                        $operations->operation_date = $operation_da;
                        $operations->initiator = $idp;
                        $operations->persist();

                        if (isset($_POST['names']) && isset($_POST['weights'])) {
                            $names = $_POST['names'];
                            $weights = $_POST['weights'];

                            Repartition::deleteRes($x);

                            foreach ($names as $index => $user_name) {
                                $weight = $weights[$index];
                                if($weight >=0){
                                $user_id = User::get_userforid($user_name);

                                $errors=Repartition::validateWeight($weight);
                                if(count($errors)==0){
                                    $repartition = Repartition::getoperation($x, $user_id);

                                    if ($repartition == null) {
                                        $repartition = new Repartition($x, $user_id, $weight);
                                        $repartition->addrepartition();
                                    }
                                    else {
                                        $repartition->weight = $weight;
                                        $repartition->addrepartition();
                                    }
                        }
                                }
                    }
                    $this->redirect("Operation","index",$x);
                }
            }
        }
        (new View("editoperation"))->show(["users"=>$users,"amounts"=>$amounts,
            "titles"=>$titles,"fullnames"=>$fullnames,
            "repartitions"=>$repartitions,"names"=>$names,"weights"=>$weights,"rems"=>$rems,"nametitles"=>$nametitles
            ,"operation_da"=>$operation_da,"errors"=>$errors,"res"=>$res,"operations"=>$operations,"participons"=>$participons,"CheckedParts"=>$CheckedParts]);
    }



    public function deleteopertaion():void{
        $user=$this->get_user_or_redirect();
        $id=$_GET["param1"];
        $res=Operation::operationamount($id);
        $idtricounts=Operation::idtricount($id);
        Repartition::deleteRe($id);
        $this->redirect("Tricount","tri",$idtricounts);
        //  $this->redirect("user","tricounts");
        (new View("deleteoperation"))->show(["res"=>$res]);

    }
    public function delet():void{
        $user=$this->get_user_or_redirect();
        $id=$_GET["param1"];
        $res=Operation::operationamount($id);
        (new View("deleteoperation"))->show(["res"=>$res]);
    }
}