function isEmptyInput($inputField){
    return $inputField.val()==='';
}

function disableBtn($btn){
    $btn.prop('disabled', true);
}

function enableBtn($btn){
    $btn.prop('disabled', false);
}

function addClass($element,cssClass){
    $element.addClass(cssClass);
}
function removeClass($element,cssClass){
    $element.removeClass(cssClass);
}

function showMessageOnEmpty($field,$msgPlace,message){
    if(isEmptyInput($field)){
        $msgPlace.text(message);
    }
    else{
        $msgPlace.text('');
    }
}

exports.isEmptyInput=isEmptyInput;
exports.disableBtn=disableBtn;
exports.enableBtn=enableBtn;
exports.addClass =addClass;
exports.removeClass = removeClass;
exports.showMessageOnEmpty =showMessageOnEmpty;