<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 07.03.2019
 * Time: 4:12
 */
require_once ('db_queries.php');
require_once ('User_Groups.php');
require_once ('Urls.php');
require_once ('session_manage.php');
require_once ('query_res_format.php');
require_once ('read_write_json.php');
require_once ('send_mail.php');

function get_main_page($app){
    if(no_user_session($app))
        return $app->redirect(Urls::LOGIN_PAGE_ABSOLUTE);
    else
        return $app->redirect(Urls::USER_PAGE_ABSOLUTE);
}

function get_registration_page($app){
    $groups =  db_get_user_groups($app);
    $faculties =db_get_faculties($app);
    $departments = db_get_departments($app);
    $specialities = db_get_specialities($app);
    return $app['twig']->render('registration.twig',array(
        'groups' =>$groups,
        'faculties'=>$faculties,
        'departments' => $departments,
        'specialities'=> $specialities
        ));
}

function get_login_page($app){
    return $app['twig']->render('login.twig');
}

function get_teacher_page($app){
    if(no_user_session($app)){
        return $app->redirect(Urls::LOGIN_PAGE_ABSOLUTE);
    }
    else{
        if(get_session_user_group($app)===User_Groups::TEACHER_ID){
            $login = get_session_user_login($app);
            $group_id = get_session_user_group($app);
            $teacher_info = db_get_worker_info($app,$login,$group_id);
            $teacher_name = $teacher_info['surname'].' '.$teacher_info['name'].' '.$teacher_info['father_name'];
            $teacher_themes = db_get_teacher_themes($app,$login);
//            $faculty_themes = db_get_all_faculty_themes($app,$login,$group_id);//db_get_faculty_themes($app,$group_id,$login);
            $dp = new Department_Statuses();
            $statuses=$dp->getConstants();
            $years = year_to_corr_format(db_get_years($app));
            $departs = departs_to_corr_format(db_get_user_fac_departs($app,$login,$group_id));
            $specs = specs_to_corr_format(db_get_user_fac_specs($app,$login,$group_id));
            $theme_types = toNameValueFormat(db_get_work_types($app),'type_id','name');
            $enroll_opened = isThemeEnrollOpened();
            return $app['twig']->render('teacher.twig',['teacher_username'=>$teacher_info['username'],
                'email'=>$teacher_info['email'],
                'role'=>'Викладач',
                'name'=>$teacher_name,
                'faculty'=>$teacher_info['faculty'],
                'department'=>$teacher_info['department'],
                'teacher_themes'=>$teacher_themes,
//                'themes'=>$faculty_themes,
                'STATUSES'=>$statuses,
                'courses'=>$years,
                'departs'=>$departs,
                'specs'=>$specs,
                'work_types'=>$theme_types,
                'theme_enroll_opened'=>$enroll_opened]);
        }
        else
            return $app->redirect(Urls::USER_PAGE_ABSOLUTE);
    }
}

function get_department_worker_page($app){
    if(no_user_session($app)){
        return $app->redirect(Urls::LOGIN_PAGE_ABSOLUTE);
    }
    else{
        if(get_session_user_group($app)===User_Groups::DEPARTMENT_WORKER_ID){
            $login = get_session_user_login($app);
            $group_id = get_session_user_group($app);
            $depart_worker_info = db_get_worker_info($app,$login,$group_id);
            $worker_name = $depart_worker_info['surname'].' '.$depart_worker_info['name'].' '.$depart_worker_info['father_name'];
            $dp = new Department_Statuses();
            $statuses=$dp->getConstants();
            $years = year_to_corr_format(db_get_years($app));
            $departs = departs_to_corr_format(db_get_user_fac_departs($app,$login,$group_id));
            $specs=specs_to_corr_format(db_get_user_fac_specs($app,$login,$group_id));
//            $faculty_themes = db_get_all_faculty_themes($app,$login,$group_id);
            $theme_types = toNameValueFormat(db_get_work_types($app),'type_id','name');
            $enroll_opened = isThemeEnrollOpened();
            return $app['twig']->render('department_worker.twig',[
                'worker_username'=>$login,
                'email'=>$depart_worker_info['email'],
                'role'=>'Працівник кафедри',
                'name'=>$worker_name,
                'faculty'=>$depart_worker_info['faculty'],
                'department'=>$depart_worker_info['department'],
                'STATUSES'=>$statuses,
                'courses'=>$years,
                'departs'=>$departs,
                'specs'=>$specs,
//                'themes'=>$faculty_themes,
                'work_types'=>$theme_types,
                'theme_enroll_opened'=>$enroll_opened
            ]);
        }
        else
            return $app->redirect(Urls::USER_PAGE_ABSOLUTE);
    }
}

