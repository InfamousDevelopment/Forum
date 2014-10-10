﻿
(function(){CKEDITOR.inline=function(element,instanceConfig){if(!CKEDITOR.env.isCompatible)
return null;element=CKEDITOR.dom.element.get(element);if(element.getEditor())
throw'The editor instance "'+element.getEditor().name+'" is already attached to the provided element.';var editor=new CKEDITOR.editor(instanceConfig,element,CKEDITOR.ELEMENT_MODE_INLINE),textarea=element.is('textarea')?element:null;if(textarea){editor.setData(textarea.getValue(),null,true);element=CKEDITOR.dom.element.createFromHtml('<div contenteditable="'+!!editor.readOnly+'" class="cke_textarea_inline">'+
textarea.getValue()+'</div>',CKEDITOR.document);element.insertAfter(textarea);textarea.hide();if(textarea.$.form)
editor._attachToForm();}else{editor.setData(element.getHtml(),null,true);}
editor.on('loaded',function(){editor.fire('uiReady');editor.editable(element);editor.container=element;editor.setData(editor.getData(1));editor.resetDirty();editor.fire('contentDom');editor.mode='wysiwyg';editor.fire('mode');editor.status='ready';editor.fireOnce('instanceReady');CKEDITOR.fire('instanceReady',null,editor);},null,null,10000);editor.on('destroy',function(){if(textarea){editor.container.clearCustomData();editor.container.remove();textarea.show();}
editor.element.clearCustomData();delete editor.element;});return editor;};CKEDITOR.inlineAll=function(){var el,data;for(var name in CKEDITOR.dtd.$editable){var elements=CKEDITOR.document.getElementsByTag(name);for(var i=0,len=elements.count();i<len;i++){el=elements.getItem(i);if(el.getAttribute('contenteditable')=='true'){data={element:el,config:{}};if(CKEDITOR.fire('inline',data)!==false)
CKEDITOR.inline(el,data.config);}}}};CKEDITOR.domReady(function(){!CKEDITOR.disableAutoInline&&CKEDITOR.inlineAll();});})();