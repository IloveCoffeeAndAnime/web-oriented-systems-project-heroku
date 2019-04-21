$(function(){
    let openCloseEnroll = require('./openCloseEnroll');
    let createUser = require('./createUser');
    let changeInfo = require('./changeInfoDialog');
    let approveRefuseTheme = require('./approvRefuseTheme');
    let adminSearch = require('./adminSearch');

    openCloseEnroll.initOpenCloseThmeEnroll();
    createUser.initCreateUser();
    changeInfo.initChangeDialog();
    approveRefuseTheme.initApprovRefuseTheme();
    adminSearch.initAdminSearch();
    let pagesJS = require('./pagesJS');
});