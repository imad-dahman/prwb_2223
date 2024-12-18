<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Log In</title>
    <base href="<?= $web_root ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/MyStyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" >
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <h1 class=" p-3 bg-primary border border-primary-subtle  "   >
        <i class="fas fa-cat" style="color:white" aria-hidden="true"></i>   Tricount</h1>



</head>
<body>
<div class="card">
<div  class="text-center mt-3 ">Log In</div>
<div class="menu">
    <a href=""></a>
</div>
    <div class="main">
    <br><br>
    <form action="main/login" method="post"  class="container col mb-3 " >

    <table>
        <tr>
            <div class="input-group mb-3">
                <span class="input-group-text">@</span>
                <input class="form-text form-control" type="text" placeholder="Mail" name="mail" value="<?= $mail?>">
            </div>

        </tr>
        <tr>
            <div class="input-group mb-3">
                <span class="input-group-text"><i class="fas fa-lock" aria-hidden="true"></i></span>
                <input class="form-text form-control" type="password" placeholder="Password" name="password"value="<?= $password ?>">
            </div>
        </tr>
    </table>


    <div class="input-group mb-3">
        <button class=" btn btn-primary form-control" type="submit" >Log In</a></button>
    </div>
    <div class="text-center mt-3 ">
        <a href="main/signup">New here ? Click here to join the party</a><img src="https://cdn-icons-png.flaticon.com/512/873/873427.png" width="25px" height="25px" alt="Party horn free icon" title="Party horn free icon">

    </div>
</form>

    <?php if (count($errors) != 0): ?>
        <div class='errors'>
            <p>Please correct the following error(s) :</p>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
</div>
</body>
</html>



