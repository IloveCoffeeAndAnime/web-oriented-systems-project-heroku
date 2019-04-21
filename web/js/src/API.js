/**
 * Created by chaika on 09.02.16.
 */
var API_URL = "http://localhost/web-oriented-systems-project/web";

// $.ajax = (($oldAjax) => {
//     // on fail, retry by creating a new Ajax deferred
//     function check(a,b,c){
//         var shouldRetry = b != 'success' && b != 'parsererror';
//         if( shouldRetry && --this.retries > 0 )
//             setTimeout(() => { $.ajax(this) }, this.retryInterval || 100);
//     }
//     return settings => $oldAjax(settings).always(check)
// })($.ajax);

// function backendGetRetryOnError(url, callback) {
//     $.ajax({
//         url: API_URL + url,
//         type: 'GET',
//         tryCount : 0,
//         retryLimit : 3,
//         success: function(data){
//             callback(null, data);
//         },
//         error: function(xhr, textStatus, errorThrown) {
//             if (textStatus == 'timeout') {
//                 this.tryCount++;
//                 if (this.tryCount <= this.retryLimit) {
//                     //try again
//                     $.ajax(this);
//                     return;
//                 }
//                 return;
//             }
//             if (xhr.status == 500) {
//                 callback(new Error("Ajax Failed"));
//             } else {
//                 callback(new Error("Ajax Failed"));
//             }
//         },
//     })
// }

function backendGet(url, callback) {
    $.ajax({
        url: API_URL + url,
        type: 'GET',
        cache: false,
        success: function(data){
            callback(null, data);
        },
        error: function() {
            callback(new Error("Ajax Failed"));
        },
        // timeout       : 4000,
        // retries       : 3,
        // retryInterval : 3000,
    })
}

function backendPost(url, data, callback) {
    $.ajax({
        url: API_URL + url,
        type: 'POST',
        contentType : 'application/json',
        data: JSON.stringify(data),
        cache: false,
        success: function(data){
            callback(null, data);
        },
        error: function() {
            callback(new Error("Ajax Failed"));
        },
        // timeout       : 4000,
        // retries       : 3,
        // retryInterval : 3000,
    })
}

exports.sendUserRegisterInfo = function(user_info,callback){
    backendPost("/register/user-info/",user_info,callback);
};

exports.sendUserLoginInfo = function(user_info,callback){
    backendPost('/login/user-info/',user_info,callback);
};

exports.getUserGroups = function (callback) {
    backendGet('/',callback);
};

exports.sendThemeNameSearch = function(input,callback){
    backendPost('/user-page/search/',input,callback);
};

exports.sendFilteredSearch = function(input,callback){
    backendPost('/user-page/search/filtered/',input,callback);
};

exports.getAllThemes = function(callback){
   backendGet('/user-page/search/all-themes/',callback);
};

exports.postStudentEnroll = function(data,callback){
  backendPost('/student-page/enroll/',data,callback);
};

exports.postStudentRefuseTheme = function(data,callback){
    backendPost('/student-page/refuse-theme/',data,callback);
};

exports.getOpenThemeEnroll = function(callback){
    backendGet('/admin-page/open-theme-enroll/',callback);
};

exports.getCloseThemeEnroll = function(callback){
    backendGet('/admin-page/close-theme-enroll/',callback);
};

exports.postCreateTheme = function(data,callback){
    backendPost('/teacher-page/create-theme/',data,callback);
};

exports.postUpdateTheme = function(data,callback){
  backendPost('/teacher-page/update-theme/',data,callback);
};

exports.postDeleteTheme = function(data,callback){
    backendPost('/teacher-page/delete-theme/',data,callback);
};

exports.getTeacherOwnThemes = function(callback){
    backendGet('/teacher-page/get-own-themes/',callback);
};

exports.postThemeExists = function (data,callback) {
  backendPost('/teacher-page/theme-exists/',data,callback);
};

exports.postThemeApprove = function (data,callback) {
  backendPost('/department-worker-page/theme-approve/',data,callback);
};

exports.postDisapproveTheme = function (data,callback) {
  backendPost('/department-worker-page/theme-disapprove/',data,callback);
};

exports.postChangeLogin = function(data,callback){
    backendPost('/user-page/change-login/',data,callback);
};

exports.postChangeLogin = function(data,callback){
    backendPost('/user-page/change-email/',data,callback);
};

// exports.getUserRoles = function(callback){
//     backendGet('/user-page/get-roles/',callback);
// };
//
// exports.getFaculties = function (callback) {
//     backendGet('/user-page/get-faculties/',callback);
// };
//
// exports.getSpecialities = function (callback) {
//     backendGet('/user-page/get-specialities/',callback);
// };
//
// exports.getDepartments = function(callback){
//     backendGet('/user-page/get-departments/',callback);
// };

exports.getSearchBlockInfo = function(callback){
    backendGet('/user-page/get-search-block-info/',callback);
};

exports.getUsersNoAdmins = function(callback){
    backendGet('/admin-page/search/all-users-no-admins/',callback);
};

exports.postSearchByNameUsers = function(data,callback){
    backendPost('/admin-page/search/users-no-admins-by-name/',data,callback);
};

exports.postFilteredSearchUsers = function (data,callback) {
    backendPost('/admin-page/search/users-no-admins-filtered/',data,callback);
};

exports.postCreateUser = function(data,callback){
    backendPost('/admin-page/create-user/',data,callback);
};

exports.postDeleteUser = function (data,callback) {
  backendPost('/admin-page/delete-user/',data,callback);
};

exports.getChangepassword = function(callback){
    backendGet('/user-page/change-password/',callback);
};