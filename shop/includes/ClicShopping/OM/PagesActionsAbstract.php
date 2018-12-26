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

  namespace ClicShopping\OM;

  abstract class PagesActionsAbstract implements \ClicShopping\OM\PagesActionsInterface {
    protected $page;
    protected $file;
    protected $is_rpc = false;

    public function __construct(\ClicShopping\OM\PagesInterface $page)  {
        $this->page = $page;

        if (isset($this->file)) {
            $this->page->setFile($this->file);
        }
    }

    public function isRPC()  {
        return ($this->is_rpc === true);
    }
  }
