<!DocTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <title>Balance</title>
    <base href="<?= $web_root ?>"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" >
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous">
    <style>
        .balance-bar {
            height: 15px;
            display: inline-block;
            position: absolute;
            left: 0;
            top: 0;
            z-index: -1;
        }
        .balance-bar.positive {
            background-color: green;
        }
        .balance-bar.negative {
            background-color: red;
        }
        .participant-name {
            display: inline-block;
            position: relative;
            top: -3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: calc(40% - 5px);
        }
        .participant-balance {
            display: inline-block;
            width: calc(60% - 5px);
            text-align: right;
            position: relative;
        }
        .positive-balance {
            color: black;
        }
        .negative-balance {
            color: black;

        }
        .balance-bar span {
            position: relative;

        }

        .balance-bar span:before {
            position: absolute;
            top: -25px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 14px;
            white-space: nowrap;
        }

    </style>
</head>
<body>
<div class=" p-3 bg-primary text-white p-20 border border-primary-subtle col-sm text-right " style="height:100px ">
    <h3 class="text-right"><?=$nameTricount?> > Balance</h3>
    <button type="button" class=" float-left " ><a style="color:black" href="user/tricounts">Back</a></button>

</div>
<div class="container mt-5 ">
    <div class="card">
        <table   class="table table-borderless">
            <?php $max_balance = 0; ?>
            <?php foreach ($participants as $participant): ?>
                <?php $balance = User::get_total_paid_by_me($participant["id"], $id) - User::get_Total($participant["id"], $operations); ?>
                <?php $max_balance = max($max_balance, abs($balance)); ?>
            <?php endforeach;?>
            <?php foreach ($participants as $participant): ?>
                <?php $balance = User::get_total_paid_by_me($participant["id"], $id) - User::get_Total($participant["id"], $operations); ?>
                <tr class="col">
                <?php if ($balance >= 0): ?>
                    <td class="participant-name"><?= $participant["full_name"] ?></td>
                    <td class="participant-balance positive-balance">
                        <div class="progress">

                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= ($balance / $max_balance) * 50 ?>%;" aria-valuenow="<?= $balance ?>" aria-valuemin="0" aria-valuemax="<?= $max_balance ?>">

                                <?= number_format($balance, 2) ?>
                            </div>
                        </div>
                    </td>
                    </tr>
                    <tr class="col">
                <?php else: ?>
                    <td class="participant-balance negative-balance">
                        <div class="progress">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: <?= (abs($balance) / $max_balance) * 50 ?>%;" aria-valuenow="<?= $balance ?>" aria-valuemin="<?= $max_balance * -1 ?>" aria-valuemax="0">

                                <?= number_format($balance, 2) ?>
                            </div>
                        </div>
                    <td class="participant-name"><?= $participant["full_name"] ?></td>
                    </td>

                <?php endif; ?>
                </tr>
            <?php endforeach;?>
        </table>
    </div>
</div>
</body>
</html>
