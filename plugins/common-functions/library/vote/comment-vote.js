jQuery(document).ready(function($) {
    $(".comment-menus .vote a").click(function(){
        var commentID = $(this).getCommentID();
        var voteText=$(this).find('.text').html();
        var votesNumber=$(this).find('.number').html();
        var votesDetail=$(this).find('.detail').html();

        $.ajax({
            url: ajaxurl,
            data: {ID: commentID, action: 'ajax_vote_comment'},
            type: 'post',
            dataType: "json",
            error: function (xhr, status, error) {
                console.log(xhr.responseText);
            },
            success: function (res) {
                $('#comment-' + commentID + ' .comment-menus .vote a').html($(res.menuItem).find('a').html());
                   // $('#comment-' + commentID + ' .comment-menus .vote a').html($('<div/>',{html:res.menuItem}).find('.vote').html());
            }
        });
 /*
        if(voteText.match(/Voted/i)) {
            var dialogHTML = '<p>The following people (including you) think this cmment is useful.</p>'+ votesDetail;
            var dialogTitle = 'Voted Up';
            var dialogButtons = [
                    {
                        html: "OK",
                        click: function () {
                            $(this).remove();
                        },
                    },
                ];
            $("<div/>",{html:dialogHTML}).dialog({
                title: dialogTitle,
                buttons: dialogButtons,
            });
        }else{

            var dialogHTML = 'The following people think this cmment is useful. Click the "Vote" button to show your support!'+votesDetail;
            var dialogTitle = 'Vote Up';
            var dialogButtons = [
                    {
                        html: "Vote",
                        click: function () {

                            $.ajax({
                                url: ajaxurl,
                                data: {ID: commentID, action: 'ajax_vote_comment'},
                                type: 'post',
                                dataType: "json",
                                error: function (xhr, status, error) {
                                    console.log(xhr.responseText);
                                },
                                success: function (res) {
                                    $(this).remove();
                                    $('#comment-' + commentID + ' .vote a').html(res.voteText);
                                }
                            });

                        },
                    },
                    {
                        html: "Cancel",
                        click: function () {
                            $(this).remove();
                        },
                    },
                ];
            $("<div/>",{html:dialogHTML}).dialog({
                title: dialogTitle,
                buttons: dialogButtons,
            });
        }
*/
        return false;
    });
});