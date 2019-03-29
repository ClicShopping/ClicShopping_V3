<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  
  class ht_lazyload {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_header_tags_lazyload_title');
      $this->description = CLICSHOPPING::getDef('module_header_tags_lazyload_description');

      if ( defined('MODULES_HEADER_TAGS_LAZYLOAD_STATUS') ) {
        $this->sort_order = MODULES_HEADER_TAGS_LAZYLOAD_SORT_ORDER;
        $this->enabled = (MODULES_HEADER_TAGS_LAZYLOAD_STATUS == 'True');
      }
    }

    public function execute() {
      $CLICSHOPPING_Template = Registry::get('Template');

      $footer_lazyload = '<!--Lazyload Script start-->' . "\n";
      $footer_lazyload .='<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/lozad/dist/lozad.min.js"></script>' . "\n";

      $footer_lazyload .='<script>';
      $footer_lazyload .= '$(document).ready(function(){ ';
      $footer_lazyload .= 'var images = document.querySelectorAll(\'img\') ';
      $footer_lazyload .= 'var imageData = [] ';
      $footer_lazyload .= 'function replaceImage (node) { ';
      $footer_lazyload .= 'if ( ';
      $footer_lazyload .= 'node.getAttribute(\'data-lazyload-replaced\') || ';
      $footer_lazyload .= '(window.scrollY + window.innerHeight) <= node.offsetTop ';
      $footer_lazyload .= ') { ';
      $footer_lazyload .= 'return ';
      $footer_lazyload .= '} ';
      $footer_lazyload .= 'var newImage = new Image() ';
      $footer_lazyload .= 'newImage.src = node.getAttribute(\'data-src\') ';
      $footer_lazyload .= 'newImage.onload = (function (event) { ';
      $footer_lazyload .= 'setTimeout(function () { ';
      $footer_lazyload .= 'node.parentElement.replaceChild(newImage, node) ';
      $footer_lazyload .= '}, Math.random() * 2000 + 500) ';
      $footer_lazyload .= '}).bind(this) ';
      $footer_lazyload .= 'node.setAttribute(\'data-lazyload-replaced\', true) ';
      $footer_lazyload .= '} ';
      $footer_lazyload .= 'document.addEventListener(\'scroll\', function (event) { ';
      $footer_lazyload .= 'for (var i = 0, numImages = images.length; i < numImages; i++) { ';
      $footer_lazyload .= 'replaceImage(images[i]) ';
      $footer_lazyload .= '} ';
      $footer_lazyload .= '}) ';
      $footer_lazyload .= 'for (var i = 0, numImages = images.length; i < numImages; i++) { ';
      $footer_lazyload .= 'images[i].onload = function (event) { ';
      $footer_lazyload .= 'replaceImage(event.target) ';
      $footer_lazyload .= '} ';
      $footer_lazyload .= '} ';
      $footer_lazyload .= '}); ';
      $footer_lazyload .= '</script>' . "\n";
      
      $footer_lazyload .= '<!--End Lazyload Script-->' . "\n";

      $CLICSHOPPING_Template->addBlock($footer_lazyload, 'footer_scripts');
    }

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULES_HEADER_TAGS_LAZYLOAD_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez vous activer ce module ?',
          'configuration_key' => 'MODULES_HEADER_TAGS_LAZYLOAD_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Souhaitez vous activer ce module ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Ordre de tri d\'affichage',
          'configuration_key' => 'MODULES_HEADER_TAGS_LAZYLOAD_SORT_ORDER',
          'configuration_value' => '80',
          'configuration_description' => 'Ordre de tri pour l\'affichage (Le plus petit nombre est montré en premier)',
          'configuration_group_id' => '6',
          'sort_order' => '60',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );
    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return ['MODULES_HEADER_TAGS_LAZYLOAD_STATUS',
              'MODULES_HEADER_TAGS_LAZYLOAD_SORT_ORDER'
             ];
    }
  }
?>