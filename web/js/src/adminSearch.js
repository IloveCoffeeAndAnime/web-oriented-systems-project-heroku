let PageInfo = require('./showPageInfo');
let Urls = require('./Urls');
let API = require('./API');

// let optionId = 'option_templ';
// let userInSearchId = 'user_in_search';

let defFilterOption;

let $roleFilter = $('#role-filter');
let $userRoleInput = $('#userRoleInput');
let $facultyFilter = $('#faculty-filter');
let $userFacultyInput = $('#userFacultyInput');
let $specsFilter = $('#specs-filter');
let $departsFilter = $('#departs-filter');
let $yearFilter = $('#year-filter');
let $searchInputUsers = $('#search-input-users');
let $searchResultContentUsers = $('#search-result-content-users');
let $searchButtonUsers = $('#search-button-users');
let $searchFilterBtnUsers = $('#search-filter-btn-users');
let $dropFiltersBtnUsers = $('#drop-filters-btn-users');

let $searchResAmountSpan = $('#search-res-amount-span');

let SearchUsersLastQuery = {
    name:null,
    role: null,
    faculty: null ,
    speciality:null,
    year:null,
    department: null
};

function initAdminSearch(){
    defFilterOption = $('option.def-search-filter-option').val();
    $departsFilter.parent().hide();
    $yearFilter.parent().hide();
    $specsFilter.parent().hide();
    showSearchBlockInfo();
    // showUserGroups();
    // showFaculties();
    // PageInfo.initSpecialities();
    // PageInfo.initDepartments();
    initFacultySelectionChanged();
    initRoleSelectionChanged();
    initSearchUsers();
    showAllUsers();
}

// function showUserGroups(){
//     PageInfo.initRoles(function(roles){
//         Urls.onTwigTemplateLoad(Urls.Option,optionId,function(template){
//             roles.forEach(function(element){
//                 $roleFilter.append(template.render({value:element['id'],name:element['name']}));
//                 $userRoleInput.append(template.render({value:element['id'],name:element['name']}));
//             });
//         });
//     });
// }
//
// function showFaculties(){
//     PageInfo.initFaculties(function(facults){
//         Urls.onTwigTemplateLoad(Urls.Option,optionId,function(template){
//             facults.forEach(function(element){
//                 $facultyFilter.append(template.render({value:element['faculty_id'],name:element['name']}));
//                 $userFacultyInput.append(template.render({value:element['faculty_id'],name:element['name']}));
//             });
//         });
//     });
// }

function showSearchBlockInfo(){
    PageInfo.initSearchBlockInfo(function(roles,faculties,specialities,departments){
        Urls.onTwigTemplateLoad(Urls.Option,Urls.optionId,function(template){
            roles.forEach(function(element){
                $roleFilter.append(template.render({value:element['id'],name:element['name']}));
                $userRoleInput.append(template.render({value:element['id'],name:element['name']}));
            });
            faculties.forEach(function(element){
                $facultyFilter.append(template.render({value:element['faculty_id'],name:element['name']}));
                $userFacultyInput.append(template.render({value:element['faculty_id'],name:element['name']}));
            });
        });
    });
}

function initRoleSelectionChanged(){
    $roleFilter.on('input',function(){
       if($roleFilter.val()===PageInfo.getIdByRoleName('Студент')){
           $departsFilter.parent().slideUp();
           $specsFilter.parent().slideDown();
           $yearFilter.parent().slideDown();
       }
       else if($roleFilter.val()===PageInfo.getIdByRoleName('Працівник кафедри') || $roleFilter.val()===PageInfo.getIdByRoleName('Викладач')){
           $specsFilter.parent().slideUp();
           $yearFilter.parent().slideUp();
           $departsFilter.parent().slideDown();
       }
       else{
           $specsFilter.parent().slideUp();
           $yearFilter.parent().slideUp();
           $departsFilter.parent().slideUp();
       }
        setAllSelectorsToDefault();
    });
}

function initFacultySelectionChanged(){
    $facultyFilter.on('input',function(){
        let facultId = $(this).val();
        $specsFilter.find('option:not(.def-search-filter-option)').remove();
        $departsFilter.find('option:not(.def-search-filter-option)').remove();
        $specsFilter.val('0');
        $departsFilter.val('0');
        let specsToShow = PageInfo.getFacultySpecs(facultId);
        let departsToShow = PageInfo.getFacultyDepartment(facultId);
        Urls.onTwigTemplateLoad(Urls.Option,Urls.optionId,function(template){
            specsToShow.forEach(function(elem){
                $specsFilter.append(template.render({value:elem['speciality_id'],name:elem['name']}));
            });
            departsToShow.forEach(function(elem){
                $departsFilter.append(template.render({value:elem['department_id'],name:elem['name']}));
            });
        });
    });
}

