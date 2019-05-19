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


  namespace ClicShopping\Apps\Configuration\Weight\Classes\Shop;

  use ClicShopping\OM\Registry;

  class Weight
  {
    protected $weight_classes = [];
    protected $precision = 2;

    public function __construct($precision = null)
    {
      if (is_int($precision)) {
        $this->precision = $precision;
      }

      $this->prepareRules();
    }

    public static function getNumericDecimalSeparator()
    {
      return '.';
    }

    public static function getNumericThousandsSeparator()
    {
      return ' ';
    }


    public static function getTitle($id, $language_id = null)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (is_null($language_id)) {
        $Qweight = $CLICSHOPPING_Db->prepare('select weight_class_title
                                              from :table_weight_classes
                                              where weight_class_id = :weight_class_id
                                              and language_id = :language_id
                                             ');
        $Qweight->bindInt(':weight_class_id', $id);
        $Qweight->bindInt(':language_id', $CLICSHOPPING_Language->getID());
        $Qweight->execute();
      } else {
        $Qweight = $CLICSHOPPING_Db->prepare('select weight_class_title
                                            from :table_weight_classes
                                            where weight_class_id = :weight_class_id
                                            and language_id = :language_id
                                           ');
        $Qweight->bindInt(':weight_class_id', $id);
        $Qweight->bindInt(':language_id', $language_id);
        $Qweight->execute();
      }

      return $Qweight->value('weight_class_title');
    }

    public function prepareRules()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      $Qrules = $CLICSHOPPING_Db->prepare('select r.weight_class_from_id,
                                                  r.weight_class_to_id,
                                                  r.weight_class_rule
                                          from :table_weight_classes_rules r,
                                                :table_weight_classes c
                                          where c.weight_class_id = r.weight_class_from_id
                                          ');
      $Qrules->setCache('weight-rules');
      $Qrules->execute();

      while ($Qrules->fetch()) {
        $this->weight_classes[$Qrules->valueInt('weight_class_from_id')][$Qrules->valueInt('weight_class_to_id')] = $Qrules->value('weight_class_rule');
      }

      $Qclasses = $CLICSHOPPING_Db->prepare('select weight_class_id,
                                                    weight_class_key,
                                                    weight_class_title
                                              from :table_weight_classes
                                              where language_id = :language_id
                                              ');
      $Qclasses->bindInt(':language_id', $CLICSHOPPING_Language->getID());
      $Qclasses->setCache('weight-classes');
      $Qclasses->execute();

      while ($Qclasses->fetch()) {
        $this->weight_classes[$Qclasses->valueInt('weight_class_id')]['key'] = $Qclasses->value('weight_class_key');
        $this->weight_classes[$Qclasses->valueInt('weight_class_id')]['title'] = $Qclasses->value('weight_class_title');
      }
    }

    public function convert($value, $unit_from, $unit_to)
    {
      if (!is_null($value)) {
        if ($unit_from == $unit_to) {
          $convert = number_format($value, $this->precision, static::getNumericDecimalSeparator(), static::getNumericThousandsSeparator());
        } else {
          $convert = number_format($value * $this->weight_classes[(int)$unit_from][(int)$unit_to], $this->precision, static::getNumericDecimalSeparator(), static::getNumericThousandsSeparator());
        }

        return $convert;
      }
    }

    public function display($value, $class)
    {
      return number_format($value, $this->precision, static::getNumericDecimalSeparator(), static::getNumericThousandsSeparator()) . $this->weight_classes[$class]['key'];
    }

    public static function getClasses()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      $weight_class_array = [];

      $Qclasses = $CLICSHOPPING_Db->prepare('select weight_class_id,
                                                    weight_class_title
                                              from :table_weight_classes
                                              where language_id = :language_id
                                              order by weight_class_title
                                            ');
      $Qclasses->bindInt(':language_id', $CLICSHOPPING_Language->getID());
      $Qclasses->execute();

      while ($Qclasses->fetch()) {
        $weight_class_array[] = ['id' => $Qclasses->valueInt('weight_class_id'),
          'title' => $Qclasses->value('weight_class_title')
        ];
      }

      return $weight_class_array;
    }
  }