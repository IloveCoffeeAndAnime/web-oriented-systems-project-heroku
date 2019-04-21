<?php
//all queries to database written here
require_once "Department_Statuses.php";

function db_execute_one_row_st($app,$query,$params_arr){
    $st = $app['pdo']->prepare($query);
    $st->execute($params_arr);
    return $st->fetch(PDO::FETCH_ASSOC);
}

function db_get_work_types($app){
    $types = array();
    $st = $app['pdo']->prepare('SELECT * FROM public."THEME_TYPES";');
    $st->execute();
    while($row=$st->fetch(PDO::FETCH_ASSOC)) {
        array_push( $types,$row);
    }
    return $types;
}

function db_get_years($app){
    $st = $app['pdo']->prepare('SELECT DISTINCT year
	FROM public."FACULTY_YEARS";');
    $st->execute();
    $all_years = array();
    while($row=$st->fetch(PDO::FETCH_ASSOC)) {
        array_push( $all_years,$row['year']);
    }
    return $all_years;
}

function db_get_user_groups($app){
    $st = $app['pdo']->prepare('SELECT * FROM public."USER_GROUPS";');
    $st->execute();

    $names = array();
    while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
        $app['monolog']->addDebug('Row ' . $row['name']);
        array_push($names,$row);
    }
    return $names;
}

function db_get_user_groups_exclude_admin($app){
    $names = array();
    try{
        $st = $app['pdo']->prepare('SELECT * FROM public."USER_GROUPS" WHERE id!=:admin_id;');
        $st->execute(array('admin_id'=>User_Groups::ADMIN_ID));

//    $names = array();
        while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
            $app['monolog']->addDebug('Row ' . $row['name']);
            array_push($names,$row);
        }
    }catch(PDOException $exception){
     throw new Exception('error');
    }finally{
        $st = null;
    }
    return $names;
}

function db_get_faculties($app){
    $res = array();
    try{
        $st = $app['pdo']->prepare('SELECT faculty_id,name FROM public."FACULTIES";');
        $st->execute();
//        $res = array();
        while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
            $app['monolog']->addDebug('Row ' . $row['name']);
            array_push($res,$row);
        }
    }catch(PDOException $exception){
        throw new Exception('error');
    }finally{
        $st = null;
    }
    return $res;
}

function db_get_departments($app){
    $st =  $app['pdo']->prepare('SELECT department_id,name FROM public."DEPARTMENTS";');
    $st->execute();
    $res = array();
    while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
        $app['monolog']->addDebug('Row ' . $row['name']);
        array_push($res,$row);
    }
    return $res;
}

function db_get_specialities($app){
    $st =  $app['pdo']->prepare('SELECT speciality_id,name FROM public."SPECIALITIES";');
    $st->execute();
    $res = array();
    while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
        $app['monolog']->addDebug('Row ' . $row['name']);
        array_push($res,$row);
    }
    return $res;
}

function db_get_specs_with_facs_ids($app){
    $res = array();
    try{
        $st =  $app['pdo']->prepare('SELECT faculty_id,speciality_id,name FROM public."SPECIALITIES";');
        $st->execute();
//    $res = array();
        while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
            $app['monolog']->addDebug('Row ' . $row['name']);
            array_push($res,$row);
        }
    }catch(PDOException $exception){
       throw  new Exception('error');
    }finally{
        $st = null;
    }
    return $res;
}

function db_get_departs_with_facs_ids($app){
    $res = array();
    try{
        $st =  $app['pdo']->prepare('SELECT faculty_id,department_id,name FROM public."DEPARTMENTS";');
        $st->execute();
//    $res = array();
        while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
            $app['monolog']->addDebug('Row ' . $row['name']);
            array_push($res,$row);
        }
    }catch(PDOException $exception){
        throw  new Exception('error');
    }finally{
        $st = null;
    }
    return $res;
}

