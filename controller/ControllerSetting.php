<?php
require_once 'model/User.php';
require_once 'model/Tricount.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerSetting extends Controller
{

    public function index(): void
    {
        $this->setting();
    }
    public function setting() : void {
        $user = $this->get_user_or_redirect();
        if (isset($_GET["param1"]) && $_GET["param1"] !== "") {
            $user = User::get_user_by_mail($_GET["param1"]);
        }
        (new View("setting"))->show(["user" => $user]);
    }




}