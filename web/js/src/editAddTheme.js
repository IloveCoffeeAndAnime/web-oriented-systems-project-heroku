let API = require('./API');
let Urls = require('./Urls');
let $themeDialogTitle = $('#themeDialogTitle');
let $allTeacherThemesHolder = $('#allTeacherThemesHolder');
let $openCreateThemeDialogBtn = $('#openCreateThemeDialogBtn');
let $themeDialog= $('#themeDialog');
let $themeNameInput = $('#themeNameInput');
let $themeYearInput = $('#themeYearInput');
let $themeAnnotationInput = $('#themeAnnotationInput');
let $createThemeBtn = $('#createThemeBtn');
let $editThemeBtn= $('#editThemeBtn');
let $confDelThemeDialog=$('#confDelThemeDialog');
let $delThemeBtn = $('#delThemeBtn');

let $themeNameWarning = $('#themeNameWarning');

let teacherOwnThemeTemplId = 'teacher_own_themes_templ_id';

function  initCreateEditTeacherTheme() {
    $openCreateThemeDialogBtn.click(function(){
        clearThemeDialog();
        $themeDialogTitle.text('Нова тема');
        $createThemeBtn.show();
        $themeDialog.modal('show');
    });
    initOpenEditThemeDialog();
    initThemeDialogValidation();
    initCreateTheme();
    initEditTheme();
    initOpenDeleteThemeDialog();
    initDeleteTheme();
}

function clearThemeDialog(){
    $themeDialogTitle.text('');
    $createThemeBtn.hide();
    $editThemeBtn.hide();
    $createThemeBtn.prop('disabled', true);
    $editThemeBtn.prop('disabled', true);
    $themeNameInput.val('');
    $themeYearInput.val('0');
    $themeAnnotationInput.val('');
    $editThemeBtn.val('');
    $themeNameWarning.text('');
    $themeNameInput.parent().removeClass('has-error');
    $themeNameInput.removeClass('border-danger');
}

function setThemeDialogValues(name,year,annot){
    $themeNameInput.val(name);
    $themeYearInput.val(year);
    $themeAnnotationInput.val(annot);
}

function initThemeDialogValidation(){
    $themeNameInput.on('input',function(){
        $themeNameWarning.text('');
        $themeNameInput.parent().removeClass('has-error');
        $themeNameInput.removeClass('border-danger');
        enableCreateEditBtnsIfAllCorrect();
    });
    $themeYearInput.on('input',function(){
        enableCreateEditBtnsIfAllCorrect();
    });
    $themeAnnotationInput.on('input',function () {
        enableCreateEditBtnsIfAllCorrect();
    });
}

function enableCreateEditBtnsIfAllCorrect(){
    if(allThemeInputsCorrect()){
        $createThemeBtn.prop('disabled', false);
        $editThemeBtn.prop('disabled', false);
    }
    else{
        $createThemeBtn.prop('disabled', true);
        $editThemeBtn.prop('disabled', true);
    }
}

function allThemeInputsCorrect(){
    return $themeNameInput.val()!=='' && $themeAnnotationInput.val()!=='' && $themeYearInput.val()!=='0';
}

function initOpenEditThemeDialog(){
    $allTeacherThemesHolder.on("click",".teacher-theme-holder > .teacher-theme-info > .holder > .management > .edit-theme-btn",function(){
        clearThemeDialog();
        $themeDialogTitle.text('Редагування теми');
        $editThemeBtn.val($(this).val());
        $editThemeBtn.show();
        let $currTheme = $(this).closest('.teacher-theme-holder');
        let themeName = $currTheme.find('.teacher-theme-theme > .theme').text();
        let themeYear = $currTheme.find('.teacher-theme-info > .holder > .theme-info-row > .info-inner-content').text();
        let themeAnnot = $currTheme.find('.teacher-theme-info > .holder > .annotation > .ann-holder').text();
        setThemeDialogValues(themeName,themeYear,themeAnnot);
        $themeDialog.modal('show');
    });
}

function initCreateTheme(){
    $createThemeBtn.click(function(){
        API.postThemeExists({theme_name:$themeNameInput.val()},function(err,data){
            if(err){
                $themeDialog.modal('hide');
                alert('Неможливо створити тему.');
            }
            else if(data['success']){
                $themeNameWarning.text('Тема з тиким ім\'ям вже існує');
                $themeNameInput.parent().addClass('has-error');
                $themeNameInput.addClass('border-danger');
                //alert('Тема з тиким ім\'ям вже існує');
            } else{
                let newTheme = {
                    name:$themeNameInput.val(),
                    annotation:$themeAnnotationInput.val(),
                    year:$themeYearInput.find(':selected').val()
                };
                API.postCreateTheme({'theme':newTheme},function(err,data){
                    if(err || !data['success']){
                        $themeDialog.modal('hide');
                        alert('Неможливо створити тему.');
                    }
                    else{
                        $themeDialog.modal('hide');
                        updateTeacherThemesInfo();
                    }
                });
            }
        });
    });
}

function initEditTheme(){
    $editThemeBtn.click(function() {
        API.postThemeExists({theme_name: $themeNameInput.val()}, function (err, data) {
            if (err) {
                $themeDialog.modal('hide');
                alert('Неможливо редагувати тему.');
            } else if (data['success']) {
                $themeNameWarning.text('Тема з таким ім\'ям вже існує');
                $themeNameInput.parent().addClass('has-error');
                $themeNameInput.addClass('border-danger');
                //alert('Тема з тиким ім\'ям вже існує');
            } else {
                let theme = {
                    id:$editThemeBtn.val(),
                    name:$themeNameInput.val(),
                    annotation:$themeAnnotationInput.val(),
                    year:$themeYearInput.find(':selected').val()
                };
                API.postUpdateTheme({'theme':theme},function(err,data){
                    if(err || !data['success']){
                        $themeDialog.modal('hide');
                        alert('Не можливо оновити інформцію про тему.');
                    }
                    else{
                        $themeDialog.modal('hide');
                        updateTeacherThemesInfo();
                    }
                });
            }
        });
    })
}

function initOpenDeleteThemeDialog(){
    $allTeacherThemesHolder.on("click",".teacher-theme-holder > .teacher-theme-info > .holder > .management > .del-theme-btn",function(){
        $delThemeBtn.val($(this).val());
        $confDelThemeDialog.modal('show');
    });
    $confDelThemeDialog.on('hidden.bs.modal', function (e) {
        $delThemeBtn.val('');
    });
}

function initDeleteTheme(){
    $delThemeBtn.click(function(){
        let themeId = {'theme_id':$(this).val()};
        API.postDeleteTheme(themeId,function(err,data){
            if(err || data['success']===false){
                alert('Не можливо видалити тему.');
            }
            else{
                updateTeacherThemesInfo();
            }
        });
    });
}

function updateTeacherThemesInfo(){
    API.getTeacherOwnThemes(function(err,data){
        if(err)
            alert('Не можливо відобразити Ваші теми.');
        else{
            Urls.onTwigTemplateLoad(Urls.TeacherThemeOwn,teacherOwnThemeTemplId,function(template){
                $allTeacherThemesHolder.empty();
                data['themes'].forEach(function(element){
                    $allTeacherThemesHolder.append(template.render({'theme':element,'STATUSES':data['STATUSES']}));
                });
            });
        }
    });
}


exports.initCreateEditTeacherTheme = initCreateEditTeacherTheme;