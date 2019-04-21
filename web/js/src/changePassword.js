let $newPasswordInput = $('#newPasswordInput');
let $newPasswordAgain = $('#newPasswordInputAgain');
let $passwordMsg = $('#passwordMsg');
let $passwordMsgAgain = $('#passwordMsgAgain');
let $submitNewPasswordBtn = $('#submitNewPasswordBtn');

$(function(){
    $newPasswordInput.on('input',function(){
        checkPassword();
        checkForCorrectInput();
    });
    $newPasswordAgain.on('input',function(){
        checkConfirm();
        checkForCorrectInput();
    });
});

function checkPassword(){
    if( $newPasswordInput.val() === '' ){
        setError($passwordMsg.parent());
        $passwordMsg.text('Поле не може бути пустим');
    }
    else{
        setSuccess($passwordMsg.parent());
        $passwordMsg.text('');
    }
}

function checkConfirm() {
    if($newPasswordAgain.val()===''){
        setError($passwordMsgAgain.parent());
        $passwordMsgAgain.text('Поле не може бути пустим!');
    }
    else if(!passwordsMatch()){
        setError($passwordMsgAgain.parent());
        $passwordMsgAgain.text("Паролі не збігаються!");
    }else{
        setSuccess($passwordMsgAgain.parent());
        $passwordMsgAgain.text('');
    }
}

function checkForCorrectInput(){
    if(checkInputs()){
        $submitNewPasswordBtn.prop('disabled', false);
        setSuccess($passwordMsg.parent());
        setSuccess($passwordMsgAgain.parent());
        $passwordMsg.text('');
        $passwordMsgAgain.text('');
    }
    else {
        $submitNewPasswordBtn.prop('disabled', true);
    }
}

function checkInputs(){
    return $newPasswordInput.val() !== '' && $newPasswordAgain.val() !== '' && passwordsMatch();
}

function passwordsMatch(){
    return $newPasswordInput.val() === $newPasswordAgain.val();
}

function setSuccess($elem){
    $elem.removeClass('has-error');
    $elem.addClass('has-success');
}

function setError($elem){
    $elem.removeClass('has-success');
    $elem.addClass('has-error');
}