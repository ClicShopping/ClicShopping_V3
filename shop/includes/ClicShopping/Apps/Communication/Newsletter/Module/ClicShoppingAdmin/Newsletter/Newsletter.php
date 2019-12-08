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

  namespace ClicShopping\Apps\Communication\Newsletter\Module\ClicShoppingAdmin\Newsletter;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\FileSystem;

  use ClicShopping\Apps\Communication\Newsletter\Newsletter as AppNewsletter;
  use ClicShopping\Apps\Configuration\TemplateEmail\Classes\ClicShoppingAdmin\TemplateEmailAdmin;

  class Newsletter
  {
    public $show_choose_audience;
    public $title;
    public $content;

    protected $twitter;
    protected $file;
    protected $languageId;
    protected $customerGroupId;
    protected $createFile;
    protected $newsletterNoAccount;

    public function __construct($title, $content)
    {
      if (!Registry::exists('Newsletter')) {
        Registry::set('Newsletter', new AppNewsletter());
      }

      $this->app = Registry::get('Newsletter');

      $this->app->loadDefinitions('modules/newsletter');

      $this->show_choose_audience = false;
      $this->title = $title;
      $this->content = $content;
      $this->emailFrom = htmlentities($this->app->getDef('email_from'));


      $this->twitter = HTML::sanitize($_GET['at']); // send to twitter

      if (isset($_GET['ana'])) {
        $this->newsletterNoAccount = HTML::sanitize($_GET['ana']);
      }

      $this->fileId = HTML::sanitize($_GET['nID']); // id file on disk
      $this->languageId = HTML::sanitize($_GET['nlID']);
      $this->customerGroupId = HTML::sanitize($_GET['cgID']);
      $this->createFile = HTML::sanitize($_GET['ac']);

    }

    public function choose_audience()
    {
      return false;
    }

    public function confirm()
    {
      $CLICSHOPPING_Hooks = Registry::get('Hooks');
//
// delete all entries in the table newsletter temp for initilization
//
      $Qdelete = $this->app->db->prepare('delete from :table_newsletters_customers_temp');
      $Qdelete->execute();

      $file_name = '';

// ----------------------
// customer with an account
// ----------------------
      if ($this->languageId == 0) {

        $Qmail = $this->app->db->prepare('select count(*) as count
                                          from :table_customers
                                          where customers_newsletter = 1
                                          and customers_group_id = :customers_group_id
                                          and customers_email_validation = 0
                                        ');
        $Qmail->bindInt(':customers_group_id', (int)$this->customerGroupId);

        $Qmail->execute();

      } else {

        $Qmail = $this->app->db->prepare('select count(*) as count
                                          from :table_customers
                                          where customers_newsletter = 1
                                          and languages_id = :languages_id
                                          and customers_group_id = :customers_group_id
                                          and customers_email_validation = 0
                                        ');
        $Qmail->bindInt(':customers_group_id', (int)$this->customerGroupId);
        $Qmail->bindInt(':languages_id', (int)$this->languageId);

        $Qmail->execute();
      }

      if ($this->createFile == 1) {
// newsletter file inserted in the pub directory
        if (function_exists('file_put_contents')) {
          $file_newsletter = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'sources/public/newsletter/newsletter_' . $this->fileId . '.html';
          $directory = '<a href="' . CLICSHOPPING::getConfig('http_server', 'Shop') . '/sources/public/newsletter/newsletter_' . $this->fileId . '.html" target="_blank" rel="noreferrer">' . CLICSHOPPING::getConfig('http_server', 'Shop') . '/sources/public/newsletter/newsletter_' . $this->fileId . '.html</a>';
// ----------------------
// creating document
// ----------------------
          $content = '<!DOCTYPE html>
                    <html ' . $this->app->getDef('html_params') . '>
                    <head>
                      <meta charset="' . $this->app->getDef('charset') . '" />
                      <meta http-equiv="X-UA-Compatible" content="IE=edge">
                      <meta name="robots" content="noindex,nofollow" />
                      <meta name="viewport" content="width=device-width, initial-scale=1">
                      <title>' . $this->title . '</title>
                      <meta name="Description" content ="' . $this->title . '">
                     </head>
                    <body>
                      ' . $this->content . '
                    </body>
                   </html>
                  ';

// Write the contents back to the file
          if (FileSystem::isWritable(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'sources/public/newsletter')) {
            file_put_contents($file_newsletter, $content, LOCK_EX);
          }
        }

        if (FileSystem::isWritable(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'sources/public/newsletter')) {
          $file_name = '<div class="alert alert-success text-md-center" role="alert">';
          $file_name .= '<p class="text-md-center"><strong>' . $this->app->getDef('text_file_newsletter') . '</strong> newsletter_' . (int)$this->fileId . '.html<br /><span style="color:#ff0000;"><strong>' . $this->app->getDef('text_file_directories') . '</b></strong> ' . $directory . '</span></p>';
          $file_name .= '</div>';
        } else {
          $file_name = '<div class="alert alert-warning text-md-center" role="alert">';
          $file_name .= 'Newsletter no created : <strong>' . CLICSHOPPING::getConfig('dir_root', 'Shop') . 'sources/public/newsletter -  no writable</strong>';
          $file_name .= '</div>';
        }
      }

// ----------------------
// Display a button if subcription is > 0
// ----------------------
      if (SEND_EMAILS == 'true' && $Qmail->valueInt('count') > 0) {
        $send_button = '<span class="float-md-right">' . HTML::button($this->app->getDef('button_send'), null, $this->app->link('ConfirmSend&page=' . (int)$_GET['page'] . '&nID=' . $this->fileId . '&nlID=' . $this->languageId . '&cgID=' . $this->customerGroupId . '&ac=' . $this->createFile . '&at=' . $this->twitter . '&ana=' . $this->newsletterNoAccount), 'success', null) . '</span>';
      } else {
        $send_button = '';
      }

      $confirm_string = '';

      $confirm_string .= '
      <div class="contentBody">
        <div class="row" id="newsletterButton">
          <div class="col-md-12">
            <div class="card card-block headerCard">
              <div class="row">
                <span class="col-md-12">
      ';
      $confirm_string .= $send_button;
      $confirm_string .= '<span class="float-md-right">' . HTML::button($this->app->getDef('button_cancel'), null, $this->app->link('Newsletter&page=' . (int)$_GET['page'] . '&nID=' . $this->fileId), 'warning') . '&nbsp;</span>';
      $confirm_string .= '
                </span>
              </div>
            </div>
          </div>
        </div>
    ';

      $confirm_string .= '<div class="separator"></div>';

      $confirm_string .= '<div id="newsletterBody">' . "\n";
      $confirm_string .= '<div class="text-md-center alert alert-info" id="newsletterAlert">';
      $confirm_string .= '<div id="newsletterCount"><strong>' . $this->app->getDef('text_count_customers') . ' ' . $Qmail->valueInt('count') . '<strong></div>';
      $confirm_string .= '</div>' . "\n";

      $confirm_string .= $file_name . "\n";
      $confirm_string .= '<div class="separator"></div>' . "\n";
      $confirm_string .= '<div><strong>' . $this->title . '</strong></div>' . "\n";
      $confirm_string .= '<div class="separator"></div>' . "\n";
      $confirm_string .= '<div>' . $this->content . '</div>' . "\n";
      $confirm_string .= '<div class="separator"></div>';
      $confirm_string .= '</div>';
      $confirm_string .= '</div>';

      $confirm_string .= $CLICSHOPPING_Hooks->output('Newsletter', 'NewsletterContentPreAction', null, 'display');

      return $confirm_string;
    }


// Envoi du mail sans gestion de Fckeditor
    public function send($newsletter_id)
    {
      $CLICSHOPPING_Mail = Registry::get('Mail');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      if (!defined('CLICSHOPPING_APP_NEWSLETTER_NL_STATUS') || CLICSHOPPING_APP_NEWSLETTER_NL_STATUS == 'False') {
        return false;
      }


      if ($this->languageId == 0) {
        $Qmail = $this->app->db->prepare('select customers_firstname,
                                               customers_lastname,
                                               customers_email_address
                                        from :table_customers
                                        where customers_newsletter = 1
                                        and customers_group_id = :customers_group_id
                                        and customers_email_validation = 0
                                       ');
        $Qmail->bindInt(':customers_group_id', (int)$this->customerGroupId);
        $Qmail->execute();
      } else {
        $Qmail = $this->app->db->prepare('select customers_firstname,
                                                 customers_lastname,
                                                 customers_email_address
                                          from :table_customers
                                          where customers_newsletter = 1
                                          and languages_id = :languages_id
                                          and customers_group_id = :customers_group_id
                                          and customers_email_validation = 0
                                          ');
        $Qmail->bindInt(':customers_group_id', (int)$this->customerGroupId);
        $Qmail->bindInt(':languages_id', (int)$this->languageId);
        $Qmail->execute();
      } //end $this->languageId


      $max_execution_time = 0.8 * (int)ini_get('max_execution_time');
      $time_start = explode(' ', PAGE_PARSE_START_TIME);

// ----------------------
// if the file is created
// ----------------------
      if ($this->createFile == 1) {
        $CLICSHOPPING_Mail->addText('<p class="text-md-center">' . $this->app->getDef('text_send_newsletter_email', ['store_owner_email_address' => STORE_OWNER_EMAIL_ADDRESS]) . '</p>' . $this->content . ' ' . $this->app->getDef('text_send_newsletter', ['store_name' => STORE_NAME]) . ' ' . HTTP::getShopUrlDomain() . 'sources/public/newsletter/newsletter_' . $this->fileId . '.html<br /><br />' . TEXT_UNSUBSCRIBE . HTTP::getShopUrlDomain() . 'index.php?Account&Newsletters');
      } else {
        $CLICSHOPPING_Mail->addText('<p class="text-md-center">' . $this->app->getDef('text_send_newsletter_email', ['store_owner_email_address' => STORE_OWNER_EMAIL_ADDRESS]) . '</p>' . $this->content . ' ' . $this->app->getDef('text_send_newsletter', ['store_name' => STORE_NAME]) . ' ' . HTTP::getShopUrlDomain() . 'index.php?Account&Newsletters');
      }

      $CLICSHOPPING_Mail->build_message();

// ------------------------------------------
// copy e-mails to a temporary table if that table is empty
// ------------------------------------------

      $Qcheck = $this->app->db->prepare('select count(customers_email_address) as num_customers_email_address
                                         from :table_newsletters_customers_temp
                                       ');
      $Qcheck->execute();

      if ($Qcheck->valueInt('num_customers_email_address') == 0) {

        // ------------------------------------------
        // copy customers account in temp newsletter
        // ------------------------------------------

        while ($copy_customers_account = $Qmail->fetch()) {
          if (preg_match("#^[-a-z0-9._]+@([-a-z0-9_]+\.)+[a-z]{2,6}$#i", $copy_customers_account['customers_email_address'])) {

            $this->app->db->save('newsletters_customers_temp', [
                'customers_firstname' => addslashes($copy_customers_account['customers_firstname']),
                'customers_lastname' => addslashes($copy_customers_account['customers_lastname']),
                'customers_email_address' => $copy_customers_account['customers_email_address']
              ]
            );
          }
        }  // end while
      } else {
        echo 'There is a problem with your newsletters_customers_temp database, please, click cancel to go back and retry.<br />';
      }

      $QmailNewsletterAccountTemp = $this->app->db->prepare('select customers_firstname,
                                                                       customers_lastname,
                                                                       customers_email_address
                                                                from :table_newsletters_customers_temp
                                                              ');
      $QmailNewsletterAccountTemp->execute();

      while ($QmailNewsletterAccountTemp->fetch()) {

        $time_end = explode(' ', microtime());
        $timer_total = number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);

        if ($timer_total > $max_execution_time) {
          echo("<meta http-equiv=\"refresh\" content=\"12\">");
        }

        $CLICSHOPPING_Mail->send($QmailNewsletterAccountTemp->value['customers_firstname'] . ' ' . $QmailNewsletterAccountTemp->value['customers_lastname'], $QmailNewsletterAccountTemp->value['customers_email_address'], '', $this->emailFrom, $this->title);

// delete all entry in the table
        $Qdelete = $this->app->db->prepare('delete
                                              from :table_newsletters_customers_temp
                                              where customers_email_address = :customers_email_address
                                              ');
        $Qdelete->bindValue(':customers_email_address', $QmailNewsletterAccountTemp->value['customers_email_address']);
        $Qdelete->execute();
      } //end while


      $newsletter_id = HTML::sanitize($newsletter_id);

      $Qupdate = $this->app->db->prepare('update :table_newsletters
                                            set date_sent = now(),
                                            status = 1
                                            where newsletters_id = :newsletters_id
                                           ');
      $Qupdate->bindInt(':newsletters_id', $newsletter_id);
      $Qupdate->execute();


      $CLICSHOPPING_Hooks->call('Newsletter', 'NewsletterSend');


      $this->sendTwitter();
    } // end function

// ***************************************************
//                     HTML NEWSLETTER
// **************************************************

    public function sendCkeditor()
    {
      $CLICSHOPPING_Mail = Registry::get('Mail');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      if (!defined('CLICSHOPPING_APP_NEWSLETTER_NL_STATUS') || CLICSHOPPING_APP_NEWSLETTER_NL_STATUS == 'False') {
        return false;
      }
// ----------------------
//customer witt account
// ----------------------

      $template_email_signature = TemplateEmailAdmin::getTemplateEmailSignature();
      $template_email_newsletter_footer = TemplateEmailAdmin::getTemplateEmailNewsletterTextFooter();
      $email_footer = '<br />' . $template_email_signature . '<br />' . $template_email_newsletter_footer;

      $max_execution_time = 0.8 * (int)ini_get('max_execution_time');
      $time_start = explode(' ', PAGE_PARSE_START_TIME);


      if ($this->languageId == 0) {
        $Qmail = $this->app->db->prepare('select customers_firstname,
                                                 customers_lastname,
                                                 customers_email_address
                                          from :table_customers
                                          where customers_newsletter = 1
                                          and customers_group_id = :customers_group_id
                                          and customers_email_validation = 0
                                         ');
        $Qmail->bindInt(':customers_group_id', (int)$this->customerGroupId);
        $Qmail->execute();
      } else {
        $Qmail = $this->app->db->prepare('select customers_firstname,
                                                 customers_lastname,
                                                 customers_email_address
                                          from :table_customers
                                          where customers_newsletter = 1
                                          and languages_id = :languages_id
                                          and customers_group_id = :customers_group_id
                                          and customers_email_validation = 0
                                         ');
        $Qmail->bindInt(':customers_group_id', $this->customerGroupId);
        $Qmail->bindInt(':languages_id', $this->languageId);
        $Qmail->execute();
      }

      $Qcheck = $this->app->db->prepare('select count(customers_email_address) as num_customers_email_address
                                         from :table_newsletters_customers_temp
                                        ');
      $Qcheck->execute();

      if ($Qcheck->value('num_customers_email_address') == 0) {
// ------------------------------------------
// copy customers account in temp newsletter
// ------------------------------------------
        while ($Qmail->fetch()) {
          $time_end = explode(' ', microtime());
          $timer_total = number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);

          if ($timer_total > $max_execution_time) {
            echo("<meta http-equiv=\"refresh\" content=\"12\">");
          }

          if (preg_match("#^[-a-z0-9._]+@([-a-z0-9_]+\.)+[a-z]{2,6}$#i", $Qmail->value('customers_email_address'))) {

            $this->app->db->save('newsletters_customers_temp', ['customers_firstname' => addslashes($Qmail->value('customers_firstname')),
                'customers_lastname' => addslashes($Qmail->value('customers_lastname')),
                'customers_email_address' => $Qmail->value('customers_email_address')
              ]
            );
          }
        }  // end while
      } else {
        echo 'There is a pb with newsletters_customers_temp Database, Click Cancel to go back and retry.<br />';
      }

      $QmailNewsletterAccountTemp = $this->app->db->prepare('select customers_firstname,
                                                                   customers_lastname,
                                                                   customers_email_address
                                                            from :table_newsletters_customers_temp
                                                         ');
      $QmailNewsletterAccountTemp->execute();

      if ($this->createFile == 1) {
        $message = html_entity_decode('<p class="text-md-center">' . $this->app->getDef('text_send_newsletter_email', ['store_name' => STORE_NAME, 'store_owner_email_address' => STORE_OWNER_EMAIL_ADDRESS]) . '</p>' . $this->content . $this->app->getDef('text_send_newsletter', ['store_name' => STORE_NAME]) . HTTP::getShopUrlDomain() . 'sources/public/newsletter/newsletter_' . $this->fileId . '.html<br /><br />' . $email_footer);
      } else {
        $message = html_entity_decode('<p class="text-md-center">' . $this->app->getDef('text_send_newsletter_email', ['store_name' => STORE_NAME, 'store_owner_email_address' => STORE_OWNER_EMAIL_ADDRESS]) . '</p>' . $this->content . $email_footer);
      }

      $message = str_replace('src="/', 'src="' . HTTP::getShopUrlDomain(), $message);
      $CLICSHOPPING_Mail->addHtmlCkeditor($message);
      $CLICSHOPPING_Mail->build_message();


      $CLICSHOPPING_Hooks->call('Newsletter', 'NewsletterSendCkEditor');


      $this->sendTwitter();
    }

    private function sendTwitter()
    {
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      if (!defined('CLICSHOPPING_APP_NEWSLETTER_NL_STATUS') || CLICSHOPPING_APP_NEWSLETTER_NL_STATUS == 'False') {
        return false;
      }

      if (FileSystem::isWritable(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'sources/public/newsletter')) {
        if ($this->twitter == 1 && $this->createFile == 1 && $this->errorCreatingFile !== true) {
          $CLICSHOPPING_Hooks->call('Newsletter', 'SendTwitter');
        }
      } else {
        $alert = '<div class="separator"></div>';
        $alert .= '<div class="alert alert-warning text-md-center" role="alert">';
        $alert .= $this->app->getDef('error_twitter') . ' ' . CLICSHOPPING::getConfig('dir_root', 'Shop') . 'sources/public/newsletter';
        $alert .= '</div>';

        echo $alert;
      }
    }
  }
