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

$(document).ready(function(){

  $(window).scroll(function(){
    if ($(this).scrollTop() > 100) {
      $('#go-top-desktop').fadeIn();
    } else {
      $('#go-top-desktop').fadeOut();
    }
  });

  $('#go-top-desktop').click(function(){
    $('html, body').animate({scrollTop : 0}, 800);
    return false;
  });

});

function searchRedirect(searchpage) {

  var term = (arguments.length == 1) ? document.getElementById('mainsearch').value : document.getElementById('searchbox').value;
  var form = (arguments.length == 1) ? document.getElementById('search-main') : document.getElementById('search-form');
  form.action = '/search/' + term + '/';

}

function preventLeaving() {
  window.onbeforeunload = function (e) {
    return "Nem mentett változtatásaid vannak az oldalon.";
  };
}

function allowLeaving() {
  window.onbeforeunload = null;
}

$('#dropdown-toggle').click(function(){
    $('#menu-wrapper').slideToggle();
});

$(main);
