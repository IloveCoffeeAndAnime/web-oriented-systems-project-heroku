<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 20.04.2019
 * Time: 11:48
 */

require_once ('Urls.php');
const DOMAIN = "sandboxd3c973ca01c84f3dbcd5d4204164ebbc.mailgun.org";
const FROM_NAME = 'Сервіс запису на курсові та дипломні';

function send_email($mg,$to_name,$to_email,$subject,$text){
    $success = true;
    try{
        $res = $mg->messages()->send(DOMAIN, [
            'from'    => FROM_NAME.' <mailgun@'.DOMAIN.'>',
            'to'      => $to_name.' <'.$to_email.'>',
            'subject' => $subject,
            'text'    => $text
        ]);
    }catch(Exception $e){
        $success = false;
    }finally{
        return $success;
    }
}

function send_email_confirm_new_email($mg,$to_email,$confirm_id){
    $confirm_url = Urls::SERVER_APP_URL.Urls::CONFIRM_EMAIL_URL.$confirm_id;
    return send_email($mg,'Користувач',$to_email,'Підтвердження про зміну електронної пошти',
        "Ви отримали цей лист, оскільки вказали цю адресу електронної пошти у налаштуваннях акаунту на Сервісі запису на курсові та дипломні(".Urls::SERVER_APP_URL.").
         \nДля підтвердження використання цієї адреси для отримання повідомлень від Сервісу запису на курсові та дипломні, перейдіть за посиланнням:\n
         $confirm_url");
}

function send_email_change_password($mg,$to_email,$confirm_id){
    $password_url = Urls::SERVER_APP_URL.Urls::CHANGE_PASSWORD_URL.$confirm_id;
    return send_email($mg,'Користувач',$to_email,'Підтвердження про зміну пароля',"Ви отримали цей лист, оскільки вказали цю адресу електронної пошти у налаштуваннях акаунту на Сервісі запису на курсові та дипломні(".Urls::SERVER_APP_URL.").
    \nДля зміни паролю перейдіть за посиланням:\n$password_url");
}

function send_email_theme_student_enrolled($mg,$to_email,$theme_name){
    return send_email($mg,'Викладач',$to_email,'На Вашу тему записавcя студент',"На Вашу тему \"$theme_name\" записавcя студент.
    \nПовідомлення від Cервісу запису на курсові та дипломні (".Urls::SERVER_APP_URL.').');
}