<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */


  namespace ClicShopping\Apps\Configuration\ProductsLength\Classes\Shop;

  use ClicShopping\OM\Registry;

  class ProductsLength
  {
    protected $products_length_classes = [];
    protected $precision = 2;

    public function __construct($precision = null)
    {
      if (is_int($precision)) {
        $this->precision = $precision;
      }

      $this->prepareRules();
    }

    /**
     * Numeric decimal separatof
     * @return string
     */
    public static function getNumericDecimalSeparator()
    {
      return '.';
    }

    /**
     * Numeric thousand separatof
     * @return string
     */
    public static function getNumericThousandsSeparator()
    {
      return ' ';
    }


    /**
     * @param $id products length class id
     * @param null $language_id user catalog language
     * @return mixed
     */
    public static function getTitle($id, $language_id = null)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (\is_null($language_id)) {
        $Qproducts_length = $CLICSHOPPING_Db->prepare('select products_length_class_title
                                                        from :table_products_length_classes
                                                        where products_length_class_id = :products_length_class_id
                                                        and language_id = :language_id
                                                       ');
        $Qproducts_length->bindInt(':products_length_class_id', $id);
        $Qproducts_length->bindInt(':language_id', $CLICSHOPPING_Language->getID());
        $Qproducts_length->execute();
      } else {
        $Qproducts_length = $CLICSHOPPING_Db->prepare('select products_length_class_title
                                                      from :table_products_length_classes
                                                      where products_length_class_id = :products_length_class_id
                                                      and language_id = :language_id
                                                     ');
        $Qproducts_length->bindInt(':products_length_class_id', $id);
        $Qproducts_length->bindInt(':language_id', $language_id);
        $Qproducts_length->execute();
      }

      return $Qproducts_length->value('products_length_class_title');
    }

    /**
     * @param $id products length class id
     * @param null $language_id user catalog language
     * @return mixed
     */
    public static function getUnit($id, $language_id = null)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (\is_null($language_id)) {
        $Qproducts_length = $CLICSHOPPING_Db->prepare('select products_length_class_key
                                                        from :table_products_length_classes
                                                        where products_length_class_id = :products_length_class_id
                                                        and language_id = :language_id
                                                       ');
        $Qproducts_length->bindInt(':products_length_class_id', $id);
        $Qproducts_length->bindInt(':language_id', $CLICSHOPPING_Language->getID());
        $Qproducts_length->execute();
      } else {
        $Qproducts_length = $CLICSHOPPING_Db->prepare('select products_length_class_key
                                                      from :table_products_length_classes
                                                      where products_length_class_id = :products_length_class_id
                                                      and language_id = :language_id
                                                     ');
        $Qproducts_length->bindInt(':products_length_class_id', $id);
        $Qproducts_length->bindInt(':language_id', $language_id);
        $Qproducts_length->execute();
      }

      return $Qproducts_length->value('products_length_class_key');
    }

    /**
     *
     */
    public function prepareRules()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      $Qrules = $CLICSHOPPING_Db->prepare('select r.products_length_class_from_id,
                                                  r.products_length_class_to_id,
                                                  r.products_length_class_rule
                                          from :table_products_length_classes_rules r,
                                                :table_products_length_classes c
                                          where c.products_length_class_id = r.products_length_class_from_id
                                          ');
      $Qrules->setCache('products_length-rules');
      $Qrules->execute();

      while ($Qrules->fetch()) {
        $this->products_length_classes[$Qrules->valueInt('products_length_class_from_id')][$Qrules->valueInt('products_length_class_to_id')] = $Qrules->value('products_length_class_rule');
      }

      $Qclasses = $CLICSHOPPING_Db->prepare('select products_length_class_id,
                                                    products_length_class_key,
                                                    products_length_class_title
                                              from :table_products_length_classes
                                              where language_id = :language_id
                                              ');
      $Qclasses->bindInt(':language_id', $CLICSHOPPING_Language->getID());
      $Qclasses->setCache('products_length-classes');
      $Qclasses->execute();

      while ($Qclasses->fetch()) {
        $this->products_length_classes[$Qclasses->valueInt('products_length_class_id')]['key'] = $Qclasses->value('products_length_class_key');
        $this->products_length_classes[$Qclasses->valueInt('products_length_class_id')]['title'] = $Qclasses->value('products_length_class_title');
      }
    }

    /**
     * Convert length
     * @param $value : length value
     * @param $unit_from : length value from
     * @param $unit_to : length value to
     * @return boolean value of convertion
     */
    public function convert($value, $unit_from, $unit_to)
    {
      if ($unit_from == $unit_to) {
        $convert = number_format($value, $this->precision, static::getNumericDecimalSeparator(), static::getNumericThousandsSeparator());
      } else {
        $convert = number_format($value * $this->products_length_classes[(int)$unit_from][(int)$unit_to], $this->precision, static::getNumericDecimalSeparator(), static::getNumericThousandsSeparator());
      }
      return $convert;
    }

    /**
     * @param $value : length value
     * @param $class : products length class id
     * @return string
     */
    public function display($value, $class)
    {
      return number_format($value, $this->precision, static::getNumericDecimalSeparator(), static::getNumericThousandsSeparator()) . $this->products_length_classes[$class]['key'];
    }

    /**
     * get class id and title
     * @return array
     */
    public static function getClasses()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      $products_length_class_array = [];

      $Qclasses = $CLICSHOPPING_Db->prepare('select products_length_class_id,
                                                    products_length_class_title
                                              from :table_products_length_classes
                                              where language_id = :language_id
                                              order by products_length_class_title
                                            ');
      $Qclasses->bindInt(':language_id', $CLICSHOPPING_Language->getID());
      $Qclasses->execute();

      while ($Qclasses->fetch()) {
        $products_length_class_array[] = ['id' => $Qclasses->valueInt('products_length_class_id'),
          'title' => $Qclasses->value('products_length_class_title')
        ];
      }

      return $products_length_class_array;
    }
  }