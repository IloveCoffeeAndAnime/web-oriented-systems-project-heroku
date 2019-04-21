let API = require('./API');
let $login_btn = $('#login_btn');
let $login_input_login = $('#login_input_login');
let $password_input_login = $('#password_input_login');
let $login_message_div = $('#login_message_div');
let $password_message_div = $('#password_message_div');

function initLoginPage(){
    $login_btn.click(function(){
        let user_info = {
            login: $login_input_login.val(),
            password: $password_input_login.val()
        };
        API.sendUserLoginInfo(user_info,function(err,data){
            if(err)
                alert(err.toString());
            else{
               if(data['success'])
                   window.location.href=data['redirect_url'];
               else{
                   if(data['error_type']==='user_err')
                       $login_message_div.text(data['message']);
                   else
                       $password_message_div.text(data['message']);
               }
            }
        });
    });

    $login_input_login.on("change paste keyup",function(){
        check_login_empty();
    });

    $password_input_login.on("change paste keyup",function(){
        check_password_empty();
    });
}

function isEmptyLoginInput(){
    return $login_input_login.val()==='';
}

function isEmtyPasswodInput(){
    return $password_input_login.val()==='';
}

function disableLoginBtn(){
    $login_btn.prop('disabled', true);
}

function enableLoginBtn(){
    $login_btn.prop('disabled', false);
}

function check_login_empty(){
    if(isEmptyLoginInput()){
        $login_message_div.text('введіть логін');
    }
    else{
        $login_message_div.text('');
    }
    enableLoginBtnIfAllCorrect();
}

function check_password_empty(){
    if(isEmtyPasswodInput()){
        $password_message_div.text('введіть пароль');
    }
    else{
        $password_message_div.text('');
    }
    enableLoginBtnIfAllCorrect();
}

function enableLoginBtnIfAllCorrect(){
    if(isEmtyPasswodInput() ||isEmptyLoginInput()){
        disableLoginBtn();
    }
    else{
        enableLoginBtn();
    }
}


exports.initLoginPage = initLoginPage;