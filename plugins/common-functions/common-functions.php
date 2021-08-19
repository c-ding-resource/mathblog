<?php
defined( 'ABSPATH' ) OR exit;
/**
 * Plugin Name: Common Functions
 * Description: common functions for all themes
 */

/*
 * login
 */
function my_custom_login() {
    echo '<link rel="stylesheet" type="text/css" href="' . plugins_url( '/library/',__FILE__ ) . 'admin/admin.css" />';
}
add_action('login_head', 'my_custom_login');

function social_sign_in($message){
    switch_to_blog(get_main_site_id());
    do_action( 'wordpress_social_login' );
    restore_current_blog();
    return "Or with your Functors.net account";
}
//add_filter('login_message','social_sign_in');

function my_login_logo() { ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(<?php echo plugins_url( '/library/icon/logo.png',__FILE__ ); ?>);
            /*height:65px;
            width:320px;
            background-size: 320px 65px;
            background-repeat: no-repeat;
            padding-bottom: 30px;*/
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );

function my_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {
    return 'Functors.net';
}
add_filter( 'login_headertitle', 'my_login_logo_url_title' );

function add_favicon(){ ?>
    <!-- Custom Favicons -->
    <link rel="shortcut icon" href="<?php  echo plugins_url( '/library/icon/logo.ico',__FILE__ ); ?>"/>
    <link rel="apple-touch-icon" href="<?php  echo plugins_url( '/library/icon/logo.ico',__FILE__ ); ?>">
<?php }
add_action('wp_head','add_favicon');

function hide_admin_bar(){
    if (current_user_can('delete_site')) {
        return true;
    }
}
add_filter( 'show_admin_bar', 'hide_admin_bar' );
function block_admin_page() {
    if (   is_admin() //is_admin() does not verify whether the current user has permission to view the Dashboard or the administration panel // &&! current_user_can( 'administrator' )
        &&  ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) && !current_user_can('delete_site') ) {
        wp_redirect(home_url());
        exit;
    }
}
add_action( 'admin_init', 'block_admin_page' );


//disable buddypress registration
function disable_bp_registration() {
    remove_action( 'bp_init',    'bp_core_wpsignup_redirect' );
    remove_action( 'bp_screens', 'bp_core_screen_signup' );
}
add_action( 'bp_loaded', 'disable_bp_registration' );

add_filter( 'bp_get_signup_page', "firmasite_redirect_bp_signup_page");
function firmasite_redirect_bp_signup_page($page ){
    return bp_get_root_domain() . '/wp-signup.php';
}

remove_filter( 'wpmu_signup_blog_notification', 'bp_core_activation_signup_blog_notification', 1, 7 );
remove_filter( 'wpmu_signup_user_notification', 'bp_core_activation_signup_user_notification', 1, 4 );
//remove_filter( 'wpmu_welcome_user_notification', 'bp_core_disable_welcome_email',1 );
//remove_filter( 'wpmu_welcome_notification', 'bp_core_disable_welcome_email',1 );

add_filter('update_welcome_user_email','fix_welcome_user_filter',11,3);
add_filter('update_welcome_email','fix_welcome_filter',11,4);
function fix_welcome_user_filter($welcome_email,$user_id,$password){
    //$welcome_email = str_replace( "PASSWORD", $password, $welcome_email );
    $welcome_email = str_replace( "[User Set]", $password, $welcome_email );
    return $welcome_email;
}
function fix_welcome_filter($welcome_email,$blog_id,$user_id,$password){
    //$welcome_email = str_replace( "PASSWORD", $password, $welcome_email );
    $welcome_email = str_replace( "[User Set]", $password, $welcome_email );
    return $welcome_email;
}

function my_loginout($echo=true){
    $separator="<span> / </span>";
    $html1='';$html2='';
    if(is_user_logged_in()){
        $current_user_primary_blog_id=current_user_has_blogs();
        if($current_user_primary_blog_id){
            if($current_user_primary_blog_id==get_current_blog_id()) {
                $setting_url = bp_loggedin_user_domain() . 'settings/';
                $html1= "<a href='$setting_url'><i class='fas fa-cog'></i> Settings</a>";
            }else{
                $current_user_primary_blog=get_blog_details($current_user_primary_blog_id);
                $current_user_primary_blog_url=$current_user_primary_blog->siteurl;
                $html1= "<a href='$current_user_primary_blog_url'><i class='fas fa-home'></i> My Blog</a>";
            }
        }else{
            $html1= "<a class='no-blog' href='".wp_registration_url()."'><i class='fas fa-home'></i> My Blog</a>";
            ?>

            <?php
        }
    }else{
        $html1= "<a  href='".wp_registration_url()."'><i class='fas fa-user-plus'></i> Register</a>";
    }
    //echo "$separator";
    $redirect=get_home_url();
    if(is_single()||is_page()){
        $redirect=get_permalink();
    }
    if(!is_user_logged_in()){
        $html2= "<a class='log-in-link' href='".wp_login_url($redirect)."'><i class='fas fa-sign-in-alt'></i> Log In</a>";
    }else{
        $html2= "<a  href='".wp_logout_url($redirect)."'><i class='fas fa-sign-out-alt'></i> Log Out</a>";
    }
    $html= $html1.$separator.$html2;
    if ($echo==true){
        echo $html;
    }else{
        return $html;
    }
}

add_shortcode('loginout','loginout_shortcode');
function loginout_shortcode(){
    return my_loginout(false);
}

