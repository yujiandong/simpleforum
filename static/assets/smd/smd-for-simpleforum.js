$(function(){
    $(document).on('click', '.insert-image', function () {
        insertAtCursor($('#editor'), ' ![]('+$(this).attr('id')+') ');
    }).on('click', '.reply-to', function () {
        var atString = '@' + $(this).attr('params') + "\u00a0", comment = $('#editor'), oldContent, newContent;
        oldContent = comment.val();

        newContent = '';
        scrollToAnchor(comment);
        if(oldContent.length > 0){
            if (oldContent != atString) {
                newContent = oldContent + "\n" + atString;
            }
        } else {
            newContent = atString;
        }
        comment.val(newContent).focusEnd();
    });
});
