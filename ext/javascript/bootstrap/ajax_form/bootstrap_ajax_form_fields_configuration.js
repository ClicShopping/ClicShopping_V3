/*
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

$(function () {

  $("#ajaxform").submit(function (e) {
    e.preventDefault(); //STOP default action do this first in case any errors thrown in code
    var form = this;

    $("#simple-msg").html("<img src='../../../../images/loadingAnimation.gif' />");
    var postData = $(this).serializeArray();
    var formURL = $(this).attr("action");

    $.ajax({
      url: formURL,
      type: "POST",
      data: postData,
      success: function (data, textStatus, jqXHR) {
        $("#simple-msg").html('<pre><code class="prettyprint">' + data + '</code></pre>');
// cleanup now
//        form.reset()
       },
       error: function (jqXHR, textStatus, errorThrown) {
         $("#simple-msg").html('<pre><code class="prettyprint">AJAX Request Failed<br/> textStatus=' + textStatus + ', errorThrown=' + errorThrown + '</code></pre>');
       }
     });
  });
});