function current_user_has_blogs(){
    $has_blogs=false;
    if(is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $current_user_id = $current_user->ID;
        $primary_blog_id = get_active_blog_for_user($current_user_id)->blog_id;

        if($primary_blog_id && $primary_blog_id!=get_main_site_id() ) {
            switch_to_blog($primary_blog_id);

            if (current_user_can("edit_posts")) {
                $has_blogs=$primary_blog_id;
            }
            restore_current_blog();
        }
    }
    return $has_blogs;
}

/*
 * scripts
 */
add_action( 'wp_enqueue_scripts', 'common_scripts' );
function common_scripts(){
    $version=wp_get_theme()->get('Version');
    $library_url= plugins_url( '/library/',__FILE__ ) ;
    wp_register_style('site-wide',$library_url.'site-wide/site-wide.css',array(),$version);
    wp_register_script('site-wide',$library_url.'site-wide/site-wide.js',array('jquery'),$version,false);
    //wp_register_style('icon',$library_url.'icon/iconfont.css',array(),$version);
    wp_register_style('fontawesome',$library_url.'fontawesome/css/all.css',array(),$version);
    //wp_register_script('fontawesome','https://kit.fontawesome.com/b78475f7c8.js',array(),$version,false);
    //wp_register_script('icon',$library_url.'icon/iconfont.js',array(),$version,true);
    //wp_register_style('jquery-ui-css',$library_url.'jquery-ui/jquery-ui.css',array(),$version);
    wp_register_style('dialog',$library_url.'dialog/dialog.css',array(),$version);
    //wp_register_style('ui-dialog',$library_url.'dialog/ui-dialog.css',array('dialog'),$version);
    wp_register_script('dialog',$library_url.'dialog/dialog.js',array('jquery','site-wide','jquery-ui-dialog','jquery-ui-progressbar'),$version,true);
    wp_register_script('IESupport','https://polyfill.io/v3/polyfill.min.js?features=es6',$version,true);
    wp_register_script('mathjax-config',$library_url.'mathjax/mathjax-config.js',array(),$version,false);
    wp_register_script('MathJax', 'https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml.js',array('mathjax-config','IESupport'),$version,true);
    wp_register_script('mathjax-typeset',$library_url.'mathjax/mathjax-typeset.js',array('MathJax','jquery'),$version,true);
    //wp_register_style('SimpleMDE',"https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css",array('dialog'),$version);
    //wp_register_script('SimpleMDE',"https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js",array(),$version,true);
    //wp_register_style('SimpleMDE',"https://unpkg.com/easymde/dist/easymde.min.css",array('dialog'),$version);
    //wp_register_script('SimpleMDE',"https://unpkg.com/easymde/dist/easymde.min.js",array(),$version,true);

    //wp_register_script('citation-js',"https://cdn.jsdelivr.net/npm/citation-js@0.4.0-7/build/citation.js",array(),$version,true);
    wp_register_script('citation-js',$library_url."citation/citation-0.5.0.js",array(),$version,true);
    //wp_register_script('citeproc',$library_url.'NewMDE/citeproc.js',array(),$version,false);
    //wp_register_script('citeproc-demo',$library_url.'NewMDE/citeproc-demo.js',array('citeproc'),$version,false);
    wp_register_script('markdown-it',"https://cdnjs.cloudflare.com/ajax/libs/markdown-it/11.0.0/markdown-it.min.js",array(),$version,true);
    wp_register_script('MathjaxEditing',$library_url.'parser/MathjaxEditing.js',array('MathJax'),$version,true);
    //wp_register_script('NewMDE',$library_url.'NewMDE/NewMDE.js',array('jquery','jquery-ui-dialog','jquery-ui-progressbar','dialog','SimpleMDE','citation-js','MathjaxEditing','markdown-it'),$version,true);

    wp_register_style('markitup-skin',$library_url."editor/markitup/skins/my-skin/style.css",array(),$version);
    wp_register_style('markitup-style',$library_url."editor/markitup/sets/my-settings/style.css",array(),$version);
    wp_register_script('markitup-settings',$library_url."editor/markitup/sets/my-settings/set.js",array('jquery','jquery-ui-dialog','jquery-ui-tabs','dialog','citation-js','MathjaxEditing','markdown-it'),$version,true);
    wp_register_script('markitup',$library_url."editor/markitup/jquery.markitup.js",array('markitup-settings'),$version,true);
    wp_register_script('must-log-in',$library_url.'not-logged-in/must-log-in.js',array('jquery','dialog'),$version,true);
    wp_register_script('post-edit',$library_url.'post-edit/post-edit.js',array('markitup'),$version,true);
    wp_register_script('comment',$library_url.'comment/comment.js',array('markitup'),$version,true);
    wp_register_script('no_blog',$library_url.'no-blog/no-blog.js',array('jquery','dialog'),$version,true);
    wp_register_script('download',$library_url.'download/download.js',array('jquery'),$version,true);
    wp_register_script('comment-list',$library_url.'comment-list/comment-list.js',array('jquery','site-wide','dialog'),$version,true);
    wp_register_script('comment-vote',$library_url.'vote/comment-vote.js',array('jquery','site-wide','dialog'),$version,true);

    wp_enqueue_style('site-wide');
    wp_enqueue_script('site-wide');
    wp_enqueue_script('jquery');
    wp_enqueue_script('mathjax-config');
    wp_enqueue_script('MathJax');
    wp_enqueue_script('mathjax-typeset');
    wp_enqueue_style('icon');
    wp_enqueue_style('fontawesome');
    //wp_enqueue_script('fontawesome');
    //wp_enqueue_script('icon');
    wp_enqueue_script('jquery-ui-dialog');
    wp_enqueue_script('jquery-ui-progressbar');
    wp_enqueue_script('jquery-ui-tabs');
    wp_enqueue_script('dialog');
    wp_enqueue_style('dialog');

    //wp_enqueue_style('jquery-ui-css');
    //wp_enqueue_style('ui-dialog');

    //wp_enqueue_script('markdown-it');
    //wp_enqueue_style('markitup-skin');
    //wp_enqueue_script('markitup-settings');
    //wp_enqueue_script('markitup');

    if(is_page(insert_edit_page())){
        wp_enqueue_style('markitup-skin');
        wp_enqueue_script('post-edit');
    }
    if((is_single()||is_page())  && comments_open()){
        wp_enqueue_style('markitup-skin');
        wp_enqueue_script('comment');
    }
    if(is_user_logged_in() ) {
        if (!current_user_has_blogs()){
            wp_enqueue_script('no_blog');
        }
    }else{
        wp_enqueue_script('must-log-in');
    }
    //if(current_user_can('edit_posts')){
        wp_enqueue_script('download');
    //}
    if(is_single()||is_page()) {
        wp_enqueue_script('comment-list');
        wp_enqueue_script('comment-vote');
    }


    wp_localize_script( 'markitup-settings', 'wpData',
        array(
                'root'=>$library_url.'editor/',
            'previewTemplatePath'=>'~/markitup/templates/preview.html',
            'ajaxURL'=>admin_url('admin-ajax.php'),
        )
    );
}

