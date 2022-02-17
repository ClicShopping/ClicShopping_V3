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

  namespace ClicShopping\Apps\Communication\EMail\Sites\ClicShoppingAdmin\Pages\Home\Actions\SendEmailToUser;

  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Configuration\TemplateEmail\Classes\ClicShoppingAdmin\TemplateEmailAdmin;

  use ClicShopping\Apps\Configuration\Administrators\Classes\ClicShoppingAdmin\AdministratorAdmin;

  class Process extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $from;
    protected $subject;
    protected $messageMail;
    protected $templateEmailSignature;
    protected $templateEmailFooter;
    protected $mail;
    protected mixed $app;

    public function __construct()
    {

      $this->from = HTML::sanitize($_POST['from']);
      $this->subject = HTML::sanitize($_POST['subject']);
      $this->messageMail = $_POST['message'];

      $this->templateEmailSignature = TemplateEmailAdmin::getTemplateEmailSignature();
      $this->templateEmailFooter = TemplateEmailAdmin::getTemplateEmailTextFooter();
      $this->mail = Registry::get('Mail');
      $this->app = Registry::get('EMail');
    }

    public function execute()
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      if (isset($_POST['customers_email_address'])) {

        switch ($_POST['customers_email_address']) {
          case '***':

            $Qmail = $this->app->db->prepare('select customers_firstname,
                                                       customers_lastname,
                                                       customers_email_address
                                                from :table_customers
                                                where customers_email_validation = 0
                                               ');
            $Qmail->execute();

            break;

          case '**D':

            $Qmail = $this->app->db->prepare('select customers_firstname,
                                                       customers_lastname,
                                                       customers_email_address
                                                from :table_customers
                                                where customers_newsletter = 1
                                                and customers_email_validation = 0
                                               ');
            $Qmail->execute();

            break;

// B2B
          case 'group':
            $QCustomersGroup = $this->app->db->prepare('select distinct customers_group_name,
                                                                          customers_group_id
                                                          from :table_customers_groups
                                                          where customers_group_id != 0
                                                          order by customers_group_id
                                                        ');
            $QCustomersGroup->execute();

// A analyse pb avec la B2B
            if ($QCustomersGroup->rowCount() > 0) {
              while ($QCustomersGroup->fetch()) {

                $Qmail = $this->app->db->prepare('select customers_firstname,
                                                            customers_lastname,
                                                            customers_email_address,
                                                            customers_group_id
                                                       from :table_customers
                                                       where customers_group_id = :customers_group_id
                                                       and customers_email_validation = 0
                                                    ');

                $Qmail->bindInt(':customers_group_id', (int)$QCustomersGroup->valueInt('customers_group_id'));

                $Qmail->execute();
              }
            }
            break;

          default:
            $customers_email_address = HTML::sanitize($_POST['customers_email_address']);

            $Qmail = $this->app->db->prepare('select customers_id,
                                                       customers_firstname,
                                                       customers_lastname,
                                                       customers_email_address
                                                from :table_customers
                                                where customers_email_address = :customers_email_address
                                                and customers_email_validation = 0
                                              ');

            $Qmail->bindValue(':customers_email_address', $customers_email_address);

            $Qmail->execute();


            $QmailSave = $this->app->db->prepare('select customers_id,
                                                           customers_firstname,
                                                           customers_lastname,
                                                           customers_email_address
                                                    from :table_customers
                                                    where customers_email_address = :customers_email_address
                                                    and customers_email_validation = 0
                                                   ');
            $QmailSave->bindValue(':customers_email_address', $customers_email_address);
            $QmailSave->execute();

            if ($QmailSave->fetch()) {
              $customers_id = $QmailSave->valueInt('customers_id');

              if (!empty($customers_id) && !empty($this->messageMail)) {
// notes clients
                $this->app->db->save('customers_notes', ['customers_id' => $customers_id,
                    'customers_notes' => $this->subject . ' <br />' . $this->messageMail,
                    'customers_notes_date' => 'now()',
                    'user_administrator' => AdministratorAdmin::getUserAdmin(),
                  ]
                );
              }
            } else {
              $CLICSHOPPING_MessageStack->add($this->app->getDef('error_email_sent'), 'error', 'email');
            }

            break;
          }

        $message = $this->messageMail . '<br />' . $this->templateEmailSignature . '<br />' . $this->templateEmailFooter;

// Envoie du mail avec gestion des images pour Fckeditor
        $message = str_replace('src="/', 'src="' . HTTP::getShopUrlDomain(), $message);

        $this->mail->addHtmlCkeditor($message);

        while ($Qmail->fetch()) {
          $this->mail->send($Qmail->value('customers_email_address'), $Qmail->value('customers_firstname') . ' ' . $Qmail->value('customers_lastname'), $this->from, null, $this->subject);
        }

        $CLICSHOPPING_MessageStack->add($this->app->getDef('success_email_sent'), 'success', 'email');

      } else {
        $CLICSHOPPING_MessageStack->add($this->app->getDef('error_email_sent'), 'error', 'email');
      }

      $this->app->redirect('EMail&EMail');
    }
  }