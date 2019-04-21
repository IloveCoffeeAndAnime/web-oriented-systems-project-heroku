let API = require('./API');
let Search = require('./search');

let $confApproveThemeDialog = $('#confApproveThemeDialog');
let $approveThemeBtn = $('#approveThemeBtn');

let $confDisappThemeDialog = $('#confDisappThemeDialog');
let $disappThemeBtn = $('#disappThemeBtn');

function initApprovRefuseTheme(){
    $('#search-result-content').on('click',".theme-holder > .theme-more > .btn-div > .btn-approve",function(){
        $approveThemeBtn.val($(this).val());
        $confApproveThemeDialog.modal('show');
    });
    $('#search-result-content').on('click',".theme-holder > .theme-more > .btn-div > .btn-disapprove",function(){
        $disappThemeBtn.val($(this).val());
        $confDisappThemeDialog.modal('show');
    });
    initSubmitApproveTheme();
    initSubmitDisaproveTheme();
}

function initSubmitApproveTheme(){
    $approveThemeBtn.click(function(){
        let data = {
            theme_id:$(this).val()
        };
        API.postThemeApprove(data,function (err,data) {
            if(err || data['success']===false)
                alert('Неможливо схвалити тему.');
            else{
                Search.showThemesByLastQuery();
            }
        });
    });
}

function initSubmitDisaproveTheme(){
    $disappThemeBtn.click(function(){
        let data = {
            theme_id:$(this).val()
        };
        API.postDisapproveTheme(data,function(err,data){
            if(err || data['success']===false){
                alert('Неможливо відхилити тему.');
            }
            else{
                Search.showThemesByLastQuery();
            }
        });
    });
}

exports.initApprovRefuseTheme=initApprovRefuseTheme;