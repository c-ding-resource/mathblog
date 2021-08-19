jQuery(document).ready(function($) {

    $('.comment-menus .must-log-in a,.entry-footer .must-log-in a').click(function () {
        var dialogHTML='You must be logged in to do this.';
       $('<div/>',{html:dialogHTML}) .dialog({
               title: "Please Log In",
               buttons: [
                   {
                   html:'Log in',
                   click: function(){
                       window.location=$('.log-in-link').attr('href');
                       $(this).remove();
                   },
                   },
                   {
                   html:'Cancel',
                   click: function(){$(this).remove()},
               }],
           }
       );
       return false;

    });
});