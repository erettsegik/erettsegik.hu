var updatePreviewTarget = function(data, status) {
  $('.spinner').hide();
  $('#preview-target').html(data);
  $('#hide-preview').show();
  $('#preview-target code').each(function(i, block) {
    hljs.highlightBlock(block);
  });
  renderLatexExpressions();
}

var renderLatexExpressions = function() {
  $('.latex-container').each(
    function(index) {
      katex.render(
        this.textContent,
        this,
        { displayMode: $(this).data('displaymode') == 'block' }
      );
    }
  );
}

var main = function() {
  $('div code').each(function(i, block) {
    hljs.highlightBlock(block);
  });

  $('button#preview').click(
    function() {
      $('#preview-target').html('');
      $('.spinner').show();
      $('h1.preview').text($('input[name=title]').val())
      $.post('/note_preview', {text: $('#note-txt').val()}, updatePreviewTarget);
    }
  );

  renderLatexExpressions();

  $('#leftside p:has(img)').addClass('img-wrapper');

  $("textarea[name='footnotes']").focusin(function(){
    $(this).height($(this).height() + 100);
  }).focusout(function(){
    $(this).height($(this).height() - 100);
  });
};

function searchRedirect(event, searchpage) {

  var term = (arguments.length == 2) ? document.getElementById('mainsearch').value : document.getElementById('searchbox').value;

  window.location = '/search/' + term + '/';

  event.preventDefault(event);

}

$('#dropdown-toggle').click(function(){
    $('#main-menu').slideToggle();
});

function preventLeaving() {
  window.onbeforeunload = function (e) {
    return "Nem mentett változtatásaid vannak az oldalon.";
  };
}

function allowLeaving() {
  window.onbeforeunload = null;
}

$(main);
