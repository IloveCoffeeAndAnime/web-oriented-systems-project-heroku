let API = require('./API');
let searchThemes = require('./search');
let Urls = require('./Urls');

let $searchResultContent = $('#search-result-content');
let $refuseBtnHolder = $('#refuseBtnHolder');
let $studThemeInfo = $('#studThemeInfo');
let $studThemeTeacher = $('#studThemeTeacher');

let $enrollThemeOkModal = $('#enrollThemeOkModal');
let $refuseThemeOkModal = $('#refuseThemeOkModal');

function initStudentEnroll(){
    initStudentEnrollBtnCLick();
    initStudentRefuseThemeBtnClick();
}

function initStudentEnrollBtnCLick(){
    $searchResultContent.on('click','.theme-holder > .theme-more > .btn-div > .student-enroll-btn',function(){
        API.postStudentEnroll({theme_id:$(this).val()},function(err,data){
            if(err || data['success']===false){
                alert('Неможливо записатись на цю тему.');
            }else{
                $enrollThemeOkModal.modal('show');
                searchThemes.showThemesByLastQuery();
                let theme = data['theme'];
                updateStudentInfoBlock(theme['theme_id'],theme['theme'],theme['author_surname']+' '+theme['author_name']+' '+theme['author_father_name'],data['theme_enroll_opened']);
            }
        });
    });
}

function initStudentRefuseThemeBtnClick(){
    $refuseBtnHolder.on('click','.refuseThemeBtn',function(){
        API.postStudentRefuseTheme({theme_id:$(this).val()},function(err,data){
            if(err || data['success']===false){
                alert('Неможливо виписатись з теми.');
            }else{
                $refuseThemeOkModal.modal('show');
                searchThemes.showThemesByLastQuery();
                updateStudentInfoBlock(null,null,null,null);
            }
        });
    });
}

function updateStudentInfoBlock(theme_id,theme_name,teacher,enroll_theme_opened){
    if(theme_id){
        $studThemeInfo.find('.info-inner-content').text(theme_name);
        $studThemeTeacher.find('.info-inner-content').text(teacher);
        if(enroll_theme_opened){
            if($refuseBtnHolder.children().length > 0 ){
                $refuseBtnHolder.find('.refuseThemeBtn').val(theme_id);
            }else{
                Urls.onTwigTemplateLoad(Urls.UserRefuseBtn,Urls.userRefuseBtnId,function(template){
                    $refuseBtnHolder.append(template.render({enroll_theme_id:theme_id}));
                });
            }
        }
    }else{
        $studThemeInfo.find('.info-inner-content').text('_');
        $studThemeTeacher.find('.info-inner-content').text('_');
        $refuseBtnHolder.empty();
    }
}

exports.initStudentEnroll = initStudentEnroll;