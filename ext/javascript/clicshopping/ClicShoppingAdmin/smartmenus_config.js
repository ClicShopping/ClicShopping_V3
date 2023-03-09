/*
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

// SmartMenus desktop menu initialization
var mainMenu = document.getElementById('main-menu');
mainMenu.smartmenus({
  subMenusSubOffsetX: 6,
  subMenusSubOffsetY: 0,
  showOnClick:false,
});

// SmartMenus mobile menu toggle button
var mainMenuState = document.getElementById('main-menu-state');
if (mainMenuState) {
  // animate mobile menu
  mainMenuState.addEventListener('change', function(e) {
    var menu = document.getElementById('main-menu');
    if (this.checked) {
      menu.style.display = 'none';
      menu.style.display = 'block';
      menu.style.height = '0px';
      var menuHeight = menu.scrollHeight;
      menu.style.height = menuHeight + 'px';
    } else {
      menu.style.height = '0px';
    }
  });

  // hide mobile menu beforeunload
  window.addEventListener('beforeunload', function() {
    if (mainMenuState.checked) {
      mainMenuState.click();
    }
  });
  window.addEventListener('unload', function() {
    if (mainMenuState.checked) {
      mainMenuState.click();
    }
  });
}