/*
 * load mathjax
 */
/*add_action('wp_head','mathjax');
function mathjax(){
    ?>
    <script>
        MathJax = {
            startup: {
typeset:false,
            },
            tex: {
                tags: 'ams',
                inlineMath: [['$', '$'], ['\\(', '\\)']]
            },
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js" type="text/javascript"></script>
    <!--[if lt IE 9]>
    <script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv-printshiv.min.js"></script>
    <![endif]-->
    <script>
        jQuery(document).ready(function($) {
            $('article').each(function(i,domObject) {
                //MathJax.startup.document.state(0);
                MathJax.texReset();
                MathJax.typesetPromise([domObject]);
            });
            MathJax.texReset();
            MathJax.typesetPromise();
        });
    </script>
    <?php
}
*/

/*
 * conversion
 */
function get_conversion_path(){
    //$blog_id=get_current_blog_id();
    $user_id=get_current_user_id();
    $conversion_path=plugin_dir_path(__FILE__)."data/user-$user_id/convert/";
    //$conversion_path=plugin_dir_url(__FILE__)."data/user-$user_id/convert/";
    wp_mkdir_p($conversion_path);
    return $conversion_path;
}

function tex2md($tex,$convertsion_path=null){
    //$tex=tex2md_filter($tex);
    if(!$conversion_path){$conversion_path=get_conversion_path();}
    $file= fopen($conversion_path."tex.tex","w") or die("Unable to open file!");
    fwrite($file, $tex);
    fclose($file);

    //$EXELocation='%SystemRoot%\system32\WindowsPowerShell\v1.0\powershell.exe ';
    $command="cd $conversion_path && pandoc --citeproc tex.tex -o md.md --mathjax 2>&1";
    exec($command,$output);
    //var_dump($output);
    $file = fopen($conversion_path."md.md", "r") or die("Unable to open file!");
    $md=fread($file,filesize($conversion_path.'md.md'));
    fclose($file);
    if($md) {
        return $md;
    }else{
        return $output;
    }
}

function md2html_filter($md){    
    $md=preg_replace('/([^\$\s]{1}\s*)(\\\\eqref\{.*?\})/','$1\$$2\$',$md);
    $md=preg_replace('/(\\\\eqref\{.*?\})(\s*[^\$\s]{1})/','\$$1\$$2',$md);
    $md=preg_replace('/([^\$\s]{1}\s*)(\\\\ref\{.*?\})/','$1\$$2\$',$md);
    $md=preg_replace('/(\\\\ref\{.*?\})(\s*[^\$\s]{1})/','\$$1\$$2',$md);
    return $md;
}

$escape=array();
$temporary_string="ImATemporaryStringToBeReplaced";
$index=-1;
function md2html($md,$bib=''){
    $pattern='/\\\\(eq)?ref\{.+?\}/';
    $md=preg_replace_callback($pattern,function($matches){
        global  $escape,$temporary_string;
        $escape[]=$matches[0];
        return $temporary_string;
    },$md);

    $conversion_path=get_conversion_path();

    $file = fopen($conversion_path."bib.bib", "w") or die("Unable to open file!");
    fwrite($file, $bib);
    fclose($file);
    $file = fopen($conversion_path."md.md", "w") or die("Unable to open file!");
    $add_bib=<<<fix
---
bibliography:
- 'bib.bib'
---

fix;

    fwrite($file, $add_bib.$md);
    fclose($file);
    //$EXELocation='%SystemRoot%\system32\WindowsPowerShell\v1.0\powershell.exe ';
    $command="cd $conversion_path && pandoc --citeproc  md.md -o html.html --mathjax 2>&1";
    //$command="cd $conversion_path && pandoc --filter pandoc-citeproc --toc --mathjax md.md -o html.html 2>&1";
    exec($command,$output);
    //var_dump($output);
    $file= fopen($conversion_path."html.html","r") or die("Unable to open file!");
    $html=fread($file,filesize($conversion_path.'html.html'));
    fclose($file);
    $html=preg_replace('/<span class="citation".*?>\(<strong>(\S.*?)\?<\/strong>\)<\/span>/','@$1',$html);//for mentions
    /*$html=preg_replace_callback('/(\\\\\[)([\s\S]*?)(\\\\\])/','remove_slashes_in_math',$html);
    $html=preg_replace_callback('/(\\\\\()([\s\S]*?)(\\\\\))/','remove_slashes_in_math',$html);*/
    //$html=md2html_filter($html);*/
    global $index, $temporary_string;
    $index=-1;
    $html=preg_replace_callback("/$temporary_string/",function($matches){
        global $index, $escape;
        $index++;
        return $escape[$index];
    },$html);
    return $html;
}
function remove_slashes_in_math($match){
    return $match[1].stripslashes_deep($match[2]).$match[3];
}

