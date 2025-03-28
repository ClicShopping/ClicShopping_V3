<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM\Module\Hooks\Shop\Footer;

class FooterOutputServiceWorker
{
  /**
   * Generates and returns a JavaScript script block that includes a service worker registration implementation for offline functionality.
   *
   * @return string The generated script block as a string.
   */
  public function display(): string
  {
    $output = '<script defer>
// This is the "Offline page" service worker
// Add this below content to your HTML page, or add the js file to your page at the very top to register service worker

// Check compatibility for the browser we\'re running this in
if ("serviceWorker" in navigator) {
  if (navigator.serviceWorker.controller) {
    console.log("[PWA Builder] active service worker found, no need to register");
  } else {
    // Register the service worker
    navigator.serviceWorker
      .register("pwabuilder-sw.js", {
        scope: "./"
      })
      .then(function (reg) {
        console.log("[PWA Builder] Service worker has been registered for scope: " + reg.scope);
      });
  }
}
      </script>' . "\n";

    return $output;
  }
}