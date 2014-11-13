function bold() {

    var textarea = document.getElementById('note-txt');
    var len = textarea.value.length;
    var start = textarea.selectionStart;
    var end = textarea.selectionEnd;
    var sel = textarea.value.substring(start, end);

    var replace = '*' + sel + '*';

    textarea.value = textarea.value.substring(0, start) + replace + textarea.value.substring(end, len);

}
