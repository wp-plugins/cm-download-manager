(function($){
    $(document).ready(function($){
        var q = window.location.hash.substring(1);

        // initialize scrollable
        $(".scrollable").scrollable({circular: true}).navigator().autoscroll({
            interval: 5000
        });

        $('a[href="#' + q + '"]').trigger('click');

        window.onhashchange = function(){
            var q = window.location.hash.substring(1);
            $('a[href="#' + q + '"]').trigger('click');
        };

        ////////////
        $(".tabNav li a").click(function(){
            var tabIndex = $(this).parent("li").index();
            //console.log(tabIndex);
            $(".tabNav li").removeClass("on");
            $(this).parent("li").addClass("on");
            $(".tabItem").hide();
            $(".tabItem").eq(tabIndex).show();
        });
        $(".tabNav li a:first").trigger('click');
    });
})(jQuery);