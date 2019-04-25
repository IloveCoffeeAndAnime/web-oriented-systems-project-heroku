let API = require('./API');
let Urls = require('./Urls');

let roles = [];
let faculties = [];
let specialities =[];
let departments = [];
let facs_years = [];

// function initRoles(callback){
//     API.getUserRoles(function(err,data){
//         if(err){
//             // alert('Не можливо відобразити ролі користувачів');
//         }else{
//             roles = data;
//             if(!isUndef(callback))
//                 callback(data);
//         }
//     });
// }
//
// function initFaculties(callback){
//     API.getFaculties(function(err,data){
//         if(err){
//             // alert('Не можливо відобразити факультети');
//         }
//         else{
//             faculties = data;
//             if(!isUndef(callback))
//                 callback(data);
//         }
//     });
// }
//
// function initSpecialities(callback){
//     API.getSpecialities(function(err,data){
//         specialities = data;
//          if(!isUndef(callback))
//             callback(data);
//     });
// }
//
// function initDepartments(callback){
//     API.getDepartments(function (err,data) {
//         if(err){
//             // alert('Неможливо відобразити список кафедр');
//         }
//         else{
//             departments = data;
//             if(!isUndef(callback))
//                 callback(data);
//         }
//     });
// }

function initSearchBlockInfo(callback){
    API.getSearchBlockInfo(function(err,data){
        if(err)
            alert('Неможливо відобразати дані для пошуку');
        else{
            roles = data['user_roles'];
            faculties = data['faculties'];
            specialities = data['specialities'];
            departments = data['departments'];
            facs_years = data['facs_years'];
            callback(roles,faculties,specialities,departments);
        }
    });
}

function getFacultySpecs(facultId){
    let res = [];
    specialities.forEach(function(elem){
        if(elem['faculty_id'].toString()===facultId.toString())
            res.push(elem);
    });
    return res;
}

function getFacultyYears(facultId){
    let res = [];
    facs_years.forEach(function(elem){
        if(elem['faculty_id'].toString()===facultId.toString())
            res.push(elem);
    });
    return res;
}

function getFacultyDepartment(facultId){
    let res = [];
    departments.forEach(function(elem){
        if(elem['faculty_id'].toString()===facultId.toString())
            res.push(elem);
    });
    return res;
}

function getUserRoles(){
    return roles;
}

function getFaculties(){
    return faculties;
}

function getIdByRoleName(name){
    let id = null;
    let userRoles = getUserRoles();
    userRoles.forEach(function(elem){
        if(elem['name']===name)
            id = elem['id'].toString();
    });
    return id;
}

function isUndef(variable){
    return typeof(variable) == 'undefined';
}

exports.getIdByRoleName = getIdByRoleName;
exports.getFacultySpecs = getFacultySpecs;
exports.getFacultyDepartment = getFacultyDepartment;
// exports.initRoles = initRoles;
// exports.initFaculties = initFaculties;
// exports.initSpecialities =initSpecialities;
// exports.initDepartments = initDepartments;
exports.getFaculties = getFaculties;
exports.getUserRoles = getUserRoles;
exports.initSearchBlockInfo = initSearchBlockInfo;
exports.isUndef = isUndef;
exports.getFacultyYears = getFacultyYears;