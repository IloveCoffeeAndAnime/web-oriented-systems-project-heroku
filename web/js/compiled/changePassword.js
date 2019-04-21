(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
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
},{}]},{},[1]);