function get_admin_page($app){
    if(no_user_session($app)){
        return $app->redirect(Urls::LOGIN_PAGE_ABSOLUTE);
    }
    else{
        if(get_session_user_group($app)===User_Groups::ADMIN_ID){
            $login =get_session_user_login($app);
            $email =db_get_user_email($app,$login);
            $full_name = db_get_full_name_by_login($app,$login);
            $years = year_to_corr_format(db_get_years($app));
            $user_roles =(new User_Groups())->getConstants();
            $enroll_opened = isThemeEnrollOpened();
            return $app['twig']->render('admin.twig',[
                'years'=>$years,
                'ROLES'=>$user_roles,
                'admin_username'=>$login,
                'email'=>$email,
                'role'=>'Адміністратор',
                'name'=>$full_name,
                'theme_enroll_opened'=>$enroll_opened
            ]);
        }
        else
            return $app->redirect(Urls::USER_PAGE_ABSOLUTE);
    }
}

function get_student_page($app){
    if(no_user_session($app)){
        return $app->redirect(Urls::LOGIN_PAGE_ABSOLUTE);
    }
    else{
        if(get_session_user_group($app)===User_Groups::STUDENT_ID){
            $user_login = get_session_user_login($app);
            $user_group_id =get_session_user_group($app);
            $student_info = db_get_student_info($app,$user_login,$user_group_id);
            $student_full_name = $student_info['surname'].' '.$student_info['student_name'].' '.$student_info['father_name'];
//            $faculty_themes = db_get_all_faculty_themes($app,$user_login,$user_group_id);//db_get_faculty_themes($app,$user_group_id,$user_login);
            $student_theme = db_get_student_theme($app,$user_login);
            $student_theme_type = db_get_theme_type($app,$student_info['faculty_id'],$student_info['course_year']);
            $years = year_to_corr_format(db_get_years($app));
            $departs = departs_to_corr_format(db_get_user_fac_departs($app,$user_login,$user_group_id));
            $specs=specs_to_corr_format(db_get_user_fac_specs($app,$user_login,$user_group_id));
            $theme_types = toNameValueFormat(db_get_work_types($app),'type_id','name');
            $enroll_opened = isThemeEnrollOpened();
            return $app['twig']->render('student.twig',['role'=>'Студент',
                'student_username'=>$user_login,
                'email'=>$student_info['student_email'],
                'name'=>$student_full_name,
                'faculty'=>$student_info['student_faculty'],
                'speciality'=>$student_info['student_speciality'],
                'student_theme'=>$student_theme['theme'],
                'theme_type'=>$student_theme_type,
                'theme_teacher'=>$student_theme['author_surname'].' '.$student_theme['author_name'].' '.$student_theme['author_father_name'],
                'enroll_theme_id'=>$student_theme['theme_id'],
                'student_course'=>$student_info['course_year'],
//                'themes'=>$faculty_themes,
                'courses'=>$years,
                'departs'=>$departs,
                'specs'=>$specs,
                'work_types'=>$theme_types,
                'theme_enroll_opened'=>$enroll_opened]);
        }
        else
            return $app->redirect(Urls::USER_PAGE_ABSOLUTE);
    }
}