function md2tex($md,$bib=''){
    $md=wp_specialchars_decode($md);

    $conversion_path=get_conversion_path();

    $file = fopen($conversion_path."bib.bib", "w") or die("Unable to open file!");
    fwrite($file, $bib);
    fclose($file);
    $file = fopen($conversion_path."md.md", "w") or die("Unable to open file!");
    $add_bib=<<<fix
---
bibliography:
- 'bib.bib'
---

fix;

    fwrite($file, $add_bib.$md);
    fclose($file);
    //$EXELocation='%SystemRoot%\system32\WindowsPowerShell\v1.0\powershell.exe ';
    $command="cd $conversion_path &&  pandoc --citeproc --toc --standalone  md.md  -o tex.tex 2>&1";
    exec($command,$output);
    //var_dump($output);
    $file= fopen($conversion_path."tex.tex","r") or die("Unable to open file!");
    $tex=fread($file,filesize($conversion_path.'tex.tex'));
    fclose($file);
    $tex=md2tex_filter($tex);
    return $tex;
}

function tex2md_filter($tex){
    $enviroments=array("align","Bmatrix","bmatrix","cases","CD","eqnarray","equation","gather","matrix","multiline","pmatrix","smallmatrix","split","subarray", "Vmatrix","matrix");
    foreach ($enviroments as $enviroment) {
        $tex = preg_replace("/(\\\\begin\{\s*?$enviroment.*?}[\s\S]*?\\\\end\{\s*?$enviroment.*?\})/", '\$\$$1\$\$', $tex);
    }
    //$tex=preg_replace('/(\\\\begin\{equation.*?\}[\s\S]*?\\\\end\{equation.*?\})/','\$\$$1\$\$',$tex);
    return $tex;
}

function md2tex_filter($tex){
    $enviroments=array("align","Bmatrix","bmatrix","cases","CD","eqnarray","equation","gather","matrix","multiline","pmatrix","smallmatrix","split","subarray", "Vmatrix","matrix");
    foreach ($enviroments as $enviroment) {
        $tex = preg_replace("/\\\\\[(\\\\begin\{\s*?$enviroment.*?}[\s\S]*?\\\\end\{\s*?$enviroment.*?\})\\\\\]/", '$1', $tex);
    }
    $tex=preg_replace("/\\\\\((\\\\newcommand.*?)\\\\\)/",'$1',$tex);
    return $tex;
}

/*
 * edit page
 */
//add_action('init','insert_edit_page');
function insert_edit_page(){
    $post_id= 0;
    $edit_page=array('post_name'=>'edit','post_type'=>'page', 'post_content' =>  '','post_status' => 'private','fields'=>'ids');
    $check=get_posts($edit_page);
    if($check){
        $post_id=$check[0];
    }else {
        $edit_page = array(
            'ID' => $post_id,
            'post_title' => 'edit',
            'post_status' => 'private',
            'post_type' => 'page',
            'post_content' => '',
            'post_name' => 'edit'
            //templete is not needed, wordpress would automatically apply page-edit
        );
        $post_id= wp_insert_post($edit_page);
    }
    return $post_id;
}

function get_edit_post_link_filter( $link,$post_id) {
    if(!current_user_can('delete_site')) {
        $link = get_permalink(insert_edit_page()) . '?id=' . $post_id;
    }
    return $link;
}
add_filter( 'get_edit_post_link', 'get_edit_post_link_filter',10,2 );

function get_insert_post_link($post_id=0){
    return get_permalink(insert_edit_page()) . '?id=' . $post_id;
}


/*
 * edit posts
 */
add_action('wp_ajax_ajax_get_post', 'ajax_get_post');
function ajax_get_post(){
    $post=null;
    if(current_user_can("edit_posts")) {
        $post_id = $_POST['id'];
        $post = get_post($post_id);
        $post_category_ids=$post->post_category;
        //var_dump($post_category_ids);
        $post_tags=$post->tags_input;
        $post_bib=get_post_meta($post_id,'bib',true);
        $post_md=get_post_meta($post_id,'md',true);
        /*if($post->post_status=='trash'){
            $post=null;
        }*/

        $return = array(
            //'success'	=> $success,
            'post' => $post,
            'categoryIDs'=>$post_category_ids,
            'tags'=>$post_tags,
            'citations'=>$post_bib,
            'md'=>$post_md,
            // 'message'	=> $message,
        );
    }
    echo json_encode($return);
    die();
}

