<?php  
/* 
 *  Template Name: editinghelp
 */   
get_header(); ?>


<div class="wrap">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php
			while ( have_posts() ) :
				the_post();

				get_template_part( 'template-parts/page/content', 'page' );			
?>

<div id='demo'style="margin-top:2em;">
<!--<h2 ></h2>
    <p><ul id="writingstyles" class="InlineList"><li class="HintTitle"><a href="#demo">Demo</a></li><li id="standard"><a class="demolink" href="#demo" data-content="standard">standard</a></li><li id="diagram"><a class="demolink" href="#demo" data-content="diagram">diagram</a></li><li id="wordpresscom"><a class="demolink" href="#demo" data-content="wordpresscom">wordpress.com</a></li><li id="tohtml"><a class="demolink" href="#demo" data-content="tohtml">html</a></li></ul></p>-->

<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/lib/markdown-mathjax/wmd.css" /><!--Don't minify, cause problems -->
<form action="<?php echo admin_url('admin-ajax.php')?>" method="post" id="MyEditForm">

	<div id="Editor_Content">
	
		<?php $WMDFix=''; echo MyWMDEditor($WMDFix,'');?>
	
	</div>
	
	
	<div id="Actions" class='hide' >
		<button id="publish" name="publish" type="button" value="update_publish" disabled="disabled"></button>
		<button id="private" name="private" type="button" value="update_private" class="noborder" disabled="disabled"></button>
		<button id="trash" name="trash" type="button" value="update_trash" class="noborder" disabled="disabled">Delete</button>
		<span id="post-status" ><span id="STATUS" class="ForReader"> </span><span id="hint" class='normal'>Ready</span><span id="view"><a href="#"></a></span></span>
	</div>
	
</form>	
</div>


<script>
var Fix="<?php echo $WMDFix;?>";
</script>
<script>
jQuery(document).ready(function($){			
MyWMDEditor(Fix);
});
</script>
<script>
jQuery(document).ready(function($){	
$.ChangeActionsButton=function(Task,Status){
	var PublishButton=$("button[name='publish']");
	var DraftButton=$("button[name='private']");
	var TrashButton=$("button[name='trash']");
	if(Task=='AddNew'){
		PublishButton.val('insert_publish');
		PublishButton.text('Publish');
		PublishButton.attr('disabled',false);
		DraftButton.val('insert_private');	
		DraftButton.text('Save As Draft');	
		DraftButton.attr('disabled',false);
			
		TrashButton.attr('disabled',true);
		TrashButton.addClass('DispalyNone');		
	}
	if(Task=='edit'){
		if(Status=='publish'){
		PublishButton.val('update_publish');
		PublishButton.text('Update');
		PublishButton.attr('disabled',false);
		DraftButton.val('update_private');	
		DraftButton.text('Save As Draft');	
		DraftButton.attr('disabled',false);
		
		TrashButton.attr('disabled',false);	
		TrashButton.removeClass('DisplayNone');
		}
		if(Status=='private'){
		PublishButton.val('update_publish');
		PublishButton.text('Publish');
		PublishButton.attr('disabled',false);
		DraftButton.val('update_private');	
		DraftButton.text('Save As Draft');	
		DraftButton.attr('disabled',false);
		
		TrashButton.removeClass('DisplayNone');
		TrashButton.attr('disabled',false);	
		}
		
	}

}
});
</script>

<script>
function ContentForComparing(Fix){
	//return document.getElementById("MyEditArea").value;
	return document.getElementById("post_title").value + document.getElementById("wmd-input"+Fix).value + document.getElementById("post_tags").value; 
}
//var OldContent=ContentForComparing(Fix);

jQuery(document).ready(function( $ ) {

	var OldViewHTML=$('#post-status').html();

	$('#wmd-input'+Fix).keyup(function(){
		//alert("ok");
		if(OldContent!=ContentForComparing(Fix)){
			$('#hint').html('editing...');
			$('#view a').text('');
		}
		else{
			$('#post-status').html(OldViewHTML);
		}
	});
});

</script>

