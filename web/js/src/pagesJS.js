(function($) {
    $('#search-result-content').on("click",".theme-holder > .theme-more > .btn-div > .btn-info",function(){
        $(this).closest('.theme-more').find('.annotation').slideToggle('fast');
    });
    $("#search-result-content").on("click",".theme-holder > .theme-inner", function (event){
        $(this).parent().find(".theme-more").slideToggle("fast");
        $(this).toggleClass("active");
    });
    $("#search-more-btn").click(function(){
        $(this).parent().parent().find(".search-more-holder").slideToggle("fast");
    });
    $(".info-themes-inner").on("click",".teacher-theme-holder > .teacher-theme-theme", function (event){
        $(this).parent().find(".teacher-theme-info").slideToggle("fast");
        $(this).toggleClass("active");
    });
    $('#search-result-content-users').on('click',".user-holder > .name-holder", function (){
        $(this).parent().find(".info-holder").slideToggle("fast");
        $(this).toggleClass("active");
    });
    // $(".theme-inner").click(function(){
    //     $(this).parent().find(".theme-more").slideToggle("fast");
    //     $(this).toggleClass("active");
    // });
    // $("#search-more-btn").click(function(){
    //     $(this).parent().parent().find(".search-more-holder").slideToggle("fast");
    // });
    // $(".teacher-theme-theme").click(function(){
    //     $(this).parent().find(".teacher-theme-info").slideToggle("fast");
    //     $(this).toggleClass("active");
    // });
})(jQuery);