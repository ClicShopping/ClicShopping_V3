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

  use ClicShopping\OM\MessageStack;
  use ClicShopping\OM\Registry;

  require_once (CLICSHOPPING::getConfig('dir_root', 'Shop') . 'ext/PHPMailer-master/vendor/autoload.php');

  use PHPMailer\PHPMailer\Exception;
  use PHPMailer\PHPMailer\PHPMailer;

  class Mail {

    protected $html;
    protected $text;
    protected $html_text;
    protected $lf;
    protected $debug = 2;
//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
    protected $debugOutput = 'phpmail_error.log';
    protected $phpMail;

    public $messageStack;

    public function __construct($headers = '') {

      $this->phpMail = new PHPMailer();

      $this->phpMail->XMailer = 'ClicShopping ' . CLICSHOPPING::getVersion();
      $this->phpMail->SMTPDebug = $this->debug;
      $this->phpMail->debugOutput =  CLICSHOPPING::BASE_DIR . 'Work/Log/phpmail_error.log';
      $this->phpMail->CharSet = CLICSHOPPING::getDef('charset');
      $this->phpMail->WordWrap = 998;
      $this->phpMail->Encoding = 'quoted-printable';

      $this->messageStack = Registry::get('MessageStack');
/*
//Configure message signing (the actual signing does not occur until sending)
      $phpMail->sign('/path/to/cert.crt', //The location of your certificate file
                    '/path/to/cert.key', //The location of your private key file
                    'yourSecretPrivateKeyPassword', //The password you protected your private key with (not the Import Password! may be empty but parameter must not be omitted!)
                    '/path/to/certchain.pem' //The location of your chain file
                    );
*/

      if (EMAIL_TRANSPORT == 'smtp' || EMAIL_TRANSPORT == 'gmail') {
        $this->phpMail->IsSMTP();

        $this->phpMail->Port = EMAIL_SMTP_PORT;

        if (EMAIL_SMTP_SECURE !== 'no') {
          $this->phpMail->SMTPSecure = EMAIL_SMTP_SECURE;
        }

        $this->phpMail->Host = EMAIL_SMTP_HOSTS;
        $this->phpMail->SMTPAuth = EMAIL_SMTP_AUTHENTICATION;

        $this->phpMail->Username = EMAIL_SMTP_USER;
        $this->phpMail->Password = EMAIL_SMTP_PASSWORD;

      } else {
        try {
          $this->phpMail->isSendmail();
        } catch (Exception $e) {
          $this->messageStack->add(CLICSHOPPING::getDef('error_phpmailer', ['phpmailer_error' => $this->phpMail->ErrorInfo]), 'error');
        }
      }

      if (EMAIL_LINEFEED == 'CRLF') {
        $this->lf = "\r\n";
      } else {
        $this->lf = "\n";
      }
    }


/**
   nl2br() prior PHP 4.2.0 did not convert linefeeds on all OSs (it only converted \n)
 */
    private function convertLinefeeds($from, $to, $string) {

      return str_replace($from, $to, $string);
    }

    public function addText($text = '') {
      $this->phpMail->IsHTML(false);
      $this->text = $this->convertLinefeeds(array("\r\n", "\n", "\r"), $this->lf, $text);
    }

/**
 * Adds a html part to the mail.
 * Also replaces image names with
 * content-id's.
 */

    public function addHtml($html, $text = NULL, $images_dir = NULL) {
      $this->phpMail->IsHTML(true);
      $this->html = $this->convertLinefeeds(array("\r\n", "\n", "\r"), '<br />', $html);
      $this->html_text = $this->convertLinefeeds(array("\r\n", "\n", "\r"), $this->lf, $text);

      if (isset($images_dir)) $this->html = $this->phpMail->msgHTML($this->html, $images_dir);
    }

/**
 * Adds a html part to the mail.
 * Also replaces image names with
 * content-id's.
 */

// FCKeditor
    public function addHtmlCkeditor($html, $text = NULL, $images_dir = NULL) {
      $this->phpMail->IsHTML(true);

      $this->html = $this->convertLinefeeds(array("\r\n", "\n", "\r"), '', $html);
      $this->html_text = $this->convertLinefeeds(array("\r\n", "\n", "\r"), $this->lf, $text);

      if (isset($images_dir)) $this->html = $this->phpMail->msgHTML($this->html, $images_dir);
    }

/**
 * @param $path
 * @param string $name
 * @param string $encoding
 * @param string $type
 * @param string $disposition
 * @throws Exception
 */
    public function addAttachment($path, $name = '', $encoding = 'base64', $type = '', $disposition = 'attachment') {
      $this->phpMail->AddAttachment($path, $name, $encoding, $type, $disposition);
    }

/**
 * Must be removed
 */
    public function build_message() {
      //out of work function
    }

/**
 * @param string $to_name
 * @param string $to_addr
 * @param string $from_name
 * @param string $from_addr
 * @param string $subject
 * @param bool $reply_to
 * @return bool
 * @throws \PHPMailer\PHPMailer\Exception
 */
    public function send($to_name, $to_addr, $from_name, $from_addr, $subject = '', $reply_to = false) {
      if ((strstr($to_name, "\n") !== false) || (strstr($to_name, "\r") !== false)) {
        return false;
      }

      if ((strstr($to_addr, "\n") !== false) || (strstr($to_addr, "\r") !== false)) {
        return false;
      }

      if ((strstr($subject, "\n") !== false) || (strstr($subject, "\r") !== false)) {
        return false;
      }

      if ((strstr($from_name, "\n") !== false) || (strstr($from_name, "\r") !== false)) {
        return false;
      }

      if ((strstr($from_addr, "\n") !== false) || (strstr($from_addr, "\r") !== false)) {
        return false;
      }

//Set who the message is to be sent from

      $this->phpMail->setFrom($from_addr, $from_addr);

//Set who the message is to be sent to
      $this->phpMail->AddAddress($to_addr, $to_name);

//Set an alternative reply-to address
      if ($reply_to) {
        $this->phpMail->AddReplyTo(EMAIL_SMTP_REPLYTO, STORE_NAME);
      } else {
        $this->phpMail->AddReplyTo($from_addr, $from_name);
      }

      $this->phpMail->Subject = $subject;

      if (!empty($this->html)) {
        $this->phpMail->Body = $this->html;
        $this->phpMail->AltBody = $this->html_text;

      } else {
        $this->phpMail->Body = $this->text;
      }

      if (EMAIL_TRANSPORT == 'smtp' || EMAIL_TRANSPORT == 'gmail') {

        $this->phpMail->IsSMTP();

        $this->phpMail->Port = EMAIL_SMTP_PORT;

        if (EMAIL_SMTP_SECURE !== 'no') {
          $this->phpMail->SMTPSecure = EMAIL_SMTP_SECURE;
        }

        $this->phpMail->Host = EMAIL_SMTP_HOSTS;
        $this->phpMail->SMTPAuth = EMAIL_SMTP_AUTHENTICATION;
        $this->phpMail->Username = EMAIL_SMTP_USER;
        $this->phpMail->Password = EMAIL_SMTP_PASSWORD;

      } else {
        try {
          $this->phpMail->isSendmail();
        } catch (Exception $e) {
          $this->messageStack->add(CLICSHOPPING::getDef('error_phpmailer', ['phpmailer_error' => $this->phpMail->ErrorInfo]), 'error');
        }
      }

      $error = false;

      if (!$this->phpMail->Send()) {
        $error = true;
      }

      $this->phpMail->clearAddresses();
      $this->phpMail->clearAttachments();

      if ($error === true) {
        return false;
      }

      return true;
    }

/**
 * Send email (text/html) using MIME
 * This is the central mail function. The SMTP Server should be configured
 * @param string $to_name The name of the recipient
 * @param string $to_email_address The email address of the recipient
 * @param string $subject The subject of the email
 * @param string $body The body text of the email
 * @param string $from_name The name of the sender
 * @param string $from_email_address The email address of the sender
 * @access public
 */
    public function clicMail($to_name, $to_email_address, $email_subject, $email_text, $from_email_name, $from_email_address) {
      if (SEND_EMAILS != 'true') return false;

// Build the text version
      $text = strip_tags($email_text);

      if (EMAIL_USE_HTML == 'true') {
        $this->addHtml($email_text, $text);
      } else {
        $this->addText($text);
      }

      // Send message
      $this->build_message();
      $this->send($to_name, $to_email_address, $from_email_name, $from_email_address, $email_subject);
    }

/*
 * Analyse the customer email  domain and validate or not the email
 * @param string : $email : mail of the customer
 * @param string return : value 0,1 the value of the test result
 * return : 1  valid email
 * return : 0 email no valid
*/

    public function validateDomainEmail($email) {

//check for all the non-printable codes in the standard ASCII set,
//including null bytes and newlines, and exit immediately if any are found.
      if (preg_match("/[\\000-\\037]/",$email)) {
        return false;
      }

      $pattern = "/^[-_a-z0-9\'+*$^&%=~!?{}]++(?:\.[-_a-z0-9\'+*$^&%=~!?{}]+)*+@(?:(?![-.])[-a-z0-9.]+(?<![-.])\.[a-z]{2,6}|\d{1,3}(?:\.\d{1,3}){3})(?::\d++)?$/iD";

      if(!preg_match($pattern, $email)){
        return false;
      }
// Validate the domain exists with a DNS check
// if the checks cannot be made (soft fail over to true)
      list($user,$domain) = explode('@',$email);

      if( function_exists('checkdnsrr') ) {
        if( !checkdnsrr($domain,"MX") ) { // Linux: PHP 4.3.0 and higher & Windows: PHP 5.3.0 and higher
          return false;
        }
      } else if( function_exists("getmxrr") ) {
        if ( !getmxrr($domain, $mxhosts) ) {
          return false;
        }
      }

      return true;
    } // end function validate_email
  }