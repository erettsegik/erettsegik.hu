var updatePreviewTarget = function(data, status) {
  $('#preview-target').html(data);
}

var main = function() {
  $('div code').each(function(i, block) {
    hljs.highlightBlock(block);
  });

  $('button#preview').click(
    function() {
      $.post('/note_preview', {text: $('#note-txt').val()}, updatePreviewTarget);
    }
  );

  $('.latex-container').each(
    function(index) {
      katex.render(
        this.innerText,
        this,
        { displayMode: $(this).data('displaymode') == 'block' }
      );
    }
  );
};

function searchRedirect(searchpage) {

  event.preventDefault();

  var term = (arguments.length == 1) ? document.getElementById('mainsearch').value : document.getElementById('searchbox').value;

  window.location = '/search/' + term + '/';
}

$(main);
