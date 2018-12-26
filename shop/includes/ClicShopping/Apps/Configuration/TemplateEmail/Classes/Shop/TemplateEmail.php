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

  namespace ClicShopping\Apps\Configuration\TemplateEmail\Classes\Shop;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTTP;

  class TemplateEmail {

/**
 * the name of the template
 *
 * @param string  $template_email_id, $language_id
 * @return string $template_email_name['template_name'],  name.of the template email
 * @access public
 */
    public static function getTemplateEmailName($template_email_id, $language_id) {
      $CLICSHOPPING_Db = Registry::get('Db');

      $QtemplateEmail = $CLICSHOPPING_Db->prepare('select template_email_name
                                            from :table_template_email_description
                                            where template_email_id = :template_email_id
                                            and language_id = :language_id
                                           ');
      $QtemplateEmail->bindInt(':template_email_id',(int)$template_email_id);
      $QtemplateEmail->bindInt(':language_id',(int)$language_id);
      $QtemplateEmail->execute();

      $template_email_name = $QtemplateEmail->fetch();

      return $template_email_name['template_email_name'];
    }


/**
 * the template email short description
 *
 * @param string  $template_email_id, $language_id
 * @return string $template_email['template_short_description'],  the short description of the template email
 * @access public
 */
    public static function getTemplateEmailShortDescription($template_email_id, $language_id) {
      $CLICSHOPPING_Db = Registry::get('Db');

      $QtemplateEmailShortDescription = $CLICSHOPPING_Db->prepare('select template_email_short_description
                                                            from :table_template_email_description
                                                            where template_email_id = :template_email_id
                                                            and language_id = :language_id
                                                           ');
      $QtemplateEmailShortDescription->bindInt(':template_email_id',(int)$template_email_id);
      $QtemplateEmailShortDescription->bindInt(':language_id',(int)$language_id);
      $QtemplateEmailShortDescription->execute();

      $template_email_short_description = $QtemplateEmailShortDescription->fetch();

      return $template_email_short_description['template_email_short_description'];
    }

/**
 * the template email description who is sent
 *
 * @param string  $template_email_id, $language_id
 * @return string $template_email['template_email_description'],  the description of the template email who is sent
 * @access public
 */
    public static function getTemplateEmailDescription($template_email_id, $language_id) {

      $CLICSHOPPING_Db = Registry::get('Db');

      $QtemplateEmailDescription = $CLICSHOPPING_Db->prepare('select template_email_description
                                                        from :table_template_email_description
                                                        where template_email_id = :template_email_id
                                                        and language_id = :language_id
                                                       ');
      $QtemplateEmailDescription->bindInt(':template_email_id',(int)$template_email_id);
      $QtemplateEmailDescription->bindInt(':language_id',(int)$language_id);
      $QtemplateEmailDescription->execute();

      $template_email_description = $QtemplateEmailDescription->fetch();

      return $template_email_description['template_email_description'];
    }

/**
 * the footer of email
 *
 * @param string  $template_email_footer
 * @return string $template_email_footer,  the footer of the email template who is sent
 * @access public
 */
    public static function getTemplateEmailTextFooter() {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      $QtextTemplateEmailFooter = $CLICSHOPPING_Db->prepare('select te.template_email_variable,
                                                              ted.template_email_description
                                                       from :table_template_email te,
                                                            :table_template_email_description  ted
                                                       where te.template_email_variable = :template_email_variable
                                                       and te.template_email_id = ted.template_email_id
                                                       and ted.language_id = :language_id
                                                      ');

      $QtextTemplateEmailFooter->bindValue(':template_email_variable', 'TEMPLATE_EMAIL_TEXT_FOOTER');
      $QtextTemplateEmailFooter->bindInt(':language_id',(int)$CLICSHOPPING_Language->getId());
      $QtextTemplateEmailFooter->execute();

      $template_email_footer = $QtextTemplateEmailFooter->value('template_email_description');

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
 * the signature of email
 *
 * @param string  $template_email_signature
 * @return string $template_email_signature,  the signature of the email template who is sent
 * @access public
 */
     public static function getTemplateEmailSignature() {
       $CLICSHOPPING_Db = Registry::get('Db');
       $CLICSHOPPING_Language = Registry::get('Language');

       $QtextTemplateEmailSignature = $CLICSHOPPING_Db->prepare('select te.template_email_variable,
                                                              ted.template_email_description
                                                       from :table_template_email te,
                                                            :table_template_email_description  ted
                                                       where te.template_email_variable = :template_email_variable
                                                       and te.template_email_id = ted.template_email_id
                                                       and ted.language_id = :language_id
                                                      ');

       $QtextTemplateEmailSignature->bindValue(':template_email_variable', 'TEMPLATE_EMAIL_SIGNATURE');
       $QtextTemplateEmailSignature->bindInt(':language_id',(int)$CLICSHOPPING_Language->getId());
       $QtextTemplateEmailSignature->execute();

       $template_email_signature = $QtextTemplateEmailSignature->value('template_email_description');

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
 * the template email welcome catalog who is sent
 *
 * @param string  $template_email_welcome_admin
 * @return string $template_email_welcome_admin,  the description of the template email welcome admin who is sent
 * @access public
 */
     public static function getTemplateEmailWelcomeCatalog() {
       $CLICSHOPPING_Db = Registry::get('Db');
       $CLICSHOPPING_Language = Registry::get('Language');

       $QtextTemplateEmailWelcomeCatalog = $CLICSHOPPING_Db->prepare('select te.template_email_variable,
                                                                        ted.template_email_description
                                                                 from :table_template_email te,
                                                                      :table_template_email_description  ted
                                                                 where te.template_email_variable = :template_email_variable
                                                                 and te.template_email_id = ted.template_email_id
                                                                 and ted.language_id = :language_id
                                                                ');

       $QtextTemplateEmailWelcomeCatalog->bindValue(':template_email_variable', 'TEMPLATE_EMAIL_WELCOME');
       $QtextTemplateEmailWelcomeCatalog->bindInt(':language_id',(int)$CLICSHOPPING_Language->getId());
       $QtextTemplateEmailWelcomeCatalog->execute();

       $template_email_welcome_catalog = $QtextTemplateEmailWelcomeCatalog->value('template_email_description');

       $keywords = array('/{{store_name}}/',
                        '/{{store_owner_email_address}}/',
                        '/{{http_shop}}/'
                        );

       $replaces = array(STORE_NAME,
                        STORE_OWNER_EMAIL_ADDRESS,
                         HTTP::getShopUrlDomain()
                        );

       $template_email_welcome_catalog = preg_replace($keywords, $replaces, $template_email_welcome_catalog);

       return $template_email_welcome_catalog;
     }


/**
 * the template email coupon who is sent
 *
 * @param string  $template_email_coupon_admin
 * @return string $template_email_coupon_admin,  the description of the template email coupon who is sent
 * @access public
 */
    public static function getTemplateEmailCouponCatalog() {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      $QtextTemplateEmailCouponCatalog = $CLICSHOPPING_Db->prepare('select te.template_email_variable,
                                                                          ted.template_email_description
                                                                   from :table_template_email te,
                                                                        :table_template_email_description  ted
                                                                   where te.template_email_variable = :template_email_variable
                                                                   and te.template_email_id = ted.template_email_id
                                                                   and ted.language_id = :language_id
                                                                  ');

      $QtextTemplateEmailCouponCatalog->bindValue(':template_email_variable', 'TEMPLATE_EMAIL_TEXT_COUPON');
      $QtextTemplateEmailCouponCatalog->bindInt(':language_id',(int)$CLICSHOPPING_Language->getId());
      $QtextTemplateEmailCouponCatalog->execute();

      $template_email_coupon_catalog = $QtextTemplateEmailCouponCatalog->value('template_email_description');

      $keywords = array('/{{store_name}}/',
                        '/{{store_owner_email_address}}/',
                        '/{{http_shop}}/'
                        );

      $replaces = array(STORE_NAME,
                        STORE_OWNER_EMAIL_ADDRESS,
                        HTTP::getShopUrlDomain()
                        );

      $template_email_coupon_catalog = preg_replace($keywords, $replaces, $template_email_coupon_catalog);

      return $template_email_coupon_catalog;
    }

/**
 * the template order intro command who is sent
 *
 * @param string  $template_email_intro_command
 * @return string $template_email_intro_command,  the description of the template email order intro command who is sent
 * @access public
 */
    public static function getTemplateEmailIntroCommand() {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      $QtextTemplateEmailIntroCommand = $CLICSHOPPING_Db->prepare('select te.template_email_variable,
                                                                          ted.template_email_description
                                                                   from :table_template_email te,
                                                                        :table_template_email_description  ted
                                                                   where te.template_email_variable = :template_email_variable
                                                                   and te.template_email_id = ted.template_email_id
                                                                   and ted.language_id = :language_id
                                                                  ');

      $QtextTemplateEmailIntroCommand->bindValue(':template_email_variable', 'TEMPLATE_EMAIL_INTRO_COMMAND');
      $QtextTemplateEmailIntroCommand->bindInt(':language_id',(int)$CLICSHOPPING_Language->getId());
      $QtextTemplateEmailIntroCommand->execute();

      $template_email_intro_command = $QtextTemplateEmailIntroCommand->value('template_email_description');

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

/**
 * Extract email to send more one email
 * bug with SEND_EXTRA_ORDER_EMAILS_TO
 *
 * @param string : email
 * @return string $emails, email
 * @access public
 */
    public static function getExtractEmailAddress($string) {
      foreach(preg_split('/\s/', $string) as $token) {
        $email = filter_var(filter_var($token, FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL);
        if ($email !== false) {
          $emails[] = $email;
        }
      }
      return $emails;
    }
  }