/*
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

document.addEventListener('DOMContentLoaded', function() {
  var form = document.querySelector('#ajaxform');
  form.addEventListener('submit', function(e) {
    e.preventDefault(); //STOP default action do this first in case any errors thrown in code

    /* ckeditor textarea*/
    for ( instance in CKEDITOR.instances ) {
      CKEDITOR.instances[instance].updateElement();
    }

    var postData = new FormData(form);
    var formURL = form.getAttribute('action');

    var xhr = new XMLHttpRequest();
    xhr.open('POST', formURL, true);
    xhr.onload = function () {
      if (xhr.status === 200) {
        var response = xhr.responseText;
        document.querySelector('#simple-msg').innerHTML = '<pre><code class="prettyprint">' + response + '</code></pre>';

        /* clear ckeditor instance */
        for(k in CKEDITOR.instances){
          var instance = CKEDITOR.instances[k];
          instance.setData( '' );
        }

        // cleanup now
        form.reset();
      }
      else {
        document.querySelector('#simple-msg').innerHTML = '<pre><code class="prettyprint">AJAX Request Failed<br/> textStatus=' + xhr.statusText + '</code></pre>';
      }
    };
    xhr.onerror = function () {
      document.querySelector('#simple-msg').innerHTML = '<pre><code class="prettyprint">AJAX Request Failed<br/> textStatus=' + xhr.statusText + '</code></pre>';
    };
    xhr.send(postData);
  });
});
