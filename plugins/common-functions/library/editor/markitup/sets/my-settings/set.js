// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
mySettings = {
	root:wpData.root,//data from wordpress
	nameSpace:  'mEditor', // Useful to prevent multi-instances CSS conflict
	//previewInWindow:true,
	previewInElement:'.markItUpPreview',
	//previewTemplatePath:wpData.previewTemplatePath,
	//previewTemplatePath:'~/markitup/templates/preview.html',
	previewAutoRefresh:true,
	previewParser:function(content){
		/*function fixedEncodeURIComponent (str) {
			return encodeURIComponent(str).replace(/[!'()*]/g, function(c) {
			  return '%' + c.charCodeAt(0).toString(16);
			});
		}  
		content=fixedEncodeURIComponent(content); //important!	Or, use jquery .serialize() and remove magic quotes on the server side*/
		var textareaName='temporary';
		content=jQuery('<textarea/>',{name:textareaName}).val(content).serialize();
		content=content.substring(textareaName.length+1);
		//console.log(content);
		var data={
			action:'ajax_render', 
			text:content,
			citations:markIt.cite.format('bibtex'),
		};
		//console.log(data.text);
		return data;
		//return markIt.md2html(content);
	},
	/*previewHandler:function(parsedData){
		writeInPreview('s');
	},*/
	previewParserPath: wpData.ajaxURL,
	mathJax:window.MathJax,
	synScroll:true,

	onShiftEnter:       {keepDefault:false, openWith:'\n\n'},
	markupSet: [
		{name:'Bold', key:"B", className:'fas fa-bold bold mobile', openWith:'**', closeWith:'**',placeHolder:'strong text'},
		{name:'Italic', key:"I", openWith:'*', closeWith:'*',placeHolder:'emphasized text',className:'italic fas fa-italic'},
		{name:'Quotes', key:"Q", className:'quote fas fa-quote-left',placeHolder:'Blockquote',
			//replaceWith:function(markItUp){markIt.markdownQuote(markItUp);return false;}
			openWith:'> ',
			openBlockWith:function(markItUp){return markIt.markdownQuote(markItUp).openBlockWith;},
			closeBlockWith:function(markItUp){return markIt.markdownQuote(markItUp).closeBlockWith;},
			multiline:true,
		},
		{separator:'|' },

		{name:'Link', key:"L",className:"fas fa-link necessary",
			replaceWith:function(markItUp){markIt.markdownLink(markItUp);return false;},
		},
		{name:'Image', key:"G",className:"fas fa-image necessary",
			replaceWith:function(markItUp){markIt.markdownImage(markItUp);return false;},
		},
		{name:'Citation', key:"M",className:"fas fa-book necessary",
			replaceWith:function(markItUp){markIt.markdownCitation(markItUp);return false;},
		},
		{name:'TeX', key:"T",className:"fas fa-dollar-sign necessary",
			replaceWith:function(markItUp){markIt.markdownTeX(markItUp);return false;},
		},
		{separator:'|' },

		{
			name: 'Heading', key: "H", className: 'Heading fas fa-heading', placeHolder: 'Your title here...',
			openWith:'## ',closeWith:' ##',multiline:true,
			openBlockWith:function(markItUp){return markIt.markdownHeading(markItUp).openBlockWith;},
			closeBlockWith:function(markItUp){return markIt.markdownHeading(markItUp).closeBlockWith;},
		},
		{name:'Bulleted List', key:'U', openWith:'- ', className:'bulleted-list, fas fa-list-ul',placeHolder:'List item',
			openBlockWith:function(markItUp){return markIt.markdownList(markItUp).openBlockWith;},
			closeBlockWith:function(markItUp){return markIt.markdownList(markItUp).closeBlockWith;},
			multiline:true,
		},
		{name:'Numeric List',key:'O', className:'numeric-list fas fa-list-ol',placeHolder:'List item', openWith:function(markItUp) {
				return markItUp.line+'. ';
			},
			openBlockWith:function(markItUp){return markIt.markdownList(markItUp).openBlockWith;},
			closeBlockWith:function(markItUp){return markIt.markdownList(markItUp).closeBlockWith;},
			multiline:true,
		},
		{separator:'|' },
		{name:'expand',title:'Expand',key:'E', replaceWith:function(markItUp){
			container=jQuery(markItUp.textarea).closest('.markItUpContainer');
			jQuery('html,body').toggleClass('temp');
			container.toggleClass('full-screen');
			return false;
		}, className:"expand fas fa-expand-alt necessary",},
		{name:'preview',call:'preview',title:'Preview',key:'P', className:"preview-button  fas fa-eye necessary",},
		{name:'Help',className:'help fas fa-question necessary', replaceWith:function(markItUp){window.location="//functors.net/edit-help";return false;},},
		//{name:'Code Block / Code', openWith:'(!(\t|!|`)!)', closeWith:'(!(`)!)'},
		//{name:'synScroll',className:'synScroll fas fa-bold',replaceWith:function(markItUp){alert('d');return false;markIt.synScroll(markItUp);return false;},},
        //{name:'preview-side-by-side',title:'Preview side by side',key:'E', replaceWith:function(markItUp){markIt.switchPreview(markItUp);return false;}, className:"sideBySide fas fa-columns necessary",},
		/*{name:'preview-in-window',title:'Preview in window',className:"inWindow fas fa-window-restore necessary",
			//call:'preview',
			replaceWith:function(markItUp){markIt.switchPreview(markItUp);return false;},
		},*/
        //{name:'preview-upon-textarea', title:'Preview upon textarea',key:'P',replaceWith:function(markItUp){markIt.switchPreview(markItUp);return false;}, className:"uponTextarea fas fa-eye necessary",},
	],
};

