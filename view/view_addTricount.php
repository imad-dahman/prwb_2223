<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tricounts</title>
    <base href="<?= $web_root ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/MyStyle.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" >
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous">
    <script src="lib/jquery-3.6.3.min.js" type="text/javascript"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="lib/just-validate-4.2.0.production.min.js" type="text/javascript"></script>
    <script src="lib/just-validate-plugin-date-1.2.0.production.min.js" type="text/javascript"></script>
    <script src="lib/sweetalert2@11.js" type="text/javascript"></script>
    <script>
        let errDescription, description, title, errPseudo, pseudoExists = false;
        const javavalidateEnabled = "<?= Configuration::get('java_validate') ?>";
        if(javavalidateEnabled) {

            function checkPseudo() {
                let ok = true;
                errPseudo.html("");
                title.removeClass("is-invalid is-valid");
                if (!(/^.{3,16}$/).test(title.val())) {
                    errPseudo.html("<p>Title length must be between 3 and 16.</p>");
                    title.addClass("is-invalid");
                    ok = false;
                } else if (title.val().length > 0 && !(/^[a-zA-Z][a-zA-Z0-9 ]*$/).test(title.val())) {
                    errPseudo.html(errPseudo.html() + "<p>Title must start by a letter and must contain only letters and numbers.</p>");
                    title.addClass("is-invalid");
                    ok = false;
                } else if ((/^[  ]*$/)) {
                    title.addClass("is-valid");
                    ok = true;

                } else if (ok) {
                    checkPseudoExists();
                }

                return ok;
            }

            async function checkPseudoExists() {
                const title1= title.val();
                const data = await $.post("Tricount/tricount_exists_service/" ,{title:title1});
                if (data==="true") {
                    errPseudo.html("<p>Title already exists for this creator.</p>");
                    title.addClass("is-invalid");
                    pseudoExists = true;
                } else if (title.val().length >= 3 || data==="false") {
                    title.addClass("is-valid");
                    pseudoExists = false;
                }
            }

            function checkDescription() {
                let ok = true;
                errDescription.html("");
                description.removeClass("is-invalid is-valid");
                if (description.val().length > 0) {
                    if (!(/^.{3,16}$/).test(description.val())) {
                        errDescription.html("<p>Description length must be between 3 and 16.</p>");
                        description.addClass("is-invalid");
                        ok = false;
                    }
                }
                if (ok) {
                    description.addClass("is-valid");
                }
                return ok;
            }

            function checkAll() {
                let ok = checkPseudo();
                ok = checkDescription() && ok;
                if (!ok || pseudoExists) {
                    return false;
                }
                return true;
            }

            $(function () {
                title = $("#title");
                description = $("#description");
                errPseudo = $("#errPseudo");
                errDescription = $("#errDescription");
                title.on("input", checkPseudo);
                title.on("blur", checkPseudoExists);
                description.on("input", checkDescription);
                description.on("blur", checkDescription);

                $("input:text:first").focus();

                $("#save").on("click", function (e) {
                    e.preventDefault();
                    console.log(checkAll());
                    if (checkAll()) {
                        $("#form").submit();
                    }
                });
            });
        }
    </script>
    <script>


        let TitleExist=false;
        const justvalidateEnabled = "<?= Configuration::get('just_validate') ?>";
        if(justvalidateEnabled) {

            function debounce(fn, time) {
                // Variable pour stocker l'ID du timer
                var timer;

                // La fonction renvoyée qui encapsule `fn`
                return function () {
                    // Annule le timer précédent (s'il existe) pour éviter que la fonction ne soit exécutée plusieurs fois
                    clearTimeout(timer);

                    // Crée un nouveau timer pour appeler la fonction `fn` après `time` millisecondes
                    timer = setTimeout(() => {
                        // Applique la fonction `fn` avec les mêmes arguments que ceux passés à la nouvelle fonction
                        fn.apply(this, arguments);
                    }, time);
                }
            }

            $(function () {
                const validation = new JustValidate('#AddTricountForm', {
                    validateBeforeSubmitting: true,
                    lockForm: true,
                    focusInvalidField: false,
                    successLabelCssClass: ['success'],
                    errorLabelCssClass: ['errors'],
                    successFieldCssClass: ['is-valid'],
                    errorFieldCssClass: ['is-invalid']
                });

                validation
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
                            value: 16,
                            errorMessage: 'Maximum 16 characters'
                        },
                        {
                            rule: 'customRegexp',
                            value: /^[a-zA-Z][a-zA-Z0-9 ]*$/,
                            errorMessage: 'Title must start by a letter and must contain only letters and numbers'
                        },
                    ], {successMessage: 'Looks good !'})

                    .addField('#description', [
                        {
                            rule: 'minLength',
                            value: 3,
                            errorMessage: 'Minimum 3 characters'
                        },
                        {
                            rule: 'maxLength',
                            value: 16,
                            errorMessage: 'Maximum 16 characters'
                        }
                    ], {successMessage: 'Looks good !'})
                    .onValidate(debounce(async function (event) {
                        const title = $("#title").val();
                        const TitleExist1 = await $.post("Tricount/tricount_exists_service/", {title: title});
                        if (TitleExist1 === "true") {
                            TitleExist = true;
                            this.showErrors({'#title': 'Tilte already exists'});
                        } else
                            TitleExist = false;
                    }, 300))
                    .onSuccess(function (event) {
                        if (!TitleExist)
                            event.target.submit(); //par défaut le form n'est pas soumis
                    });

                $("input:text:first").focus();
            });

        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded',function ()
            {
                const justvalidateEnabled = "<?= Configuration::get('just_validate') ?>";
                if(justvalidateEnabled) {
                    const cancelButton = document.getElementById('btn-cancel');
                    cancelButton.addEventListener('click', function (event) {
                        event.preventDefault();
                        Swal.fire(
                            {
                                title: 'Unsaved changes !',
                                text: "are you sure you want to leave this form ? changes you made will not be saved.",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#8B0000',
                                cancelButtonColor: '#696969',
                                confirmButtonText: 'Leave Page',
                                cancelButtonText: 'Cancel'
                            }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'user/tricounts';
                            }
                        });
                    });
                }
            });
    </script>

</head>
<body>
<form id="AddTricountForm" action="Tricount/saveTricount" method="post" onsubmit="return checkAll()">
    <div class="was-validate">
        <div class=" p-3 bg-primary text-white p-20 border border-primary-subtle col-sm text-right ">
            <h3 class="text-center">Tricount > Add</h3>
            <button id="btn-cancel" type="button" class=" float-left "><a style="color:black" href="user/tricounts">Cancel</a></button>
            <input class=" float-right" type="submit" value="Save" style="color:black"><br>
        </div>

        <div class="from-group">
            <label for="title" class="form-label">Title :</label>
            <input class="form-control" value="<?=$title?>" type="text" id="title" name="title">
            <div style="color: red" class="errors" id="errPseudo"></div>
        </div>
        <div class="from-group">
            <label for="description" class="form-label">Description (optional) :</label>
            <textarea type="text" class="form-control"  name="description" id="description" rows="3"><?=$description?></textarea>
            <div class="errors" id="errDescription"></div>
        </div>
    </div>
</form>
</div>

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
<br><br>
</body>
</html>
