<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>add_operation</title>
    <base href="<?=$web_root?>"/>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" >
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous">
    <script src="lib/jquery-3.6.3.min.js" type="text/javascript"></script>
    <script src="lib/just-validate-plugin-date-1.2.0.production.min.js" type="text/javascript"></script>
    <script src="https://unpkg.com/just-validate@latest/dist/just-validate.production.min.js"></script>
    <script src="https://unpkg.com/browse/just-validate-plugin-date@1.2.0/dist/just-validate-plugin-date.production.min.js"></script>
    <script src="lib/sweetalert2@11.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {

            function update() {
                let totalAmount = parseFloat($("#amount").val());
                let count = $('input[name^="weight"]').length;
                let totalWeight = 0;
                let positiveWeights = true;
                $('input[name^="weight"]').each(function() {
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
                    $("input[name^='weight']").each(function() {
                        var weight = parseFloat($(this).val());
                        var amountToPay = 0;
                        if (weight > 0) {
                            amountToPay = totalAmount * weight / totalWeight;
                        }
                        $(this).closest(".input-group").find("input[name='amounted']").val(amountToPay);

                        $(this).data("previous-weight", weight);
                        var personInput = $(this).closest(".input-group").find("input[name^='name']");
                        if (weight >= 1) {
                            personInput.prop("checked", true);
                        } else {
                            $(this).closest(".input-group").find("input[name^='name']").prop("checked", false);
                        }
                        $(this).data("previous-weight", weight);
                    });
                }
            }
            $("#amount").on("change", function() {
                update();
            });

            $("input[name^='weight']").on("change", function() {
                if ($(this).val() === 0) {
                    $(this).closest(".input-group").find("input[name^='name']").prop("checked", false);
                }

                update();
            });
            $("input[name^='name']").on("change", function() {
                var isChecked = $(this).prop("checked");
                var weights = $(this).closest(".input-group").find("input[name^='weight']");
                var weight = parseFloat(weights.val());
                if (isChecked) {
                    weights.val(1);
                    if (weight > 0) {
                        update();
                    }
                } else {
                    weights.val(0);
                    $(this).closest(".input-group").find("input[name='amounted']").val(0);
                }

                update();
            });
            $('input[name^="name"]').each(function() {
                update();
            });

        });
    </script>
    <script>
        $(document).ready(function() {
            var isJsEnabled = true;
            if(!window.jQuery) {
                isJsEnabled = false;
            }

            if (isJsEnabled) {
                $('.amountjs').show();
            } else {
                $('.amountjs').hide();
            }
        });
    </script>
    <script>
        let titleAvailable=false;let idtricounts=false;
        let id ="<?=$idtricount?>" ;
        const justvalidateEnabled = "<?= Configuration::get('just_validate') ?>";
        if(justvalidateEnabled) {

            function debounce(fn, time) {
                var timer;

                return function () {
                    clearTimeout(timer);
                    timer = setTimeout(() => {
                        fn.apply(this, arguments);
                    }, time);
                }
            }

            $(function () {
                const validator = new JustValidate('#add_form', {
                    validateBeforeSubmitting: true,
                    lockForm: true,
                    focusInvalidField: false,
                    successLabelCssClass: ['success'],
                    errorLabelCssClass: ['errors'],
                    errorFieldCssClass: ['is-invalid'],
                    successFieldCssClass: ['is-valid']
                });

                validator
                    .addField('#title', [
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
                            value: /^[a-zA-Z][a-zA-Z0-9 ]*$/,
                            errorMessage: 'Title must start with a letter and can contain only letters, numbers, and spaces.'
                        },
                    ], {successMessage: 'Looks good!'})
                    .addField('#amount', [
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
                            errorMessage: '<div>Amount must be greater than or equal to 1 cent</div> '
                        },
                    ], {
                        successMessage: '<div>Looks good!</div>'
                    })
                    .addField('#operation_date', [
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
                    .addField('#pets', [
                        {
                            rule: 'required',
                        },
                    ])

                    .addRequiredGroup('#checkboxadd', 'Select at least one participant', {})


                    .onValidate(debounce(async function (event) {
                        const title = $("#title").val();
                        titleAvailable = await $.post("Operation/title_available_service/" , {title:title} );
                        idtricounts = await $.post("Operation/title_available_services/"  , {title:title});
                        if (titleAvailable === "false" && idtricounts === id)
                            this.showErrors({'#title': 'title already exists'});
                    }, 300))


                    .onSuccess(function (event) {
                        if (!titleAvailable && idtricounts !== id)
                            event.target.submit();
                        else if (titleAvailable)
                            event.target.submit();
                    });

            });
        }
    </script>

    <script>


            document.addEventListener('DOMContentLoaded', function () {
                const justvalidateEnabled = "<?= Configuration::get('just_validate') ?>";
                if(justvalidateEnabled) {
                    const cancelButton = document.getElementById('cancel-add');

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
                                window.location.href = `tricount/tri/<?=$idtricount?>`;
                            }
                        });
                    });
                }
            });

    </script>



