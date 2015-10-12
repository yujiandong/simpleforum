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
	var editorName, comment, oldContent, prefix, newContent;
	if($('.wysibb-body').length>0) {
		editorName = 'wysibb';
    	comment = $('.wysibb-body');
		oldContent = comment.html();
		oldText = comment.text();
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
		    if (oldContent != prefix) {
		        newContent = oldContent + "\n" + prefix;
		    }
		} else {
		    newContent = prefix;
		}
		comment.val(newContent);
	} else {
		if(oldText.length>0){
		    if (oldContent != prefix) {
		        newContent = oldContent + "<br />" + prefix;
		    }
		} else {
		    newContent = prefix;
		}
		comment.html(newContent);
	}
	comment.focusEnd();
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
