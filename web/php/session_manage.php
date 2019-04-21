<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 22.03.2019
 * Time: 18:38
 */

function begin_user_session($app,$login,$user_group){
$app['session']->set('user', array('login' => $login,'user_group'=>$user_group));
}

function end_user_session($app){
    $app['session']->clear();
}

function exists_user_session($app){
    $val = $app['session']->get('user');
    return (null!==$val);
}

function no_user_session($app){
    $val = $app['session']->get('user');
    return (null===$val);
}

function get_session_user_group($app){
    $session_user = $app['session']->get('user');
    return $session_user['user_group'];
}

function get_session_user_login($app){
    $session_user = $app['session']->get('user');
    return $session_user['login'];
}
