<!DOCTYPE html>
<html lang="en">

<head>
    <title>Session1</title>
    <base href="<?= $web_root ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="lib/jquery-3.6.3.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" >
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous">

    <style>
        .button-container {
            display: flex;
            flex-direction: row;
            justify-content: center;
        }
    </style>
    <script>
    let idtricount,iduser;
    function enbale(){
        document.getElementById("bttshow").disabled=false;
    }
    async function add(){
        const id = idtricount.val();
        const idu=iduser.val();
        const data = await $.post("Session/add_services/", { idtricount1: id, idusers: idu });
        if(data==="true"){
            const tricountTitle = idtricount.find("option:selected").text();
            const tricountOption = `<option value="${id}">${tricountTitle}</option>`;
            const selectedOption = idtricount.find("option:selected");
            selectedOption.remove();
            $("#listp").append(tricountOption);
        }
    }
    $(function (){
        idtricount=$("#listnp");
        iduser=$("#liste")
    })
    </script>
</head>

<body>
<nav class="navbar  fixed-top  navbar-expand-lg" style="background-color: #e3f2fd;">
    <div class="container-fluid">
        <a class="btn btn-sm btn-outline-danger" type="button" href="user">Back</a>
        <span class="navbar-text"><b>Session 1</b></span>
    </div>
</nav>
<div class="pt-5 pb-3"></div>
<div class="main pb-2">
    <form action="Session/show" method="post">
        <div class="row">
            <div class="col-9">
                <select id="liste" name="users" class="form-select">
                    <option>-- Select a User --</option>
                    <?php foreach ($users as $user): ?>
                    <option value="<?=$user["id"]?>"<?php if(isset($_GET["param1"])) if($user["id"] == $_GET["param1"]) echo "selected";?>><?=$user["full_name"]?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="col-3">
                <button class="btn btn-outline-secondary" type="submit">Show</button>
            </div>
        </div>
        <div class="form-label mt-2">Participates in these tricounts</div>
        <select id="listp" name="listp" style="width: 30%" size=5 class="form-select">
            <?php foreach ($participants as $participant): ?>
                <option value="<?=$participant["id"]?>"><?= $participant["title"]?></option>
            <?php endforeach;?>
        </select>
        <div class="col m-2 p-0 button-container">
            <button onclick="add()" id="bttshow" class="btn btn-outline-secondary" type="button" disabled>
                <i class="fa-solid fa-arrow-up"></i>
            </button>
        </div>
        <div class="form-label mt-2">Does not participate in these tricounts</div>
        <select style="width: 30%" size=5 onchange="enbale()" id="listnp" name="listnap" class="form-select">
            <?php foreach ($NOparticipants as $NOparticipant): ?>
                <option value="<?=$NOparticipant->id?>"><?= $NOparticipant->title?></option>
            <?php endforeach;?>
        </select>
    </form>
</div>


</body>

</html>