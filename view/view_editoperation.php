<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <title>edit_operation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?=$web_root?>"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" >
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous">
    <script src="lib/jquery-3.6.3.min.js" type="text/javascript"></script>
    <script src="lib/sweetalert2@11.js" type="text/javascript"></script>
    <?php foreach ($res as $re):?>
    <script>
        $(document).ready(function() {
            var isJsEnabled = true;
            if(!window.jQuery) {
                isJsEnabled = false;
            }

            // Cacher ou afficher la div "orderDiv" en fonction de la présence de JavaScript
            if (isJsEnabled) {
                $('.amountjs').show();
            } else {
                $('.amountjs').hide();
            }
        });
    </script>
    <script src="lib/just-validate-4.2.0.production.min.js" type="text/javascript"></script>
    <script src="lib/just-validate-plugin-date-1.2.0.production.min.js" type="text/javascript"></script>
    <script src="https://unpkg.com/just-validate@latest/dist/just-validate.production.min.js"></script>
    <script src="https://unpkg.com/browse/just-validate-plugin-date@1.2.0/dist/just-validate-plugin-date.production.min.js"></script>

    <script>
        let titleAvailable =false;
        let  idtricounts ;
        let idoperation;
        const id = "<?=$re['id']?>";
        const idtricount = "<?=$re['tricount']?>";
        const titleoperation= "<?=$operations->tricount?>"
        const idoperations= "<?=$operations->id?>";

        $(document).ready(function() {

            const justvalidateEnabled = "<?= Configuration::get('just_validate') ?>";

            function debounce(fn, time) {
                var timer;

                return function() {
                    clearTimeout(timer);

                    timer = setTimeout(() => {
                        fn.apply(this, arguments);
                    }, time);
                }
            }

            if (justvalidateEnabled) {
                const validator = new JustValidate('#edit_form', {
                    validateBeforeSubmitting: true,
                    lockForm: true,
                    focusInvalidField: false,
                    successLabelCssClass: ['success'],
                    errorLabelCssClass: ['errors'],
                    errorFieldCssClass: ['is-invalid'],
                    successFieldCssClass: ['is-valid'],
                });

                validator
                    .addField('#titles', [
                        {
                            rule: 'required',
                            errorMessage: 'Field is required'
                        },
                        {
                            rule: 'minLength',
                            value: 3,
                            errorMessage: 'Minimum 3 characters'
                        },
                        {
                            rule: 'maxLength',
                            value: 256,
                            errorMessage: 'Maximum 256 characters'
                        },
                        {
                            rule: 'customRegexp',
                            value: /^[a-zA-ZÀ-ÖØ-öø-ÿ][a-zA-ZÀ-ÖØ-öø-ÿ0-9 ]*$/,
                            errorMessage: 'Title must start with a letter and can only contain letters, numbers, and spaces.'
                        },
                    ], { successMessage: 'Looks good!' })
                    .addField('#amounts', [
                        {
                            rule: 'required',
                            errorMessage: '<div>Amount is required</div>'
                        },
                        {
                            rule: 'number',
                        },
                        {
                            rule: 'minNumber',
                            value: 0.01,
                            errorMessage: '<div>Amount must be greater than or equal to 1 cent</div>'
                        },
                    ], {
                        successMessage: '<div>Looks good!</div>'
                    })
                    .addField('#operation_da', [
                        {
                            rule: 'required',
                            errorMessage: 'La date est obligatoire',
                        },
                        {
                            plugin: JustValidatePluginDate(() => ({
                                format: 'yyyy-MM-dd',
                                isBeforeOrEqual: new Date(),
                            })),
                            errorMessage: 'La date ne peut pas être dans le futur',
                        },
                    ])
                    .addField('#pes', [
                        {
                            rule: 'required',
                        },
                    ])
                    .addRequiredGroup(
                        '#checkboxid',
                        'Select at least one participant'
                    )
                    .onValidate(debounce(async function(event) {
                        const title =$("#titles").val();
                        titleAvailable = await $.post("Operation/title_available_service/"  , {title: title});
                        idtricounts = await $.post("Operation/title_available_services/" , {title: title});
                        idoperation = await $.post("Operation/title_availableid_services/" , {title: title});

                        if (titleAvailable=== "false"  && idtricounts === titleoperation && idoperation !== idoperations)
                            this.showErrors({ '#titles': 'Title already exists' });
                    }, 300))
                    .onSuccess(function(event) {
                        if (!titleAvailable && idtricounts !== titleoperation && idoperation !== idoperations)
                            event.target.submit();
                        else if (titleAvailable && idtricounts === titleoperation && idoperation === idoperations)
                            event.target.submit();
                        else if (titleAvailable && idtricount === titleoperation && idtricount !==idtricounts)
                            event.target.submit();
                    });
            }
        });
    </script>







    <script>
        $(document).ready(function() {

            function update() {
                let totalAmount = parseFloat($("#amounts").val());
                let count = $('input[name^="weights"]').length;
                let totalWeight = 0;
                let positiveWeights = true;
                $('input[name^="weights"]').each(function() {
                    var weight = parseFloat($(this).val());
                    if (isNaN(weight)) {
                        weight = 0;
                    }
                    if (weight < 0) {
                        positiveWeights = false;
                    }
                    totalWeight += weight;
                });
                if (totalAmount > 0 && count > 0 && positiveWeights) {
                    $("input[name^='weights']").each(function() {
                        var weight = parseFloat($(this).val());
                        var amountToPay = 0;
                        if (weight > 0) {
                            amountToPay = totalAmount * weight / totalWeight;
                        }
                        $(this).closest(".input-group").find("input[name='amounted']").val(amountToPay);


                        var personInput = $(this).closest(".input-group").find("input[name^='names']");
                        if (weight >= 1) {
                            personInput.prop("checked", true);
                        } else {
                            $(this).closest(".input-group").find("input[name^='names']").prop("checked", false);
                        }
                        $(this).data("previous-weight", weight);
                    });
                }
            }
            $("#amounts").on("change", function() {
                update();
            });

            $("input[name^='weights']").on("change", function() {
                if ($(this).val() === 0) {
                    $(this).closest(".input-group").find("input[name^='names']").prop("checked", false);
                }

                update();
            });
            $('input[name^="names"]').each(function() {
                update();
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            function update() {
                let totalAmount = parseFloat($("#amounts").val());
                let count = $('input[name^="weights"]').length;
                let totalWeight = 0;
                let positiveWeights = true;
                let numChecked = 0;

                $('input[name^="names"]').each(function() {
                    var isChecked = $(this).prop("checked");
                    let weights = $(this).closest(".input-group").find("input[name^='weights']");
                    var weight = parseFloat(weights.val());

                    if (!isChecked && weight >= 1) {
                        totalWeight -= weight;
                        weight = 0;
                        weights.val(0);
                    }

                    weights.on('change', function() {
                        weight = parseFloat($(this).val());
                        update();
                    });

                    totalWeight += weight;

                    if (isChecked) {
                        numChecked++;
                    }
                    if (isChecked && weight === 0) {
                        weight = 1;
                        weights.val(weight);
                    }
                });

                totalWeight = 0;
                $('input[name^="weights"]').each(function() {
                    totalWeight += parseFloat($(this).val());
                });

                if (totalWeight >= 0) {
                    $("input[name^='weights']").each(function() {
                        var weight = parseFloat($(this).val());
                        var amountToPay = 0;
                        if (weight > 0) {
                            amountToPay = totalAmount * weight / totalWeight;
                        }
                        $(this).closest(".input-group").find("input[name='amounted']").val(amountToPay);
                    });
                } else {
                    $("input[name='amounted']").val(0);
                }

                if (numChecked === 0) {
                    $("input[name^='amounted']").val(0);
                } else {
                    if (totalAmount > 0 && count > 0 && positiveWeights) {
                        $("input[name^='weights']").each(function() {
                            var name = $(this).closest(".input-group").find("input[name^='names']");
                            var isChecked = name.prop("checked");
                            var weight = isChecked ? parseFloat($(this).val()) : 0;
                            $(this).val(weight);
                        });
                    }
                }
            }
            $("#amounts").on("change", function() {
                update();
            });

            $("input[name^='weights']").on("change", function() {
                update();
            });

            $("input[name^='names']").on("change", function() {
                update();
            });
        });
    </script>



    <script>


            const justvalidateEnabled = "<?= Configuration::get('just_validate') ?>";
            if(justvalidateEnabled){

        document.addEventListener('DOMContentLoaded', function() {
            const deleteButton = document.getElementById('delete-button');
            deleteButton.href = 'Operation/deleteopertaion/' + <?=$re['id']?>;
            deleteButton.addEventListener('click', function(event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    html: `
                        <p>Do you really want to delete operation  "<b><?=$re['title']?></b>"
                        and all of its dependencies ?.</p>
                        <p>This process cannot be undone.<p>

                    `,
                    icon: 'warning',
                    position: 'top',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!',
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(deleteButton.href, { method: 'POST' }).then(response => {
                            if (response.ok) {
                                Swal.fire(
                                    'Deleted!',
                                    'This operation has been deleted.',
                                    'success'
                                ).then(() => {
                                    const idtricounts = <?=$re['tricount']?>;
                                    window.location.href = "Tricount/tri/"+idtricounts;
                                });
                            } else {
                                Swal.fire(
                                    'Erreur!',
                                    'Une erreur est survenue pendant la suppression.',
                                    'error'
                                );
                            }
                        }).catch(error => {
                            Swal.fire(
                                'Erreur!',
                                'Une erreur est survenue pendant la suppression.',
                                'error'
                            );
                        });
                    }
                });
            });
        });
        }
    </script>

    <script>

            document.addEventListener('DOMContentLoaded', function () {
                const justvalidateEnabled = "<?= Configuration::get('just_validate') ?>";
                if(justvalidateEnabled) {
                const cancelButton = document.getElementById('cancel-btn');

                cancelButton.addEventListener('click', function (event) {
                    event.preventDefault();

                    Swal.fire({
                        title: 'Unsaved changes !',
                        text: "Are you sure you want to leave this form ? Changes you made will not be saved.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#8B0000',
                        cancelButtonColor: '#696969',
                        confirmButtonText: 'Leave Page',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `Operation/operation/<?=$re['id']?>`;
                        }
                    });
                });
                }
            });

    </script>





