jQuery(document).ready(function($) {
    $('#settings-dialog>div>p').css('font-size','smaller').css('color','grey');
    $('.content-section').css('position','relative');
    var loading=$('<div class="overlay" style="z-index:200"></div><div class="fixed-center" style="z-index:201"><i style="color:white" class="fa fa-spinner fa-spin fa-2x"></i></div>').appendTo('body');
    //loading.progressbar({value:false});

    /*
    * loading post
    */
    var textarea=$('textarea[name="content"]');
    markIt.cite=new Cite($('input[name="citations"]').val());
    textarea.markItUp(mySettings);
    
    var oldContent;

    $.ajax({
        url:ajaxurl,
        data:{id:$('input[name="id"]').val(),action:'ajax_get_post'},
        type:'post',
        dataType: "json",
        beforeSend: function () {
        },
        error: function(xhr, status, error) {
            console.log(xhr.responseText);
        },
        success: function (res) {
            //console.log(res.post_category_ids);
            updateEditForm(res);
            //simplemde.codemirror.refresh();
            loading.hide();
        }
    });

    function updateEditForm(res) {
        console.log(res.post);
        
        if(res.post  && res.post.post_status != 'trash'){
            var post=res.post,
                tags=res.tags,
                categoryIDs=res.categoryIDs,
                citeData= res.citations;
                //console.log(citeData);
            //console.log(citations);
            //if (post.post_status != 'trash') {
            $('input[name="title"]').val(post.post_title);
            //$('textarea[name="content"]').val(post.post_content);//contains special chars, like '  and "
            $('input[name="citations"]').val(citeData);

            markIt.cite=new Cite(citeData);

            $('input[name="tags"]').val(tags.join(', '));
            $('input[name="id"]').val(post.ID);
            $('input[name="category-ids"]').val(categoryIDs.join(', '));
            $('input[name="password"]').val(post.post_password);
            $('button[value="publish"]').html('update');
            $('button[value="trash"]').show();
            $('input[value="publish"]').prop("checked",true);
            if(post.post_status=='private'){
                $('input[value="private"]').prop("checked",true);
            }else{
                if(post.post_password){
                    $('input[name="visibility"][value="password"]').prop("checked",true);
                    showPasswordInput('password');
                }
            }

            //}
        }else{
            $('input[name="title"]').val('');
            $('textarea[name="content"]').val('');
            $('textarea[name="content"]').trigger('focus');
            $('.markItUpPreview>*').html('');
            $('input[value="citations"]').val('');
            $('input[name="tags"]').val('');
            $('input[name="id"]').val(0);
            $('input[name="category-ids"]').val('');
            $('input[name="password"]').val('');
            $('button[value="publish"]').html('publish');
            $('button[value="trash"]').hide();
            $('input[value="publish"]').prop("checked",true);

        }
		
		oldContent=textarea.val();
    }
    /*
    settings dialog
     */
    $(".post-settings").click(function() {
        var settingsDialog=$('#settings-dialog');
        settingsDialog.dialog({
            appendTo:"#edit-form",
            buttons:{
                OK:function(){
                    $(this).dialog("close");
                }
            }
        });
        return false;
    });
    function showPasswordInput(value){
        if(value=='password'){ $('input[name="password"]').show();}else{
            $('input[name="password"]').hide();
        }
    }
    //showPasswordInput(  $('input[name="visibility[]"]:checked').val());
    $('input[name="visibility"]').on("change",function(){
        showPasswordInput($(this).val());
    });

    /*
    actions
     */
    var  form=$('#edit-form');
    $(document).on('click','.actions-section button',function(){
        var  action=$(this).val();

        if(action=='trash'){
            if(!confirm('You are about to delete the post. Are you sure?')){
                return false;
            }else{
                $('input[name="task"]').val('trash');
                form.submit();
            }
        }
        else {
            if($("input[name='title']").val()==''){
                alert("Please enter your title!");
                return false;
            }else{
                $('input[name="task"]').val('publish');
                if (action=='publish' && $("input[name='id']").val()==0){
                    var dialogContent=selectCategoriesForm();
                    var categorizeDialog=$('<div/>',{id:'dialog-select-categories',html:dialogContent,});
                    categorizeDialog.dialog({title:'Select Categories',
                        buttons:{
                            Done:function(){
                                var categorySelector = '#dialog-select-categories li ';
                                var categoryIDs = jQuery(categorySelector + ' input[type="checkbox"]:checked').map(function () {
                                    return jQuery(this).getItemID();
                                });//https://stackoverflow.com/questions/21307137/using-jquery-to-get-data-attribute-values-with-each
                                categoryIDs=categoryIDs.get();
                                //alert(categoryIDs);
                                $('#edit-form input[name="category-ids"]').val(categoryIDs);
                                form.submit();
                                $(this).remove();
                            },
                            Cancel:function(){$(this).remove();},
                        }
                    });
                }
                else{
                    //insert_save_post
                    form.submit();
                }
            }
        }
    });

    form.off("submit").on("submit",function(){
        //$('textarea[name="content"]').val(simplemde.value());
               console.log($('textarea[name="content"]').val());

        $('input[name="citations"]').val(markIt.cite.format('bibtex'));
        //$('<input type="hidden" name="mhtml"/>').appendTo('form').val(markIt.md2html(textarea.val(),{cite:markIt.cite}));

        loading.show();
        $.ajax({
            url:form.attr('action'),
            data:form.serialize(),
            /*data:{
                "content":fixedEncodeURIComponent(textarea.val()),
                "citations":markIt.cite.format('bibtex'),
                //"mhtml":simplemde.getHTML(),
                "task":$('input[name="task"]').val(),
                "id":$('input[name="id"]').val(),
                "category-ids":$('input[name="category-ids"]').val(),
                "action":$('input[name="action"]').val(),
                "title":$('input[name="title"]').val(),
                "tags":$('input[name="tags"]').val(),
                "visibility":$('input[name="visibility"]').val(),
                "password":$('input[name="password"]').val(),
            },*/
            type:form.attr('method'), // POST
            dataType: "json",
            beforeSend:function(){
                //console.log(this.data);
            },
            success:function(res){
                loading.hide();                
                updateEditForm(res);
                //console.log(res.txt);
                if(!res.post){
                    return false;
                }
                var messageBox=$('<div/>',{html:res.message});
                messageBox.dialog(
                    {
                        buttons:[
                            {
                                html: res.view,//link
                                click: function () {
                                    window.location=res.redirect;
                                }
                            },
                            {
                                html: 'Stay',
                                click:function(){
                                    $(this).remove();
                                    if(res.post.post_status!="trash"){
                                        history.pushState(null, null, "?id="+res.post.ID);
                                    }else{
                                        history.pushState(null, null, "?id="+"0");
                                    }
                                    textarea.closest('.markItUpContainer').find('.markItUpHeader .preview-button').trigger('mouseup');
                                }
                            }
                        ],
                    }
                );

            }
        });
        return false;
    });

    $(window).on('beforeunload',function(){
        if(oldContent!=textarea.val()){
            if(!confirm("Your work hasn't been saved yet, are you sure to leave?")){
                return false;
            }
        }
    });
});