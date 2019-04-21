
$(function(){
    //This code will execute when the page is ready
    let register = require('./register');
    let login = require('./login');
    let search  = require('./search');
    // let openCloseEnrol = require('./openCloseEnroll');
    let creatEditTheme = require('./editAddTheme');
    // let createUser = require('./createUser');
    let changeInfo = require('./changeInfoDialog');
    let approveRefuseTheme = require('./approvRefuseTheme');
    let studentEnroll = require('./studentEnroll');
    // let adminSearch = require('./adminSearch');

    register.initRegisterPage();
    login.initLoginPage();
    search.initSearchBlock();
    // openCloseEnrol.initOpenCloseThmeEnroll();
    creatEditTheme.initCreateEditTeacherTheme();
    // createUser.initCreateUser();
    changeInfo.initChangeDialog();
     approveRefuseTheme.initApprovRefuseTheme();
     studentEnroll.initStudentEnroll();
    // adminSearch.initAdminSearch();
    let pagesJS = require('./pagesJS');
});