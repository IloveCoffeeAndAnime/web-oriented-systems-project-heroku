<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 22.03.2019
 * Time: 18:40
 */

function year_to_corr_format($years){
    $resArr = array();
    foreach($years as $year){
        array_push($resArr,['value'=>$year,'name'=>$year]);
    };
    return $resArr;
}

function departs_to_corr_format($departs){
    $resArr=array();
    foreach ($departs as $dep){
        array_push($resArr,['value'=>$dep['department_id'],'name'=>$dep['name']]);
    }
    return $resArr;
}

function specs_to_corr_format($specs){
    $resArr=array();
    foreach ($specs as $spec){
        array_push($resArr,['value'=>$spec['speciality_id'],'name'=>$spec['name']]);
    }
    return $resArr;
}

function toNameValueFormat($arr,$propValName,$propNameName){
    $resArr=array();
    foreach ($arr as $elem){
        array_push($resArr,['value'=>$elem[$propValName],'name'=>$elem[$propNameName]]);
    }
    return $resArr;
}

function filter_themes_by_name_part($theme_arr,$input){
    if($input===null)
        return $theme_arr;
    $res_themes = array();
    $regex = '/.*'.mb_strtolower($input).'.*/';
    foreach($theme_arr as $theme){
        if(preg_match($regex,mb_strtolower($theme['theme']))){
            array_push($res_themes,$theme);
        }
    }
    return $res_themes;
}

function filter_themes_by_teacher($themes_arr,$input){
    return filter_arr_by_prop_part($themes_arr,$input,'teacher');
}

function filter_users_by_name_part($users_arr,$input){
   return filter_arr_by_prop_part($users_arr,$input,'name');

};

function filter_arr_by_prop_part($arr,$input,$prop_name){
    if($input===null)
        return $arr;
    $res = array();
    $regex = '/.*'.mb_strtolower($input).'.*/';
    foreach($arr as $elem){
        if(preg_match($regex,mb_strtolower($elem[$prop_name]))){
            array_push($res,$elem);
        }
    }
    return $res;
}

function generate_url_key($login){
    return bin2hex(random_bytes(50));
}