function get_user_page($app){
    if(no_user_session($app)){
        return $app->redirect(Urls::LOGIN_PAGE_ABSOLUTE);
    }
    else{
        $user_group = get_session_user_group($app);
        if($user_group===User_Groups::STUDENT_ID)
            return $app->redirect(Urls::STUDENT_PAGE_ABSOLUTE);
        else if($user_group===User_Groups::TEACHER_ID)
            return $app->redirect(Urls::TEACHER_PAGE_ABSOLUTE);
        else if($user_group===User_Groups::DEPARTMENT_WORKER_ID)
            return $app->redirect(Urls::DEPARTMENT_WORKER_ABSOLUTE);
        else if($user_group===User_Groups::ADMIN_ID)
            return $app->redirect(Urls::ADMIN_PAGE_ABSOLUTE);
        else
            return $app->redirect(Urls::USER_LOGOUT_ABSOLUTE);
    }
}

//function post_user_register($app,$request){
//    $user_info = array(
//            'login' => $request->request->get('login'),
//            'users_group_id'  => $request->request->get('users_group_id'),
//            'department_id'  => $request->request->get('department_id'),
//            'speciality_id'  => $request->request->get('speciality_id'),
//            'password_hash'  => password_hash($request->request->get('password_hash'),PASSWORD_DEFAULT ),
//            'email'  => $request->request->get('email'),
//            'surname'  => $request->request->get('surname'),
//            'name' =>  $request->request->get('name'),
//            'father_name' =>  $request->request->get('father_name'),
//        );
//    if(db_is_login_in_db($app,$user_info['login'])){
//        return $app->json(array('message'=>'користувач з таким логіном вже існує','success'=>false,'error_type'=>'user_err','redirect_url'=>'http://localhost/web-oriented-systems-project/web/login/'));
//    }
//    if(db_is_email_in_db($app,$user_info['email'])){
//        return $app->json(array('message'=>'користувач з такою поштою вже існує','success'=>false,'error_type'=>'email_err','redirect_url'=>'http://localhost/web-oriented-systems-project/web/login/'));
//    }
//    db_add_user($app,$user_info);
//    begin_user_session($app,$user_info['login'],intval ($user_info['users_group_id']));
//    return $app->json(array('success'=>true,'redirect_url'=>'http://localhost/web-oriented-systems-project/web/user-page/'));
//}

function post_user_login($app,$login,$password){
    $ps_hash = db_get_password_by_login($app,$login);
    if(is_null($ps_hash))
        return $app->json(array('message'=>'no such user','success'=>false,'error_type'=>'user_err','redirect_url'=>Urls::SERVER_APP_URL.Urls::LOGIN_PAGE_ABSOLUTE));
    if(password_verify($password,$ps_hash)){
        $user_group = db_get_user_group_id($app,$login);
        begin_user_session($app,$login,$user_group);
        return $app->json(array('message'=>'logged','success'=>true,'redirect_url'=>Urls::SERVER_APP_URL.Urls::USER_PAGE_ABSOLUTE));
    }
    else
        return $app->json(array('message'=>'password not match','success'=>false,'error_type'=>'password_err','redirect_url'=>Urls::SERVER_APP_URL.Urls::LOGIN_PAGE_ABSOLUTE));
}

function get_user_logout($app){
    end_user_session($app);
    return $app->redirect(Urls::LOGIN_PAGE_ABSOLUTE);
}

function post_login_exists($app,$login){
    $success = db_is_login_in_db($app,$login);
    return $app->json(array('success'=>$success));
}

function post_change_login($app,$newLogin){
    if(no_user_session($app)){
        return $app->redirect(Urls::LOGIN_PAGE_ABSOLUTE);
    }
    $login = get_session_user_login($app);
    $alreadyExists = db_is_login_in_db($app,$newLogin);
    if($alreadyExists){
       return $app->json(array('success'=>false));
    }
    else{
        $success = db_set_user_login($app,$login,$newLogin);
        if($success){
            begin_user_session($app,$newLogin,get_session_user_group($app));
        }
        return $app->json(array('success'=>$success));
    }
}

