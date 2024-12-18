<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Profil</title>
    <base href="<?= $web_root ?>"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" >
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous">
    <script src="lib/jquery-3.6.3.min.js" type="text/javascript"></script>
    <script src="https://unpkg.com/just-validate@latest/dist/just-validate.production.min.js"></script>
    <script src="lib/just-validate-plugin-date-1.2.0.production.min.js" type="text/javascript"></script>
    <script src="https://unpkg.com/browse/just-validate-plugin-date@1.2.0/dist/just-validate-plugin-date.production.min.js"></script>
    <script src="lib/sweetalert2@11.js" type="text/javascript"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const cancelButton = document.getElementById('cancel-edit-profil');
            const justvalidateEnabled = "<?= Configuration::get('just_validate') ?>";
            if(justvalidateEnabled) {

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
                            window.location.href = `setting`;
                        }
                    });
                });
            }
        });
    </script>
    <script>
        let emailExists = false;

        // Variables pour stocker les valeurs précédentes de l'email et du nom
        const previousEmail = "<?=$email?>";
        const justvalidateEnabled = "<?= Configuration::get('just_validate') ?>";
        if(justvalidateEnabled) {

            function debounce(fn, time) {
                let timer;

                return function () {
                    clearTimeout(timer);

                    timer = setTimeout(() => {
                        fn.apply(this, arguments);
                    }, time);
                };
            }

            $(function () {
                const validation = new JustValidate('#editprofil', {
                    validateBeforeSubmitting: true,
                    lockForm: true,
                    focusInvalidField: false,
                    successLabelCssClass: ['success'],
                    errorLabelCssClass: ['errors'],
                    errorFieldCssClass: ['is-invalid'],
                    successFieldCssClass: ['is-valid'],
                });

                validation
                    .addField('#email', [
                        {
                            rule: 'required',
                        },
                        {
                            rule: 'email',
                        },
                    ], {successMessage: 'Looks good!'})
                    .addField('#name', [
                        {
                            rule: 'required',
                            errorMessage: 'Field is required',
                        },
                        {
                            rule: 'minLength',
                            value: 3,
                            errorMessage: 'Minimum 3 characters',
                        },
                        {
                            rule: 'maxLength',
                            value: 16,
                            errorMessage: 'Maximum 16 characters',
                        },
                        {
                            rule: 'customRegexp',
                            value: /^[a-zA-ZÀ-ÖØ-öø-ÿ'][a-zA-ZÀ-ÖØ-öø-ÿ0-9 ']*$/,
                            errorMessage: 'Pseudo must start by a letter and must contain only letters and numbers',
                        },
                    ], {successMessage: 'Looks good!'})

                    .onValidate(debounce(async function (event) {
                        const email = $("#email").val();

                        const emailResponse = await $.post("User/email_available_service", {param1: email});

                        if (emailResponse === "false" && email !== previousEmail) {
                            emailExists = true;
                            this.showErrors({'#email': 'Email already exists'});
                        } else {
                            emailExists = false;
                        }

                    }, 300))

                    .onSuccess(function (event) {
                        if (!emailExists) {
                            event.target.submit();
                        }
                    });
            });
        }
    </script>

</head>
<body>
<h1 class=" p-3 bg-primary border border-primary-subtle text-center">Edit Profil</h1>
<form id="editprofil" action="User/editprofil" method="post">
    <table>
        <tr>
            
            <div class="input-group mb-3">
                <span class="input-group-text">Email</i></span>
                <input class="form-text form-control"  id="email" name="email" type="email" value="<?php echo $_POST['email'] ?? $email ?>">

                </div>
        </tr>
        <tr>

            <div class="input-group mb-3">
                <span class="input-group-text">IBAN</i></span>
                <input class="form-text form-control"  id="Iban" name="Iban" type="text" value="<?php echo $_POST['Iban'] ?? $Iban ?>">
            </div>
        </tr>
        <tr>

            <div class="input-group mb-3">
                <span class="input-group-text">Name</i></span>
                <input class="form-text form-control"  id="name" name="name" type="text" value="<?php echo $_POST['name'] ?? $name ?>">
            </div>
        </tr>
        

    </table>
    <div class="input-group mb-3">
       <input class=" btn btn-primary form-control" type="submit"  value="Modifier" ><br>
        </div>
    <div class="input-group mb-3">
        <button type="button" class=" btn btn-primary form-control" id="cancel-edit-profil" > <a style="color:black" href='setting'>Cancel</a> </button>
    </div>
</form>
<?php if (count($errors) != 0): ?>
    <div class='errors'>
        <br><br><p>Please correct the following error(s) :</p>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

</body>

</html>
