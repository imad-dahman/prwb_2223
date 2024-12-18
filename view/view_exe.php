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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" >
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous">
    <script src="lib/jquery-3.6.3.min.js" type="text/javascript"></script>
    <script src="lib/just-validate-plugin-date-1.2.0.production.min.js" type="text/javascript"></script>
    <script src="https://unpkg.com/just-validate@latest/dist/just-validate.production.min.js"></script>
    <script src="lib/sweetalert2@11.js" type="text/javascript"></script>
    <style>
        .button-container {
            display: flex;
            flex-direction: row;
            justify-content: center;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <script>
        let id;
        let div;
        let operations;

        async function show() {
            const data = await $.post("Exe/add_services/", { id: id.val() });

            if (data === "false") {
                div.html("no");
            } else {
                operations = await $.getJSON("Exe/get_operation_services/" + id.val());

                div.html("");

                let html = "<h5>Expenses initiated by this user:</h5>";
                html += "<br>";
                html += "<ul>";
                for (let m of operations) {
                    html += "<li>";
                    html += "<input type='checkbox' value='" + m.id + "'>";
                    html += m.title;
                    html += " [ ";
                    html += m.amount ;
                    html += " ] "
                    html += "</li>";
                }
                html += "</ul>";
                html += "<br>";
                html += "<button type='button' id='inflationButton' onclick='inflation()'>Inflation</button>";

                div.html(html);
            }
        }
         function inflation() {
            const checkbox =$("input[type='checkbox']:checked");
            for (let c of checkbox) {
                operation=$(c).val();
                change();
            }
            show();
        }
       async function change(){
            const date=await $.post("Exe/update_amount_services/",{op:operation});
        }



        $(function() {
            id = $("#list");
            div = $("#checkbox");
        });
    </script>


</head>

<body>
<div class="main">
    <div>
        <h5>Select a user : </h5>
        <form action="Exe/show" method="POST">
            <select name="user">
                <option value="0">--Select user--</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?=$user["id"]?>"<?php if(isset($_GET["param1"])) if($user["id"] == $_GET["param1"]) echo "selected";?>><?=$user["full_name"]?></option>
                <?php endforeach;?>
            </select>
            <input type="submit" value="Search tricounts">
        </form>
    </div>
    <?php if(isset($_GET["param1"])):?>
    <div>
        <?php if ($tricounts != false): ?>
            <h5>Select a tricount :</h5>
            <select onchange="show()" id="list" name="list">
                <option value="0">--Select tricount--</option>
                <?php foreach ($tricounts as $tricount): ?>
                    <option value="<?= $tricount->id ?>"><?= $tricount->title ?></option>
                <?php endforeach; ?>
            </select>
        <?php else: ?>
            <p>pas de tricount</p>
        <?php endif; ?>
    </div>
    <?php endif; ?>


    <div class="div" id="checkbox">

</div>

<div>


</body>

</html>
