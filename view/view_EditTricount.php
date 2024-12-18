<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>EditTricounts</title>
    <base href="<?= $web_root ?>"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" >
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="lib/jquery-3.6.3.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js%22%3E"</script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.2/js/bootstrap.bundle.min.js%22%3E"</script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="lib/sweetalert2@11.js" type="text/javascript"></script>
    <script src="lib/just-validate-4.2.0.production.min.js" type="text/javascript"></script>
    <script src="lib/just-validate-plugin-date-1.2.0.production.min.js" type="text/javascript"></script>

    <style>
        .cell {
            padding-right: 20px;
            position: relative;
        }
    </style>


    <script>
        let errDescription, description, title, errPseudo, pseudoExists = false,checkCreator=false;
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
            }
            else if (title.val().length > 0 && !(/^[a-zA-Z][a-zA-Z0-9 ]*$/).test(title.val())) {
                errPseudo.html(errPseudo.html() + "<p>Title must start by a letter and must contain only letters and numbers.</p>");
                title.addClass("is-invalid");
                ok = false;
            }
            else if( (/^[  ]*$/)){
                title.addClass("is-valid");
                ok=true;

            }
            else if (ok) {
                checkPseudoExists();
            }

            return ok;
        }

   /*     async function checkCreator1() {
            const data1 = await $.getJSON("Tricount/tricount_creator_service/" + title.val());
            if (!data1)
            {
                errPseudo.html("<p>juste le créateur du tricount peut modifier le nom.</p>");
                title.addClass("is-invalid");
                checkCreator = false;
            }
        else
            {
                title.addClass("is-valid");
                checkCreator = true;
            }
        }*/

            async function checkPseudoExists() {
                let OLDTITLE = "<?= $title?>";

                if(title.val().toUpperCase()=== "<?=$title?>".toUpperCase()){
                    title.addClass("is-valid");
                    pseudoExists=false;
                    return;
                }
                const title1 = title.val();
                const data = await $.post("Tricount/tricount_exists_service/" ,{title:title1});
                const  TricountCreator = await $.post("Tricount/tricount_creator_service/" ,{param1:OLDTITLE});
                if (data==="true") {
                    errPseudo.html("<p>Title already exists for this creator.</p>");
                    title.addClass("is-invalid");
                    pseudoExists = true;
                } else if(title.val().length >=3|| data==="false") {
                    title.addClass("is-valid");
                    pseudoExists = false;
                }
                if (TricountCreator==="false")
                {
                    errPseudo.html("<p>Only creator can edit the title.</p>");
                    title.addClass("is-invalid");
                    pseudoExists = true;
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

        $(function() {
            title = $("#title");
            description = $("#description");
            errPseudo = $("#errPseudo");
            errDescription = $("#errDescription");
            title.on("input", checkPseudo);
            title.on("blur", checkPseudoExists);
            description.on("input", checkDescription);
            description.on("blur", checkDescription);
            $("#save").on("click", function(e) {
                e.preventDefault();
                if (checkAll()) {
                    $("#form").submit();
                }
            });
        });}
    </script>
    <script>

        document.addEventListener('DOMContentLoaded',function ()
        {
            const justvalidateEnabled = "<?= Configuration::get('just_validate') ?>";
            if (justvalidateEnabled) {
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
<script>
    document.addEventListener('DOMContentLoaded',function (){
        const justvalidateEnabled = "<?= Configuration::get('just_validate') ?>";
        if (justvalidateEnabled) {
            const deleteButton = document.getElementById('btn-delete');
            deleteButton.href = 'Tricount/delete/' + <?=$tricount?>;
            deleteButton.addEventListener('click', function (event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    html: `
                        <p>Do you really want to delete Tricount "<b><?=$titreTricount ?></b>" and all of its dependencies ?. </p>
                        <p> this process cannot be undone. <p>
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
                        fetch(deleteButton.href, {method: 'POST'}).then(response => {
                            if (response.ok) {
                                Swal.fire(
                                    'Deleted!',
                                    'Your file has been deleted.',
                                    'success'
                                ).then(() => {
                                    window.location.href = "Tricount/index";
                                });
                            } else {
                                Swal.fire(
                                    'Erreur!',
                                    'Une erreur est survenue pendant la supression.',
                                    'Erreur'
                                );
                            }

                        }).catch(error => {
                            Swal.fire(
                                'Erreur!',
                                'Une erreur est survenue pendant la supression.',
                                'Erreur'
                            );
                        });

                    }
                });
            });
        }
    });
</script>
    <script>
        let TitleExist=false;
        let TitleCreator=false;
        let OLDTITLE = "<?= $title?>";
        const justvalidateEnabled = "<?= Configuration::get('just_validate') ?>";
        if (justvalidateEnabled) {
        function debounce(fn, time) {
            // Variable pour stocker l'ID du timer
            var timer;

            // La fonction renvoyée qui encapsule `fn`
            return function() {
                // Annule le timer précédent (s'il existe) pour éviter que la fonction ne soit exécutée plusieurs fois
                clearTimeout(timer);

                // Crée un nouveau timer pour appeler la fonction `fn` après `time` millisecondes
                timer = setTimeout(() => {
                    // Applique la fonction `fn` avec les mêmes arguments que ceux passés à la nouvelle fonction
                    fn.apply(this, arguments);
                }, time);
            }
        }

        $(function() {
            const validation = new JustValidate('#editTricount', {
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
                ],{successMessage: 'Looks good !'})
                .onValidate(debounce(async function (event) {
                    const title = $("#title").val();
                    const  TitleExist1 = await $.post("Tricount/tricount_exists_service/" ,{title:title});
                    const  TricountCreator = await $.post("Tricount/tricount_creator_service/" ,{param1:OLDTITLE});

                    if (TitleExist1=== "true" && title.toUpperCase()!==OLDTITLE.toUpperCase()){
                        TitleExist=true;
                        this.showErrors({'#title': 'Title already exists'});
                    }
                    else
                    {
                        TitleExist=false;
                    }
                    if (TricountCreator === "false") {
                        TitleCreator = true;
                        this.showErrors({'#title': 'le créateur seul peut modifier le tricount'});
                    } else
                        TitleCreator = false;

                }, 300))
                .onSuccess(function(event) {
                    if (!TitleExist && !TitleCreator)
                        event.target.submit(); //par défaut le form n'est pas soumis
                });

            $("input:text:first").focus();
        });}
    </script>
</head>
<body>
<form id="editTricount" action="Tricount/editTricount2" method="post" onsubmit="return checkAll()">
    <input type="hidden" name="tricount" value="<?=$tricount?>">

<div class=" p-3 bg-primary text-white p-20 border border-primary-subtle col-sm text-right ">
    <p class="text-center"><?=$titreTricount ?>> Edit </p>
    <button id="btn-cancel" type="button" class=" float-left " ><a style="color:black" href="user/tricounts">Back</a></button>
    <input class="  float-reigt" type="submit"  value="Save"  style="color:black"><br>
</div>
<LABEL>Settings : </LABEL>
<div class="mb-3">
    <div class=" mb-3">
        <label for="basic-url" class="form-label">Title : </label>
        <input class="form-text form-control" type="text" id="title" name="title" type="text" value="<?= $title?>">
        <div style="color: red" class="errors" id="errPseudo"></div>
    </div>

    <table>
        <td class="errors" id="errPseudo"></td>
    </table>
    <div class="mb-3">
        <label for="exampleFormControlTextarea1" class="form-label">Description (optional) :</label>
        <textarea type="text" class="form-control"  name="description" id="description" rows="3"><?=$description?></textarea>
        <div class="errors" id="errDescription"></div>
    </div>

</div>

</form>


<div>
        <label>Subscriptions</label>
    <table>
        <tr>
            <th></th>
            <th></th>
        </tr>
        <?php foreach ($participants as $participant): ?>
            <tr>
                <?php if ($participant["full_name"]==$creator["full_name"]):?>
                    <td class="cell"><?= $participant["full_name"] ?> (creator)</td>
                <?php else:?>
                    <td class="cell"><?= $participant["full_name"] ?></td>
                <?php endif;?>
               <?php if (Tricount::get_Participants_In_Operations($id,$participant["full_name"]) && $participant["full_name"]!=$creator["full_name"]):?>
                   <td>
                       <form action="tricount/deleteparticipant" method="post">
                           <button type="submit" name="delete" value="<?=$id?>">
                               <i class="fa fa-trash">     </i>
                           </button>
                           <input type="hidden" name="full_name" value="<?= $participant["full_name"] ?>">
                       </form>
                   </td>
                <?php endif;?>
            </tr>
        <?php endforeach;?>
    </table>
<form action="tricount/addparticipant" style="margin-top: 10%" method="post">
    <div class="input-group mb-3">
    <select  class="btn btn-outline-secondary dropdown-toggle form-control" id="listeParticipant" name="participants">
        <option value="default">--Add a new subscriber--</option>
        <?php foreach ($users as $user): ?>
            <option value="<?=$user["id"]?>"><?= $user["full_name"]?></option>
        <?php endforeach;?>
    </select>
   <input type="hidden" name="tricount" value="<?=$tricount?>">    <input class="btn btn-primary" type="submit" id="addParticipant" value="add">
    </div>
</form>
        <div class="input-group mb-3">
           <button id="btn-delete"   class=" btn btn-outline-danger form-control btn btn-outline-danger mt-3" ><a href="Tricount/removeTricount/<?=$tricount?>">Delete</a></button>
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
</body>
</html>
