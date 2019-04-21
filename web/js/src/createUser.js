let PageInfo = require('./showPageInfo');
let Urls = require('./Urls');
let API = require('./API');
let adminSearch = require('./adminSearch');
// let AdminSearch = require('./adminSearch');

let $createNewUserBtn = $('#createNewUserBtn');
let $userCreateDialog = $('#userCreateDialog');
let $userSurnameInput = $('#userSurnameInput');
let $userNameInput = $('#userNameInput');
let $userMidnameInput = $('#userMidnameInput');
let $userLoginInput = $('#userLoginInput');
let $userRoleInput = $('#userRoleInput');
let $createUserBtn = $('#createUserBtn');

let $userFacultyInput = $('#userFacultyInput');
let $userSpecialityInput = $('#userSpecialityInput');
let $userYearInput = $('#userYearInput');
let $userDepartInput = $('#userDepartInput');

let $facultyInputHolder = $('#facultyInputHolder');
let $specInputHolder = $('#specInputHolder');
let $yearInputHolder = $('#yearInputHolder');
let $depInputHolder = $('#depInputHolder');

let $userEmailInput = $('#userEmailInput');
let $emailErrorText = $('#email-error-text');

let $createdUserShowModal = $('#createdUserShowModal');
let $newUserRole = $('#newUserRole');
let $newUserLogin = $('#newUserLogin');
let $newUserPassword = $('#newUserPassword');
let $newUserFullName = $('#newUserFullName');
let $newUserEmail = $('#newUserEmail');
let $newUserFaculty = $('#newUserFaculty');
let $newUserDepartment = $('#newUserDepartment');
let $newUserSpeciality= $('#newUserSpeciality');
let $newUserCourse = $('#newUserCourse');

let $confDelUserDialog = $('#confDelUserDialog');
let $delUserBtn = $('#delUserBtn');


let defOption;

function  initCreateUser() {
    defOption = $('option.def-search-filter-option').val();
    console.log(defOption);
    initThemeDialogValidation();
    $createNewUserBtn.click(function(){
        clearThemeDialog();
        generateLoginAndPassword();
        $userCreateDialog.modal('show');
    });
    initCreateUserBtn();
    initDeleteUserBtn();
    initDropNewUserModalOnClose();
}

function clearThemeDialog(){
    $facultyInputHolder.slideUp();
    $specInputHolder.slideUp();
    $yearInputHolder.slideUp();
    $depInputHolder.slideUp();
    $createUserBtn.prop('disabled', true);
    $userSurnameInput.val('');
    $userNameInput.val('');
    $userMidnameInput.val('');
    $userEmailInput.val('');
    $('.create-user-form > .form-group > select.search-more-content').val(defOption);
    $userSpecialityInput.find('option:not(.def-search-filter-option)').remove();
    $userDepartInput.find('option:not(.def-search-filter-option)').remove();
    removeEmailErrorMessage();
}

function initThemeDialogValidation(){
    $userSurnameInput.on('input',function(){
        enableCreateEditBtnsIfAllCorrect();
    });
    $userNameInput.on('input',function(){
        enableCreateEditBtnsIfAllCorrect();
    });
    $userMidnameInput.on('input',function () {
        enableCreateEditBtnsIfAllCorrect();
    });
    $userEmailInput.on('input',function(){
        removeEmailErrorMessage();
        enableCreateEditBtnsIfAllCorrect();
    });
    $userRoleInput.on('input',function () {
        changeInputs();
        enableCreateEditBtnsIfAllCorrect();
    });
    $userFacultyInput.on('input',function () {
        changeSpecialityDepartmentInputs($userFacultyInput.val());
        enableCreateEditBtnsIfAllCorrect();
    });
    $userSpecialityInput.on('input',function () {
        enableCreateEditBtnsIfAllCorrect();
    });
    $userYearInput.on('input',function () {
        enableCreateEditBtnsIfAllCorrect();
    });
    $userDepartInput.on('input',function () {
        enableCreateEditBtnsIfAllCorrect();
    });
}

function enableCreateEditBtnsIfAllCorrect(){
    if(allInputsCorrect()){
        $createUserBtn.prop('disabled', false);
    }
    else{
        $createUserBtn.prop('disabled', true);
    }
}

function allInputsCorrect(){
    return $userSurnameInput.val()!=='' && $userNameInput.val()!==''
        && $userMidnameInput.val()!=='' && correctRole() && correctInfo() && emailCorrect();
}

function correctRole() {
    return $userRoleInput.val() === PageInfo.getIdByRoleName('Студент') || $userRoleInput.val() === PageInfo.getIdByRoleName('Викладач')
            || $userRoleInput.val() ===PageInfo.getIdByRoleName('Працівник кафедри');
}

function correctInfo() {
    if($userRoleInput.val() === PageInfo.getIdByRoleName('Студент')){
        return $userFacultyInput.val() !== null && $userSpecialityInput.val() !== null &&
            $userYearInput.val() !== null;
    }
    if($userRoleInput.val() === PageInfo.getIdByRoleName('Викладач') || $userRoleInput.val() === PageInfo.getIdByRoleName('Працівник кафедри')){
        return $userFacultyInput.val() !== null && $userDepartInput.val() !== null;
    }
    return true;
}

function emailCorrect(){
    return $userEmailInput.val()!=='' && /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test($userEmailInput.val());
}