add_action('wp_ajax_ajax_edit_post', 'ajax_edit_post');
//add_action('wp_ajax_nopriv_ajax_edit_post', 'ajax_edit_post');
function ajax_edit_post(){
    //$new_post=null;
    
    if(current_user_can('edit_posts')) {
        //$_POST=array_map('urldecode',$_POST);
        if(function_exists('wp_magic_quotes'))
            {
                $_POST      = array_map( 'stripslashes_deep', $_POST );
            }
                
        $post_title = $_POST['title'];
        $post_content = $_POST['content'];
        $post_tags_text = $_POST['tags'];
        $post_tags = texts_to_array($post_tags_text);
        $post_visibility = $_POST['visibility'];
        $post_task = $_POST['task'];
        $post_status = 'publish';
        //$post_password=$post_visibility;
        if ($post_visibility == 'password') {
            $post_password = $_POST['password'];
            //$post_password='hhh';
        }
        if ($post_visibility == 'private') {
            $post_status = 'private';
        }
        if ($post_task == 'trash') {
            $post_status = 'trash';
        }
        //$post_status=$_POST['status'];
        $post_id = (int)$_POST['id'];
        $post_category_ids_text = $_POST['category-ids'];
        $post_category_ids = texts_to_array($post_category_ids_text);
        $post_bib=$_POST['citations'];
        $mhtml=md2html($post_content,$post_bib);
        //$post_html=md2html($post_content,$post_bib);
        if($post_id){
            $post_date=get_the_date('Y-m-d H:i:s',$post_id);
        }
        $post = array(
            'ID' => $post_id,
            'post_date'=>$post_date,
            'post_title' => $post_title,
            'post_content' => wp_slash($post_content),
            'tags_input'=>$post_tags,
            'post_status'=>$post_status,
            'post_category'=>$post_category_ids,
            'post_password'=>$post_password,
            'comment_status'=>'open',
            'meta_input'=>array(
              'bib'=>wp_slash($post_bib),
                'md'=>wp_slash($post_content),
                'mhtml'=>wp_slash($mhtml),
                //'html'=>wp_slash($post_html),
            ),
        );

        $new_post_id = wp_insert_post($post);
        if (is_wp_error($new_post_id)) {
            $message = $new_post_id->get_error_message();
        } else {
            $new_post = get_post($new_post_id);
            $new_post_status = $new_post->post_status;
            $new_post_title = $new_post->post_title;
            $new_post_link = get_permalink($new_post_id);
            $new_post_link_and_title = "<a href='" . $new_post_link . "'>" . $new_post_title . "</a>";
            $new_post_category_ids = wp_get_post_categories($new_post_id, array('fields' => 'ids'));
            $new_post_category_ids_text = implode(', ', $new_post_category_ids);
            $new_post_category_list = get_the_category_list(', ', '', $new_post_id);
            $new_post_password = $new_post->post_password;
            $new_post_tags = $new_post->tags_input;

            if ($new_post_status == 'trash') {
                $message = "Your post <em>" . $new_post_title . "</em> has been deleted from " . $new_post_category_list . "!";
                $view = "Home";
                $redirect=get_home_url();
            } else {
                $view="View";
                $redirect=$new_post_link;
                $message = "Your post " . $new_post_link_and_title . " has been published in " . $new_post_category_list;
                if ($new_post_password) {
                    $message = $message . ", protected with a password you choose.";
                } elseif ($new_post_status == 'private') {
                    $message = $message . ", only visible to youself.";
                } else {
                    $message = $message . '.';
                }
            }

        }
        $return = json_encode(array(
                'post' => $new_post,
                'categoryIDs' => $new_post_category_ids,
                'tags' => $new_post_tags,
                //'citations'=>$post_bib,
                //'password'=>$post_password,
                'message' => $message,
                'view' => $view,
                'redirect'=>$redirect,
                //'txt'=>$post_content.FFF.$mhtml,
            )
        );
    }
    echo $return;
    

    wp_die();

}

function fix_special_html_tags($data) {
    $keys=array('post_title','post_content'
        //'tags_input'
    );
    foreach ($keys as $key){
        $data[$key]=wp_specialchars_decode($data[$key]);
    }
    return $data;
}
//add_filter('wp_insert_post_data', 'fix_special_html_tags', 99, 1);

add_action('wp_ajax_nopriv_ajax_render', 'ajax_render');
add_action('wp_ajax_ajax_render', 'ajax_render');
function ajax_render(){
    
    if(function_exists('wp_magic_quotes'))
    {
        $md=urldecode(stripslashes_deep($_POST['text']));
        //$md_buffer=urldecode(stripslashes_deep($_POST['textBuffer']));
    }else{
        $md=urldecode($_POST['text']);
        //$md_buffer=urldecode($_POST['textBuffer']);
    }
    $md=wp_specialchars_decode($md,ENT_QUOTES);
    $bib=$_POST['citations'];
    $caretPosition=$_POST['caretPosition'];
    $preview=md2html($md,$bib);
    $buffer=md2html(substr($md,0,$caretPosition),$bib);//.substr($md,0,$caretPosition);
    //$buffer=md2html($md_buffer,$bib);
    $return=array(
        'buffer'=>$buffer,
        'preview'=>$preview,
        'md'=>$md,
    );

    echo json_encode($return);
    /*//if (get_magic_quotes_gpc())
    if(function_exists('wp_magic_quotes'))
    {
        $_POST      = array_map( 'stripslashes_deep', $_POST );
    }
    //$_POST = array_map( 'stripslashes_deep', $_POST );
    $md=$_POST['md'];
    $bib=$_POST['citations'];
    $html=md2html($md,$bib);
    
    $return=array(
            'md'=>$md,
            'html'=>$html,
    );

    echo json_encode($return);
    */
    die();
}

