function searchRedirect(searchpage) {

  event.preventDefault();

  var term = (arguments.length == 1) ? document.getElementById('mainsearch').value : document.getElementById('searchbox').value;

  window.location = '/search/' + term + '/';

}
