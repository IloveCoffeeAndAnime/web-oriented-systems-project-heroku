<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 16.04.2019
 * Time: 0:58
 */
const PATH_ENROLL = __DIR__ . '\config_theme_enroll\config_enroll.json';

function readFromJson($file){
    $json = json_decode(file_get_contents($file),TRUE);
    return $json;
}

function writeToFile($file,$arr){
   $success = file_put_contents($file, json_encode($arr));
   return $success!==false;
}

function isThemeEnrollOpened(){
    return readFromJson(PATH_ENROLL)['ENROLL_OPENED'];
}

function openThemeEnroll(){
    return writeToFile(PATH_ENROLL,['ENROLL_OPENED'=>true]);
}

function closeThemeEnroll(){
    return writeToFile(PATH_ENROLL,['ENROLL_OPENED'=>false]);
}
