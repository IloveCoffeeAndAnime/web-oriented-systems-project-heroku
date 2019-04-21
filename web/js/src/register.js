let API = require('./API');
let Commons = require('./commons');
let $login_input = $("#reg_login_input");
let $surname_input = $("#reg_surname_input");
let $name_input = $("#reg_name_input");
let $father_name_input = $("#reg_father_name_input");
let $email_input = $("#reg_email_input");
let $password_input = $("#reg_pass_input");
let $password_repeat = $('#reg_pass_repeat');
let $user_group_select = $("#user_group_select");
let $department_select = $("#department_select");
let $speciality_select = $("#speciality_select");
let $speciality_select_div = $('#speciality_select_div');
let $department_select_div = $('#department_select_div');
let $reg_login_msg = $('#reg_login_msg');
let $reg_name_msg = $('#reg_name_msg');
let $reg_surname_msg = $('#reg_surname_msg');
let $reg_email_msg = $('#reg_email_msg');
let $reg_pass_msg = $('#reg_pass_msg');
let $reg_pass_msg_repeat = $('#reg_pass_msg_repeat');
let $register_btn = $("#reg_btn");

function initRegisterPage(){
    $department_select_div.hide();
    $register_btn.click( function(){
        if(!formIncorrecttlyFilled()){
            senduserInfoToServer();
        }
    });
    initRegisterPageValidation();
}

function senduserInfoToServer(){
    let user_info ={
        login: $login_input.val(),
        users_group_id:$user_group_select.find(":selected").val(),
        department_id :$department_select.find(":selected").val(),
        speciality_id : $speciality_select.find(":selected").val(),
        password_hash : $password_input.val(),
        email:$email_input.val(),
        surname :$surname_input.val(),
        name:$name_input.val(),
        father_name:$father_name_input.val(),
    };

    console.log(user_info);
    API.sendUserRegisterInfo(user_info,function(err,data){
        if(err){
            alert(err.toString());
        }
        else{
            if(data['success']){
                window.location.href = data['redirect_url'];
            }
           else{
               if(data['error_type']==='user_err'){
                   $reg_login_msg.text(data['message']);
               }
               else if(data['error_type']==='email_err'){
                   $reg_email_msg.text(data['message']);
               }
            }
        }
    });
}

function initRegisterPageValidation(){
    $login_input.on("change paste keyup",function(){
        Commons.showMessageOnEmpty($login_input,$reg_login_msg,'введіть логін');
        enableRegisterBtnIfAllCorrect();
    });
    $name_input.on("change paste keyup",function(){
        Commons.showMessageOnEmpty($name_input,$reg_name_msg,'введіть ім\'я');
        enableRegisterBtnIfAllCorrect();
    });
    $surname_input.on("change paste keyup",function(){
        Commons.showMessageOnEmpty($surname_input,$reg_surname_msg,'введіть прізвище');
        enableRegisterBtnIfAllCorrect();
    });
    $password_input.on("change paste keyup",function(){
        Commons.showMessageOnEmpty($password_input,$reg_pass_msg,'введіть пароль');
        checkPassEqual();
        enableRegisterBtnIfAllCorrect();
    });
    $password_repeat.on("change paste keyup",function(){
        checkPassEqual();
        enableRegisterBtnIfAllCorrect();
    });
    $email_input.on("change paste keyup",function(){
        checkEmail();
        enableRegisterBtnIfAllCorrect();
    });
    $user_group_select.on('change', function() {
        if(parseInt($user_group_select.find(":selected").val())===1){
            $department_select_div.hide();
            $speciality_select_div.show();
        }
        else if(parseInt($user_group_select.find(":selected").val())===2 || parseInt($user_group_select.find(":selected").val())===3){
            $speciality_select_div.hide();
            $department_select_div.show();
        }
    });
}

function enableRegisterBtnIfAllCorrect(){
    if(formIncorrecttlyFilled())
        Commons.disableBtn($register_btn);
    else{
        Commons.enableBtn($register_btn);
     }
}

function formIncorrecttlyFilled(){
    return (Commons.isEmptyInput($login_input) ||
        Commons.isEmptyInput($name_input) ||
        Commons.isEmptyInput($surname_input) ||
        Commons.isEmptyInput($password_input) ||
        !isPassEqual() || !isCorrectEmail());
}

function isPassEqual(){
    return $password_repeat.val()===$password_input.val();
}

function isCorrectEmail(){
    return /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test($email_input.val());
}

function checkPassEqual(){
    if(!isPassEqual()){
        $reg_pass_msg_repeat.text('паролі не співпадають');
    }
    else{
        $reg_pass_msg_repeat.text('');
    }
}

function checkEmail(){
    if(isCorrectEmail()){
        $reg_email_msg.text('');
    }
    else{
        $reg_email_msg.text('Некоректний формат пошти');
    }
}

exports.initRegisterPage = initRegisterPage;