<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <base href="<?=$web_root?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" >
    
    <script>
        function myFunction() {
            let text = "SignUp ok!\nEither OK or Cancel.";
            if (confirm(text) == true) {
                <a href="main/login"></a>;
            } else {
                <a href="main/login"></a>;
            }
        }
    </script>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous">
    <script src="lib/jquery-3.6.3.min.js" type="text/javascript"></script>
    <script src="lib/just-validate-4.2.0.production.min.js" type="text/javascript"></script>
    <script src="lib/just-validate-plugin-date-1.2.0.production.min.js" type="text/javascript"></script>
    <script src="https://unpkg.com/just-validate@latest/dist/just-validate.production.min.js"></script>
    <script src="https://unpkg.com/browse/just-validate-plugin-date@1.2.0/dist/just-validate-plugin-date.production.min.js"></script>

    <script>
        let emailExists = false;
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
                const validation = new JustValidate('#signupForm', {
                    validateBeforeSubmitting: true,
                    lockForm: true,
                    focusInvalidField: false,
                    successLabelCssClass: ['success'],
                    errorLabelCssClass: ['errors'],
                    errorFieldCssClass: ['is-invalid'],
                    successFieldCssClass: ['is-valid'],
                });

                validation
                    .addField('#mail', [
                        {
                            rule: 'required',
                        },
                        {
                            rule: 'email',
                        },
                    ], {successMessage: 'Looks good !'})
                    .addField('#full_name', [
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
                            errorMessage: 'Pseudo must start by a letter and must contain only letters and numbers'
                        },
                    ], {successMessage: 'Looks good !'})
                    .addField('#hashed_password', [
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

                    .addField('#password_confirm', [
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
                                if (fields['#hashed_password'] && fields['#hashed_password'].elem) {
                                    const repeatPasswordValue = fields['#hashed_password'].elem.value;
                                    return value === repeatPasswordValue;
                                }
                                return true;
                            },
                            errorMessage: 'Passwords should be the same',
                        },
                    ], {successMessage: 'Looks good !'})
                    .onValidate(debounce(async function (event) {
                        const mail = $("#mail").val();

                        const emailResponse = await $.post("User/email_available_service", {param1: mail});


                        if (emailResponse === "false" ) {
                            emailExists = true;
                            this.showErrors({'#mail': 'Email already exists'});
                        } else {
                            emailExists = false;
                        }
                    }, 300))

                    .onSuccess(function (event) {

                        if (!emailExists) {
                            event.target.submit();
                        }
                    });


                $("input:text:first").focus();
            });
        }
    </script>



</head>
<body >
    <h1 class=" p-3 bg-primary border border-primary-subtle  "   >
        <i class="fas fa-cat" style="color:white" aria-hidden="true"></i>   Tricount</h1>

    <div class="card " >
<div  class="text-center mt-3 ">Sign Up</div>
<div class="menu">
    <a href=""></a>
</div>
<div class="main">
<br><br>
    <form   id="signupForm"  class="container col mb-3 "   action="main/signup" method="post">
        <table>
            <tr>
            <div class="input-group mb-3">
            <span class="input-group-text">@</span>
            <input  id="mail" class="form-text form-control " placeholder="Mail"  name="mail" type="email"  value="<?= $mail ?>">
            </div>
            
            </tr>
            <tr>
            
            <div class="input-group mb-3">
            <span class="input-group-text"><i class="fas fa-user" aria-hidden="true"></i></span>
            <input  class="form-text form-control " id="full_name" placeholder="Name" name="full_name" type="text"  value="<?= $full_name ?>">
            </div>
            </tr>
                 
            <tr>
                <div class="input-group mb-3">
                <span class="input-group-text"><i class="fas fa-credit-card" aria-hidden="true"></i></span>
                <input class="form-text form-control" id="iban" placeholder="Iban" name="iban" type="text"  value="<?= $iban ?>">
                </div>
            </tr>
            <tr>
                <div class="input-group mb-3">
                <span class="input-group-text"><i class="fas fa-lock" aria-hidden="true"></i></span>
                <input class="form-text form-control" id="hashed_password" placeholder="Password" name="hashed_password" type="password"  value="<?= $hashed_password ?>">
                </div>
            </tr>
            <tr>
                <div class="input-group mb-3">
                <span class="input-group-text"><i class="fas fa-lock" aria-hidden="true"></i></span>
                <input class="form-text form-control" id="password_confirm" placeholder="Password_confirm" name="password_confirm" type="password"  value="<?= $password_confirm ?>">
                </div>
            </tr>
        </table>
        <div class="input-group mb-3">
       <input class=" btn btn-primary form-control" type="submit"  value="Sign Up" onclick="myFunction()"><br>
        </div>
        <div class="input-group mb-3">
       <button class=" btn btn-outline-danger form-control btn btn-outline-danger" type="button" ><a href="main/login" >Cancel</a></button>   
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
</div>
    </div>
</body>
</html>