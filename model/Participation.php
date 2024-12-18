<?php

require_once "framework/Model.php";
require_once "Participation.php";

class Participation extends Model
{

    public function __construct(public int $tricount,public int $user){}


    public function persist():Participation
    {
        self::execute("INSERT INTO subscriptions(tricount,user) VALUES (:tricount,:user)",["tricount"=>$this->tricount,"user"=>$this->user]);
        return $this;
    }

    public static function Delete_Participant(int $idUser,int $idTricount):void
    {
        self::execute("DELETE FROM subscriptions WHERE subscriptions.tricount=:idtricount and subscriptions.user=:idUSER",["idtricount"=>$idTricount,"idUSER"=>$idUser]);

    }
}