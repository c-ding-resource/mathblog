
jQuery(document).ready(function($){
    $(".entry-footer .download a").click(function() {
        var ID = $(this).getPostID();
        download(ID);
        return false;
    });
    $(".comment-menus .download a").click(function(){
       var ID=$(this).getCommentID();
       var postType='comment';
       download(ID,postType);
       return false;
    });
    function download(ID,postType){
        var downloading=$('<div class="overlay" style="z-index:200"></div><div class="fixed-center" style="z-index:201"><i style="color:white" class="fa fa-spinner fa-spin fa-2x"></i></div>').appendTo('body');
        postType=defaultParameter(postType,'post');
        $.ajax({
            url:ajaxurl,
            data:{id:ID, postType:postType,action:'download_file'},
            type:'POST', // POST
            dataType: "json",
            beforeSend:function(){				//filter.find('button').text('Processing...'); // changing the button label
                //alert(CatID);
            },
            success:function(res){
                var a = document.createElement('a');
                var url = res.url;
                var filename = 'download.tex';
                a.href = url;
                a.download = filename;
                a.click();
                downloading.remove();
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(XMLHttpRequest.status);
                alert(XMLHttpRequest.readyState);
                alert(textStatus);
            },
            complete: function(XMLHttpRequest, textStatus) {
                this;
            }

        });
    }
});