</head>
<body>

<form id="add_form"   action="Operation/addoperation/<?=$idtricount?>" method="post">
    <div class=" p-3 bg-primary text-white p-20 border border-primary-subtle col-sm text-right ">
        <button type="button" class=" float-left " id="cancel-add" ><a style="color:black" href='tricount/tri/<?=$idtricount?>'>Cancel</a></button>
        <input class="  float-reigt" type="submit"  value="Save"  style="color:black" ><br>
    </div>
    <table>
        <tr>
            <div class="input-group mb-3">
                <input class="form-text form-control" id="title" placeholder="Title" name="title" type="text"  value="<?= $title ?>">
            </div>
        </tr>
        <?php if (isset($errors['title'])): ?>
            <div class="errors">
                <?php foreach ($errors['title'] as $error): ?>
                    <ul>
                        <li style="color: red"><?= $error ?></li>
                    </ul>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <tr>
            <div class="input-group mb-3">
                <input class="form-text form-control" id="amount" name="amount" placeholder="Amount" type="number"   value="<?= $amount ?>">
                <span class="input-group-text">EUR</span>
            </div>
        </tr>
        <?php if (isset($errors['amount'])): ?>
            <div class="errors">
                <?php foreach ($errors['amount'] as $error): ?>
                    <ul>
                        <li style="color: red"><?= $error ?></li>
                    </ul>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <tr>
            <div class="mb-3">
                <label for="basic-url" class="form-label">Date </label>
                <div class=" mb-3">
                    <input class="form-text form-control" id="operation_date" name="operation_date" type="date"  value="<?=$operation_date?>">
                </div>
            </div>
        </tr>
        <?php if (isset($errors['operation_date'])): ?>
            <div class="errors">
                <?php foreach ($errors['operation_date'] as $error): ?>
                    <ul>
                        <li style="color: red"><?= $error ?></li>
                    </ul>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <tr>
            <label for="basic-url" class="form-label">Paid by : </label>


            <select class="btn btn-outline-secondary dropdown-toggle form-control" name="pest" id="pets">
                <?php foreach ($participons as $participon): ?>
                    <option  value="<?=$participon[0]?>"><?= $participon[0] ?></option>
                <?php endforeach ;?>
            </select>


        </tr>
        <div id="checkboxadd">
            <label for="basic-url" class="form-label">For whom? (select at least one)</label>

            <?php foreach ($participons as $x=>$participon): ?>
                <div class="input-group mb-3">
                    <div class="input-group-text">
                        <input id="name"  name="name<?=$x?>" type="checkbox"  value="<?=$participon[0]?>" >
                    </div>
                    <label class="form-control"  for="check"><?= $participon[0] ?></label>
                    <div class="input-group-text amountjs " style="display: none;">
                <span> Amount <br>
                    <input class="form-control"   name="amounted"  id="amounted" type="number"
                           value="<?=$amount?>"  disabled>
                    </div>
                    <div class="input-group-text">
                        <input class="form-control" id="weight" name="weight<?=$x?>" type="number" placeholder="Weight" min="0" >
                    </div>

                    <?php if (isset($errors['weight'.$x]) && is_array($errors['weight'.$x])): ?>
                        <div class="errors">
                            <?php foreach ($errors['weight'.$x] as $error): ?>
                                <ul>
                                    <li style="color: red"><?= $error ?></li>
                                </ul>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <?php if (isset($errors['name']) && empty($_POST['name'])): ?>
                <div class="errors">
                    <ul>
                        <li style="color: red"><?= $errors['name'] ?></li>
                    </ul>
                </div>
            <?php endif; ?>

        </div>

    </table>

</form>


</body>
</html>