function generateLoginAndPassword(){
    $userLoginInput.val(Math.random().toString(36).slice(-8));
}

function changeInputs(){
    if(correctRole()){
        if($userRoleInput.val() === PageInfo.getIdByRoleName('Студент')){
            $facultyInputHolder.slideDown();
            $specInputHolder.slideDown();
            $yearInputHolder.slideDown();
            $depInputHolder.slideUp();
        }
        else if($userRoleInput.val() === PageInfo.getIdByRoleName('Викладач') || $userRoleInput.val() === PageInfo.getIdByRoleName('Працівник кафедри')){
            $facultyInputHolder.slideDown();
            $specInputHolder.slideUp();
            $yearInputHolder.slideUp();
            $depInputHolder.slideDown();
        }
        else{
            $facultyInputHolder.slideUp();
            $specInputHolder.slideUp();
            $yearInputHolder.slideUp();
            $depInputHolder.slideUp();
        }
    }
}

function changeSpecialityDepartmentInputs(facultyVal){
    $userSpecialityInput.find('option:not(.def-search-filter-option)').remove();
    $userDepartInput.find('option:not(.def-search-filter-option)').remove();
    $userSpecialityInput.val(defOption);
    $userDepartInput.val(defOption);
    let specialities = PageInfo.getFacultySpecs(facultyVal);
    let departments = PageInfo.getFacultyDepartment(facultyVal);
    Urls.onTwigTemplateLoad(Urls.Option,Urls.optionId,function(template){
        specialities.forEach(function(elem){
            $userSpecialityInput.append(template.render({value:elem['speciality_id'],name:elem['name']}));
        });
        departments.forEach(function(elem){
            $userDepartInput.append(template.render({value:elem['department_id'],name:elem['name']}));
        });
    });
}

function initCreateUserBtn(){
    $createUserBtn.click(function(){
        let faculty = $userFacultyInput.find(':selected').text();
        let department = $userDepartInput.find(':selected').text();
        let speciality = $userSpecialityInput.find(':selected').text();
        let role = $userRoleInput.find(':selected').text();
        let errMsg = 'Неможливо створити нового користувача';
        let userInfo ={
            email: $userEmailInput.val(),
            group_id: $userRoleInput.val(),
            depart_id: $userDepartInput.val(),
            spec_id: $userSpecialityInput.val(),
            password: $userLoginInput.val(),
            name: $userNameInput.val(),
            surname : $userSurnameInput.val(),
            father_name : $userMidnameInput.val(),
            year : $userYearInput.val()
        };
        API.postCreateUser({user_info:userInfo},function(err,data){
            if(err){
                $userCreateDialog.modal('hide');
                alert(errMsg);
            }
            else if(data['success']===false){
                if(PageInfo.isUndef(data['error_type'])){
                    $userCreateDialog.modal('hide');
                    alert(errMsg);
                }
                else if(data['error_type']==='email_err'){
                    $emailErrorText.text(data['message']);
                    $userEmailInput.addClass('border-danger');
                    $emailErrorText.show();
                }
            }
            else{
                $userCreateDialog.modal('hide');
                showNewUserModal(role,userInfo.email,userInfo.password,userInfo.surname+' '+userInfo.name+' '+userInfo.father_name,faculty,department,speciality,userInfo.year);
                adminSearch.ShowUserInfoByLastQuery();
            }
        });
    });
}

function removeEmailErrorMessage(){
    $emailErrorText.text('');
    $userEmailInput.removeClass('border-danger');
    $emailErrorText.hide();
}

function initDeleteUserBtn(){
    $('#search-result-content-users').on('click','.user-holder > .info-holder > .btn-holder > button.delete-user',function(){
        let userLogin = $(this).val();
        $delUserBtn.val(userLogin);
        $confDelUserDialog.modal('show');
    });

    $delUserBtn.click(function(){
        API.postDeleteUser({login:$(this).val()},function(err,data){
            if(err || data['success']===false) {
                let message = 'Неможливо видалити користувача.';
                if(data['message'])
                    message +=data['message'];
                alert(message);
            }else{
                adminSearch.ShowUserInfoByLastQuery();
            }
        });
    });
}


function showNewUserModal(role,email,password,fullName,faculty,department,speciality,year){
    $newUserRole.text(role);
    $newUserLogin.text(email);
    $newUserEmail.text(email);
    $newUserPassword.text(password);
    $newUserFullName.text(fullName);
    $newUserFaculty.text(faculty);
    $newUserDepartment.text(department);
    $newUserSpeciality.text(speciality);
    $newUserCourse.text(year);
    if(role==='Студент'){
        $newUserSpeciality.parent().show();
        $newUserCourse.parent().show();
    }
    else if(role==='Викладач' || role==='Працівник кафедри'){
        $newUserDepartment.parent().show();
    }
    $createdUserShowModal.modal('show');
}

function initDropNewUserModalOnClose(){
    $createdUserShowModal.on('hidden.bs.modal', function (e) {
        $newUserLogin.text('');
        $newUserEmail.text('');
        $newUserPassword.text('');
        $newUserFullName.text('');
        $newUserFaculty.text('');
        $newUserDepartment.text('');
        $newUserSpeciality.text('');
        $newUserCourse.text('');
        $newUserDepartment.parent().hide();
        $newUserSpeciality.parent().hide();
        $newUserCourse.parent().hide();
    })
}


exports.initCreateUser = initCreateUser;