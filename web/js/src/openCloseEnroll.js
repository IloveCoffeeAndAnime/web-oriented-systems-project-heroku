let API = require('./API');
let $admin_func_div = $('#admin-func-div');
let openEnrolBtnId = 'open-theme-enroll-btn';
let closeEnrolBtnId = "close-theme-enroll-btn";

function initOpenCloseThmeEnroll() {
    $admin_func_div.on("click",'#'+openEnrolBtnId,function(){
        onOpenThemeEnrollBtnClick();
    });
    $admin_func_div.on("click",'#'+closeEnrolBtnId,function(){
        onCloseThemeEnrollBtnClick();
    });

}

function onOpenThemeEnrollBtnClick(){
    API.getOpenThemeEnroll(function(err,data){
        if(err)
            alert('Error while opening theme enrollment');
        else{
            if(data['success']){
               let $btn= $admin_func_div.find('#'+openEnrolBtnId);
                $btn.attr('class', 'close-enrolling');
                $btn.text('Закрити запис');
                $btn.attr('id',closeEnrolBtnId);
            }
        }
    });
}


function onCloseThemeEnrollBtnClick(){
    API.getCloseThemeEnroll(function(err,data){
        if(err){
            alert('Error while closing theme enrollment');
        }
        else{
            if(data['success']){
                let $btn= $admin_func_div.find('#'+closeEnrolBtnId);
                $btn.attr('class', 'open-enrolling');
                $btn.text('Відкрити запис');
                $btn.attr('id',openEnrolBtnId);
            }
        }
    });
}

exports.initOpenCloseThmeEnroll = initOpenCloseThmeEnroll;