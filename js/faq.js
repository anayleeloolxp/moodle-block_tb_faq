require(["jquery"],function($) {
    
    $(document).ready(function() {
        $(".faq_title").click(function(){
            $(this).parent().find('.faq_des').slideToggle();
        });
    });    
});