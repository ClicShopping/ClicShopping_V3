/*
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */
document.addEventListener("DOMContentLoaded", function () {
  const ajaxform = document.querySelector("#ajaxform");

  ajaxform.addEventListener("submit", function (e) {
    e.preventDefault(); //STOP default action do this first in case any errors thrown in code
    const form = this;

    const postData = new FormData(form);
    const formURL = form.getAttribute("action");

    const xhr = new XMLHttpRequest();
    xhr.open("POST", formURL, true);

    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4) {
        if (xhr.status === 200) {
          const data = xhr.responseText;
          document.querySelector("#simple-msg").innerHTML = `<pre><code class="prettyprint">${data}</code></pre>`;
          // cleanup now
          // form.reset()
        } else {
          document.querySelector("#simple-msg").innerHTML = `<pre><code class="prettyprint">AJAX Request Failed<br/> textStatus=${xhr.statusText}, errorThrown=${xhr.status}</code></pre>`;
        }
      }
    };

    xhr.send(postData);
  });
});