function dropAllSelectors(){
    $roleFilter.val('0');
    setAllSelectorsToDefault();
    $specsFilter.parent().slideUp();
    $yearFilter.parent().slideUp();
    $departsFilter.parent().slideUp();
}

function setAllSelectorsToDefault(){
    $facultyFilter.val('0');
    $specsFilter.val('0');
    $departsFilter.val('0');
    $yearFilter.val('0');
    $specsFilter.find('option:not(.def-search-filter-option)').remove();
    $departsFilter.find('option:not(.def-search-filter-option)').remove();
}

function showAllUsers(){
    setUserSearchLastQueryParams('',defFilterOption,defFilterOption,defFilterOption,defFilterOption,defFilterOption);
    API.getUsersNoAdmins(function (err,data) {
        if(err){
             alert('Неможливо відобразити інформацію про користувачів.');
        }
        else{
            showUserInfo(data);
            $searchResAmountSpan.parent().hide();
            $searchResAmountSpan.text(data['users'].length);
        }
    });
}

function initSearchUsers(){
    $searchButtonUsers.click(function(){
        dropAllSelectors();
        searchByNameUsers();
    });
    $searchFilterBtnUsers.click(function(){
        searchFilteredUsers();
    });
    $dropFiltersBtnUsers.click(function(){
        dropAllSelectors();
        if($searchInputUsers.val() === '')
            showAllUsers();
        else
            searchByNameUsers();
    });
}

function searchFilteredUsers(){
    let filters = {
        name:$searchInputUsers.val() === '' ? null : $searchInputUsers.val(),
        role: $roleFilter.val() === defFilterOption ? null : $roleFilter.val(),
        faculty: $facultyFilter.val() === defFilterOption ? null : $facultyFilter.val() ,
        speciality: $specsFilter.val() === defFilterOption ? null : $specsFilter.val(),
        year: $yearFilter.val() === defFilterOption ? null : $yearFilter.val(),
        department: $departsFilter.val() === defFilterOption ? null : $departsFilter.val()
    };
    setUserSearchLastQueryParams(filters.name,filters.role,filters.faculty,filters.department,filters.speciality,filters.year);
    API.postFilteredSearchUsers({input:filters},function(err,data){
        if(err)
            alert('Неможливо здійснити фільтрований пошук користувачів.');
        else{
            showUserInfo(data);
            $searchResAmountSpan.text(data['users'].length);
            $searchResAmountSpan.parent().show();
        }
    });
}

function searchByNameUsers(){
    let name = $searchInputUsers.val() === '' ? null : $searchInputUsers.val();
    setUserSearchLastQueryParams(name,defFilterOption,defFilterOption,defFilterOption,defFilterOption,defFilterOption);
    API.postSearchByNameUsers({name:name},function (err,data) {
        if(err)
            alert('Неможливо здійснити пошук користувачів за іменем.');
        else{
            showUserInfo(data);
            $searchResAmountSpan.text(data['users'].length);
            $searchResAmountSpan.parent().show();
        }
    });
}

function ShowUserInfoByLastQuery(){
    if(isSetSearchUserlastQuery()){
        API.postFilteredSearchUsers({input:SearchUsersLastQuery},function(err,data){
            if(err)
                alert('Неможливо відобразити дані коритсувачів.');
            else{
                showUserInfo(data);
                $searchResAmountSpan.text(data['users'].length);
                $searchResAmountSpan.parent().show();
            }
        });
    }
    else{
        showAllUsers();
    }
}


function showUserInfo(data){
    Urls.onTwigTemplateLoad(Urls.UserInSearch,Urls.userInSearchId,function (template) {
        $searchResultContentUsers.empty();
        let userInfo = data['users'];
        let userRoles = data['ROLES'];
        userInfo.forEach(function(elem){
            $searchResultContentUsers.append(template.render({user:elem,ROLES:userRoles}));
        });
    });
}

function setUserSearchLastQueryParams(name,role,faculty,department,speciality,year){
    SearchUsersLastQuery.name= name  === '' ? null : name;
    SearchUsersLastQuery.role = role  === defFilterOption ? null : role;
    SearchUsersLastQuery.faculty = faculty  === defFilterOption ? null : faculty;
    SearchUsersLastQuery.department = department  === defFilterOption ? null : department;
    SearchUsersLastQuery.speciality = speciality  === defFilterOption ? null : speciality;
    SearchUsersLastQuery.year = year  === defFilterOption ? null : year;
}

function isSetSearchUserlastQuery(){
    return SearchUsersLastQuery.name!==null || SearchUsersLastQuery.role!==null || SearchUsersLastQuery.faculty!==null ||
        SearchUsersLastQuery.speciality!==null || SearchUsersLastQuery.department!==null || SearchUsersLastQuery.year!==null;
}

exports.initAdminSearch = initAdminSearch;
exports.ShowUserInfoByLastQuery = ShowUserInfoByLastQuery;