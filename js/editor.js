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

}

function header() {

  var textarea = document.getElementById('note-txt');
  var len = textarea.value.length;
  var start = textarea.selectionStart;
  var end = textarea.selectionEnd;
  var sel = textarea.value.substring(start, end);

  if (sel == '') {
    var replace = '## cím\n';
  } else {
    var replace = '## ' + sel;
  }

  textarea.value = textarea.value.substring(0, start) + replace + textarea.value.substring(end, len);

}

function image() {

  var textarea = document.getElementById('note-txt');
  var len = textarea.value.length;
  var start = textarea.selectionStart;
  var end = textarea.selectionEnd;
  var sel = textarea.value.substring(start, end);

  if (sel == '') {
    var replace = '![kép címe](kép url-je)\n';
  } else {
    var replace = '![kép címe](' + sel + ')\n';
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

  textarea.value = textarea.value.substring(0, start) + replace + textarea.value.substring(end, len);

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
      var replace = '[latex]képlet[/latex]\n';
    } else {
      var replace = '[latex]' + sel + '[/latex]\n';
    }

  }

  textarea.value = textarea.value.substring(0, start) + replace + textarea.value.substring(end, len);

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