function db_get_user_fac_departs($app,$login,$group_id){
    $facult_id = db_get_user_facult_id($app,$login,$group_id);
    $st=$app['pdo']->prepare('SELECT department_id, name FROM public."DEPARTMENTS" WHERE faculty_id = :f_id;');
    $st->execute(array('f_id'=>$facult_id));
    $departs = array();
    while($row=$st->fetch(PDO::FETCH_ASSOC)){
        array_push($departs,$row);
    }
//    $st=$app['pdo']->prepare('SELECT department_id, name
//FROM public."DEPARTMENTS"
//WHERE faculty_id IN (SELECT faculty_id FROM public."SPECIALITIES" WHERE speciality_id IN (SELECT speciality_id
//						FROM public."USERS"
//						WHERE login=:login));');
//    $st->execute(array('login'=>$login));
//    $departs = array();
//    while($row=$st->fetch(PDO::FETCH_ASSOC)){
//        array_push($departs,$row);
//    }
    return $departs;
}

function db_get_user_fac_specs($app,$login,$group_id){
    $facult_id = db_get_user_facult_id($app,$login,$group_id);
    $st=$app['pdo']->prepare('SELECT speciality_id, name
FROM public."SPECIALITIES"
WHERE faculty_id = :f_id;');
    $st->execute(array('f_id'=>$facult_id));
    $specs = array();
    while($row=$st->fetch(PDO::FETCH_ASSOC)){
        array_push($specs,$row);
    }
    return $specs;
}

function db_get_user_facult_id($app,$login,$group_id){
    if($group_id===User_Groups::STUDENT_ID){
        $faculty_id = db_execute_one_row_st($app,'SELECT faculty_id FROM public."SPECIALITIES" WHERE speciality_id IN (SELECT speciality_id
						FROM public."USERS"
						WHERE login=:login);',array('login'=>$login))['faculty_id'];
    }
    else{
        $faculty_id = db_execute_one_row_st($app,'SELECT faculty_id FROM public."DEPARTMENTS" WHERE department_id IN (SELECT department_id
						FROM public."USERS"
						WHERE login=:login);',array('login'=>$login))['faculty_id'];
    }
    return $faculty_id;
}

function db_add_user($app,$user_info){
    $login = $user_info['login'];
    $users_group_id = $user_info['users_group_id'];
    $department_id = $user_info['department_id'];
    $speciality_id = $user_info['speciality_id'];
    $password_hash = $user_info['password_hash'];
    $name = $user_info['name'];
    $surname = $user_info['surname'];
    $father_name = $user_info['father_name'];
    $email = $user_info['email'];
    $st =  $app['pdo']->prepare('INSERT INTO 
public."USERS"(login,users_group_id,department_id,speciality_id,password_hash,email,surname,name,father_name)
VALUES (:login, :users_group_id, :department_id,:speciality_id, :password_hash, :email, :surname, :name, :father_name);');
    $st_res = $st->execute(array(':login'=>$login,':users_group_id'=>$users_group_id,':department_id'=>$department_id,':speciality_id'=>$speciality_id,':password_hash'=>$password_hash,':email'=>$email,':surname'=>$surname,':name'=>$name,':father_name'=>$father_name));
    return $st_res;
}

function db_create_user($app,$login,$group_id,$department_id,$speciality_id,$password_hash,$name,$surname,$father_name,$email,$year){
    $st = $app['pdo']->prepare(' INSERT INTO public."USERS"(   login, users_group_id, department_id, speciality_id, password_hash, email, surname, name, father_name, photo_path, year)
    VALUES (:login, :users_group_id, :department_id, :speciality_id, :password_hash, :email, :surname, :name, :father_name, :photo_path, :year);');
    $st_res = $st_res = $st->execute(array('login'=>$login,'users_group_id'=>$group_id,'department_id'=>$department_id,'speciality_id'=>$speciality_id,'password_hash'=>$password_hash,'email'=>$email,'surname'=>$surname,'name'=>$name,'father_name'=>$father_name,'photo_path'=>null,'year'=>$year));
    return $st_res;
}

function db_delete_user($app,$user_login){
    $st = $app['pdo']->prepare('DELETE FROM public."USERS"
    WHERE login=:user_login');
    $st_res = $st_res = $st->execute(array('user_login'=>$user_login));
    return $st_res;
}

function db_get_password_by_login($app,$login){
    $st = $app['pdo']->prepare('SELECT password_hash FROM public."USERS" WHERE login = :login;');
    $st->execute(array(':login'=>$login));
    $res = $st->fetch(PDO::FETCH_ASSOC);
    return $res['password_hash'];
}

function db_get_user_group_id($app,$login){
    $st =  $app['pdo']->prepare('SELECT users_group_id FROM public."USERS" WHERE login = :login;');
    $st->execute(array(':login'=>$login));
    $res = $st->fetch(PDO::FETCH_ASSOC);
    return $res['users_group_id'];
}

function db_is_login_in_db($app,$login){
    $st =  $app['pdo']->prepare('SELECT login FROM public."USERS" WHERE login = :login;');
    $st->execute(array(':login'=>$login));
    $res = $st->fetch(PDO::FETCH_ASSOC);
    return $res!==false;
}
function db_is_email_in_db($app,$email){
    $st =  $app['pdo']->prepare('SELECT email FROM public."USERS" WHERE email = :email;');
    $st->execute(array(':email'=>$email));
    $res = $st->fetch(PDO::FETCH_ASSOC);
    return $res!==false;
}

function db_get_user_email($app,$login){
    $res = db_execute_one_row_st($app,'SELECT email FROM public."USERS" WHERE login = :login;',array(':login'=>$login));
    return $res['email'];
}

function db_get_student_info($app, $login, $group_id){
    $st = $app['pdo']->prepare('SELECT public."USERS".login AS student_username, public."USERS".email AS student_email,  public."USERS".year AS course_year,
public."SPECIALITIES".name AS student_speciality, public."FACULTIES".name AS student_faculty, public."FACULTIES".faculty_id AS faculty_id,
public."USERS".surname AS surname, public."USERS".name AS student_name, public."USERS".father_name AS father_name
	FROM (public."USERS" INNER JOIN public."SPECIALITIES" ON public."USERS".speciality_id=public."SPECIALITIES".speciality_id)
	INNER JOIN public."FACULTIES" ON public."SPECIALITIES".faculty_id=public."FACULTIES".faculty_id
	WHERE public."USERS".login=:login AND public."USERS".users_group_id=:group_id;');
    $st->execute(array('login'=>$login,'group_id'=>$group_id));
    $res = $st->fetch(PDO::FETCH_ASSOC);
    return $res;
}

function db_get_student_theme($app,$login){
    $st = $app['pdo']->prepare('SELECT public."WORK_THEME".work_theme_id AS theme_id, public."WORK_THEME".name AS theme, public."WORK_THEME".year AS work_year,
public."USERS".surname AS author_surname, 
public."USERS".name AS author_name, public."USERS".father_name AS author_father_name, public."USERS".email AS email,
public."WORK_THEME".info AS theme_info
FROM (public."WORK_THEME" INNER JOIN public."USERS" ON public."WORK_THEME".author=public."USERS".login)
WHERE public."WORK_THEME".assignee = :login ;');
    $st->execute(array('login'=>$login));
    return $st->fetch(PDO::FETCH_ASSOC);
}

function db_get_theme_type($app,$faculty_id,$year){
    $st = $app['pdo']->prepare('SELECT public."THEME_TYPES".name AS theme_type
FROM public."THEME_TYPES"
WHERE type_id IN (SELECT theme_type_id
				 FROM public."FACULTY_YEARS"
				 WHERE faculty_id=:faculty_id AND year=:year);');
    $st->execute(array('faculty_id'=>$faculty_id,'year'=>$year));
    return $st->fetch(PDO::FETCH_ASSOC)['theme_type'];
}

function db_get_worker_info($app, $login, $group_id){
    $st = $app['pdo']->prepare('SELECT public."USERS".login AS username, public."USERS".email AS email,
public."DEPARTMENTS".name AS department, public."FACULTIES".name AS faculty, public."FACULTIES".faculty_id AS faculty_id,
public."USERS".surname AS surname, public."USERS".name AS name, public."USERS".father_name AS father_name
	FROM (public."USERS" INNER JOIN public."DEPARTMENTS" ON public."USERS".department_id=public."DEPARTMENTS".department_id)
	INNER JOIN public."FACULTIES" ON public."DEPARTMENTS".faculty_id=public."FACULTIES".faculty_id
	WHERE public."USERS".login=:login AND public."USERS".users_group_id=:group_id;');
    $st->execute(array('login'=>$login,'group_id'=>$group_id));
    $res = $st->fetch(PDO::FETCH_ASSOC);
    return $res;
}

function db_get_teacher_themes($app,$login){
    $st = $app['pdo']->prepare('SELECT public."WORK_THEME".work_theme_id AS theme_id, public."WORK_THEME".name AS theme, public."WORK_THEME".info AS info,
public."WORK_THEME".year AS theme_year,public."WORK_THEME".assignee AS assignee,
public."WORK_THEME".department_status_id AS status
FROM public."WORK_THEME"
WHERE public."WORK_THEME".author=:login
ORDER BY public."WORK_THEME".work_theme_id;');
    $st->execute(array('login'=>$login));
    $themes_arr = array();
    while($row=$st->fetch(PDO::FETCH_ASSOC)){
        $student = $row['assignee']=== null ? '' : db_get_full_name_by_login($app,$row['assignee']);
        $theme = ['theme'=>$row['theme'],
            'status'=>$row['assignee']=== null ? $row['status'] : Department_Statuses::TAKEN,
            'student'=>$student,
            'info'=>$row['info'],
            'year'=>$row['theme_year'],
            'theme_id'=>$row['theme_id']];
        array_push($themes_arr,$theme);
    }
    return $themes_arr;
}

function db_get_full_name_by_login($app,$login){
    $st = $app['pdo']->prepare('SELECT name,surname, father_name FROM public."USERS" WHERE login = :login;');
    $st->execute(array('login'=>$login));
    $row=$st->fetch(PDO::FETCH_ASSOC);
    return $row['surname'].' '.$row['name'].' '.$row['father_name'];
}

function db_get_theme_by_name_part($app,$group_id,$login,$input){
    $theme_arr = db_get_all_faculty_themes($app,$login,$group_id);
    $res_themes = filter_themes_by_name_part($theme_arr,$input);
    return $res_themes;
}

function db_set_student_enroll($app,$login,$theme_id){
    try {
        $app['pdo']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $app['pdo']->beginTransaction();

        $student_theme = db_get_student_theme($app,$login)['theme_id'];
        if($student_theme)
            db_set_student_refuse_theme($app,$login,$student_theme);
        $st = $app['pdo']->prepare('UPDATE public."WORK_THEME"
	                                SET assignee=:login, available=false
	                                WHERE work_theme_id=:theme_id;');
        $st->execute(array('login'=>$login,'theme_id'=>$theme_id));

        $app['pdo']->commit();
        return true;
    } catch (Exception $e) {
        $app['pdo']->rollBack();
        return false;
    }
}

function db_set_student_refuse_theme($app,$login,$theme_id){
    $st = $app['pdo']->prepare('UPDATE public."WORK_THEME"
	SET assignee=null, available=true
	WHERE work_theme_id=:theme_id AND assignee=:login;');
    $is_success=$st->execute(array('login'=>$login,'theme_id'=>$theme_id));
    return $is_success;
}

function db_get_filtered_themes($app,$login,$group_id,$type,$course,$teacher,$depart,$spec,$avail,$approve_status,$input){
    $faculty_info = db_get_faculty_info($app,$login,$group_id);
    $user_course = db_get_user_year($app,$login);
    $params_arr = array('faculty_id'=>$faculty_info['faculty_id']);
    $query = 'SELECT public."WORK_THEME".work_theme_id AS theme_id, public."WORK_THEME".name AS theme, public."WORK_THEME".info AS info,
public."WORK_THEME".year AS theme_year, public."WORK_THEME".available AS available, public."WORK_THEME".assignee AS assignee_login,public."WORK_THEME".department_status_id AS department_status_id,
"ASSIGNEE".name AS assign_name, "ASSIGNEE".surname AS assign_surname, "ASSIGNEE".father_name AS assign_father_name,
public."USERS".name AS teacher_name, public."USERS".surname AS teacher_surname,
public."USERS".father_name AS father_name, public."DEPARTMENTS".name AS department,
public."FACULTIES".name AS faculty, public."THEME_TYPES".name AS type
FROM (((((public."WORK_THEME" INNER JOIN public."USERS" ON public."WORK_THEME".author = public."USERS".login)
INNER JOIN public."DEPARTMENTS" ON public."USERS".department_id = public."DEPARTMENTS".department_id)
INNER JOIN public."FACULTIES" ON public."FACULTIES".faculty_id = public."DEPARTMENTS".faculty_id)
INNER JOIN public."FACULTY_YEARS" ON  public."FACULTIES".faculty_id = public."FACULTY_YEARS".faculty_id AND public."WORK_THEME".year=public."FACULTY_YEARS".year)
INNER JOIN public."THEME_TYPES" ON public."FACULTY_YEARS".theme_type_id=public."THEME_TYPES".type_id)
LEFT OUTER JOIN public."USERS" "ASSIGNEE" ON  public."WORK_THEME".assignee="ASSIGNEE".login
WHERE public."FACULTIES".faculty_id =:faculty_id';
    if($type !=null){
        $query.=' AND public."THEME_TYPES".type_id=:theme_type';
        $params_arr['theme_type'] = $type;
    }
    if($course!==null){
        $query.=' AND public."WORK_THEME".year=:course';
        $params_arr['course'] = $course;
    }
    if($depart!==null){
        $query.=' AND  public."DEPARTMENTS".department_id=:department';
        $params_arr['department'] = $depart;
    }
    if($spec!==null){
        $query.=' AND "ASSIGNEE".speciality_id=:speciality';
        $params_arr['speciality'] = $spec;
    }
    if($avail!==null){
        if($avail){
            $query.=' AND public."WORK_THEME".available=true AND public."WORK_THEME".year=:user_year';
        }
        else{
            $query.=' AND (public."WORK_THEME".available=false OR public."WORK_THEME".year!= :user_year)';
        }
        $params_arr['user_year'] =$user_course;
    }
    if($group_id===User_Groups::DEPARTMENT_WORKER_ID && $approve_status!==null){
        $query.=' AND public."WORK_THEME".department_status_id=:department_status_id';
        if( $approve_status ===Department_Statuses::TAKEN){
            $status_id = Department_Statuses::APPROVED;
            $query.=' AND public."WORK_THEME".available=false';
        }
        else if( $approve_status ===Department_Statuses::APPROVED){
            $status_id = Department_Statuses::APPROVED;
            $query.=' AND public."WORK_THEME".available=true';
        }
        else
            $status_id = $approve_status;
        $params_arr['department_status_id'] =$status_id;
    }
    else if($group_id===User_Groups::STUDENT_ID || $group_id===User_Groups::TEACHER_ID){
        $query.=' AND public."WORK_THEME".department_status_id=:department_status_id';
        $params_arr['department_status_id']=Department_Statuses::APPROVED;
    }
    $query.=' ORDER BY public."WORK_THEME".work_theme_id;';
    $themes_arr = db_execute_get_faculty_themes($app,$user_course,$faculty_info['name'],$query,$params_arr);
    $themes_res = filter_themes_by_name_part($themes_arr,$input);
    $themes = filter_themes_by_teacher($themes_res,$teacher);
    return $themes;
}


function db_get_all_faculty_themes($app, $login, $group_id){
    $user_course = db_get_user_year($app,$login);
    $faculty_info = db_get_faculty_info($app,$login,$group_id);
    $faculty_id = $faculty_info['faculty_id'];
    $params = array('faculty_id'=>$faculty_id);
    $query = 'SELECT public."WORK_THEME".work_theme_id AS theme_id, public."WORK_THEME".name AS theme, public."WORK_THEME".info AS info,
public."WORK_THEME".year AS theme_year, public."WORK_THEME".available AS available, public."WORK_THEME".assignee AS assignee_login,public."WORK_THEME".department_status_id AS department_status_id,
"ASSIGNEE".name AS assign_name, "ASSIGNEE".surname AS assign_surname, "ASSIGNEE".father_name AS assign_father_name,
public."USERS".name AS teacher_name, public."USERS".surname AS teacher_surname,
public."USERS".father_name AS father_name, public."DEPARTMENTS".name AS department,
public."FACULTIES".name AS faculty, public."THEME_TYPES".name AS type
FROM (((((public."WORK_THEME" INNER JOIN public."USERS" ON public."WORK_THEME".author = public."USERS".login)
INNER JOIN public."DEPARTMENTS" ON public."USERS".department_id = public."DEPARTMENTS".department_id)
INNER JOIN public."FACULTIES" ON public."FACULTIES".faculty_id = public."DEPARTMENTS".faculty_id)
INNER JOIN public."FACULTY_YEARS" ON  public."FACULTIES".faculty_id = public."FACULTY_YEARS".faculty_id AND public."WORK_THEME".year=public."FACULTY_YEARS".year)
INNER JOIN public."THEME_TYPES" ON public."FACULTY_YEARS".theme_type_id=public."THEME_TYPES".type_id)
LEFT OUTER JOIN public."USERS" "ASSIGNEE" ON  public."WORK_THEME".assignee="ASSIGNEE".login
WHERE public."FACULTIES".faculty_id =:faculty_id';
    if($group_id===User_Groups::STUDENT_ID || $group_id===User_Groups::TEACHER_ID){
        $query.=' AND public."WORK_THEME".department_status_id=:department_status_id';
        $params['department_status_id']=Department_Statuses::APPROVED;
    }
    $query.=' ORDER BY public."WORK_THEME".work_theme_id;';
    $themes_arr = db_execute_get_faculty_themes($app,$user_course,$faculty_info['name'],$query,$params);
    return $themes_arr;
}

function db_execute_get_faculty_themes($app,$user_course,$faculty_name,$query,$params){
    $st = $app['pdo']->prepare($query);
    $themes_arr = array();
    $st->execute($params);
    while($row=$st->fetch(PDO::FETCH_ASSOC)){
        $teacher_name = $row['teacher_surname'].' '.$row['teacher_name'].' '.$row['father_name'];
        $student_name = $row['assignee_login']===null ? '_': $row['assign_name'].' '.$row['assign_surname'].' '.$row['assign_father_name'];
        $available = $row['available'];
        $status = $row['department_status_id']===Department_Statuses::APPROVED && !$available ? Department_Statuses::TAKEN :$row['department_status_id'];
        $theme = ['theme'=>$row['theme'],
            'availiable'=>$available && $row['theme_year']===$user_course,
            'type'=>$row['type'],
            'teacher'=>$teacher_name,
            'faculty'=>$faculty_name,
            'department'=>$row['department'],
            'year'=>$row['theme_year'],
            'student'=>$student_name,
            'info'=>$row['info'],
            'theme_id'=>$row['theme_id'],
            'status'=>$status];
        array_push($themes_arr,$theme);
    }
    return $themes_arr;
}

function db_get_faculty_info($app,$login,$group_id)
{
    if($group_id===User_Groups::STUDENT_ID){
        $faculty_info = db_execute_one_row_st($app,'SELECT faculty_id,name FROM public."FACULTIES"
WHERE faculty_id IN (SELECT faculty_id
					FROM public."SPECIALITIES" WHERE speciality_id IN (SELECT speciality_id
						FROM public."USERS"
						WHERE login=:login));',array('login'=>$login));
    }
    else{
        $faculty_info = db_execute_one_row_st($app,'SELECT faculty_id,name FROM public."FACULTIES"
WHERE faculty_id IN (SELECT faculty_id
					FROM public."DEPARTMENTS" WHERE department_id IN (SELECT department_id
						FROM public."USERS"
						WHERE login=:login));',array('login'=>$login));
    }
    return $faculty_info;
}

function db_get_user_year($app,$login)
{
    $year = db_execute_one_row_st($app,'SELECT year FROM public."USERS" WHERE login=:login',array('login'=>$login))['year'];
    return $year;
}

function db_create_theme($app,$teacher,$name,$year,$annotation){
    $st =  $app['pdo']->prepare('INSERT INTO public."WORK_THEME"(
      author, checked_by, department_status_id, name, info, assignee, year, available)
    VALUES ( :teacher, NULL, 1, :title , :annotation, NULL, :theme_year, false);');
    $st->execute(array('teacher'=>$teacher,'title'=>$name,'annotation'=>$annotation,'theme_year'=>$year));
    return $st;
}

function db_update_theme($app,$id,$name,$year,$annotation){
    $st =  $app['pdo']->prepare('UPDATE public."WORK_THEME"
SET  checked_by=NULL, department_status_id=1, name=:newName, info=:newAnnot, assignee=NULL, year=:newYear, available=false
WHERE   work_theme_id=:id;');
    $st->execute(array('id'=>$id,'newName'=>$name,'newAnnot'=>$annotation,'newYear'=>$year));
    return $st;
};


function db_delete_theme($app,$id){
    $st =  $app['pdo']->prepare('DELETE FROM public."WORK_THEME" WHERE work_theme_id=:id;');
    $st->execute(array('id'=>$id));
    return $st;
}

function db_get_theme_by_name($app,$name){
    $res =  db_execute_one_row_st($app,'SELECT work_theme_id FROM public."WORK_THEME" WHERE name = :theme;',array('theme'=>$name));
    return $res['work_theme_id'];
}

function db_approve_theme($app,$checked_by,$theme_id){
//    $st=$app['pdo']->prepare('UPDATE public."WORK_THEME"
//SET  checked_by=:checked_by, department_status_id=:status,available=true
//WHERE   work_theme_id=:id;');
//    $st->execute(array('id'=>$theme_id,'status'=>Department_Statuses::APPROVED,'checked_by'=>$checked_by));
//    return $st;
    return db_set_theme_status($app,$checked_by,$theme_id,Department_Statuses::APPROVED);
}

function db_disapprove_theme($app,$checked_by,$theme_id){
    return db_set_theme_status($app,$checked_by,$theme_id,Department_Statuses::NOT_APPROVED);
}

function db_set_theme_status($app,$checked_by,$theme_id,$status){
    $st=$app['pdo']->prepare('UPDATE public."WORK_THEME"
SET  checked_by=:checked_by, department_status_id=:status,available=true
WHERE   work_theme_id=:id;');
    $st->execute(array('id'=>$theme_id,'status'=>$status,'checked_by'=>$checked_by));
    return $st;
}

function db_set_user_login($app,$loginOld,$loginNew){
    $st=$app['pdo']->prepare('UPDATE public."USERS"
    SET login=:newlogin
    WHERE login=:login;');
    $res= $st->execute(array('login'=>$loginOld,'newlogin'=>$loginNew));
    return $res!==false;
}

function db_set_user_email($app,$login,$newEmail){
    $st = $app['pdo']->prepare('UPDATE public."USERS"
    SET email=:newEmail
    WHERE login=:login;');
    $res= $st->execute(array('login'=>$login,'newEmail'=>$newEmail));
    return $res;
}

function db_get_users_with_no_admins($app){
    $query = 'SELECT  users_group_id,login, email, public."DEPARTMENTS".name as department, public."SPECIALITIES".name as speciality,  surname, public."USERS".name as firstname, father_name as fathername,  year, "FD".name as d_facult, "FS".name as s_facult
    FROM public."USERS" LEFT JOIN public."DEPARTMENTS" ON public."USERS".department_id = public."DEPARTMENTS".department_id
    LEFT JOIN public."SPECIALITIES" ON public."SPECIALITIES".speciality_id = public."USERS".speciality_id
    LEFT JOIN public."FACULTIES" "FD" ON "FD".faculty_id = public."DEPARTMENTS".faculty_id
	LEFT JOIN public."FACULTIES" "FS" ON "FS".faculty_id = public."SPECIALITIES".faculty_id
	WHERE users_group_id!=:admin_role_id
	 ORDER BY public."USERS".surname;';
    $params = array('admin_role_id'=>User_Groups::ADMIN_ID);
    return execute_users_search_query($app,$query,$params);
}

function db_search_filtered_users($app,$role,$faculty,$speciality,$department,$year){
    $params_arr = array('admin_role_id'=>User_Groups::ADMIN_ID);
    $query = 'SELECT  users_group_id,login, email, public."DEPARTMENTS".name as department, public."SPECIALITIES".name as speciality,  surname, public."USERS".name as firstname, father_name as fathername,  year, "FD".name as d_facult, "FS".name as s_facult
    FROM public."USERS" LEFT JOIN public."DEPARTMENTS" ON public."USERS".department_id = public."DEPARTMENTS".department_id
    LEFT JOIN public."SPECIALITIES" ON public."SPECIALITIES".speciality_id = public."USERS".speciality_id
    LEFT JOIN public."FACULTIES" "FD" ON "FD".faculty_id = public."DEPARTMENTS".faculty_id
	LEFT JOIN public."FACULTIES" "FS" ON "FS".faculty_id = public."SPECIALITIES".faculty_id
	WHERE users_group_id!=:admin_role_id';
    if($role!==null){
        $query.=' AND users_group_id=:user_role';
        $params_arr['user_role'] = $role;
    }
    if($faculty!==null){
        if($role===null || intval($role)===User_Groups::ADMIN_ID){
            $query.=' AND ("FS".faculty_id=:faculty OR "FD".faculty_id=:faculty)';
            $params_arr['faculty'] = $faculty;
        }
        else if(intval($role)===User_Groups::STUDENT_ID) {
            $query .= ' AND "FS".faculty_id=:faculty';
            $params_arr['faculty'] = $faculty;
        }
        else if(intval($role)===User_Groups::TEACHER_ID || intval($role)===User_Groups::DEPARTMENT_WORKER_ID){
            $query.=' AND "FD".faculty_id=:faculty';
            $params_arr['faculty'] = $faculty;
        }
    }
    if($speciality!==null){
        $query.=' AND public."SPECIALITIES".speciality_id=:speciality';
        $params_arr['speciality'] = $speciality;
    }
    if($department!==null){
        $query.=' AND public."DEPARTMENTS".department_id=:department';
        $params_arr['department']=$department;
    }
    if($year!==null){
        $query.=' AND year=:year';
        $params_arr['year']=$year;
    }
    $query.=' ORDER BY public."USERS".surname ;';
    return execute_users_search_query($app,$query,$params_arr);
}

function execute_users_search_query($app,$query,$params){
    $users_res = array();
    try{
        $ug = new User_Groups();
        $st=$app['pdo']->prepare($query);
        $st->execute($params);
        while($row=$st->fetch(PDO::FETCH_ASSOC)){
            $user = [
                'login'=>$row['login'],
                'name'=>$row['surname'].' '.$row['firstname'].' '.$row['fathername'],
                'email'=>$row['email'],
                'role_id'=>$row['users_group_id'],
                'role'=>$ug->getUserGroupName($row['users_group_id']),
                'faculty'=>$row['users_group_id']===User_Groups::STUDENT_ID ? $row['s_facult'] : $row['d_facult'],
                'speciality'=>$row['speciality'],
                'department'=>$row['department'],
                'course'=>$row['year']
            ];
            array_push($users_res,$user);
        }
    }catch(PDOException $exception) {
        $users_res = [];
        throw new Exception('error');
    }
    finally{
            $st = null;
        }
    return $users_res;
}

function db_department_worker_has_checked_themes($app,$user_login){
    $res = db_execute_one_row_st($app,'SELECT COUNT(work_theme_id) AS checked_count FROM public."WORK_THEME"
WHERE checked_by=:user_login;',array('user_login'=>$user_login));
    return $res['checked_count']!==0;
}

function db_teacher_has_themes_approved($app,$user_login){
    $res = db_execute_one_row_st($app,'SELECT COUNT(work_theme_id) AS checked_count FROM public."WORK_THEME"
WHERE author=:user_login AND department_status_id=:status;',array('user_login'=>$user_login,'status'=>Department_Statuses::APPROVED));
    return $res['checked_count']!==0;
}

function db_set_update_email_string($app,$login,$email_update,$email_new_val){
    $st = $app['pdo']->prepare('INSERT INTO public."USERS_INFO_UPDATE" (login, email_update,email_new_val)
VALUES (:login,:email_update,:email_new_val)
ON CONFLICT (login) DO UPDATE SET email_update = EXCLUDED.email_update, email_new_val = EXCLUDED.email_new_val;');
    $success = $st->execute(array('login'=>$login,'email_update'=>$email_update,'email_new_val'=>$email_new_val));
    return $success;
}

function db_set_update_password_string($app,$login,$update_pass){
    $st = $app['pdo']->prepare('INSERT INTO public."USERS_INFO_UPDATE" (login, password_update)
VALUES (:login,:password_update)
ON CONFLICT (login) DO UPDATE SET password_update = EXCLUDED.password_update;');
    $success = $st->execute(array('login'=>$login,'password_update'=>$update_pass));
    return $success;
}

function db_get_login_email_by_conf_id($app,$conf_id){
    $res = db_execute_one_row_st($app,'SELECT login, email_new_val
	FROM public."USERS_INFO_UPDATE"
	WHERE email_update=:conf_id;',array('conf_id'=>$conf_id));
    return $res;
}

function db_get_login_by_pass_update_string($app, $pass_update){
    $res = db_execute_one_row_st($app,'SELECT login FROM public."USERS_INFO_UPDATE"
WHERE password_update=:pass_update;',array('pass_update'=>$pass_update));
    return $res;
}

function db_set_user_password($app,$login,$new_pass_hash){
    $st = $app['pdo']->prepare('UPDATE public."USERS"
    SET password_hash=:pass_hash
    WHERE login=:login;');
    $res = $st->execute(array('pass_hash'=>$new_pass_hash,'login'=>$login));
    return $res;
}