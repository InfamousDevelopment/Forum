﻿
(function(){var pluginPath;var previewCmd={modes:{wysiwyg:1,source:1},canUndo:false,readOnly:1,exec:function(editor){var sHTML,config=editor.config,baseTag=config.baseHref?'<base href="'+config.baseHref+'"/>':'',eventData;if(config.fullPage)
sHTML=editor.getData().replace(/<head>/,'$&'+baseTag).replace(/[^>]*(?=<\/title>)/,'$& &mdash; '+editor.lang.preview.preview);else{var bodyHtml='<body ',body=editor.document&&editor.document.getBody();if(body){if(body.getAttribute('id'))
bodyHtml+='id="'+body.getAttribute('id')+'" ';if(body.getAttribute('class'))
bodyHtml+='class="'+body.getAttribute('class')+'" ';}
bodyHtml+='>';sHTML=editor.config.docType+'<html dir="'+editor.config.contentsLangDirection+'">'+'<head>'+
baseTag+'<title>'+editor.lang.preview.preview+'</title>'+
CKEDITOR.tools.buildStyleHtml(editor.config.contentsCss)+'</head>'+bodyHtml+
editor.getData()+'</body></html>';}
var iWidth=640,iHeight=420,iLeft=80;try{var screen=window.screen;iWidth=Math.round(screen.width*0.8);iHeight=Math.round(screen.height*0.7);iLeft=Math.round(screen.width*0.1);}catch(e){}
if(!editor.fire('contentPreview',eventData={dataValue:sHTML}))
return false;var sOpenUrl='',ieLocation;if(CKEDITOR.env.ie){window._cke_htmlToLoad=eventData.dataValue;ieLocation='javascript:void( (function(){'+'document.open();'+
('('+CKEDITOR.tools.fixDomain+')();').replace(/\/\/.*?\n/g,'').replace(/parent\./g,'window.opener.')+'document.write( window.opener._cke_htmlToLoad );'+'document.close();'+'window.opener._cke_htmlToLoad = null;'+'})() )';sOpenUrl='';}
if(CKEDITOR.env.gecko){window._cke_htmlToLoad=eventData.dataValue;sOpenUrl=pluginPath+'preview.html';}
var oWindow=window.open(sOpenUrl,null,'toolbar=yes,location=no,status=yes,menubar=yes,scrollbars=yes,resizable=yes,width='+
iWidth+',height='+iHeight+',left='+iLeft);if(CKEDITOR.env.ie)
oWindow.location=ieLocation;if(!CKEDITOR.env.ie&&!CKEDITOR.env.gecko){var doc=oWindow.document;doc.open();doc.write(eventData.dataValue);doc.close();}
return true;}};var pluginName='preview';CKEDITOR.plugins.add(pluginName,{lang:'af,ar,bg,bn,bs,ca,cs,cy,da,de,el,en,en-au,en-ca,en-gb,eo,es,et,eu,fa,fi,fo,fr,fr-ca,gl,gu,he,hi,hr,hu,id,is,it,ja,ka,km,ko,ku,lt,lv,mk,mn,ms,nb,nl,no,pl,pt,pt-br,ro,ru,si,sk,sl,sq,sr,sr-latn,sv,th,tr,ug,uk,vi,zh,zh-cn',icons:'preview,preview-rtl',hidpi:true,init:function(editor){if(editor.elementMode==CKEDITOR.ELEMENT_MODE_INLINE)
return;pluginPath=this.path;editor.addCommand(pluginName,previewCmd);editor.ui.addButton&&editor.ui.addButton('Preview',{label:editor.lang.preview.preview,command:pluginName,toolbar:'document,40'});}});})();