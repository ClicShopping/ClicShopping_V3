/*
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

/*
 * fixed the postion of the footer
 *
*/
$(document).ready(function() {
  var docHeight = $(window).height();
  var footerHeight = $('#footer').height();
  var footerTop = $('#footer').position().top + footerHeight;

  if (footerTop < docHeight) {
  $('#footer').css('margin-top', 0+ (docHeight - footerTop) + 'px');
  }
});

/*
 * Scrolling to top
 *
*/
$(document).ready(function() {
// Show or hide the sticky footer button
  $(window).scroll(function() {
    if ($(this).scrollTop() > 1) {
        $('.go-top').fadeIn(1);
    } else {
        $('.go-top').fadeOut(1);
    }
  });

// Animate the scroll to top
  $('.go-top').click(function(event) {
    event.preventDefault();

    $('html, body').animate({scrollTop: 0}, 300);
  })
});
