let API = require('./API');

let $openDialog = $('#openChangeInfoDialog');
let $submitChages = $('#changeInfoBtn');
let $modal = $('#changeInfoDialog');

let $openChangeLoginDialog = $('#openChangeLoginDialog');
let $loginModal = $('#changeLoginDialog');
let $submitChangeLoginBtn= $('#submitChangeLoginBtn');

let $inputLogin = $('#currentLogin');
let $inputNew = $('#newPassword');
let $inputNewConfirm = $('#newPasswordConfirm');

let $currentLoginValue = $('#currentLoginValue');
let $warnings = $('#warning-message');
let $warningsLogin = $('#warning-message-login');

let $changeEmailDialog = $('#changeEmailDialog');
let $emailInput = $('#emailInput');
let $openChangeEmailDialog = $('#openChangeEmailDialog');
let $submitChangeEmailBtn = $('#submitChangeEmailBtn');
let $warningMessageEmail = $('#warning-message-email');

let $changeEmailSentOkModal = $('#changeEmailSentOkModal');

// let $confSendPasswordChangeDialog = $('#confSendPasswordChangeDialog');
let $confSendChangePassBtn = $('#confSendChangePassBtn');
let $passwordChangeSentOkModal = $('#passwordChangeSentOkModal');

function  initChangeDialog() {
    setValidation();
    $openDialog.click(function(){
        clearInputs();
        $submitChages.prop('disabled', true);
        $modal.modal('show');
    });
    $openChangeLoginDialog.click(function(){
        clearInputs();
        $loginModal.modal('show');
    });
    $openChangeEmailDialog.click(function(){
        clearInputs();
        $changeEmailDialog.modal('show');
    });
    initSubmitChangeLogin();
    initSubmitChangeEmail();
    initSubmitChangepassword();
}

function clearInputs(){
    $inputLogin.val($currentLoginValue.val());
    // $inputCurrrent.val('');
    $inputNew.val('');
    $inputNewConfirm.val('');
    // $emailInput.val($currentEmailValue.val());
    $warnings.text('');
    $warningsLogin.text('');
    $warningMessageEmail.text('');
}

function setValidation(){
    $inputLogin.on('input',function () {
        checkForLogin();
    });
    // $inputNew.on('input',function () {
    //     checkForCorrectInput();
    //     checkConfirm();
    // });
    // $inputNewConfirm.on('input',function () {
    //     checkForCorrectInput();
    //     checkConfirm();
    // });
    $emailInput.on('input',function(){
        checkForEmail();
    });
}

function checkForLogin() {
    if($inputLogin.val() === ''){
        $warningsLogin.text("Логін не може бути пустим!");
        $submitChangeLoginBtn.prop('disabled', true);
    }
    else{
        $submitChangeLoginBtn.prop('disabled', false);
        $warningsLogin.text("");
    }
}

function checkForEmail(){
    if($emailInput.val()!=='' && /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test($emailInput.val())){
        $warningMessageEmail.text('');
        $submitChangeEmailBtn.prop('disabled', false);
    }
    else{
        $warningMessageEmail.text('Неправильний формат пошти!');
        $submitChangeEmailBtn.prop('disabled', true);
    }
}

// function checkConfirm() {
//     if(!passwordsMatch()/*$inputNew.val() !== $inputNewConfirm.val()*/){
//         $warnings.text("Паролі не збігаються!");
//     }
//     else{
//         //$warnings.text("");
//     }
// }

// function checkForCorrectInput(){
//     if(checkInputs()){
//         $submitChages.prop('disabled', false);
//         $warnings.text("");
//     }
//     else {
//         $submitChages.prop('disabled', true);
//         $warnings.text("Поля повинні бути заповнені!");
//     }
// }
//
// function checkInputs(){
//     return /*$inputLogin.val() !== '' &&*/  $inputNew.val() !== ''
//         && $inputNewConfirm.val() !== '' && passwordsMatch();
// }
//
// function passwordsMatch(){
//     return $inputNew.val() === $inputNewConfirm.val();
// }

function initSubmitChangeLogin(){
    $submitChangeLoginBtn.click(function(){
        let newLogin = {
            new_login:$inputLogin.val()
        };
        if(newLogin['new_login']===$currentLoginValue.val() ){
            $loginModal.modal('hide');
        }else{
            API.postChangeLogin(newLogin,function(err,data){
                if(err){
                    $warningsLogin.text('Неможливо змінити логін.');
                }else if(data['success']===false){
                    $warningsLogin.text('Користувач з таким логіном вже існує.');
                }else{
                    $loginModal.modal('hide');
                    $currentLoginValue.val(newLogin['new_login']);
                    alert('Ваш логін змінено на '+newLogin['new_login']+'.');
                }
            });
        }
    });
}


function initSubmitChangeEmail(){
    $submitChangeEmailBtn.click(function(){
        let newEmail = $emailInput.val();
        // if(newEmail===$currentEmailValue.val()){
        //     $changeEmailDialog.modal('hide');
        // }else{
            API.postChangeLogin({email:newEmail},function(err,data){
                if(err){
                    $warningMessageEmail.text('Функція зміни електронної пошти недоступна.');
                }else if(data['success']===false){
                    if(data['message']){
                        $warningMessageEmail.text(data['message']);
                    }else{
                        $warningMessageEmail.text('Функція зміни електронної пошти недоступна.');
                    }
                }
                else{
                    $changeEmailDialog.modal('hide');
                    $changeEmailSentOkModal.modal('show');
                }
            });
        // }
    });
}

function initSubmitChangepassword(){
    $confSendChangePassBtn.click(function(){
        API.getChangepassword(function(err,data){
            if(err){
                alert('Функція зміни паролю недоступна.');
            }else if(data['success']===false){
                let msg = data['message'] ? data['message'] : 'Функція зміни паролю недоступна.';
                alert(msg);
            }
            else{
                $passwordChangeSentOkModal.modal('show');
            }
        });
    });
}

exports.initChangeDialog = initChangeDialog;