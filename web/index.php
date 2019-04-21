<?php

require_once('../vendor/autoload.php');
include ('./php/db_connect.php');
require_once ('./php/server_queries.php');

use Mailgun\Mailgun;
$mg = Mailgun::create($_ENV['MAILGUN_API_KEY']);

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();
$app['debug'] = true;


// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Register view rendering
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/views',
));

$app->register(new Silex\Provider\SessionServiceProvider());

//$app->register(new Silex\Provider\AssetServiceProvider(), array(
//    'assets.named_packages' => array(
//        'stylesheets' => array('base_path' => "/php-getting-started/web/assets/"),
//        'images' => array('base_path' => $_SERVER['SERVER_NAME'] . '/assets/images'),
//        'js'=> array('base_path' =>$_SERVER['SERVER_NAME'] . '/js/compiled/')
//    ),
//));


$app->register(new Lokhman\Silex\Provider\ConfigServiceProvider(), [
    'config.dir' => __DIR__ . '/config',
]);

//pdo handler for silex
$app->register(new Csanquer\Silex\PdoServiceProvider\Provider\PDOServiceProvider('pdo'),
    array(
        'pdo.server' => array(
            'driver' => 'pgsql',
            'user' => $app['config']['db']['user'],
            'password' =>$app['config']['db']['pass'],
            'host' => $app['config']['db']['host'],
            'port' =>$app['config']['db']['port'],
            'dbname' =>$app['config']['db']['dbname']),
    )
);

$app->error(function (\Exception $e, Request $request, $code) {
    return new Response('We are sorry, but something went terribly wrong.');
});

// Our web handlers
$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});


$app->get('/',function() use($app){
    $app['monolog']->addDebug('logging output.');
    return get_main_page($app);
});

$app->get('/register/',function() use($app){
    return get_registration_page($app);
})->bind('register');

$app->get('/login/',function() use($app){
    return get_login_page($app);
})->bind('login');


$app->get('/student-page/',function()  use($app){
    return get_student_page($app);
});

//$app->get('/student-themes-list/',function() use($app){
//    return get_student_themes_list_page($app);
//});


$app->get('/teacher-page/',function() use($app){
    return get_teacher_page($app);
});

$app->get('/department-worker-page/',function() use($app){
    return get_department_worker_page($app);
});

$app->get('/user-page/',function() use($app){
    return get_user_page($app);
})->bind('user_page');

$app->get('/admin-page/',function() use($app){
    return get_admin_page($app);
});

$app->get('/admin-page/open-theme-enroll/', function()  use($app){
    return get_open_theme_enroll($app);
});

$app->get('/admin-page/close-theme-enroll/', function()  use($app){
    return get_close_theme_enroll($app);
});

$app->post('/register/user-info/', function(Request $request) use($app){
    return post_user_register($app,$request);
});

$app->post('/login/user-info/',function(Request $request) use($app){
    return post_user_login($app,$request->request->get('login'),$request->request->get('password'));
});

$app->get('/logout/',function() use($app){
    return get_user_logout($app);
})->bind('logout');


$app->post('/user-page/change-login/',function(Request $request) use($app){
    return post_change_login($app,$request->request->get('new_login'));
});

$app->post('/user-page/change-email/',function(Request $request) use($app,$mg){
    return post_change_email($app,$mg,$request->request->get('email'));
});

$app->post('/user-page/search/',function(Request $request) use($app){
    return post_search_theme_by_name($app,$request->request->get('input'));
});

$app->get('/user-page/search/all-themes/',function() use($app){
    return get_search_all_themes($app);
});

$app->post('/user-page/search/filtered/',function(Request $request) use($app){
    return post_search_filters($app,$request->request->get('input'));
});

//$app->get('/student-page/enroll/',function() use($app){
//    return get_student_enroll($app);
//});

$app->post('/student-page/enroll/',function(Request $request) use($app,$mg){
    return post_student_enroll($app,$mg,$request->request->get('theme_id'));
})->bind('enroll');

$app->post('/student-page/refuse-theme/',function(Request $request)use($app){
    return post_student_refuse_theme($app,$request->request->get('theme_id'));
})->bind('refuse-theme');

$app->post('/teacher-page/create-theme/',function(Request $request)use($app){
   return post_create_teacher_theme($app,$request->request->get('theme'));
});

$app->post('/teacher-page/update-theme/',function(Request $request) use($app){
    return post_update_teacher_theme($app,$request->request->get('theme'));
});

$app->post('/teacher-page/delete-theme/',function(Request $request) use($app){
    return post_delete_teacher_theme($app,$request->request->get('theme_id'));
});

$app->get('/teacher-page/get-own-themes/',function(Request $request) use($app){
    return get_teacher_themes($app);
});

$app->post('/teacher-page/theme-exists/',function(Request $request) use($app){
    $theme_name = $request->request->get('theme_name');
    return post_theme_exists($app,$theme_name);
});

$app->post('/department-worker-page/theme-approve/',function(Request $request) use($app){
    $theme_id = $request->request->get('theme_id');
    return post_theme_approve($app,$theme_id);
});

$app->post('/department-worker-page/theme-disapprove/',function(Request $request) use($app){
    $theme_id = $request->request->get('theme_id');
    return post_theme_disapprove($app,$theme_id);
});


//$app->get('/user-page/get-roles/',function() use($app){
//    return get_user_roles($app);
//});
//
//$app->get('/user-page/get-faculties/',function() use($app){
//    return get_faculties($app);
//});
//
//$app->get('/user-page/get-specialities/',function() use($app){
//    return get_specialities($app);
//});
//
//$app->get('/user-page/get-departments/',function() use($app){
//    return get_departments($app);
//});

$app->get('/user-page/get-search-block-info/',function () use($app){
    return get_sarch_block_info($app);
});

$app->get('/admin-page/search/all-users-no-admins/',function() use($app){
    return get_users_no_admin($app);
});

$app->post('/admin-page/search/users-no-admins-by-name/',function(Request $request) use($app){
    return post_searh_by_name_users($app,$request->request->get('name'));
});

$app->post('/admin-page/search/users-no-admins-filtered/',function(Request $request) use($app){
    return post_search_filtered_users($app,$request->request->get('input'));
});

$app->post('/admin-page/create-user/',function(Request $request) use($app){
    return post_add_user($app,$request->request->get('user_info'));
});

$app->post('/admin-page/delete-user/',function(Request $request) use($app){
    return post_delete_user($app,$request->request->get('login'));
});

$app->get('/user-page/change-email/confirm/{id}',function($id) use($app){
    return get_user_conf_change_email($app,$id);
});

$app->get('/user-page/change-password/',function() use($app,$mg){
    return get_user_change_password($app,$mg);
});

$app->get('/user-page/change-password/change-page/{id}',function($id) use($app){
    return get_change_password_page($app,$id);
});

$app->post('/user-page/change-password/confirm/',function(Request $request) use($app){
    return post_change_password_confirm($app,$request->request->get('confirm_string'),$request->request->get('password'));
})->bind('password_change_confirm');

$app->run();
