<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>setting</title>
    <base href="<?=$web_root?>"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" >
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous">
</head>
<body>
<h1 class=" p-3 bg-primary text-white p-20 border border-primary-subtle col-sm text-right ">  
        <button type="button" class=" float-left " ><a style="color:black" href="user/tricounts">Back</a></button> settings</h1>

<div class="">Hey <?=$user->full_name?></div>
<div class="" >I Know your email address is <i style="color:brown"> <?= $user->mail ?></i></div>
<br>
<div>What can I do for you?</div>
<br>
<br>
<?php if($user->role==="admin"):?>
<div class="input-group mb-3">
    <button type="button" class=" form-control btn btn-outline-primary " disabled><a href="Session/index">Session</a></button>
</div>
<?php endif;?>
<?php if($user->role==="admin"):?>
<div class="input-group mb-3">
    <button type="button" class=" form-control btn btn-outline-primary " disabled><a href="Imad/index">Session2</a></button>
</div>
<?php endif;?>

<div class="input-group mb-3">
    <button type="button" class=" form-control btn btn-outline-primary " disabled><a href="Exe/">S2</a></button>
</div>
<div class="input-group mb-3">
        <button type="button" class=" form-control btn btn-outline-primary " disabled><a href="User/editprofil">Edit profil</a></button>
        </div>

        <div class="input-group mb-3">
        <button type="button" class=" form-control btn btn-outline-primary" disabled><a href="User/changepassword">change password</a></button>
        </div>

        <div class="input-group mb-3">
       <button class=" btn btn-outline-danger form-control btn btn-outline-danger" type="button" ><a style="color:brown" href="main/logout">Logout</a></button>   
        </div> 
</body>
</html>