</head>
<body>
<form id="edit_form" action="Operation/editoperation/<?=$re['id']?>" method="post">
    <div class=" p-3 bg-primary text-white p-20  border border-primary-subtle col-sm text-right ">
        <?php foreach ($nametitles as $nametitle):?>
        <p class="text-center"><?=$nametitle['title'] ?> > Edit expense </p>

        <button class=" float-left " id="cancel-btn" style="color:white" > <a style="color:black" href='Operation/operation/<?=$re['id']?>'>Cancel</a>  </button>
        <?php endforeach;?>
        <?php endforeach;?>
        <input  class="  float-reigt " style="color:black" type="submit" value="Save">
    </div>

    <table>
        <tr>
            <div class=" mb-3">
                <label for="titles" class="form-label" >Title : </label>
                <input class="form-text form-control" type="text" id="titles" name="titles" type="text" value="<?php echo $_POST['titles'] ?? $operations->title; ?>">
            </div>
        </tr>
        <?php if (isset($errors['titles'])): ?>
            <div class="errors">
                <?php foreach ($errors['titles'] as $error): ?>
                    <ul>
                        <li style="color: red"><?= $error ?></li>
                    </ul>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <tr>
            <div class="input-group mb-3">
                <label for="amounts" class="form-label"></label>
                <input class="form-text form-control" id="amounts" name="amounts" type="number" step="any" value="<?php echo $_POST['amounts'] ?? $operations->amount ?>"
                       title="Enter the amount in euros">
                <span class="input-group-text">EUR</span>
            </div>
        </tr>
        <?php if (isset($errors['amounts'])): ?>
            <div class="errors">
                <?php foreach ($errors['amounts'] as $error): ?>
                    <ul>
                        <li style="color: red"><?= $error ?></li>
                    </ul>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <tr>
            <div class="mb-3">
                <label for="operation_da" class="form-label">Date </label>
                <div class="input-group mb-3">
                    <input class="form-text form-control" id="operation_da" name="operation_da" type="date"  value="<?=$operations->operation_date?>">
                </div>
            </div>
        </tr>
        <?php if (isset($errors['operation_da'])): ?>
            <div class="errors">
                <?php foreach ($errors['operation_da'] as $error): ?>
                    <ul>
                        <li style="color: red"><?= $error ?></li>
                    </ul>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>


        <tr>
            <label for="pes" >Paid by : </label>
            <select class="btn btn-outline-secondary dropdown-toggle form-control" name="pes" id="pes">
                <?php foreach ($participons as $participon): ?>
                    <option value="<?=$participon['full_name']?>" <?=($fullnames==$participon['full_name']) ? 'selected' : ''?>><?=$participon['full_name']?></option>
                <?php endforeach ;?>
            </select>
        </tr>
        <tr >
            <div id="checkboxid">
                <?php foreach ($participons as $i => $participon):?>
                    <div class="input-group mb-3  " >
                        <div class="input-group-text">
                            <label for="res-<?= $i ?>"></label>
                            <input  id="res-<?= $i ?> "  type="checkbox" name="names[<?= $i ?>]" value="<?= $participon[0] ?>"
                                <?php if (in_array($participon[0], array_column($CheckedParts, 0))) echo 'checked'; ?>>
                        </div>

                        <label class="form-control " for="check"><?= $participon[0] ?></label>

                        <div class="input-group-text amountjs " style="display: none;" >
                            <?php foreach ($rems as $rem): ?>
                            <span> Amount <br>
                <input class="form-control"  name="amounted"  id="amounted <?= $i ?>" type="number"
                       value="<?php
                       foreach ($CheckedParts as $Part) {
                           if($Part[0] == $participon[0]){
                               echo $Part[1]*$operations->amount/$rem[0];
                           }
                       }
                       if (!in_array($participon[0], array_column($CheckedParts, 0))) {
                           echo 0; // initialiser la valeur à 0 si la case n'est pas cochée
                       }
                       ?>"  disabled>
                <?php endforeach; ?>
                        </div>

                        <div class="input-group-text" >
                            <input class="form-control"  type="number" placeholder="Weight" name="weights[<?= $i ?>]" min="0"
                                   value="<?php
                                   foreach ($CheckedParts as $Part) {
                                       if($Part[0] == $participon[0]){
                                           echo $Part[1];
                                       }
                                   }
                                   if (!in_array($participon[0], array_column($CheckedParts, 0))) {
                                       echo 0;
                                   }
                                   ?>">
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
        </tr>
    </table>
</form>
</body>
<footer>
    <div class="input-group mb-3">
        <button id="delete-button" class="btn btn-outline-danger form-control btn btn-outline-danger mt-3" name="delete-button" type="button">
            <a href="Operation/delet/<?=$re['id']?>">Delete this operation</a>
        </button>
    </div>


</footer>
</html>

