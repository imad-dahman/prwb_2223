<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tricounts</title>
    <base href="<?= $web_root ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" >
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous">

</head>
<body>

<br><br>
<div class="card d-flex" ><br>
    <div>
        <div class="d-flex justify-content-center">
            <i class="fa fa-trash" style="font-size:78px;color:red"></i>
        </div>
        <br>
        <div class="d-flex justify-content-center">
            <h1 style="color:brown">
                Are you sure?
            </h1>
        </div>
        <div class="d-flex justify-content-center">
            <p style="color:brown">
                Do you really want to delete <?=$tricount?> and all of its dependencies</p>
        </div>
        <div class="d-flex justify-content-center">
            <h4 style="color:brown">
                This process cannot be undone.
            </h4>
        </div>
        <div class="input-group mb-3 d-flex justify-content-center">

            <button type="button" class="  btn btn-outline-primary " > <a style="color:black" href='Tricount/editTricount/<?=$tricountid?>'>Cancel</a> </button>
            <button class=" btn btn-outline-primary " ><a href="Tricount/delete/<?=$tricountid?> " style="color:black">Delete</a></button>
        </div>
    </div>
</div>
</body>
</html>
