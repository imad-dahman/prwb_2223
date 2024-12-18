<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Tricounts</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" >
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous">
        <div class="title">
            <form action="Tricount/AddTricounts" method="post">
                <!--                <button onclick="view_addTricount.php" style="margin-left: 35%" type="button" ><a href="addTricount.php">add</a> </button>
                -->
                <h3 class=" p-3 bg-primary text-white p-20 border border-primary-subtle col-sm text-left ">
                    <input class=" float-right " style="margin-left: 35%;color:black" type="submit" value="Add"> Your Tricounts</h3>
            </form>
        </div>

    </head>
    <body>

        <br><br>
        <div class="card">
            <table>
                <?php foreach ($tricounts as $tricount): ?>

               <!-- <th>
                </th>
                <th>
                </th>-->
                <tr>
                    <td scope="col"> <a style="text-decoration: none" href='Tricount/tri/<?=$tricount["id"]?>'><?= $tricount["title"]?></a></td>

                    <?php if(Tricount::get_participants_by_tricount($tricount["id"])==0):?>
                    <td scope="col"> you are alone </td>
                    <?php elseif (Tricount::get_participants_by_tricount($tricount["id"])==1):?>
                    <td> <?= Tricount::get_participants_by_tricount($tricount["id"])?> friend </td>
                    <?php else:?>
                    <td> <?= Tricount::get_participants_by_tricount($tricount["id"])?> friends </td>
                    <?php endif;?>
                </tr>
                    <tr>
                        <?php if ($tricount["description"]==""||$tricount["description"]=="NULL"): ?>
                        <td><?= "no description"?></td>

                        <?php else: ?>
                        <td><?= $tricount["description"]?></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </table>

        </div>
<div class="fixed-bottom ">

    <a  style="float: right;margin-top: 5%;margin-right: 5%" href="setting"> <svg  style="height: 60px" xmlns="http://www.w3.org/2000/svg" viewBox="-9 -9 18 18"><path d="M3.66 -2.62L5.48 -3.01L6 -1.74L4.44 -0.73A4.5 4.5 0 0 1 4.44 0.73L4.44 0.73L6 1.74L5.48 3.01L3.66 2.62A4.5 4.5 0 0 1 2.62 3.66L2.62 3.66L3.01 5.48L1.74 6L0.73 4.44A4.5 4.5 0 0 1 -0.73 4.44L-0.73 4.44L-1.74 6L-3.01 5.48L-2.62 3.66A4.5 4.5 0 0 1 -3.66 2.62L-3.66 2.62L-5.48 3.01L-6 1.74L-4.44 0.73A4.5 4.5 0 0 1 -4.44 -0.73L-4.44 -0.73L-6 -1.74L-5.48 -3.01L-3.66 -2.62A4.5 4.5 0 0 1 -2.62 -3.66L-2.62 -3.66L-3.01 -5.48L-1.74 -6L-0.73 -4.44A4.5 4.5 0 0 1 0.73 -4.44L0.73 -4.44L1.74 -6L3.01 -5.48L2.62 -3.66A4.5 4.5 0 0 1 3.66 -2.62M2.5 0A2.5 2.5 0 0 0 -2.5 -0A2.5 2.5 0 0 0 2.5 -0Z" style="fill-rule: evenodd; fill: yellow" /></svg></a>

</div>

    </body>
</html>
