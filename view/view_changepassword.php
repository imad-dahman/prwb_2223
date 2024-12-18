<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
    <title>change password</title>
    <base href="<?= $web_root ?>"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" >
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous">
    <script src="lib/sweetalert2@11.js" type="text/javascript"></script>
    <script src="lib/jquery-3.6.3.min.js" type="text/javascript"></script>
    <script src="lib/just-validate-4.2.0.production.min.js" type="text/javascript"></script>
    <script src="lib/just-validate-plugin-date-1.2.0.production.min.js" type="text/javascript"></script>
    <script src="https://unpkg.com/just-validate@latest/dist/just-validate.production.min.js"></script>
    <script src="https://unpkg.com/browse/just-validate-plugin-date@1.2.0/dist/just-validate-plugin-date.production.min.js"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const cancelButton = document.getElementById('cancel-password');
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
        let pass_new_verifer=false;
        let pass_verifer=false;
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
                const validation = new JustValidate('#changepassword', {
                    validateBeforeSubmitting: true,
                    lockForm: true,
                    focusInvalidField: false,
                    successLabelCssClass: ['success'],
                    errorLabelCssClass: ['errors'],
                    errorFieldCssClass: ['is-invalid'],
                    successFieldCssClass: ['is-valid'],
                });

                validation
                    .addField('#passwordd', [
                        {
                            rule: 'required',
                            errorMessage: 'Field is required'
                        },
                    ], {successMessage: 'Looks good !'})

                    .addField('#new_password', [
                        {
                            rule: 'required',
                            errorMessage: 'Field is required'
                        },
                        {
                            rule: 'minLength',
                            value: 8,
                            errorMessage: 'Minimum 8 characters'
                        },
                        {
                            rule: 'maxLength',
                            value: 16,
                            errorMessage: 'Maximum 16 characters'
                        },
                        {
                            rule: 'customRegexp',
                            value: /[A-Z]/,
                            errorMessage: 'Password must contain an uppercase letter'
                        },
                        {
                            rule: 'customRegexp',
                            value: /\d/,
                            errorMessage: 'Password must contain a digit'
                        },
                        {
                            rule: 'customRegexp',
                            value: /['";:,.\/?\\-]/,
                            errorMessage: 'Password must contain an special character'
                        },
                    ], {successMessage: 'Looks good !'})

                    .addField('#confirm_password', [
                        {
                            rule: 'required',
                            errorMessage: 'Field is required'
                        },
                        {
                            rule: 'minLength',
                            value: 8,
                            errorMessage: 'Minimum 8 characters'
                        },
                        {
                            rule: 'maxLength',
                            value: 16,
                            errorMessage: 'Maximum 16 characters'
                        },
                        {
                            rule: 'customRegexp',
                            value: /[A-Z]/,
                            errorMessage: 'Password must contain an uppercase letter'
                        },
                        {
                            rule: 'customRegexp',
                            value: /\d/,
                            errorMessage: 'Password must contain a digit'
                        },
                        {
                            rule: 'customRegexp',
                            value: /['";:,.\/?\\-]/,
                            errorMessage: 'Password must contain an special character'
                        },
                        {
                            validator: function (value, fields) {
                                if (fields['#new_password'] && fields['#new_password'].elem) {
                                    const repeatPasswordValue = fields['#new_password'].elem.value;
                                    return value === repeatPasswordValue;
                                }
                                return true;
                            },
                            errorMessage: 'Passwords should be the same',
                        },
                    ], {successMessage: 'Looks good !'})

                    .onValidate(debounce(async function (event) {
                        const new_password = $("#new_password").val();
                        const passwordd = $("#passwordd").val();
                        const passExist1 = await $.post("User/changepassword_verifer/", {passwordd: passwordd});
                        const passExist2 = await $.post("User/changepassword_verifers/", {new_password: new_password});
                        if (passExist1 === "true") {
                            pass_verifer = true;
                            this.showErrors({'#passwordd': 'Your password is incorrect'});
                        } else
                            pass_verifer = false;
                        if(passExist2 === "true"){
                            pass_new_verifer = true;
                            this.showErrors({'#new_password': 'le nouveau mdp doit être différent du précédent'});
                        }
                        else
                            pass_new_verifer = false;
                    }, 300))

                    .onSuccess(function (event) {
                        if (!pass_verifer && !pass_new_verifer)
                            event.target.submit();
                    });

                $("input:text:first").focus();
            });
        }
    </script>



</head>
<body>
<h1 class=" p-3 bg-primary border border-primary-subtle text-center">Change Password</h1>

<form id="changepassword" action="User/changepassword"  method="post">
    <table>
        <tr>
            <div class="input-group mb-3">
                <span class="input-group-text"> <i class="fas fa-lock" aria-hidden="true"></i> </span>
                <input class="form-text form-control" id="passwordd" name="passwordd" type="password" placeholder="Passwordd"  value="<?=$passwordd?>">
            </div>
        </tr>
        <tr>
        <div class="input-group mb-3">
                <span class="input-group-text"> <i class="fas fa-lock" aria-hidden="true"></i></span>
                <input class="form-text form-control" id="new_password" name="new_password" type="password" placeholder="New_password"  value="<?=$new_password?>">
                </div>
        </tr>
    <tr>
    <div class="input-group mb-3">
                <span class="input-group-text"><i class="fas fa-lock" aria-hidden="true"></i></span>
                <input class="form-text form-control" id="confirm_password" name="confirm_password" type="password" placeholder="confirm_password"  value="<?= $confirm_password ?>">
                </div>
    </tr>
</table>
<div class="input-group mb-3">
       <input class=" btn btn-primary form-control" type="submit"  value="change" onclick="myFunction()"><br>
        </div>
    <div class="input-group mb-3">
        <button type="button" class=" btn btn-primary form-control" id="cancel-password" > <a style="color:black" href='setting'>Cancel</a> </button>
    </div>
</form>

</div>
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
</div>
</body>
</html>