function post_change_email($app,$mg,$newEmail){
    if(no_user_session($app)){
        return $app->redirect(Urls::LOGIN_PAGE_ABSOLUTE);
    }else{
        $alreadyExists = db_is_email_in_db($app,$newEmail);
        if($alreadyExists){
            return $app->json(array('success'=>false,'message'=>'Користувач з такою поштою вже існує.'));
        }
        else{
            $login = get_session_user_login($app);
            try{
                $conf_id = generate_url_key($login);
//                $conf_id = password_hash($login.bin2hex(random_bytes(10)),PASSWORD_DEFAULT);
                $success = db_set_update_email_string($app,$login,$conf_id,$newEmail);
                if($success){
                    $isSent = send_email_confirm_new_email($mg,$newEmail,$conf_id);
                    $msg = $isSent ? 'На вказану пошту надіслано лист підтвердження. Будь ласка, перевірте Вашу поштову скриньку.':'Не вдалось надіслати лист підтвердження за вказаною поштою.';
                    return $app->json(array('success'=>$isSent,'message'=>$msg));
                }else{
                    return $app->json(array('success'=>false,'message'=>'Операція зміни електронної пошти недоступна зараз.'));
                }
            }catch(Exception $e){
                return $app->json(array('success'=>false,'message'=>'Операція зміни електронної пошти недоступна зараз.'));
            }
        }
    }
}

function get_user_conf_change_email($app,$conf_id){
    $info = db_get_login_email_by_conf_id($app,$conf_id);
    if($info===false){
        return $app['twig']->render('email_confirm_page.twig',array('success'=>false));
    }else{
        $success = db_set_user_email($app,$info['login'],$info['email_new_val']);
        return $app['twig']->render('email_confirm_page.twig',array('success'=>$success));
    }
}

function post_search_theme_by_name($app,$input){
    $user_group_id = get_session_user_group($app);
    $user_login = get_session_user_login($app);
    $themes_arr = db_get_theme_by_name_part($app,$user_group_id,$user_login,$input);
    $statuses = (new Department_Statuses())->getConstants();
    $enroll_opened = isThemeEnrollOpened();
    return $app->json(array('group'=>User_Groups::getUserGroupName($user_group_id),'themes'=>$themes_arr,'STATUSES'=>$statuses,'theme_enroll_opened'=>$enroll_opened));
}

function get_search_all_themes($app){
    $login = get_session_user_login($app);
    $group_id = get_session_user_group($app);
    $statuses = (new Department_Statuses())->getConstants();
    $themes_arr = db_get_all_faculty_themes($app,$login,$group_id);//db_get_faculty_themes($app,$group_id,$login);
    $enroll_opened = isThemeEnrollOpened();
    return $app->json(array('group'=>User_Groups::getUserGroupName($group_id),'themes'=>$themes_arr,'STATUSES'=>$statuses,'theme_enroll_opened'=>$enroll_opened));
}

function post_search_filters($app,$input){
    $login = get_session_user_login($app);
    $group_id = get_session_user_group($app);
    $statuses = (new Department_Statuses())->getConstants();
    $themes = db_get_filtered_themes($app,$login,$group_id,$input['type'],$input['course'],$input['teacher'],$input['department'],$input['speciality'],$input['available'],$input['approve_status'],$input['name']);
  //  $themes = db_get_faculty_themes_filtered($app,$login,$group_id,$input['type'],$input['course'],$input['teacher'],$input['department'],$input['speciality'],$input['available'],$input['approve_status'],$input['name']);
    $enroll_opened = isThemeEnrollOpened();
    return $app->json(array('group'=>User_Groups::getUserGroupName($group_id),'themes'=> $themes,'STATUSES'=>$statuses,'theme_enroll_opened'=>$enroll_opened));
}

function post_student_enroll($app,$mg, $theme_id){
    $group_id = get_session_user_group($app);
    if($group_id!==User_Groups::STUDENT_ID)
        return $app->redirect(Urls::USER_PAGE_ABSOLUTE);
    else{
        $login = get_session_user_login($app);
        $is_success = db_set_student_enroll($app,$login,$theme_id);
        $studTheme = db_get_student_theme($app,$login);
        $enrollOpened = isThemeEnrollOpened();
        if($is_success){
            send_email_theme_student_enrolled($mg,$studTheme['email'],$studTheme['theme']);
        }
//        return $app->redirect(Urls::STUDENT_PAGE_ABSOLUTE);
        return $app->json(array('success'=>$is_success,'theme_enroll_opened'=>$enrollOpened,'theme'=>$studTheme));
    }
}

