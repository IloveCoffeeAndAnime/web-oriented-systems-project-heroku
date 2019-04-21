module.exports = function(grunt) {
    //Налаштування збірки Grunt
    var config = {
        //Інформацію про проект з файлу package.json
        pkg: grunt.file.readJSON('package.json'),

        //Конфігурація для модуля browserify (перетворює require(..) в код
        browserify:     {
            //Загальні налаштування (grunt-browserify)
            options:      {

                //brfs замість fs.readFileSync вставляє вміст файлу
                transform:  [ require('brfs') ],
                browserifyOptions: {
                    //Папка з корнем джерельних кодів javascript
                    basedir: "web/js/src"
                }
            },

            //Збірка з назвою js
            js: {
                src:        'web/js/src/main.js',
                dest:       'web/js/compiled/main.js'
            },
            admin_js: {
                src:        'web/js/src/admin.js',
                dest:       'web/js/compiled/admin.js'
            },
            pass_change:{
                src:        'web/js/src/changePassword.js',
                dest:       'web/js/compiled/changePassword.js'
            }
        }
    };

    //Налаштування відстежування змін в проекті
    var watchDebug = {
        options: {
            'no-beep': true
        },
        //Назва завдання будь-яка
        scripts: {
            //На зміни в яких файлах реагувати
            files: ['web/js/src/**/*.js', 'web/views/**/*.twig'],
            //Які завдання виконувати під час зміни в файлах
            tasks: ['browserify:js','browserify:admin_js','browserify:pass_change']
        }
    };


    //Ініціалузвати Grunt
    config.watch = watchDebug;
    grunt.initConfig(config);

    //Сказати які модулі необхідно виокристовувати
    grunt.loadNpmTasks('grunt-browserify');
    grunt.loadNpmTasks('grunt-contrib-watch');


    //Список завданнь по замовчування
    grunt.registerTask('default',
        [
            'browserify:js',
            'browserify:admin_js',
            'browserify:pass_change'
            //Інші завдання які необхідно виконати
        ]
    );

};