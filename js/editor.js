function emphasis() {

  var textarea = document.getElementById('note-txt');
  var len = textarea.value.length;
  var start = textarea.selectionStart;
  var end = textarea.selectionEnd;
  var sel = textarea.value.substring(start, end);

  if (sel == '') {
    var replace = '*kiemelendő szöveg*';
  } else {
    var replace = '*' + sel + '*';
  }

  textarea.value = textarea.value.substring(0, start) + replace + textarea.value.substring(end, len);
  textarea.setSelectionRange(start + replace.length, start + replace.length);

}

function header() {

  var textarea = document.getElementById('note-txt');
  var len = textarea.value.length;
  var start = textarea.selectionStart;
  var end = textarea.selectionEnd;
  var sel = textarea.value.substring(start, end);

  if (sel == '') {
    var replace = '## cím';
  } else {
    var replace = '## ' + sel;
  }

  textarea.value = textarea.value.substring(0, start) + replace + textarea.value.substring(end, len);
  textarea.setSelectionRange(start + replace.length, start + replace.length);

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
  textarea.setSelectionRange(start + replace.length, start + replace.length);
}

function list(type) {

  var textarea = document.getElementById('note-txt');
  var len = textarea.value.length;
  var start = textarea.selectionStart;
  var end = textarea.selectionEnd;
  var sel = textarea.value.substring(start, end);

  if (type == 'ordered') {

    if (sel == '') {

      var replace = '1. listaelem\n';

    } else {

      var lines = sel.split('\n');
      var replace = '';

      for (var i = 0; i < lines.length; i++) {
        var index = i + 1;
        replace += index + '. ' + lines[i] + '\n';

      }

    }

  } else {

    if (sel == '') {

      var replace = ' - listaelem\n';

    } else {

      var lines = sel.split('\n');
      var replace = '';

      for (var i = 0; i < lines.length; i++) {
        replace += ' - ' + lines[i] + '\n';
      }

    }

  }

  replace = replace.slice(0, -1);

  textarea.value = textarea.value.substring(0, start) + replace + textarea.value.substring(end, len);
  textarea.setSelectionRange(start + replace.length, start + replace.length);

}

function quote(type) {

  var textarea = document.getElementById('note-txt');
  var len = textarea.value.length;
  var start = textarea.selectionStart;
  var end = textarea.selectionEnd;
  var sel = textarea.value.substring(start, end);

  if (sel == '') {

    var replace = '> idézet\n';

  } else {

    var lines = sel.split('\n');
    var replace = '';

    for (var i = 0; i < lines.length; i++) {
      replace += '> ' + lines[i] + '\n';
    }

  }

  replace = replace.slice(0, -1);

  textarea.value = textarea.value.substring(0, start) + replace + textarea.value.substring(end, len);
  textarea.setSelectionRange(start + replace.length, start + replace.length);

}

function latex(type) {

  var textarea = document.getElementById('note-txt');
  var len = textarea.value.length;
  var start = textarea.selectionStart;
  var end = textarea.selectionEnd;
  var sel = textarea.value.substring(start, end);

  if (type == 'inline') {

    if (sel == '') {
      var replace = '[latex inline]képlet[/latex]';
    } else {
      var replace = '[latex inline]' + sel + '[/latex]';
    }

  } else {

    if (sel == '') {
      var replace = '[latex]képlet[/latex]';
    } else {
      var replace = '[latex]' + sel + '[/latex]';
    }

  }

  textarea.value = textarea.value.substring(0, start) + replace + textarea.value.substring(end, len);
  textarea.setSelectionRange(start + replace.length, start + replace.length);

}

function togglehelp() {

  var div = document.getElementById('note-help');

  if (div.style.display == 'none') {
    div.style.display = 'block';
  } else {
    div.style.display = 'none';
  }

}

function hidePreview() {

  document.getElementById('preview-target').innerHTML = '';
  document.getElementById('preview-title').innerHTML = '';
  document.getElementById('hide-preview').style.display = 'none';

}