function post_student_refuse_theme($app,$theme_id){
    $group_id = get_session_user_group($app);
    if($group_id!==User_Groups::STUDENT_ID)
        return $app->redirect(Urls::USER_PAGE_ABSOLUTE);
    else{
        $login = get_session_user_login($app);
        $is_success = db_set_student_refuse_theme($app,$login,$theme_id);
        return $app->json(array('success'=>$is_success));
//        return $app->redirect(Urls::STUDENT_PAGE_ABSOLUTE);
    }
}

function get_open_theme_enroll($app){
    if(no_user_session($app)){
        return $app->redirect(Urls::LOGIN_PAGE_ABSOLUTE);
    }
    else{
        $group_id = get_session_user_group($app);
        if($group_id===User_Groups::ADMIN_ID){
            $success = openThemeEnroll();
            return $app->json(array('success'=>$success));
        }
        else{
            return $app->redirect(Urls::USER_PAGE_ABSOLUTE);
        }
    }
}

function get_close_theme_enroll($app){
    if(no_user_session($app)){
        return $app->redirect(Urls::LOGIN_PAGE_ABSOLUTE);
    }
    else{
        $group_id = get_session_user_group($app);
        if($group_id===User_Groups::ADMIN_ID){
            $success = closeThemeEnroll();
            return $app->json(array('success'=>$success));
        }
        else{
            return $app->redirect(Urls::USER_PAGE_ABSOLUTE);
        }
    }
}

function post_create_teacher_theme($app,$theme){
    $name = $theme['name'];
    $annotation =$theme['annotation'];
    $year = $theme['year'];
    $author = get_session_user_login($app);
    $success =  db_create_theme($app,$author,$name,$year,$annotation);
    return $app->json(array('success'=>$success!==false));

}

function post_update_teacher_theme($app,$theme){
    $success = db_update_theme($app,$theme['id'],$theme['name'],$theme['year'],$theme['annotation']);
    return $app->json(array('success'=>$success!==false));
}

function post_delete_teacher_theme($app,$theme_id){
    $success = db_delete_theme($app,$theme_id);
    return $app->json(array('success'=>$success!==false));
}

function get_teacher_themes($app){
    $login = get_session_user_login($app);
    $dp = new Department_Statuses();
    $statuses=$dp->getConstants();
    $teacher_themes = db_get_teacher_themes($app,$login);
        return $app->json(array('themes'=>$teacher_themes,'STATUSES'=>$statuses));
}

function post_theme_exists($app,$theme_name){
    $theme_id = db_get_theme_by_name($app,$theme_name);
    return $app->json(array('success'=>$theme_id!==null));
}

function post_theme_approve($app,$theme_id){
    $login = get_session_user_login($app);
    $success = db_approve_theme($app,$login,$theme_id);
    return $app->json(array('success'=>$success!==false));
}

function post_theme_disapprove($app,$theme_id){
    $login =  get_session_user_login($app);
    $success = db_disapprove_theme($app,$login,$theme_id);
    return $app->json(array('success'=>$success!==false));
}

//function get_user_roles($app){
//    return $app->json(db_get_user_groups_exclude_admin($app));
//}
//
//function get_faculties($app){
//    return $app->json(db_get_faculties($app));
//};
//
//function get_specialities($app){
//    return $app->json(db_get_specs_with_facs_ids($app));
//}
//
//function get_departments($app){
//    return $app->json(db_get_departs_with_facs_ids($app));
//}

function get_sarch_block_info($app){
    $user_roles = db_get_user_groups_exclude_admin($app);
    $faculties = db_get_faculties($app);
    $specialities = db_get_specs_with_facs_ids($app);
    $departments = db_get_departs_with_facs_ids($app);
    return $app->json(array('user_roles'=>$user_roles,'faculties'=>$faculties,'specialities'=>$specialities,'departments'=>$departments));
}

function get_users_no_admin($app){
    $user_roles =(new User_Groups())->getConstants();
    $users_info = db_get_users_with_no_admins($app);
    return $app->json(array('users'=>$users_info,'ROLES'=>$user_roles));
}

