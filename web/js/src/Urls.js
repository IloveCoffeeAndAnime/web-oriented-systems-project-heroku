let Twig = require('twig');
let twig = Twig.twig;

exports.TeacherThemeInSearch = '/views/frontend_compatible/themerow_new.twig';
exports.TeacherThemeOwn = '/views/frontend_compatible/fc_teacher_theme.twig';
exports.Option = '/views/frontend_compatible/fc_option.twig';
exports.UserInSearch = '/views/frontend_compatible/fc_user_row.twig';
exports.UserRefuseBtn = '/views/frontend_compatible/refuse_theme_btn.twig';

exports.optionId = 'option_templ';
exports.userInSearchId = 'user_in_search';
exports.userRefuseBtnId = 'user_refuse_btn';

function onTwigTemplateLoad(templUrl,templId,callback){
    let loadedTwig = twig({ ref: templId });
    if(loadedTwig===null){
        twig({
            id: templId,
            href: templUrl,
            load:function(template){
                callback(template);
            }
        });
    }
    else{
        callback(loadedTwig);
    }
};

exports.onTwigTemplateLoad = onTwigTemplateLoad;