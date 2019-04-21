let API = require('./API');
let Twig = require('twig');
let Urls = require('./Urls');
let twig = Twig.twig;

let $searchInput = $('#search-input');
let $searchButton = $('#search-button');
let $search_result_content = $('#search-result-content');
let $drop_filters_btn = $('#drop-filters-btn');
let $search_filter_btn= $('#search-filter-btn');
let $type_filter = $('#type-filter');
let $course_filter = $('#course-filter');
let $teacher_filter = $('#teacher-filter');
let $departs_filter = $('#departs-filter');
let $specs_filter = $('#specs-filter');
let $available_filter = $('#available-filter');
let $approve_filter = $('#approve-filter');
let $search_res_amount_span = $('#search-res-amount-span');
let defFilterOption;

let theme_templ_id = 'theme_template_id';
let LastQuery = {
    'type':null,
    'name':null,
    'course':null,
    'teacher':null,
    'department':null,
    'speciality':null,
    'available':null,
    'approve_status':null
};

function initSearchBlock(){
    searchAllThemes();
    defFilterOption = $('option.def-search-filter-option').val();
    $searchButton.click(function(){
        dropFilters();
        searchByName();
    });
    $drop_filters_btn.click(function(){
        dropFiltersBtnClick();
    });
    $search_filter_btn.click(function(){
        searchFilterBtnClick();
    });
}

function searchByName(){
    let input =  $searchInput.val();
    let dataToSend = {'input':input};
    API.sendThemeNameSearch(dataToSend,function(err,data){
        if(err){
            alert('Не можливо відобразити дані про теми.');
        }
        else{
            $search_res_amount_span.text(data['themes'].length);
            $search_res_amount_span.parent().show();
            showThemes(data);
        }
    });
};

function searchAllThemes(){
    API.getAllThemes(function (err,data) {
        if(err){
            alert('Не можливо відобразити дані про теми.');
        }
        else{
            showThemes(data);
            $search_res_amount_span.parent().hide();
            $search_res_amount_span.text(data['themes'].length);
        }
    });
}

function dropFiltersBtnClick(){
    dropFilters();
    if($searchInput.val()!==''){
        searchByName();
    }else{
        searchAllThemes();
    }
}

function dropFilters(){
    $('select.search-more-content').val(defFilterOption);
    $teacher_filter.val('');
    setLastQueriedParams(defFilterOption,defFilterOption,'',defFilterOption,defFilterOption,defFilterOption,defFilterOption,$searchInput.val());
}

function searchFilterBtnClick(){
    let type = $type_filter.find(':selected').val();
    let course = $course_filter.find(":selected").val();
    let teacher = $teacher_filter.val();
    let depart = $departs_filter.find(":selected").val();
    let spec = $specs_filter.find(":selected").val();
    let approve_status = $approve_filter.find(":selected").val();
    let name = $searchInput.val();
    console.log($available_filter);
    let avail = !$available_filter.length ? defFilterOption : $available_filter.find(":selected").val();
    let filters = {
        'type': type === defFilterOption ? null : type,
        'course':course === defFilterOption ? null : course,
        'teacher':teacher === "" ? null : teacher,
        'department':depart === defFilterOption ? null : depart,
        'speciality':spec === defFilterOption ? null : spec,
        'available':avail === defFilterOption ? null : avail==='true',
        'approve_status':approve_status === defFilterOption ? null : parseInt(approve_status,10),
        'name':name==='' ? null :name
    };
    setLastQueriedParams(type,course,teacher,depart,spec,avail,approve_status,name);
    API.sendFilteredSearch({'input':filters},function (err,data) {
        if(err){
            alert("Error during search.");
        }
        else{
            $search_res_amount_span.text(data['themes'].length);
            $search_res_amount_span.parent().show();
            showThemes(data);
        }
    });
}

function showThemes(data){
    let loadedTwig = twig({ ref: theme_templ_id });
    console.log(data);
    $search_result_content.empty();
    if(loadedTwig===null){
        twig({
            id: theme_templ_id,
            href: Urls.TeacherThemeInSearch/*"/web-oriented-systems-project/web/views/frontend_compatible/themerow_new.twig"*/,
            load:function(template){
                console.log(template);
                data['themes'].forEach(function(element){
                    $search_result_content.append(template.render({role:data['group'],theme:element,STATUSES:data['STATUSES']}));
                });
            }
        });
    }
    else{
        data['themes'].forEach(function(element){
            $search_result_content.append(loadedTwig.render({role:data['group'],theme:element,STATUSES:data['STATUSES']}));
        });
    }
}

function showThemesByLastQuery(){
    if(isSetLastQueriedParams()) {
        API.sendFilteredSearch({'input':LastQuery},function (err,data) {
                if(err){
                    alert("Не можливо відобразити останні дані.");
                }
                else{
                    $search_res_amount_span.text(data['themes'].length);
                    $search_res_amount_span.parent().show();
                    showThemes(data);
                }
            });
    }
    else{
        searchAllThemes();
    }
}

function setLastQueriedParams(type,course,teacher,depart,spec,avail,approve_status,name){
    LastQuery['type'] = type === defFilterOption ? null : type;
    LastQuery['course'] = course === defFilterOption ? null : course;
    LastQuery['teacher'] = teacher === "" ? null : teacher;
    LastQuery['department'] = depart === defFilterOption ? null : depart;
    LastQuery['speciality'] = spec === defFilterOption ? null : spec;
    LastQuery['available'] = avail === defFilterOption ? null : avail==='true';
    LastQuery['approve_status'] = approve_status === defFilterOption ? null : parseInt(approve_status,10);
    LastQuery['name'] = name === '' ? null : name;
}

function isSetLastQueriedParams(){
    return LastQuery['course']!==null || LastQuery['teacher']!==null ||  LastQuery['department']!==null || LastQuery['speciality']!==null ||  LastQuery['available']!==null ||  LastQuery['approve_status']!==null || LastQuery['name']!==null || LastQuery['type']!==null;
}


exports.initSearchBlock = initSearchBlock;
exports.showThemesByLastQuery =showThemesByLastQuery;