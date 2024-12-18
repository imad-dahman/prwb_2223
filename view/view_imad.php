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
    <script src="lib/jquery-3.6.3.min.js" type="text/javascript"></script>
    <script src="lib/just-validate-plugin-date-1.2.0.production.min.js" type="text/javascript"></script>
    <script src="https://unpkg.com/just-validate@latest/dist/just-validate.production.min.js"></script>
    <script src="https://unpkg.com/browse/just-validate-plugin-date@1.2.0/dist/just-validate-plugin-date.production.min.js"></script>
    <script src="lib/sweetalert2@11.js" type="text/javascript"></script>
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
            document.getElementById("bttn").disabled=false;
        }
        async function show(){
          const  id1=idtricount.val();
          const id2=iduser.val();
          const data= await $.post("Imad/add_services/",{idtr:id1 , idus:id2});
            if(data === "true"){
                const tricountTitlte=idtricount.find("option:selected").text();
                const tricountOption=`<option value="${id1}">${tricountTitlte}</option>`
                const selectOption= idtricount.find("option:selected");
                selectOption.remove();
                $("#listp").append(tricountOption);
            }

        }
        $(function (){
            idtricount=$("#listnop");
            iduser=$("#list");
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
    <form action="Imad/index" method="post">
        <div class="row">
            <div class="col-9">
                <select id="list" name="users" class="form-select">
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
        <select id="listp" style="width: 30%" size=5 class="form-select">
            <?php foreach ($participants as $participant): ?>
                <option value="<?=$participant["id"]?>"><?= $participant["title"]?></option>
            <?php endforeach;?>
        </select>
        <div class="col m-2 p-0 button-container">
            <button onclick="show()" id="bttn" class="btn btn-outline-secondary" type="button" disabled>
                <i class="fa-solid fa-arrow-up"></i>
            </button>
        </div>
        <div class="form-label mt-2">Does not participate in these tricounts</div>
        <select id="listnop" onchange="enbale()" style="width: 30%" size=5 class="form-select">
            <?php foreach ($noparticipants as $noparticipant): ?>
                <option value="<?=$noparticipant->id?>"><?= $noparticipant->title?></option>
            <?php endforeach;?>
        </select>
    </form>
</div>

</body>

</html>