function save_post_meta_data($post_id){
    $md=$_POST['content'];
    $bib=$_POST['citations'];
    //$mhtml=$_POST['mhtml'];
    $mhtml=md2html($md,$bib);
    update_post_meta($post_id,'md',$md);
    update_post_meta($post_id,'bib',$bib);
    //update_post_html($post_id);
    update_post_meta($post_id,'mhtml',$mhtml);
}
//add_action('wp_insert_post','save_post_meta_data');
function save_comment_meta_data($comment_id){
    if(function_exists('wp_magic_quotes'))
    {
        $_POST      = array_map( 'stripslashes_deep', $_POST );
    }
    $md=$_POST['comment'];
    $md=wp_specialchars_decode($md,ENT_QUOTES);
    $bib=$_POST['citations'];
    //$mhtml=$_POST['mhtml'];
    $mhtml=md2html($md,$bib);
    update_comment_meta($comment_id,'md',wp_slash($md));
    update_comment_meta($comment_id,'bib',wp_slash($bib));
    //update_comment_html($comment_id);
    update_comment_meta($comment_id,'mhtml',wp_slash($mhtml));
    /*wp_update_comment(array(
        'comment_ID'=>$comment_id,
        'comment_text'=>wp_slash($md),
    ));*/
}
add_action('wp_insert_comment','save_comment_meta_data');
/*remove_filter( 'the_content', 'wpautop' );
remove_filter( 'the_excerpt', 'wpautop' );*/
function content_filter($content){
    
    $post_id = get_the_ID();
    if(!post_password_required()){
        //$html = get_post_meta($post_id, 'html', true);
    $html = get_post_meta($post_id, 'mhtml', true);
    if(!$html){
        update_post_html($post_id);
        $html=get_post_meta($post_id,'html',true);
    }
    remove_filter('the_content', 'wpautop');

    return $html;
    } else{
        return $content;
    }
}
add_filter('the_content','content_filter');

function comment_text_filter($text,$comment){
    if(null!==$comment) {
        //$text=html_entity_decode($text);
        //$text=wp_specialchars_decode($text);
        $comment_id = $comment->comment_ID;
        //$html = get_comment_meta($comment_id, 'html', true);
        $html=get_comment_meta($comment_id,'mhtml',true);
        if(!$html){
            update_comment_html($comment_id);
            $html=get_comment_meta($comment_id,'html',true);
        }
        remove_filter( 'comment_text', 'wpautop', 30);//priority must be 30! https://wordpress.stackexchange.com/questions/17248/how-to-disable-empty-p-tags-in-comment-text
        return $html;
    }
}
add_filter('comment_text','comment_text_filter',10,2);


function update_post_html($post_id){
    $content = get_post_field('post_content', $post_id);
    $text=wp_specialchars_decode($content);
    $bib = get_post_meta($post_id, 'bib', true);
    $html = md2html($text, $bib);
    update_post_meta($post_id, 'html', wp_slash($html));
}
function update_comment_html($comment_id){
    $content=wp_specialchars_decode(get_comment_text($comment_id));
    $text=wp_specialchars_decode($content);
    $bib = get_comment_meta($comment_id, 'bib', true);
    $html = md2html($text, $bib);
    update_comment_meta($comment_id, 'html', wp_slash($html));
    //$html=get_comment_meta($comment_id,'html',true);
}


//add_filter( 'preprocess_comment', 'verify_comment_meta_data' );
function verify_comment_meta_data( $commentdata ) {
    //if ( ! isset( $_POST['citations'] ) )
        //wp_die( __( 'Error: please fill the required field (city).' ) );
    return $commentdata;
}



add_action('wp_ajax_ajax_edit_comment', 'ajax_edit_comment');
add_action('wp_ajax_nopriv_ajax_edit_comment', 'ajax_edit_comment');
function ajax_edit_comment(){
    $message="Error!";
    $comment_id=$_POST['ID'];
    $comment=get_comment($comment_id);
    $comment_author_email=$comment->comment_author_email;
    if($comment_author=get_user_by('email',$comment_author_email)){
        $comment_author_id=$comment_author->ID;
        //$comment_aurhor_username=$comment_author->user_login;
    }
    if(current_user_can('edit_posts')||(get_current_user_id()==$comment_author_id) ){
        $message='';
        wp_trash_comment($comment_id);
        //$comment_list=wp_list_comments(array('echo'=>false));
    }
    $return=array(
            'message'=>$message,
        'commentList'=>$comment_list,
    );
    echo json_encode($return);
    wp_die();

}


add_action('wp_ajax_ajax_vote_comment', 'ajax_vote_comment');
function ajax_vote_comment(){
    $comment_id=$_POST['ID'];
    $comment=get_comment($comment_id);

    if(is_user_logged_in()) {
        $current_user_id=get_current_user_id();
        $comment_votes = get_comment_meta($comment_id,'votes',true);
        if (!$comment_votes) {
            $comment_votes=array();
        }
            if(in_array($current_user_id,$comment_votes)) {

                $comment_votes=array_diff($comment_votes,array($current_user_id));
            }else{
                array_push($comment_votes,$current_user_id);
            }
        update_comment_meta($comment_id,'votes',$comment_votes);
    }
    $return=array(
        'votes'=>get_comment_meta($comment_id,'votes',true),
        'menuItem'=> get_menu_item(get_comment_vote_link_text($comment_id),'fas fa-thumbs-up',"vote"),
    );
    echo json_encode($return);
    wp_die();

}
//comment menus
function get_menu_item($text,$icon,$class='',$link='#'){
        //echo "<a class='$class action' href='$link'>".get_icon($icon)." $text</a>";
        return "<span class='$class menu-item'> <a href='$link'>".get_icon($icon)." $text</a></span>";
}
function menu_item($text,$icon,$class='',$link='#'){
    echo get_menu_item($text,$icon,$class,$link);
   // echo "<span class='$class menu-item'> <a href='$link'>".get_icon($icon)." $text</a></span>";
}
function comment_menus(){
    //reply vote download delete
    $comment=get_comment();
    $comment_id=$comment->comment_ID;
    $comment_author_email=$comment->comment_author_email;
    $comment_aurhor_username=$comment->comment_author;
    if($comment_author=get_user_by('email',$comment_author_email)){
        $comment_author_id=$comment_author->ID;
        $comment_aurhor_username=$comment_author->user_login;
    }

    echo "<div class='comment-menus hidden'>";
    menu_item('Reply','fas fa-reply',"respond respond-to-$comment_aurhor_username","#respond");

    if(is_user_logged_in()) {
        menu_item(get_comment_vote_link_text($comment_id),'fas fa-thumbs-up',"vote");
        if (current_user_can('edit_posts')||(get_current_user_id()==$comment_author_id) ) {
            menu_item('Latex', 'fas fa-download', 'download');
            menu_item('Delete', 'fas  fa-trash', 'edit');
        }
    }else{
        menu_item(get_comment_vote_link_text($comment_id),'fas fa-thumbs-up',"must-log-in");
    }
    echo "</div>";

}

