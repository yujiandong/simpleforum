/**
 * simple markdown - a html markdown editor
 * Copyright (c) 2015-2015, Jiandong Yu. (MIT Licensed)
 * https://github.com/yujiandong/simplemarkdown
 * http://simpleforum.org/n/smd
 */
;(function() {

var toggle = function(reg,start,end,text) {
	if(!reg.test(text)) {
		return start+text+end;
	} else {
		return text.replace(reg, "$1");
	}
};

var toggleBold = function(e, self) {
	var result = $(self).selection('get');
	e.preventDefault();
	result = toggle(/^\*{2}([^\0]*)\*{2}$/m, '**', '**', result);
	$(self).selection('replace',{text: result});
};

var toggleItalic = function(e, self) {
	var result = $(self).selection('get');
	e.preventDefault();
	result = toggle(/^_([^\0]*)_$/m, '_', '_', result);
	$(self).selection('replace',{text: result});
};

var toggleHeader = function(e, self) {
	e.preventDefault();
	//Todo
};

var toggleCodeBlock = function(e, self) {
	var result = $(self).selection('get');
	e.preventDefault();
	result = toggle(/^`{3,}[\n\r]([^\0]*)[\n\r]`{3,}$/m, '```\n', '\n```', result);
	$(self).selection('replace',{text: result});
};

var blockquote = function(e, self) {
	var result = $(self).selection('get');
	e.preventDefault();
	if (result === '') {
		result = "> quote text";
	} else {
		result = '> ' + result;
	}
	$(self).selection('replace',{text: result, caret:'end'});
};

var unOrderedList = function(e, self) {
	var result = $(self).selection('get');
	e.preventDefault();
	if (result === '') {
		result = '* text';
	} else {
		result = '* ' + result.replace(/[\n\r]/g, "\n* ");
	}
	$(self).selection('replace',{text: result, caret:'end'});
};

var orderedList = function(e, self) {
	var result = $(self).selection('get');
	e.preventDefault();
	if (result === '') {
		result = '1. text';
	} else {
		result = '1. ' + result.replace(/[\n\r]/g, "\n1. ");
	}
	$(self).selection('replace',{text: result, caret:'end'});
};

var drawLink = function(e, self) {
	var result = $(self).selection('get');
	var postion = $(self).selection('getPos');
	e.preventDefault();
	if (result === '') {
		result = '[link description here](http://)';
		len = 31;
	} else {
		result = '[' + result + '](http://)';
		len = 10;
	}
	$(self).selection('replace',{text: result, mode:'after'}).selection('setPos', {start: postion.end+len, end: postion.end+len});
};

var drawImage = function(e, self) {
	var result = $(self).selection('get');
	var postion = $(self).selection('getPos');
	e.preventDefault();
	if (result === '') {
		result = '![image description here](http://)';
		len = 33;
	} else {
		result = '![' + result + '](http://)';
		len = 11;
	}
	$(self).selection('replace',{text: result, mode:'after'}).selection('setPos', {start: postion.end+len, end: postion.end+len});
};

var togglePreview = function(e, self) {
	e.preventDefault();
	if ($(self).is(':hidden')) {
		$(self).show().focus();
		$(self).next('.smd-preview').hide();
	} else {
		$(self).hide();
		marked.setOptions({sanitize: true});
		$(self).next('.smd-preview').html(marked($(self).val())).show();
	}
};

var toggleFullScreen = function(e, self) {
	var doc = document;
	var fullscreenEnabled = doc.fullscreenEnabled || doc.webkitFullscreenEnabled || doc.msFullscreenEnabled || doc.mozFullScreenEnabled || false;
	if (fullscreenEnabled == false) {
		alert('Your browser does not support fullscreen mode');
	}
	var fullscreenElement = doc.fullscreenElement || doc.webkitFullscreenElement || doc.msFullscreenElement || doc.mozFullScreenElement;
	e.preventDefault();
	if (!fullscreenElement) {
	   $(self).parent().each(function(){
	       var el = this,
				rfs = el.requestFullscreen || el.webkitRequestFullscreen || el.msRequestFullscreen || el.mozRequestFullScreen;
	       if(!rfs) {
	           alert('Your browser does not support fullscreen mode');
	           return false;
	       }
	       rfs.call(el);
	   });
	} else {
		var rfs = doc.exitFullscreen || doc.webkitExitFullscreen || doc.msExitFullscreen || doc.mozCancelFullScreen;
		if(rfs) {
			rfs.call(doc);
		}
	}
};

var keyTab = function(e, self){
	var result = '    '+$(self).selection('get').replace(/[\n\r]/g, "\n    ");
	e.preventDefault();
	$(self).selection('replace',{text: result, caret:'end'});
};

var SimpleMarkdown = function(options) {
  options = options || {};

  if (options.target) {
    this.target = $(options.target);
  }

  options.toolbar = options.toolbar || this.toolbar;
  options.title = this.languages[options.lan || 'en-US'];

  if (!options.hasOwnProperty('status')) {
    options.status = ['lines', 'words', 'cursor'];
  }

  this.options = options;
  // If user has passed an element, it should auto rendered
  if (this.target) {
    this.render();
  }
};

SimpleMarkdown.prototype = {
	languages : {
		'en-US':{
			bold:'Bold(Ctrl+B)',
			italic:'Italic(Ctrl+I)',
			code:'Code(Ctrl+K)',
			quote:'Quote(Ctrl+Q)',
			'unordered-list':'Unordered List(Ctrl+U)',
			'ordered-list':'Ordered List(Ctrl+O)',
			link:'Link(Ctrl+L)',
			image:'Image(Ctrl+G)',
			info:'About SimpleMarkdown',
			preview:'Preview',
			fullscreen:'Fullscreen'
		},
		'zh-CN':{
			bold:'粗体(Ctrl+B)',
			italic:'斜体(Ctrl+I)',
			code:'代码(Ctrl+K)',
			quote:'引用(Ctrl+Q)',
			'unordered-list':'无序列表(Ctrl+U)',
			'ordered-list':'有序列表(Ctrl+O)',
			link:'链接(Ctrl+I)',
			image:'图片(Ctrl+G)',
			info:'关于SimpleMarkdown',
			preview:'预览',
			fullscreen:'全屏'
		},
		'ja-JP':{
			bold:'太字(Ctrl+B)',
			italic:'斜体(Ctrl+I)',
			code:'コード(Ctrl+K)',
			quote:'引用(Ctrl+Q)',
			'unordered-list':'順序無しリスト(Ctrl+U)',
			'ordered-list':'順序付きリスト(Ctrl+O)',
			link:'リンク(Ctrl+I)',
			image:'イメージ(Ctrl+G)',
			info:'SimpleMarkdown紹介',
			preview:'プレビュー',
			fullscreen:'全画面'
		}
	},

    toolbar: [
	  {name: 'bold', action: toggleBold},
	  {name: 'italic', action: toggleItalic},
	  {name: 'code', action: toggleCodeBlock},
	  '|',

	  {name: 'quote', action: blockquote},
	  {name: 'unordered-list', action: unOrderedList},
	  {name: 'ordered-list', action: orderedList},
	  '|',

	  {name: 'link', action: drawLink},
	  {name: 'image', action: drawImage},
	  '|',

	  {name: 'info', action: 'http://simpleforum.org/n/smd'},
	  {name: 'preview', action: togglePreview},
	  {name: 'fullscreen', action: toggleFullScreen}
	],

	hotkeys: {
		66: toggleBold,			//Ctrl+B
		71: drawImage,			//Ctrl+G
		72: toggleHeader,		//Ctrl+H
		73: toggleItalic,		//Ctrl+I
		75: toggleCodeBlock,	//Ctrl+K
		76: drawLink,			//Ctrl+L
		79: orderedList,		//Ctrl+O
		81: blockquote,			//Ctrl+Q
		85: unOrderedList		//Ctrl+U
	},

	bindHotKeys:function() {
		var self = this;
		$('.smd-input', this.target).on('keydown', function(e){
			switch(e.which){
			case 9:  //Tab
				keyTab(e, this);
				break;
			case 66:  //Ctrl+B
			case 71:  //Ctrl+G
			case 72:  //Ctrl+H
			case 73:  //Ctrl+I
			case 75:  //Ctrl+K
			case 76:  //Ctrl+L
			case 79:  //Ctrl+O
			case 81:  //Ctrl+Q
			case 85:  //Ctrl+U
				if(e.ctrlKey) {
				   (self.hotkeys[e.which])(e, this);
				}
				break;
			default :
				break;
			}

		});
	},
	createIcon: function(item) {
		var element;
		if (item.name) {
			element = document.createElement('a');
			$(element).addClass('icon-'+item.name+' icon-block').attr('title', this.options.title[item.name]);
			if (typeof item.action === 'function') {
				$(element).on('click',function(e) {item.action(e, $(this).parent().next('.smd-input'));})
			} else if (typeof item.action === 'string') {
				$(element).attr('href', item.action).attr('target', '_blank');
			}
		} else if (item === '|') {
			element = '<span class="separator"> | </span>';
		}
		return element;
	},
	createPriewArea: function() {
		var preview = document.createElement('div');
		$(preview).addClass('smd-preview').hide();
		$('.smd-wrapper').append($(preview));
	},
    createToolbar: function() {
		var bar = document.createElement('div');
		var self = this;
		$(bar).addClass('smd-toolbar');
		$.each(this.options.toolbar, function(key, item){
			$(bar).append(self.createIcon(item));
		});
		$('.smd-wrapper').prepend(bar);
    },
	render: function() {
		this.target.parent().html("<div class=\"smd\">\n  <div class=\"smd-wrapper\">\n    </div>\n</div>");
		this.target.addClass('smd-input');
		$('.smd-wrapper').html(this.target);
		this.createToolbar();
		this.createPriewArea();
		this.bindHotKeys();
		//setHeight();
	},
	insertAtCursor: function(text) {
		var postion = this.target.selection('getPos');
		this.target.selection('insert',{text: text, mode: 'after'}).selection('setPos', {start: postion.end+text.length, end: postion.end+text.length});
	},
	version : function() {
		alert('1.0');
	}
};

if (typeof exports === 'object') {
  module.exports = SimpleMarkdown;
} else if (typeof define === 'function' && define.amd) {
  define(function() { return SimpleMarkdown; });
} else {
  this.SimpleMarkdown = SimpleMarkdown;
}

}).call(function() {
  return this || (typeof window !== 'undefined' ? window : global);
}());
