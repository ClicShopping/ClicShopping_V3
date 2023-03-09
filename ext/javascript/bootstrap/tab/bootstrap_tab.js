/*
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

document.addEventListener("DOMContentLoaded", function() {
  if (location.hash !== '') {
    const targetTab = document.querySelector('a[href="' + location.hash + '"]');
    if (targetTab) {
      targetTab.click();
    }
  }

  const tabLinks = document.querySelectorAll("a[data-bs-toggle='tab']");
  tabLinks.forEach(function(link) {
    link.addEventListener("shown.bs.tab", function(e) {
      const hash = e.target.getAttribute("href");
      if (hash && hash.substr(0,1) == "#") {
        const position = window.scrollY;
        history.replaceState(null, null, "#" + hash.substr(1));
        window.scrollTo(0, position);
      }
    });
  });
});