<script>
var DemoStandard = function () {
/*Standard Writing Example
================

Markdown
--------
Markdown is a simple way to format text. The button bar above the editor will help you use it. A 10 minute tutorial can be found [here][1].

Inline Equation
----------------
$a^2+b^2=c^2$ (recommended), or $latex a^2+b^2=c^2$.

Centered  Equation
---------------
$$\sqrt{2}^2=2,$$
or
\begin{equation*}
\sqrt{2}^2=2.\tag{1}
\end{equation*}

Diagram
---------------
This is a simple arrow: $\xymatrix{A\ar[r]^f & B}$.\
This is a commutative diagram:
\begin{align*}
\xymatrix{
A\times B\ar[r]^{\otimes} \ar[rd]_{f} & A\otimes B\ar@{-->}[d]^{\tilde f}\\
& C
}
\end{align*}

  [1]: https://commonmark.org/help/
*/
};		
var DemoDiagram = function () {
/*Diagrams
=====

While drawing commutative diagrams is not supported on Math Overflow, we do support it.

This is a simple arrow $\xymatrix{A\ar[r]^f & B}$.

Here follows two commutative diagrams:
$$
\xymatrix{
A\times B\ar[r]^{\otimes} \ar[rd]_{f} & A\otimes B\ar@{-->}[d]^{\tilde f}\\
& C
}
$$

\begin{align*}
\xymatrix@R=1pc{
\zeta \ar@{|->} [dd] \ar@{.>}_\theta [rd] \ar@/^/^\psi [rrd] \\
 & \xi \ar@{|->} [dd] \ar_\phi [r] & \eta \ar@{|->} [dd] \\
 P_{F}\zeta \ar_t [rd] \ar@/^/ [rrd]|!{[ru];[rd]}\hole \\
 & P_{F}\xi \ar [r] & P_{F}\eta
}
\end{align*}*/
};		
var DemoWordpressCom = function () {
/*WordPress Style
=========

WordPress Bloggers  use `$latex (some latex codes...)$` to display math equations, we also support this style.

$latex a^2+b^2=c^2$,

<p style="text-align:center">$latex \displaystyle   \forall g \in {\cal F}. g^2 = \eta \ \ \ \ \ (1)$</p>
*/};
var DemoHTML = function () {
/*HTML
=========

We also support html language at the same time. This means that if you like  to write your post in latex, you can try some latex to html tool, and then directly paste the html file, as <a href="https://terrytao.wordpress.com/">Tao</a> does for his blog. The following is an example.

<p>
Look at the document source to see how to <s>strike out</s> text, how to <span style="color:#ff0000;">use</span> <span style="color:#00ff00;">different</span> <span style="color:#0000ff;">colors</span>, and how to <a href="http://www.google.com">link to URLs with snapshot preview</a> and how to <a class="snap_noshots" href="http://www.google.com">link to URLs without snapshot preview</a>.

<blockquote><b>Lemma 1 (Main)</b>  <a name="lmmain"></a> Let $latex {\cal F}&fg=000000$ be a total ramification of a compactifier, then <a name="eqlemma"><p align=center>$latex \displaystyle   \forall g \in {\cal F}. g^2 = \eta \ \ \ \ \ (1)$</p>
</a> </blockquote>

<blockquote><b>Theorem 2</b>  <a name="thad"></a> The adèle of a number field is never hyperbolically transfinite. </blockquote>

<p>


<p>
<em>Proof:</em>  Left as an exercise. $latex \Box$


<p>

<blockquote><b>Exercise 1</b>  Find a counterexample to Theorem <a href="#thad">2</a>. </blockquote>

<p>


<p>

<blockquote><b>Exercise 2 (Advanced)</b>  Prove Lemma <a href="#lmmain">1</a>. </blockquote>

<p>

<p>
It is possible to have numbered equations
<p>
<a name="eqtest"><p align=center>$latex \displaystyle   \frac 1 {x^2} \ge 0 \ \ \ \ \ (2)$</p>
</a>

and unnumbered equations
<p>
<p align=center>$latex \displaystyle  t(x) - \frac 12 > x^{\frac 13} &fg=000000$</p>


<p>
It is possible to refer to equations and theorems via the <em>ref</em>, <em>eqref</em> and <em>label</em> LaTeX commands, for example to Equation (<a href="#eqtest">2</a>), to Equation <a href="#eqlemma">(1)</a>, and to Lemma <a href="#lmmain">1</a> above.

<p>
The theorem-like environments <em>theorem</em>, <em>lemma</em>, <em>proposition</em>, <em>remark</em>, <em>corollary</em>, <em>example</em> and <em>exercise</em> are defined, as is the <em>proof</em> environment.
<p>

<p>
It is possible to have numbered and unnumbered sections and subsections. References to <em>label</em> commands which are not in the scope of a numbered equation or a numbered theorem-like environment will refer to the section number, such as a reference to Section <a href="#sec">1</a> below.

<p>

<p>

<p align=center><b> —  1. A section  — </b></p>

 <a name="sec"></a>
<p>

<p align=center><b> —   And Subsections  —</b></p>

<p>

<ul> <li>Case a. Description of case a <li>Case b. Description of case b
</ul>*/
};		
</script>
<script>
function getMultiLine(str) {
	var lines = new String(str);
	lines = lines.substring(lines.indexOf("/*") + 2, lines.lastIndexOf("*/"));
	return lines;
}
</script>
<script>
jQuery(document).ready(function($){
	//$("#wmd-input"+Fix).val(getMultiLine(DemoStandard));
	function WritingExample(option){
		var DemoContent;
		
		var whichtab;
		//alert(option);
		switch(option)
		{
		case 'standard':
		  DemoContent=DemoStandard;
		  whichtab='standard';
		  break;
		case 'diagram':
		  DemoContent=DemoDiagram;
		  whichtab='diagram';
		  break;
		  case 'wordpresscom':
		  DemoContent=DemoWordpressCom;
		  whichtab='wordpresscom';
		  break;
		case 'tohtml':
		  DemoContent=DemoHTML;
		  whichtab='tohtml';
		  break;
		default:
		  DemoContent=DemoStandard;
		  whichtab='standard';
		}
		$('#writingstyles a').removeClass('current');
		$('#'+ whichtab + ' ' + "a").addClass('current');
		$("#wmd-input"+Fix).val(getMultiLine(DemoContent));
		var Done=DoMarkdownWithoutMathjax(getMultiLine(DemoContent));
		//$("#wmd-preview"+Fix).html(Done);
		//MathJax.Hub.Queue(["Typeset",MathJax.Hub,"wmd-preview"+Fix]);
		$('#MathBuffer'+Fix).html(Done);
		MathJax.Hub.Queue(["Typeset",MathJax.Hub,'MathBuffer'+Fix]);
	}
	
	WritingExample('standard');

	$(".demolink").click(function(){
		var option=$(this).data('content');
		WritingExample(option);

	});
	$("#writingstyles a").click(function(){
		var option=$(this).data('content');
		WritingExample(option);
		return false;
	});
	
});
 
</script>
<?php



				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;

			endwhile; // End of the loop.
			?>

		</main><!-- #main -->
	</div><!-- #primary -->
</div><!-- .wrap -->

<?php
get_footer();