Cite=require('citation-js');

// markIt nameSpace to avoid conflict.
markIt = {
	/*markdownTitle: function(markItUp, char) {
		heading = '';
		n = jQuery.trim(markItUp.selection||markItUp.placeHolder).length;
		for(i = 0; i < n; i++) {
			heading += char;
		}
		return '\n'+heading+'\n';
	},*/
	markdownQuote: function(markItUp) {
		var textarea=jQuery(markItUp.textarea);
		var lines=markItUp.textarea.value.split(/\n/);
		var caretPosition=markItUp.caretPosition;
		var slectionEnd=caretPosition+markItUp.selection.length;
		var replaceWith,openBlockWith='\n\n',closeBlockWith='\n\n';

		if(caretPosition==0) {
			openBlockWith='';
		}
		var length=0;
		jQuery.each(lines,function(index,line){
				length = length + line.length + 1;

					if (length == caretPosition) {
						if (lines[index].trim()) {
							openBlockWith = '\n';
						} else {
							openBlockWith = '';
						}
					}


				if(length==slectionEnd+1) {
					//alert(lines.length );
					if (lines.length > index + 1) {
						if (lines[index + 1].trim()) {
							closeBlockWith = '\n';
						} else {
							closeBlockWith = '';
						}
					}else{
						closeBlockWith='';
					}
				}
			});
		return {openBlockWith:openBlockWith,closeBlockWith:closeBlockWith};
		//jQuery.markItUp({openBlockWith:openBlock, openWith:open,closeBlockWith:closeBlock});
	},
	markdownList:function(markItUp){return this.markdownQuote(markItUp);},
	markdownHeading:function(markItUp){return this.markdownQuote(markItUp);},

	markdownLink: function(markItUp) {
		var that=this;
		var prompt = jQuery('<div/>', {
			title:'Insert Hyperlink',
			html:
				'<div>Paste the URL of link in the following blank, e.g., https://example.com/link<input name="url"  type="text" value="https://" autofocus onfocus="this.select();"/></div>',
		});
		prompt.dialog({
			buttons: [
				{
					html: "OK",
					click: function () {
						var url = jQuery("input[name='url']").val();
						jQuery(this).remove();
						var data={url:url};
						that.markdownLinkCallback(markItUp,data);
					},
				},
				{
					html: "Cancel",
					click: function () {
						jQuery(this).remove();
					},
				},
			]
		});
	},
	markdownImage: function(markItUp) {
		var that=this;
		var imagePrompt = jQuery('<div/>', {
				title:'Insert Image',
				html:
					'<div><ul><li><a href="#image-url">Paste URL</a></li><li><a href="#image-file">Upload Image</a></li></ul></div>' +
					'<div id="image-url">Paste the URL of your image on the web in the following blank, e.g., http://example.com/image.jpg <input name="image-url"  type="text" value="http://" autofocus onfocus="this.select();"/></div>' +
					'<div id="image-file">' +
					'Upload an your image file from your computer.' +
					'<label class="upload"><span>Browse</span><div></div><input type="file"/></label>' +
					'</div>',
			});
		imagePrompt.dialog({
			open: function () {
					imagePrompt.tabs();
				},
			buttons: [
					{
						html: "OK",
						click: function () {
							var activeTabIdx = imagePrompt.tabs('option', 'active');
							if (activeTabIdx == 0) {
								var imageURL = jQuery("input[name='image-url']").val();
								imagePrompt.remove();
								markIt.markdownImageCallback(markItUp, {url:imageURL});
							}
							if (activeTabIdx == 1) {
								var progressbar=jQuery('<div class="overlay" style="z-index:200"></div><div class="fixed-center" style="z-index:201"><i style="color:white" class="fa fa-spinner fa-spin fa-2x"></i></div>');
								progressbar.appendTo(this);
								// progressbar.appendTo(this).progressbar({value: false});
								var formData = new FormData();
								formData.append('updoc', jQuery('input[type=file]')[0].files[0]);
								formData.append('action', "upload_file");
								jQuery.ajax({
									url: ajaxurl,
									type: "POST",
									data: formData, cache: false,
									processData: false, // Don't process the files
									contentType: false, // Set content type to false as jQuery will tell the server its a query string request
									dataType: "json",
									beforeSend: function () {
									},
									success: function (data) {
										imagePrompt.remove();
										if (!data.message) {
											that.markdownImageCallback(markItUp,{url:data.imageURL});
										} else {
											alert(data.message);
										}

									},

								});
								return false;
							}

						},
					},
					{
						html: "Cancel",
						click: function () {
							jQuery(this).remove();
						},
					},
				]
		});
		},

	markdownCitation: function(markItUp, data) {
		var that=this;
		var citationPrompt = jQuery('<div/>', {
			title:'Insert Citation <span style="font-weight: normal;font-size: small"> beta</span>',
			html:
				'<div>Paste citations in the following textarea.' +
				'<textarea name="cite-data" autofocus onfocus="this.select();" rows="8">' +
				'@article{einstein,\n' +
				'    author =       "Albert Einstein",\n' +
				'    title =        "{Zur Elektrodynamik bewegter K{\\"o}rper}. ({German})\n' +
				'        [{On} the electrodynamics of moving bodies]",\n' +
				'    journal =      "Annalen der Physik",\n' +
				'    volume =       "322",\n' +
				'    number =       "10",\n' +
				'    pages =        "891--921",\n' +
				'    year =         "1905",\n' +
				'    DOI =          "http://dx.doi.org/10.1002/andp.19053221004"\n' +
				'}' +
				'</textarea>' +
				'</div>',
		});
		citationPrompt.dialog({
			buttons: [
				{
					html: "OK",
					click: function () {
						//var progressbar=jQuery('<div class="overlay" style="z-index:200"></div><div class="fixed-center" style="z-index:201"><i style="color:white" class="fa fa-spinner fa-spin fa-2x"></i></div>');
						//progressbar.appendTo(this);
						//progressbar.appendTo(this).progressbar({value: false});
						var data = jQuery('textarea[name="cite-data"]').val();
						that.markdownCitationCallback(markItUp,{citation:data});
						jQuery(this).remove();
					},
				},
				{
					html: "Cancel",
					click: function () {
						jQuery(this).remove();
					},
				},
			]
		});

	},

	markdownTeX: function (markItUp) {
		var that=this;
		var sampleURL='https://functors.net/wp-content/plugins/common-functions/library/editor/sample/sample.zip';
		var texPrompt = jQuery('<div/>', {
			class:'tex',
			title:'Upload $\\LaTeX$ Document <span style="font-size:small;font-weight: normal"> beta</span>',
			html:
				'<div id="tex-file">' +
				'<p>Upload a .zip file that contains all the files of your latex document, we\'ll try to convert it.</p>' +
				'<p>Have a look at <a href="'+ sampleURL +'" download>this sample</a> if your document is not converted correctly.</p>' +
				'<label class="upload"><span>Browse</span><div></div><input type="file"/></label>' +
				'</div>',
		});
		texPrompt.dialog({
			open: function(){
				MathJax.typeset([".ui-dialog"]);
			},
			buttons: [
				{
					html: "OK",
					click: function () {
						var progressbar=jQuery('<div class="overlay" style="z-index:200"></div><div class="fixed-center" style="z-index:201"><i style="color:white" class="fa fa-spinner fa-spin fa-2x"></i></div>');
						progressbar.appendTo(this);
						//progressbar.appendTo(this).progressbar({value: false});
						var formData = new FormData();
						formData.append('updoc', jQuery('.tex input[type=file]')[0].files[0]);
						formData.append('action', "upload_tex");
						jQuery.ajax({
							url: ajaxurl,
							type: "POST",
							data: formData, cache: false,
							processData: false, // Don't process the files
							contentType: false, // Set content type to false as jQuery will tell the server its a query string request
							dataType: "json",
							beforeSend: function () {
							},
							success: function (data) {
								texPrompt.remove();
								if (!data.message) {
									that.markdownTeXCallback(markItUp,{citation:data.citations,text:data.md});
								} else {
									var messageBox=jQuery('<div/>',{html:data.message});
									messageBox.dialog({
										buttons:{
											OK:function(){jQuery(this).remove();}
										},
									});
								}

							},

						});
						return false;
					},
				},
				{
					html: "Cancel",
					click: function () {
						jQuery(this).remove();
					},
				},
			]
		});

	},

	markdownLinkCallback: function (markItUp,data) {
		jQuery.markItUp({openWith:'[', closeWith:']('+data.url+')', placeHolder:'Your text to link here...',});
		return false;
	},

	markdownImageCallback: function (markItUp,data) {
		jQuery.markItUp({openWith:'![', closeWith:']('+data.url+')', placeHolder:'Your text to link here...',});
		return false;
	},

	markdownCitationCallback: function (markItUp,data) {
        if(data.citation) {
            try {
                var oldIDs=this.cite.getIds();
                this.cite.add(data.citation);
                var newIDs=this.cite.getIds();
                var difference = newIDs.slice(oldIDs.length,newIDs.length);

                var text=jQuery.map(difference,function(value,index){return '@'+value;});
                text=text.join('; ');
                jQuery.markItUp({openWith:'[',closeWith:']',replaceWith:text});
            } catch (error) {
                jQuery('<div/>', {html: error.message}).dialog({
                    title:"Error!",
                    buttons: [
                        {
                            html: "OK",
                            click: function () {
                                jQuery(this).remove();
                            },
                        }
                    ]

                });
                return false;
            }
        }
		return false;
	},

	markdownTeXCallback: function(markItUp,data){
		if(data.citation) {
            try {
                this.cite.add(data.citation);
            } catch (error) {

            }
        }

        var text=data.text;
		//text=text.replace(/\$\$([^\$]*?\\label\{.+?\}[^\$]*?)\$\$/g,'\\begin{equation}$1\\end{equation}');
		//text=text.replace(/\\begin\{aligned\}([\s\S]*?\\label\{.+?\}[\s\S]*?)\\end\{aligned\}/g,'\\begin{align}$1\\end{align}');

		//text=text.replace(/\$\$([^\$]*?label[^\$]*?)\$\$/g,'\\begin{equation}$1\\end{equation}');
		text=text.replace(/\[\\\[.*?\\\]\]\(.*?\)\{reference-type="(.*?)"[\s\S]*?reference="(.*?)"\}/g,'\$\\$1\{$2\}\$');
		text=text.replace(/(\[.*?\]\(.*?\))\{\s*?reference-type="(.*?)"[\s\S]*?reference="(.*?)"\s*?\}/g,'$1');
		//text=text.replace(/:::\s*?\{.*?#(\S+).*?\.(\S+).*?\}([\s\S]*?):::/g,':::\{#$1\}$3:::');
		//text=text.replace(/:::\s*?\{.*?\.(\S+).*?\}([\s\S]*?):::/g,'$2');
		//text=text.replace(/:::\s*?\{.*?#(\S+).*?\}([\s\S]*?):::/g,':::\{#$1\}$2:::');
		jQuery.markItUp({replaceWith:text});
		return false;
	},

/*
	getPreviewArea: function(markItUp){
		var previewArea=jQuery(markItUp.textarea).closest('.markItUpContainer').find('.markItUpPreview');
		if(previewArea.length){
			return previewArea;
		}else{
			return false;
		}
	},
 */


	markdownIt:window.markdownit(),
	escapeMath:new MathjaxEditing(),
	cite: new Cite(),
	md2html:function(md,options){
		var defaults={
		};
		if(!options){options={}};
		var settings=jQuery.extend({},defaults,options);

		var citeParser=function(text,cite){
			var cited=text.match(/@[\w_]*/g),
				citedItems=[],citedIDs=[];
			//console.log(cited);
			if(!cited){
				cited=[];
			}

			jQuery.each(cite.data,function(index,item){
				//console.log(item);
				if(cited.includes('@'+item.id)){
					citedItems.push(item);
					citedIDs.push(item.id);
				}
			});
			//console.log(citedIDs);

			var parsed=text.replace(/\[(.+?)\]/g,function(match,ids){
				var ids=ids.split(';');
				ids=jQuery.map(ids,function(id){
					return id.trim().replace(/@/,'');
				});

				var entry=[];
				jQuery.each(ids,function(index,id){
					if(citedIDs.includes(id)){
						entry.push(id);
					}
				});

				//console.log(cite.data);
				if(entry.length==ids.length) {
					return cite.format('citation', {entry: entry});
				}else{
					return match;
				}
			});

			var bibliography=new  Cite(citedItems);
			return parsed+bibliography.format('bibliography', {
				format: 'html',
				template: 'apa',
			});
		};

        md=md.replace(/(\[.*?\]\(.*?\))\{\s*?reference-type="(.*?)"[\s\S]*?reference="(.*?)"\s*?\}/g,'$1');
        md=this.escapeMath.removeMath(md);
        html=this.markdownIt.render(md);
        html=this.escapeMath.replaceMath(html);
        html=html.replace(/:::\s*?\{.*?#(\S+).*?\.(\S+).*?\}([\s\S]*?):::/g,'<div id="$1" class="$2">$3</div>');
        html=html.replace(/:::\s*?\{.*?\.(\S+).*?#(\S+).*?\}([\s\S]*?):::/g,'<div id="$2" class="$1">$3</div>');
        html=html.replace(/:::\s*?\{.*?\.(\S+).*?\}([\s\S]*?):::/g,'<div class="$1">$2</div>');
        html=html.replace(/:::\s*?\{.*?#(\S+).*?\}([\s\S]*?):::/g,'<div id="$1">$2</div>');
        if(settings.cite) {
			html = citeParser(html, settings.cite);
		}
        return html;
    },
    updatePreview: function(textarea,previewArea,options){
		var that=this;
		var defaults={
			mathJax:window.MathJax,
			synScroll:true,
		};
		if(!options){options={}};
		var settings=jQuery.extend({},defaults,options);

		if(previewArea) {
			previewArea.html(this.md2html(textarea.val(), {cite: settings.cite}));

			if (settings.mathJax) {
				//settings.mathJax.typesetClear([previewArea[0]]);
				settings.mathJax.texReset();
				settings.mathJax.typesetPromise([previewArea[0]]).then(() => {
					if (settings.synScroll) {
						//scroll=setTimeout(function() {
							//clearTimeout(scroll);
							if (previewAreaBuffer) {

								var caretPosition = textarea[0].selectionStart;

								textareaBuffer.val(textarea.val().substring(0, caretPosition));
								previewAreaBuffer.html(that.md2html(textareaBuffer.val(), {cite: settings.cite}));
								//textareaBuffer.show();
								//previewAreaBuffer.show();
								settings.mathJax.texReset();
								settings.mathJax.typesetPromise([previewAreaBuffer[0]]).then(() => {

									var percent = (textareaBuffer[0].scrollHeight - textarea.scrollTop()) / textarea.outerHeight();
									var scrollTop = previewAreaBuffer[0].scrollHeight - previewArea.outerHeight() * percent;
									scrollTop =  scrollTop - previewAreaBuffer.find('.csl-bib-body').outerHeight(true)-parseInt(previewAreaBuffer.find('p').css('margin-bottom'));
									//console.log([percent*textarea.outerHeight(),previewAreaBuffer[0].scrollHeight-scrollTop]);
									previewArea.scrollTop(scrollTop);
									//textareaBuffer.hide();
									//previewAreaBuffer.hide();
								});
							}
						//},500);

					}
				});
			}
			//callback(textarea,previewArea);
		}
    },


	synScroll:function(textarea,previewArea,options) {
		var that = this;
		var caretPosition = textarea[0].selectionStart;
		if (!textarea.closest('.markItUpContainer').find('.buffer').length) {
			textareaBuffer = textarea.clone().addClass('buffer').appendTo(textarea.parent());
			previewAreaBuffer = previewArea.clone().addClass('buffer').appendTo(previewArea.parent());
		}
		textareaBuffer.val(textarea.val().substring(0, caretPosition));
		refreshBuffer=setTimeout(function () {
			clearTimeout(refreshBuffer);
			that.updatePreview(textareaBuffer, previewAreaBuffer, options);
			//console.log(textarea.outerHeight()+','+textarea.scrollTop()+','+textareaBuffer[0].scrollHeight + ',' + textareaBuffer[0].scrollHeight + ',' + textarea.scrollTop());
		}, 50);
		scroll=setTimeout(function(){
			clearTimeout(scroll);
			var percent = (textareaBuffer[0].scrollHeight - textarea.scrollTop()) / textarea.outerHeight();
			var scrollTop = previewAreaBuffer[0].scrollHeight - previewArea.outerHeight() * percent;
			scrollTop=scrollTop-previewAreaBuffer.find('.csl-bib-body').outerHeight(true)-parseInt(previewAreaBuffer.find('p').css('margin-bottom'));
			previewArea.scrollTop(scrollTop);
		},100);

	},

	switchPreview:function(markItUp){
		var that=this,
			buttonName=markItUp.name,
			textarea=jQuery(markItUp.textarea),
			container=textarea.closest('.markItUpContainer'),
			//modes={uponTextarea:'uponTextarea',sideBySide:'sideBySide'},
			modes={uponTextarea:'uponTextarea',sideBySide:'sideBySide',inWindow:'inWindow'},
			buttonClass=markItUp.className;

		var mode;
		jQuery.each(modes,function(key,value){
			var button=container.find('.'+value);
			var active=button.attr('class').match(/active/);
			if(active){
				removePreview(key);
				button.removeClass('active');
			}else{
				if(buttonClass.match(value)){
					//addPreview(key);
					mode=key;
					button.addClass('active');
				}
			}
		});

		addPreview(mode);

		function addPreview(mode){
			switch (mode) {
				case modes.uponTextarea:
					textarea.wrap('<div class="markItUpBody" style="position:relative"></div>');
					var previewArea = jQuery('<div class= "markItUpPreview" style="width:100%;height:100%;position:absolute;left:0;top:0;"></div>');
					container.find('.markItUpBody').append(previewArea);
					//previewArea.width(textarea.width());
					//previewArea.css('padding-top',textarea.css('padding-top'));
					//previewArea.css('padding-bottom',textarea.css('padding-bottom'))
					container.find('.markItUpFooter').css('visibility','hidden');
					//textarea.css('visibility','hidden');
					//console.log(textarea.parent());
					if (!(container.find('textarea.buffer').length)) {
						textareaBuffer = textarea.clone().addClass('buffer').appendTo(textarea.parent());
					}
					if (!(previewArea.parent().find('div.buffer').length)) {
						previewAreaBuffer = previewArea.clone().addClass('buffer').appendTo(previewArea.parent());
					}
					//that.updatePreview(textarea, previewArea, {cite: that.cite});
                    container.find('.markItUpButton').on('mouseup.uponTextarea',function(){
						if(typeof refreshUponTextarea !== "undefined"){
							clearTimeout(refreshUponTextarea);
						}

						refreshUponTextarea=setTimeout(function(){

							that.updatePreview(textarea, previewArea, {cite: that.cite});
							//that.synScroll(textarea, previewArea, {cite: that.cite});
						},50);
                    });
					textarea.on('input.uponTextarea focus.uponTextarea', function () {
						if(typeof refreshUponTextarea !== "undefined"){
							clearTimeout(refreshUponTextarea);
						}
						refreshUponTextarea=setTimeout(function(){

                        	that.updatePreview(textarea, previewArea, {cite: that.cite});
							//that.synScroll(textarea, previewArea, {cite: that.cite});
                        	},50);
					});
					break;
				case modes.sideBySide:
					jQuery('html,body').css('overflow','hidden');
					container.addClass('full-screen');
                    //textarea.wrap('<div class="markItUpBody" style="width:100%;"></div>');
					var headerHeight=container.find('.markItUpHeader').outerHeight(true);
					textarea.wrap('<div class="markItUpBody" style="width:100%; position: absolute;bottom: 0;top:'+ headerHeight +'px;"></div>');
					var previewArea = jQuery('<div class= "markItUpPreview"></div>');
					container.find('.markItUpBody').append(previewArea);

					if (!textarea.parent().find('textarea.buffer').length) {
						textareaBuffer = textarea.clone().addClass('buffer').appendTo(textarea.parent());
					}
					if (!previewArea.parent().find('div.buffer').length) {
						previewAreaBuffer = previewArea.clone().addClass('buffer').appendTo(previewArea.parent());
					}

					//that.updatePreview(textarea, previewArea, {cite: that.cite});
					container.find('.markItUpButton').on('mouseup.sideBySide',function(){
						if(typeof refreshSideBySide !== "undefined"){
							clearTimeout(refreshSideBySide);
						}
						refreshSideBySide=setTimeout(function(){

							that.updatePreview(textarea, previewArea, {cite: that.cite});
							//that.synScroll(textarea, previewArea, {cite: that.cite});
							},50);});
					textarea.on('input.sideBySide focus.sideBySide', function () {
						//that.updatePreview(textarea, previewArea, {cite: that.cite});
						if(typeof refreshSideBySide !== "undefined"){
							clearTimeout(refreshSideBySide);
						}
						refreshSideBySide=setTimeout(function(){

							that.updatePreview(textarea, previewArea, {cite: that.cite});
							//that.synScroll(textarea, previewArea, {cite: that.cite});
							},50);//timeout has to be >1 because of markItUp,js

					});

					break;
				case modes.inWindow:
				     previewWindow = window.open('', 'preview',true);
					jQuery(window).unload(function() {
						previewWindow.close();
					});

					var data='<html style="overflow:hidden">'+jQuery(document.documentElement).html()+'</html>';
					data=data.replace(jQuery('article')[0].outerHTML,'<article class="post hentry full-screen"><header class="entry-header"><h2 class="entry-title"></h2></header><div class="entry-content"></div></article>').replace(jQuery('.sidebar').html(),'');
					//data=data.replace(jQuery('.site').html(),'').replace(jQuery('.sidebar')[0].outerHTML,'');
					//console.log(data);
					if (previewWindow && previewWindow.document) {
						try {
							sp = previewWindow.document.documentElement.scrollTop
						} catch (e) {
							sp = 0;
						}
						previewWindow.document.open();
						previewWindow.document.write(data);
						previewWindow.document.close();
						previewWindow.document.documentElement.scrollTop = sp;
						previewWindow.onload=function() {

							//var previewArea = jQuery('<div class="markItUpPreview article wrap"></div>').appendTo(jQuery(previewWindow.document).find('body')).wrap('<div class="wrap"></div>');

							//var previewContainer=jQuery(previewWindow.document).find('body .content-area').addClass('markItUpContainer');
							var previewArea=jQuery(previewWindow.document).find('body article');//.addClass('markItUpPreview full-screen');
							jQuery(previewWindow.document).find('body').css({'overflow':'hidden'});
							if (!textarea.parent().find('textarea.buffer').length) {
								textareaBuffer = textarea.clone().addClass('buffer').appendTo(textarea.parent());
							}
							if (!previewArea.parent().find('div.buffer').length) {
								previewAreaBuffer = previewArea.clone().addClass('buffer').appendTo(previewArea.parent());
							}
							/*container.find('.markItUpButton').on('mouseup.inWindow',function(){
								if(typeof refreshInWindow !== "undefined"){
									clearTimeout(refreshInWindow);
								}
								refreshInWindow=setTimeout(function () {

									that.updatePreview(textarea, previewArea, {mathJax:previewWindow.MathJax,cite:that.cite});
									//that.synScroll(textarea, previewArea, {mathJax:previewWindow.MathJax,cite:that.cite});
								}, 50);
							});*/

							that.updatePreview(textarea, previewArea, {mathJax:previewWindow.MathJax,cite:that.cite});
							//that.synScroll(textarea, previewArea, {mathJax:previewWindow.MathJax,cite:that.cite});

							textarea.on('keypress.inWindow', function (e) {
								if(e.which == 13) {
									if (typeof refreshInWindow !== "undefined") {
										clearTimeout(refreshInWindow);
									}
									refreshInWindow = setTimeout(function () {

										that.updatePreview(textarea, previewArea, {
											mathJax: previewWindow.MathJax,
											cite: that.cite
										});
										//that.synScroll(textarea, previewArea, {mathJax:previewWindow.MathJax,cite:that.cite});
									}, 50);
								}
							});
						}

					}
					break;
				//default:console.log('preview error!');
			}

		}
		function removePreview(mode){
			switch (mode) {
				case modes.uponTextarea:
					container.find('.markItUpButton').off('.uponTextarea');
					textarea.off('.uponTextarea');
					container.find('.markItUpPreview').remove();
					textarea.unwrap();
					//textarea.css('visibility','visible');
					container.find('.markItUpFooter').css('visibility','visible');
					break;
				case modes.sideBySide:
					container.find('.markItUpButton').off('.sideBySide');
					textarea.off('.sideBySide');
					container.find('.markItUpPreview').remove();
					textarea.unwrap();
					container.removeClass('full-screen');
					jQuery('html,body').css('overflow','auto');
					break;
				case modes.inWindow:
					textarea.off('.inWindow');
					previewWindow.close();
					break;
				//default:console.log('preview error!');
			}
			if(typeof textareaBuffer !== "undefined"){
				textareaBuffer.remove();
			}
			if(typeof previewAreaBuffer !== "undefined"){
				previewAreaBuffer.remove();
			}

		}

	},

	init: function(){
		jQuery('.markItUpEditor').on('input.sideBySide focus.sideBySide', function () {
			//console.log(jQuery(this).markItUp.('insert',{}));
			//that.updatePreview(textarea, previewArea, {cite: that.cite});
			//setTimeout(function(){that.updatePreview(textarea, previewArea, {cite: that.cite});},10);//timeout has to be >1 because of markItUp,js
		});
	}

};

jQuery(document).ready(function($){
	$(document).on('change', 'input[type="file"]',function() {
		var fileName = $(this).val();
		$(this).closest('label').find('div').html(fileName);
	});
});