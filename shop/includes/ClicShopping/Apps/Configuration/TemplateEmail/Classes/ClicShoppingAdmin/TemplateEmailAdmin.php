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


  namespace ClicShopping\Apps\Configuration\TemplateEmail\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTTP;

  class TemplateEmailAdmin
  {

    protected $template_email_id;
    protected $language_id;

    /**
     * the name of the template
     *
     * @param string $template_email_id , $language_id
     * @return string $template_email_name['template_name'],  name.of the template email
     * @access public
     */
    public static function getTemplateEmailName($template_email_id, $language_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!$language_id) $language_id = $CLICSHOPPING_Language->getId();

      $QtemplateEmail = $CLICSHOPPING_Db->prepare('select template_email_name
                                                  from :table_template_email_description
                                                  where template_email_id = :template_email_id
                                                  and language_id = :language_id
                                                 ');
      $QtemplateEmail->bindInt(':template_email_id', (int)$template_email_id);
      $QtemplateEmail->bindInt(':language_id', (int)$language_id);

      $QtemplateEmail->execute();

      return $QtemplateEmail->value('template_email_name');
    }


    /**
     * the template email short description
     *
     * @param string $template_email_id , $language_id
     * @return string $template_email['template_short_description'],  the short description of the template email
     * @access public
     */
    public static function getTemplateEmailShortDescription($template_email_id, $language_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!$language_id) $language_id = $CLICSHOPPING_Language->getId();

      $QtemplateEmail = $CLICSHOPPING_Db->prepare('select template_email_short_description
                                                  from :table_template_email_description
                                                  where template_email_id = :template_email_id
                                                  and language_id = :language_id
                                                 ');
      $QtemplateEmail->bindInt(':template_email_id', (int)$template_email_id);
      $QtemplateEmail->bindInt(':language_id', (int)$language_id);

      $QtemplateEmail->execute();

      return $QtemplateEmail->value('template_email_short_description');
    }

    /**
     * the template email description who is sent
     *
     * @param string $template_email_id , $language_id
     * @return string $template_email['template_email_description'],  the description of the template email who is sent
     * @access public
     */
    public static function getTemplateEmailDescription($template_email_id, $language_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!$language_id) $language_id = $CLICSHOPPING_Language->getId();

      $QtemplateEmail = $CLICSHOPPING_Db->prepare('select template_email_description
                                                  from :table_template_email_description
                                                  where template_email_id = :template_email_id
                                                  and language_id = :language_id
                                                ');
      $QtemplateEmail->bindInt(':template_email_id', (int)$template_email_id);
      $QtemplateEmail->bindInt(':language_id', (int)$language_id);

      $QtemplateEmail->execute();

      return $QtemplateEmail->value('template_email_description');
    }

    /**
     * the footer of email
     *
     * @param string $template_email_footer
     * @return string $template_email_footer,  the footer of the email template who is sent
     * @access public
     */

    public static function getTemplateEmailTextFooter()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      $QtemplateEmail = $CLICSHOPPING_Db->prepare('select te.template_email_variable,
                                                         ted.template_email_description
                                                  from :table_template_email te,
                                                       :table_template_email_description  ted
                                                  where te.template_email_variable = :template_email_variable
                                                  and te.template_email_id = ted.template_email_id
                                                  and ted.language_id = :language_id
                                               ');
      $QtemplateEmail->bindValue(':template_email_variable', 'TEMPLATE_EMAIL_TEXT_FOOTER');
      $QtemplateEmail->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());


      $QtemplateEmail->execute();

      $template_email_footer = $QtemplateEmail->value('template_email_description');

      $keywords = array('/{{store_name}}/',
        '/{{store_owner_email_address}}/',
        '/{{http_shop}}/'
      );

      $replaces = array(STORE_NAME,
        STORE_OWNER_EMAIL_ADDRESS,
        HTTP::getShopUrlDomain()
      );


      $template_email_footer = preg_replace($keywords, $replaces, $template_email_footer);

      return $template_email_footer;
    }

    /**
     * the footer of the newsletter
     *
     * @param string $template_email_newsletter_footer
     * @return string $template_email_newsletter_ footer,  the footer of the newsletter email template who is sent
     * @access public
     */
    public static function getTemplateEmailNewsletterTextFooter()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      $QtemplateEmail = $CLICSHOPPING_Db->prepare('select te.template_email_variable,
                                                         ted.template_email_description
                                                  from :table_template_email te,
                                                       :table_template_email_description  ted
                                                  where te.template_email_variable = :template_email_variable
                                                  and te.template_email_id = ted.template_email_id
                                                  and ted.language_id = :language_id
                                                ');
      $QtemplateEmail->bindValue(':template_email_variable', 'TEMPLATE_EMAIL_NEWSLETTER_TEXT_FOOTER');
      $QtemplateEmail->bindInt(':language_id', $CLICSHOPPING_Language->getId());


      $QtemplateEmail->execute();

      $template_email_newsletter_footer = $QtemplateEmail->value('template_email_description');

      $keywords = array('/{{store_name}}/',
        '/{{store_owner_email_address}}/',
        '/{{http_shop}}/'
      );

      $replaces = array(STORE_NAME,
        STORE_OWNER_EMAIL_ADDRESS,
        HTTP::getShopUrlDomain()
      );


      $template_email_newsletter_footer = preg_replace($keywords, $replaces, $template_email_newsletter_footer);

      return $template_email_newsletter_footer;
    }

    /**
     * the signature of email
     *
     * @param string $template_email_signature
     * @return string $template_email_signature,  the signature of the email template who is sent
     * @access public
     */
    public static function getTemplateEmailSignature()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      $QtemplateEmail = $CLICSHOPPING_Db->prepare('select te.template_email_variable,
                                                           ted.template_email_description
                                                    from :table_template_email te,
                                                         :table_template_email_description  ted
                                                    where te.template_email_variable = :template_email_variable
                                                    and te.template_email_id = ted.template_email_id
                                                    and ted.language_id = :language_id
                                                  ');
      $QtemplateEmail->bindValue(':template_email_variable', 'TEMPLATE_EMAIL_SIGNATURE');
      $QtemplateEmail->bindInt(':language_id', $CLICSHOPPING_Language->getId());


      $QtemplateEmail->execute();


      $template_email_signature = $QtemplateEmail->value('template_email_description');

      $keywords = array('/{{store_name}}/',
        '/{{store_owner_email_address}}/',
        '/{{http_shop}}/'

      );

      $replaces = array(STORE_NAME,
        STORE_OWNER_EMAIL_ADDRESS,
        HTTP::getShopUrlDomain()
      );


      $template_email_signature = preg_replace($keywords, $replaces, $template_email_signature);

      return $template_email_signature;
    }

    /**
     * the template email welcome admin who is sent
     *
     * @param string $template_email_welcome_admin
     * @return string $template_email_welcome_admin,  the description of the template email welcome admin who is sent
     * @access public
     */
    public static function getTemplateEmailWelcomeAdmin()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      $QtemplateEmail = $CLICSHOPPING_Db->prepare('select te.template_email_variable,
                                                         ted.template_email_description
                                                  from :table_template_email te,
                                                       :table_template_email_description  ted
                                                  where te.template_email_variable = :template_email_variable
                                                  and te.template_email_id = ted.template_email_id
                                                  and ted.language_id = :language_id
                                                ');
      $QtemplateEmail->bindValue(':template_email_variable', 'TEMPLATE_EMAIL_WELCOME_ADMIN');
      $QtemplateEmail->bindInt(':language_id', $CLICSHOPPING_Language->getId());


      $QtemplateEmail->execute();

      $template_email_welcome_admin = $QtemplateEmail->value('template_email_description');

      $keywords = array('/{{store_name}}/',
        '/{{store_owner_email_address}}/',
        '/{{http_shop}}/'
      );

      $replaces = array(STORE_NAME,
        STORE_OWNER_EMAIL_ADDRESS,
        HTTP::getShopUrlDomain()
      );

      $template_email_welcome_admin = preg_replace($keywords, $replaces, $template_email_welcome_admin);

      return $template_email_welcome_admin;
    }


    /**
     * the template email coupon who is sent
     *
     * @param string $template_email_coupon_admin
     * @return string $template_email_coupon_admin,  the description of the template email coupon who is sent
     * @access public
     */
    public static function getTemplateEmailCouponAdmin()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      $QtemplateEmail = $CLICSHOPPING_Db->prepare('select te.template_email_variable,
                                                         ted.template_email_description
                                                  from :table_template_email te,
                                                       :table_template_email_description  ted
                                                  where te.template_email_variable = :template_email_variable
                                                  and te.template_email_id = ted.template_email_id
                                                  and ted.language_id = :language_id
                                                ');
      $QtemplateEmail->bindValue(':template_email_variable', 'TEMPLATE_EMAIL_TEXT_COUPON');
      $QtemplateEmail->bindInt(':language_id', $CLICSHOPPING_Language->getId());


      $QtemplateEmail->execute();

      $template_email_coupon_admin = $QtemplateEmail->value('template_email_description');

      $keywords = array('/{{store_name}}/',
        '/{{store_owner_email_address}}/',
        '/{{http_shop}}/'
      );

      $replaces = array(STORE_NAME,
        STORE_OWNER_EMAIL_ADDRESS,
        HTTP::getShopUrlDomain()
      );

      $template_email_coupon_admin = preg_replace($keywords, $replaces, $template_email_coupon_admin);

      return $template_email_coupon_admin;
    }

    /**
     * the template order intro command who is sent
     *
     * @param string $template_email_intro_command
     * @return string $template_email_intro_command,  the description of the template email order intro command who is sent
     * @access public
     */

    public static function getTemplateEmailIntroCommand()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      $QtemplateEmail = $CLICSHOPPING_Db->prepare('select te.template_email_variable,
                                                           ted.template_email_description
                                                    from :table_template_email te,
                                                         :table_template_email_description  ted
                                                    where te.template_email_variable = :template_email_variable
                                                    and te.template_email_id = ted.template_email_id
                                                    and ted.language_id = :language_id
                                                  ');
      $QtemplateEmail->bindValue(':template_email_variable', 'TEMPLATE_EMAIL_INTRO_COMMAND');
      $QtemplateEmail->bindInt(':language_id', $CLICSHOPPING_Language->getId());


      $QtemplateEmail->execute();

      $template_email_intro_command = $QtemplateEmail->value('template_email_description');

      $keywords = array('/{{store_name}}/',
        '/{{store_owner_email_address}}/',
        '/{{http_shop}}/'
      );

      $replaces = array(STORE_NAME,
        STORE_OWNER_EMAIL_ADDRESS,
        HTTP::getShopUrlDomain()
      );

      $template_email_intro_command = preg_replace($keywords, $replaces, $template_email_intro_command);

      return $template_email_intro_command;
    }
  }