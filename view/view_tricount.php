<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title></title>
    <base href="<?= $web_root ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" >
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">



    <!-- Add icon library -->








    <style>
        .btn {
            background-color: DodgerBlue;
            border: none;
            color: white;
            padding: 12px 16px;
            font-size: 16px;
            cursor: pointer;
        }

        /* Darker background on mouse-over */
        .btn:hover {
            background-color: RoyalBlue;
        }
    </style>
    <script src="lib/jquery-3.6.3.min.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {

            var isJsEnabled = true;
            if(!window.jQuery) {
                isJsEnabled = false;
            }

            if (isJsEnabled) {
                $('#orderDiv').show();
            } else {
                $('#orderDiv').hide();
            }
            $('#order').on('change', function() {
                var value = $(this).val();
                var rows = $('#myTable tbody tr').get();

                rows.sort(function(a, b) {
                    var A,B;

                    if (value === 'Amountcr' || value === 'Amountdec') {
                        A = parseFloat($(a).find('.amount').text().replace(' €', '').replace(',', '.'));
                        B = parseFloat($(b).find('.amount').text().replace(' €', '').replace(',', '.'));
                    } else if (value === 'Datecr' || value === 'Datedec') {
                        A = new Date($(a).find('.date').text());
                        B = new Date($(b).find('.date').text());
                    } else if (value === 'Titlecr' || value === 'Titledec') {
                        A = $(a).find('.title').text();
                        B = $(b).find('.title').text();
                    }
                    else if (value === 'Initiatorcr' || value === 'Initiatordec') {
                        A = $(a).find('.Initiator').text();
                        B = $(b).find('.Initiator').text();
                    }

                    if (value === 'Amountcr' || value === 'Datecr') {
                        return A - B;
                    } else if (value === 'Amountdec' || value === 'Datedec') {
                        return B - A;
                    } else if (value === 'Titlecr') {
                        return A.localeCompare(B);
                    } else if (value === 'Titledec') {
                        return B.localeCompare(A);
                    }
                    else if (value === 'Initiatorcr') {
                        return A.localeCompare(B);
                    } else if (value === 'Initiatordec') {
                        return B.localeCompare(A);
                    }

                });

                $.each(rows, function(index, row) {
                    $('#myTable').append(row);
                });

            });
        });
    </script>


</head>
<body>
<div class=" p-3 bg-primary text-white p-20 border border-primary-subtle col-sm text-right "style="height: 100px">
    <p class="text-center"><?=$titreTricount ?> > Expenses</p>
    <button type="button" class=" float-left"style="" ><a style="color: black ;" href="user/tricounts">Back</a></button>
    <button class="  float-right" style="" ><a style="color: black" href='Tricount/editTricount/<?=$id?>'>Edit</a></button>
</div>

<?php if ($res==false): ?>
    <div style="margin-top: 15%;margin: 10%" class="card">

        <table>
            <tbody style="text-align: center">
            <div style="" class="">
                <input class="form-control text-center" type="text" placeholder="You are Alone !" aria-label="Disabled input example" disabled>
            </div>
            <tr>
                <td>
                    <span>Click Below to add friends!</span><br>
                    <div class="d-grid gap-2 d-md-block">
                        <a  href="Tricount/editTricount/<?=$id?>"><button style="margin-top: 3%" class="btn btn-primary">Add friends</button></a>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
<?php elseif ($ok==false):?>
    <div style="margin-top: 15% ; margin: 10%" class="card">
        <table>
            <tbody style="text-align: center">
            <div style="" class="">
                <input class="form-control text-center" type="text" placeholder="Your Tricount is Empty !" aria-label="Disabled input example" disabled>
            </div>
            <tr>
                <td>
                    <span>Click Below to add expenses!</span><br>
                    <div class="d-grid gap-2 d-md-block">
                        <a href='Operation/addoperation/<?=$id?>'>
                            <button  style="margin-top: 3%" class="btn btn-primary">
                                Add Expenses </button></a>
                    </div>
                </td>
            </tr>

        </table>
    </div>

<?php else:?>
<div style="margin-top: 15%" class="Full">
    <div class="d-grid gap-2">
        <a href="Tricount/balance/<?=$id?>"><BUTTON style="margin-bottom: 10%" class="form-control bg-success btn bs-success" type="button">View Balance</BUTTON></a>
    </div>
    <div id="orderDiv" style="display: none;">
        <div class="d-grid gap-2">
            <br>
            <label for="order">Order expenses by:</label>
            <select class="form-control" id="order">
                <option value="default">--Order expenses by--</option>
                <option value="Amountcr">Amount ▲  </option>
                <option value="Amountdec">Amount ▼</option>
                <option value="Datecr">Date ▲</option>
                <option value="Datedec">Date ▼</option>
                <option value="Initiatorcr">Initiator ▲</option>
                <option value="Initiatordec">Initiator ▼</option>
                <option value="Titlecr">Title ▲</option>
                <option value="Titledec">Title ▼</option>
            </select>
        </div>
    </div>
    <br><br>
    <table class="table" id="myTable">
        <?php foreach ($operations as $operation): ?>
            <tr>
                <td>
                    <div class="row">
                        <div class="col">
                            <a href='Operation/operation/<?=$operation["id"]?>' class="title"><?= $operation["title"]?></a>
                        </div>
                        <div class="col amount">
                            <?= $operation["amount"]?> €
                        </div>
                    </div>
                    <div class="row">
                        <div class="col Initiator">
                            Paid by <?=User::get_userForOperation($operation["title"],$id)?>
                        </div>
                        <div class="col date">
                            <?=$operation["operation_date"]?>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php endif;?>



</body>
<footer style="margin-top: 10%">


    <div style="text-align: center">
        <a href='Operation/addoperation/<?=$id?>'> <button  class="btn centre"><i class="fa fa-plus"></i></button></a>
    </div>
    <div class=" p-3 bg-primary text-white p-20 border border-primary-subtle col-sm text-right "style="height: 100px">

        <div class="d-flex align-items-center">
            <span class="mr-auto">
        <label>MyTotal</label><br>

        <?php   if ($mytotal!=null):?>
            <label> <?= number_format($mytotal,2) ?> € </label>
        <?php else:?>
            <label> <?= $mytotal=0?> € </label>
        <?php endif;?>
    </span>

            <span style="text-align: right">
        <label class="text-right ">Total Expenses</label><br>
        <?php   if ($totalExpenses!=null):?>
            <label> <?= $totalExpenses?> € </label>
        <?php else:?>
            <label> <?= $totalExpenses=0?> € </label>
        <?php endif;?>
    </span>
        </div>
    </div>



</footer>
</html>