function post_searh_by_name_users($app,$nameInput){
    $user_roles = (new User_Groups())->getConstants();
    $users_info = db_get_users_with_no_admins($app);
    $usr_inf_filt_name = filter_users_by_name_part($users_info,$nameInput);
    return $app->json(array('users'=>$usr_inf_filt_name,'ROLES'=>$user_roles));
}

function post_search_filtered_users($app,$input){
    $user_roles = (new User_Groups())->getConstants();
    $users_info = db_search_filtered_users($app,$input['role'],$input['faculty'],$input['speciality'],$input['department'],$input['year']);
    $usr_inf_filt_name = filter_users_by_name_part($users_info,$input['name']);
    return $app->json(array('users'=>$usr_inf_filt_name,'ROLES'=>$user_roles));
}

function post_add_user($app,$user_info){
    if(db_is_email_in_db($app,$user_info['email'])){
        return $app->json(array('message'=>'користувач з такою поштою вже існує','success'=>false,'error_type'=>'email_err'));
    }
    $password_hash = password_hash($user_info['password'],PASSWORD_DEFAULT );
    $success = db_create_user($app,$user_info['email'],$user_info['group_id'],$user_info['depart_id'],$user_info['spec_id'],$password_hash,$user_info['name'],$user_info['surname'],$user_info['father_name'],$user_info['email'],$user_info['year']);
    return $app->json(array('success'=>$success));
}

function post_delete_user($app,$user_login){
    $group = db_get_user_group_id($app,$user_login);
    if($group===User_Groups::TEACHER_ID && db_teacher_has_themes_approved($app,$user_login)) {
        return $app->json(array('success'=>false,'message'=>'Викладач має теми, затверджені кафедрою.'));
    }else if($group===User_Groups::DEPARTMENT_WORKER_ID && db_department_worker_has_checked_themes($app,$user_login)) {
        return $app->json(array('success'=>false,'message'=>'Працівник кафедри має теми, перевірені ним.'));
    }
    else{
        $success = db_delete_user($app,$user_login);
        return $app->json(array('success'=>$success));
    }
}

function get_user_change_password($app,$mg){
    if(no_user_session($app)){
        return $app->redirect(Urls::LOGIN_PAGE_ABSOLUTE);
    }
    else{
        $login = get_session_user_login($app);
        $email = db_get_user_email($app,$login);
        try{
            $key = generate_url_key($login);
            $is_key_saved = db_set_update_password_string($app,$login,$key);
            if($is_key_saved){
                $is_sent = send_email_change_password($mg,$email,$key);
                $msg = $is_sent ? 'На вашу пошту надіслано лист для зміни паролю. Будь ласка, перевірте Вашу поштову скриньку.':'Не вдалось надіслати лист про зміну паролю за вказаною поштою.';
                return $app->json(array('success'=>$is_sent,'message'=>$msg));
            }else{
                return $app->json(array('success'=>false,'message'=>'Функція зміни паролю недосутпна зараз.'));
            }
        }catch(Exception $e) {
            return $app->json(array('success'=>false,'message'=>'Функція зміни паролю недосутпна зараз.'));
        }
    }
}

function get_change_password_page($app,$pass_change_key){
    //user session NOT needed here
    $res = db_get_login_by_pass_update_string($app,$pass_change_key);
    if($res===false){
        return $app['twig']->render('page_not_found.twig');
    }else{

        return $app['twig']->render('password_change_page.twig',array('confirmString'=>$pass_change_key));
    }
}


function post_change_password_confirm($app,$pass_change_key,$new_pass){
    $res = db_get_login_by_pass_update_string($app,$pass_change_key);
    if($res===false){
        return $app['twig']->render('pass_change_result_page.twig',array('success'=>false));
    }else{
        try{
            $password_hash = password_hash($new_pass,PASSWORD_DEFAULT);
            $success =db_set_user_password($app,$res['login'],$password_hash);
            return $app['twig']->render('pass_change_result_page.twig',array('success'=>$success));
        }catch(Exception $e){
            return $app['twig']->render('pass_change_result_page.twig',array('success'=>false));
        }
    }

}