function get_comment_vote_link_text($comment_id){
    $votes=get_comment_meta($comment_id,'votes',true);
    if(!$votes){
        $votes=array();
    }
    $vote_html='Vote';
    if(is_user_logged_in()){
        if(in_array(get_current_user_id(),$votes)){
            $vote_html='Voted';
        }
    }
    $votes_number_html='';
    //$votes_detail='';
    if($votes_number=count($votes)) {
        $votes_number_html = "($votes_number)";

        /*foreach ($votes as $user_id) {
            $user_url = bp_core_get_user_domain($user_id);
            $avatar = get_avatar($user_id);
            $votes_detail = $votes_detail . "<a href='$user_url' rel='nofollow'>$avatar</a>";
        }*/
    }
    return "<span class='text'>$vote_html</span><span class='number'>$votes_number_html</span>";
}


function filter_comment_list($parsed_args)
{
    $new_args = array(
        'end-callback' => 'comment_menus',
        'max_depth'=>1,
    );
    //$parsed_args['end-callback']='comment_menus';
    return array_merge($parsed_args,$new_args);
}
add_filter( 'wp_list_comments_args', 'filter_comment_list' ,10,1);


/*
 * for editor
 */
add_action('wp_ajax_nopriv_upload_file', 'upload_file');
add_action( 'wp_ajax_upload_file', 'upload_file' );
function upload_file() {
    $message='';
    if ( $_FILES ) {
        require_once(ABSPATH . "wp-admin" . '/includes/image.php');
        require_once(ABSPATH . "wp-admin" . '/includes/file.php');
        require_once(ABSPATH . "wp-admin" . '/includes/media.php');
        $file_handler = 'updoc';
        $attach_id = media_handle_upload($file_handler,$pid );
        $attach_url = wp_get_attachment_url($attach_id);
        if( is_wp_error( $attach_id  ) ) {
            $MaxFileSize = wp_max_upload_size()/1024;
            $message="Error! Make sure that you are uploading correct file type and the file size is under $MaxFileSize K.";
        }
    }
    $return = json_encode(array(
            'imageURL'	=>	$attach_url,
            'message' => $message,
        )
    );

    echo $return;

    wp_die();
}

add_action('wp_ajax_nopriv_upload_tex', 'convert_tex');
add_action( 'wp_ajax_upload_tex', 'convert_tex' );
function convert_tex() {
    $my_dir = get_conversion_path();
    array_map('unlink',glob($my_dir.'*'));
    if ( $_FILES ) {
        require_once(ABSPATH . "wp-admin" . '/includes/image.php');
        require_once(ABSPATH . "wp-admin" . '/includes/file.php');
        require_once(ABSPATH . "wp-admin" . '/includes/media.php');
        $maxsize = wp_max_upload_size()/1024;
        $sample_url='https://functors.net/wp-content/plugins/common-functions/library/editor/sample/sample.zip';
        $message="Something wrong! <a href='$sample_url' download>Here</a> you can find a sample that can be converted correctly.";
        $file_handler = 'updoc';
/*
        //add_filter( 'wp_handle_upload_prefilter', 'rsg_pre_upload' );
        //function rsg_pre_upload( $file ) {
        add_filter('upload_dir', 'upload_tex');
        //return $file;
        //}
        function upload_tex( $param ){
            $param['path'] = $mydir;
            $param['url'] =  $mydir;
            $message=$param['path'];
            return $param;
        }
*/
        $attach_id = media_handle_upload($file_handler,$pid );

        if( !is_wp_error( $attach_id  ) ) {
            $attach_url = wp_get_attachment_url($attach_id);
            $attach=get_attached_file($attach_id);
            copy($attach,$my_dir."zip.zip");
            $zip = new ZipArchive();
            if ($zip->open($my_dir."zip.zip")) {//中文文件名要使用ANSI编码的文件格式
                $zip->extractTo($my_dir);
                $zip->close();
                //$files=list_files($my_dir);
                $files=glob($my_dir.'*.tex');
                //$root_file='';
                $root_document_content='';
                $doucument_directory='';
                //$a=0;
                foreach ($files as $file) {
                    //$a=$a+1;
                    //$filetype = wp_check_filetype($file);
                    //if(pathinfo($file, PATHINFO_EXTENSION)=='tex') {
                    $file_size=filesize($file);
                    $file_r = fopen($file, "r") or die("Unable to open file!");
                    $content = fread($file_r, $file_size);
                    $content=tex2md_filter($content);
                    fclose($file_r);
                    $file_w = fopen($file, "w") or die("Unable to open file!");
                    fwrite($file_w,$content);
                    fclose($file_w);
                        //$content=file_get_contents($file);
                       if (preg_match('/\\\\begin\{.*?document.*?\}/i', $content)) {
                            $message = '';
                            $doucument_directory=dirname($file);
                            $root_document_content=$content;
                        }

                    //}

                }

                $files = glob($my_dir . '*.bib');
                $bib='';
                foreach ($files as $file) {
                    //$filetype = wp_check_filetype($file);
                    //if(pathinfo($file, PATHINFO_EXTENSION)=='tex') {
                    $file_size = filesize($file);
                    $file = fopen($file, "r") or die("Unable to open file!");
                    $content= fread($file, $file_size);
                    fclose($file);
                    $bib=$bib.$content;
                    //}
                }

                $md = tex2md($root_document_content,$doucument_directory);

                //$zip->extractTo('/my/destination/dir/', array('pear_item.gif', 'testfromfile.php'));
                //$zip->close();

            }

        }
    }
    $bib=preg_replace('/%.*/','',$bib);
    $return = json_encode(array(
            'md'	=>	$md,
            'citations'=>$bib,
            'message' => $message,
        )
    );

    echo $return;
    wp_die();
}

