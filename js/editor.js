function emphasis() {

    var textarea = document.getElementById('note-txt');
    var len = textarea.value.length;
    var start = textarea.selectionStart;
    var end = textarea.selectionEnd;
    var sel = textarea.value.substring(start, end);

    var replace = '*' + sel + '*';

    textarea.value = textarea.value.substring(0, start) + replace + textarea.value.substring(end, len);

}

function header() {

    var textarea = document.getElementById('note-txt');
    var len = textarea.value.length;
    var start = textarea.selectionStart;
    var end = textarea.selectionEnd;
    var sel = textarea.value.substring(start, end);

    var replace = '#' + ' ' + sel;

    textarea.value = textarea.value.substring(0, start) + replace + textarea.value.substring(end, len);

}

function image() {

    var textarea = document.getElementById('note-txt');
    var len = textarea.value.length;
    var start = textarea.selectionStart;
    var end = textarea.selectionEnd;
    var sel = textarea.value.substring(start, end);

    if (sel == '') {
        var replace = '![kép címe](kép url-je)';
    } else {
        var replace = '![kép címe](' + sel + ')';
    }

    textarea.value = textarea.value.substring(0, start) + replace + textarea.value.substring(end, len);

}

function list(type) {

    var textarea = document.getElementById('note-txt');
    var len = textarea.value.length;
    var start = textarea.selectionStart;
    var end = textarea.selectionEnd;
    var sel = textarea.value.substring(start, end);

    if (type == 'ordered') {

        if (sel == '') {
            var replace = '1. listaelem';
        } else {
            var replace = '1. ' + sel + ')';
        }

    } else {

        if (sel == '') {
            var replace = ' - listaelem';
        } else {
            var replace = '![kép címe](' + sel + ')';
        }

    }

    textarea.value = textarea.value.substring(0, start) + replace + textarea.value.substring(end, len);

}
