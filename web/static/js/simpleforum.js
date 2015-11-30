/* jquery.focus.js start */
(function(root, factory) {

  if (typeof exports !== 'undefined') {
    if (typeof module !== 'undefined' && module.exports)
      module.exports = factory(global.$);
    exports = factory(global.$);
  } else {
    factory(root.$);
  }

}(this, function($) {

  $.fn.focusBegin = function() {
    return this.each(function() {
      var element = $(this)[0];

      if ($(this).is('textarea')) {
        element.focus();
        element.setSelectionRange(0, 0);
      } else if ($(this).is('input')) {
        element.selectionStart = 0;
        element.selectionEnd = 0;
        element.focus()
      } else {
        var range = document.createRange();
        var selection = window.getSelection();
        range.selectNodeContents(element);
        range.collapse(true);
        selection.removeAllRanges();
        selection.addRange(range);
      }
    });
  };

  $.fn.focusEnd = function() {
    return this.each(function() {
      var element = $(this)[0];

      if ($(this).is('textarea')) {
        element.focus();
        element.setSelectionRange($(this).val().length, $(this).val().length);
      } else if ($(this).is('input')) {
        element.selectionStart = element.value.length;
        element.selectionEnd = element.value.length;
        element.focus();
      } else {
        var range = document.createRange();
        var selection = window.getSelection();
        range.selectNodeContents(element);
        range.collapse(false);
        selection.removeAllRanges();
        selection.addRange(range);
      }
    });
  };

}));
/* jquery.focus.js end */

var replyTo = function(username) {
	var atString = '@' + username + ' ', editorName, comment, oldContent, newContent;
	if($('.wysibb-body').length>0) {
		editorName = 'wysibb';
    	comment = $('.wysibb-body');
		oldContent = comment.text();
		if(oldContent.length == 0) {
		    $('#editor').insertAtCursor(atString);
		} else if ( oldContent.length>0 && oldContent != atString ) {
		    $('#editor').insertAtCursor('<br>'+atString);
		}
		scrollToAnchor(comment);
		return;
	} else if($('.simditor-body').length>0) {
		editorName = 'simditor';
    	comment = $('.simditor-body');
		oldContent = comment.html();
		oldText = comment.text();
	} else {
		editorName = 'smd';
    	comment = $('#editor');
		oldContent = comment.val();
	} 

	prefix = '@' + username + ' ';
	newContent = '';
	scrollToAnchor(comment);
	if( editorName == 'smd') {
		if(oldContent.length > 0){
		    if (oldContent != atString) {
		        newContent = oldContent + "\n" + atString;
		    }
		} else {
		    newContent = atString;
		}
		comment.val(newContent);
	} else {
		if(oldText.length>0){
		    if (oldText != atString) {
		        newContent = oldContent + "<br />" + atString;
		    }
		} else {
		    newContent = atString;
		}
		comment.html(newContent);
	}
	comment.focusEnd();
}

var insertAtCursor= function(target, text) {
	var postion = target.selection('getPos');
	target.selection('insert',{text: text, mode: 'after'}).selection('setPos', {start: postion.end+text.length, end: postion.end+text.length});
}

var scrollToAnchor = function(anchor) {
	var fixedBar = $('.navbar-fixed-top');
	var fixedBarHeight = 0;
	if (fixedBar.length > 0) {
		fixedBarHeight = fixedBar.height();
	}
	if(anchor.length > 0){
		var t = anchor.offset().top - fixedBarHeight;
		$("html,body").animate({scrollTop : t}, {queue : false});
	}
}

var chooseNode = function(node) {
	$(".nodes-select2").val(node).trigger("change");
}

$(function(){
	var anchor = $("#"+window.location.hash.substr(1));
	scrollToAnchor(anchor);
});

$(function(){
	$('.link-external a[href^=http]')
		.not('[href*="'+location.hostname+'"]')
		.attr({target:"_blank"})
		.addClass("external");
});

$(function(){
	$('img.lazy').lazyload();
});

$(function(){
	$(document).on('click', '.reply-to', function () {
		var atString = '@' + $(this).attr('username') + ' ', editorName, comment, oldContent, newContent;
		if($('.wysibb-body').length>0) {
			editorName = 'wysibb';
	    	comment = $('.wysibb-body');
			oldContent = comment.text();
			if(oldContent.length == 0) {
			    $('#editor').insertAtCursor(atString);
			} else if ( oldContent.length>0 && oldContent != atString ) {
			    $('#editor').insertAtCursor('<br>'+atString);
			}
		} else if($('.simditor-body').length>0) {
			editorName = 'simditor';
	    	comment = $('.simditor-body');
			oldContent = comment.html();
			oldText = comment.text();
			newContent = '';
			if(oldText.length>0){
			    if (oldText != atString) {
			        newContent = oldContent + "<br />" + atString;
			    }
			} else {
			    newContent = atString;
			}
			comment.html(newContent);
			comment.focusEnd();
		} else {
			editorName = 'smd';
	    	comment = $('#editor');
			oldContent = comment.val();
			if(oldContent.length == 0) {
			    insertAtCursor(comment, atString);
			} else if ( oldContent.length>0 && oldContent != atString ) {
			    insertAtCursor(comment, "\n"+atString);
			}
		} 
		scrollToAnchor(comment);
		return false;
	});
//	$(document).on('selectstart dragstart', '.insert-image', function (e) {e.preventDefault(); return false; });
});

$(function(){
	$(document).on('click', '.insert-image', function () {
		if($('.wysibb-body').length>0) {
			$('#editor').insertImage($(this).attr('id'));
		} else if($('.simditor-body').length>0) {
		} else {
			insertAtCursor($('#editor'), ' ![]('+$(this).attr('id')+') ');
		} 
		return false;
	});
//	$(document).on('selectstart dragstart', '.insert-image', function (e) {e.preventDefault(); return false; });
});