/*
 * entry footer
 */


function download_file(){
    $ID=$_POST['id'];
    $post_type=$_POST['postType'];
    
    $conversion_path=get_conversion_path();
    $user_id=get_current_user_id();
    $download_url= plugin_dir_url(__FILE__)."data/user-$user_id/convert/tex.tex";

    switch($post_type){
        case 'post':
            $md =  get_post($ID)->post_content;
            $bib=get_post_meta($ID,'bib',true);
            break;
        case 'comment':
            $md =  get_comment($ID)->comment_content;
            $bib=get_comment_meta($ID,'bib',true);
            break;
        default:
            $md='error!';
    }

    $tex=md2tex($md,$bib);
    $file = fopen($conversion_path."tex.tex", "w") or die("Unable to open file!");
    fwrite($file, $tex);
    fclose($file);

    $return = json_encode(array(
            'url'	=> $download_url,
        )
    );
    echo $return;
    die();
}
add_action('wp_ajax_download_file', 'download_file');
add_action('wp_ajax_nopriv_download_file', 'download_file');

/*
 * icon
 */
function get_icon($icon){
    //return '<svg class="icon" aria-hidden="true"><use xlink:href="'.$icon.'"></use></svg>';
	return "<i class='$icon'></i>";
}
function icon($icon){
    echo get_icon($icon);
}

/*
 * frequently used
 */
function texts_to_array($texts){
    $text_array=explode(",",$texts);
    $array=array_map('trim',$text_array);//trim spaces at both ends of each entry
    return $array;
}


/*
 * notification
 */

add_action('wp_ajax_pull_notification', 'bp_notification');
add_action('wp_ajax_nopriv_pull_notification', 'bp_notification');
function bp_notification(){
    $return="Error!";
    $bp_notifications=  implode('; ',bp_notifications_get_notifications_for_user(get_current_user_id()));
    if(!empty($bp_notifications)){
        $bp_notifications=$bp_notifications.'.';
    }
    echo json_encode(array('bpNotification'=>$bp_notifications,
    ));
    wp_die();

}

//add email notification for blog posts
//https://codex.buddypress.org/emails/custom-emails/
function custom_bp_email_message() {

    // Do not create if it already exists and is not in the trash
    $eamil_title='[{{{site.name}}}] new comment by{{commenter.name}}';
    $post_exists = post_exists( $email_title );

    if ( $post_exists != 0 && get_post_status( $post_exists ) == 'publish' )
        return;


    // Create post object
    $my_post = array(
        'post_title'    => __( '[{{{site.name}}}] New post comment.', 'buddypress' ),
        'post_content'  => __( '{{commenter.name}} commented on your blog post {{post.name}}: 
        
        <blockquote>{{usermessage}}</blockquote>
        
        <a href="{{{comment.url}}}">Go to the discussion</a> to reply or catch up on the conversation.', 'buddypress' ),  // HTML email content.
        'post_excerpt'  => __( '{{commenter.name}} commented on your blog post.', 'buddypress' ),  // Plain text email content.
        'post_status'   => 'publish',
        'post_type' => bp_get_email_post_type() // this is the post type for emails
    );

    // Insert the email post into the database
    $post_id = wp_insert_post( $my_post );

    if ( $post_id ) {
        // add our email to the taxonomy term 'post_received_comment'
        // Email is a custom post type, therefore use wp_set_object_terms

        $tt_ids = wp_set_object_terms( $post_id, 'post_received_comment', bp_get_email_tax_type() );
        foreach ( $tt_ids as $tt_id ) {
            $term = get_term_by( 'term_taxonomy_id', (int) $tt_id, bp_get_email_tax_type() );
            wp_update_term( (int) $term->term_id, bp_get_email_tax_type(), array(
                'description' => 'A member comments on a blog post',
            ) );
        }
    }

}
//add_action( 'bp_core_install_emails', 'custom_bp_email_message' );//remember to reistall buddypress emails

function notify_post_author_of_comments( $comment_id, $comment_object ) {

    if ( $comment_object ) {
        // get the post data
        $post = get_post( $comment_object->comment_post_ID );
        // add tokens to parse in email

        $args = array(
            'tokens' => array(
                'usermessage' => restore_comment_text($comment_object->comment_content),
                'poster.name' => $comment_object->comment_author,
                'thread.url'=>get_comment_link($comment_id),
            ),
        );
        // send args and user ID to receive email
        if(get_userdata($post->post_author)->user_email!=$comment_object->comment_author_email) {
            if (bp_get_user_meta((int)$post->post_author, 'notification_activity_new_reply', true) != 'no') {
                bp_send_email('activity-comment', (int)$post->post_author, $args);
            }
        }
    }
}
add_action( 'wp_insert_comment','notify_post_author_of_comments', 20, 2 );

function restore_comment_text($text){
    $text=preg_replace('/<a.*?href=.*?>(@.*?)<\/a>/','$1',$text);
    return $text;
}

