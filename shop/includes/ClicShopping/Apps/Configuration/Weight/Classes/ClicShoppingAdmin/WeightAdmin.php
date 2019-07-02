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


  namespace ClicShopping\Apps\Configuration\Weight\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;

  class WeightAdmin extends \ClicShopping\Apps\Configuration\Weight\Classes\Shop\Weight
  {

    protected $weight_classes = [];
    protected $precision = 2;

    public function __construct($precision = null)
    {
    }

    /**
     * @param $id
     * @param null $language_id
     * @return mixed
     */
    public static function getTitle($id, $language_id = null)
    {
      return parent::getTitle($id, $language_id);
    }

    /**
     * @return array
     */
    public static function getClasses()
    {
      return parent::getClasses();
    }

    /**
     * @param $value
     * @param $class
     * @return string
     */
    public function display($value, $class)
    {
      return parent::display($value, $class);
    }

    /**
     * @param $value
     * @param $unit_from
     * @param $unit_to
     * @return string|void
     */
    public function convert(string $value, string $unit_from, string $unit_to): string
    {
      parent::convert($value, $unit_from, $unit_to);
    }

    /**
     * Drop down of the class title
     *
     * @param string $parameters , $selected
     * @return string $select_string, the drop down of the title class
     * @access public
     *
     */
    public static function getClassesPullDown(): array
    {
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qclasses = $CLICSHOPPING_Db->prepare('select weight_class_id,
                                                    weight_class_title
                                              from :table_weight_classes
                                              where language_id = :language_id
                                              order by weight_class_title
                                            ');
      $Qclasses->bindInt(':language_id', $CLICSHOPPING_Language->getID());
      $Qclasses->execute();

      while ($Qclasses->fetch() !== false) {
        $classes[] = ['id' => $Qclasses->valueInt('weight_class_id'),
          'text' => $Qclasses->value('weight_class_title')
        ];
      }

      return $classes;
    }

    /**
     * Display a weight class title
     *
     * @param int products_weight_class_id
     * @param string $result weight title
     * @access public
     */

    public static function getWeightTitle($id = null): string
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!is_null($id)) {
        $Qweight = $CLICSHOPPING_Db->prepare('select weight_class_title
                                               from :table_weight_classes
                                               where weight_class_id = :weight_class_id
                                               and language_id = :language_id
                                               ');
        $Qweight->bindInt(':weight_class_id', $id);
        $Qweight->bindInt(':language_id', $CLICSHOPPING_Language->getID());

        $Qweight->execute();

        $result = $Qweight->value('weight_class_title');

        return $result;
      }
    }
  }
