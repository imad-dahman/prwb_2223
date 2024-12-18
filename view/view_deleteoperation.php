<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" >
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous">
<head>
    <meta charset="UTF-8">
    <title>add_operation</title>
    <base href="<?=$web_root?>"/>

   
    
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
        <?php foreach ($res as $re):?>
<div class="d-flex justify-content-center">
    <p style="color:brown">
        Do you really want to delete Operation "<?=$re['title']?> " <br>and all of its dependencies?
    </p>
</div>
    <div class="d-flex justify-content-center">
        <h4 style="color:brown">
            This process cannot be undone.
        </h4>
    </div>

<div class="input-group mb-3 d-flex justify-content-center">

    <button type="button" class="  btn btn-outline-primary " > <a style="color:black" href='Operation/editoperation/<?=$re['id']?>'>Cancel</a> </button>
    <?php endforeach;?>
    <button  type="button" class=" btn btn-outline-primary " ><a style="color:black" href="Operation/deleteopertaion/<?=$re['id']?>" >Delete</a></button>
</div>
 </div>
</div> 
</body>
</html>