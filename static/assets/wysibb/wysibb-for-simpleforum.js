$(function(){
    $(document).on('click', '.insert-image', function () {
        $('#editor').insertImage($(this).attr('id'));
        return false;
    }).on('click', '.reply-to', function () {
        var atString = '@' + $(this).attr('params') + "\u00a0", comment = $('.wysibb-body'), oldContent, newContent;
        oldContent = comment.text();
        scrollToAnchor(comment);
        if(oldContent.length == 0) {
            $('#editor').insertAtCursor(atString);
        } else if ( oldContent.length>0 && oldContent != atString ) {
            $('#editor').insertAtCursor('<br>'+atString);
        }
        return false;
    });
});
