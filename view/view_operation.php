<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title></title>
    <base href="<?= $web_root ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" >
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous">
    


    <div class=" p-3 bg-primary p-20   border border-primary-subtle col-sm text-right ">

    <?php foreach ($res as $re): ?>
        <?php foreach ($operations as $operation):?>
        <p class="text-center"><?=$re['title'] ?> > <?=$operation['title']?></p>
        <button class=" float-left " style="color:white" > <a style="color:black"  href='tricount/tri/<?= $re['id']?>'>Back</a>  </button>


        <button class="  float-reigt" style="margin-left: 80%" > <a style="color:black" href='Operation/editoperation/<?=$operation['id']?>'>Edit </a></button>
    <?php endforeach;?>
    <?php endforeach; ?>
    </div>

</head>
<body>

<table >
    <?php foreach ($operations as $operation): ?>
        <tr>
            <div class="text-center font-weight-bold"> <?=$operation[0]?> €</div>
        </tr>
        <div class="d-flex align-items-center">

         <span class="mr-auto"> Paid by: <?=User::getuseroperation($operation["id"])?></span>
           <span><?=date('d/m/Y', strtotime($operation[1]))?></span>

        </div><br>
    <tr>
        <td> for <?=Operation::operationini($operation["id"])?> participants,including me</td>
    </tr>
    <?php endforeach; ?>
    
    </table>
    <br>
<table class= "table">
    <?php foreach ($participons as $participon): ?>
    <?php foreach ($rems as $rem): ?>
    <tr>
        <?php   if ($creator==$participon[0]):?>
         <td scope="col"><?=$participon[0]?> (me)</td>
            <td><?=$participon[1]*$operation[0]/$rem[0]?></td>
        <?php else:?>
        <td scope="col"><?=$participon[0]?> </td>
        <td scope="row"><?=$participon[1]*$operation[0]/$rem[0]?></td>
        <?php endif;?>
    </tr>
    <?php endforeach; ?>
    <?php endforeach; ?>
</table>

<div class=" p-3 bg-primary   fixed-bottom   border border-primary-subtle col-sm text-right ">


        <?php if ($currentindex != 0) : ?>
            <button class="float-left"><a style="color:black" href='Operation/operation/<?= $ops[$currentindex-1]['id'] ?>'>Previous</a></button>
        <?php else: ?>
            <p></p>
        <?php endif; ?>
        <?php if ($currentindex < sizeof($ops)-1) : ?>
            <button class="float-right"><a style="color:black" href='Operation/operation/<?= $ops[$currentindex+1]['id'] ?>'>Next</a></button>
        <?php else: ?>
            <p></p>
        <?php endif; ?>


</div>
</body>
</html>