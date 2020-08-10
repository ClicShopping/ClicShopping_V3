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

  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class pdfInvoice extends FPDF
  {

    public function roundedRect($x, $y, $w, $h, $r, $style = '')
    {
      $k = $this->k;
      $hp = $this->h;

      if ($style == 'F') {
        $op = 'f';
      } else if ($style == 'FD' || $style == 'DF') {
        $op = 'B';
      } else {
        $op = 'S';
      }

      $MyArc = 4 / 3 * (sqrt(2) - 1);
      $this->_out(sprintf('%.2f %.2f m', ($x + $r) * $k, ($hp - $y) * $k));
      $xc = $x + $w - $r;
      $yc = $y + $r;
      $this->_out(sprintf('%.2f %.2f l', $xc * $k, ($hp - $y) * $k));
      $this->_Arc($xc + $r * $MyArc, $yc - $r, $xc + $r, $yc - $r * $MyArc, $xc + $r, $yc);
      $xc = $x + $w - $r;
      $yc = $y + $h - $r;
      $this->_out(sprintf('%.2f %.2f l', ($x + $w) * $k, ($hp - $yc) * $k));
      $this->_Arc($xc + $r, $yc + $r * $MyArc, $xc + $r * $MyArc, $yc + $r, $xc, $yc + $r);
      $xc = $x + $r;
      $yc = $y + $h - $r;
      $this->_out(sprintf('%.2f %.2f l', $xc * $k, ($hp - ($y + $h)) * $k));
      $this->_Arc($xc - $r * $MyArc, $yc + $r, $xc - $r, $yc + $r * $MyArc, $xc - $r, $yc);
      $xc = $x + $r;
      $yc = $y + $r;
      $this->_out(sprintf('%.2f %.2f l', ($x) * $k, ($hp - $yc) * $k));
      $this->_Arc($xc - $r, $yc - $r * $MyArc, $xc - $r * $MyArc, $yc - $r, $xc, $yc - $r);
      $this->_out($op);
    }

    public function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
      $h = $this->h;
      $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c ', $x1 * $this->k, ($h - $y1) * $this->k,
        $x2 * $this->k, ($h - $y2) * $this->k, $x3 * $this->k, ($h - $y3) * $this->k));
    }

    public function Header()
    {
      $CLICSHOPPING_Template = Registry::get('Template');

// Logo
      if (is_file(CLICSHOPPING::getConfig('dir_root', 'Shop') . $CLICSHOPPING_Template->getDirectoryTemplateImages() . 'logos/invoice/' . INVOICE_LOGO)) {
        $this->Image(HTTP::getShopUrlDomain() . $CLICSHOPPING_Template->getDirectoryTemplateImages() . 'logos/invoice/' . INVOICE_LOGO, 5, 10, 50);
      }

// Nom de la compagnie
      $this->SetX(0);
      $this->SetY(10);
      $this->SetFont('Arial', 'B', 10);
      $this->SetTextColor(INVOICE_RGB);
      $this->Ln(0);
      $this->Cell(125);
      $this->MultiCell(100, 3.5, utf8_decode(STORE_NAME), 0, 'L');

// Adresse de la compagnie
      $this->SetX(0);
      $this->SetY(15);
      $this->SetFont('Arial', '', 8);
      $this->SetTextColor(INVOICE_RGB);
      $this->Ln(0);
      $this->Cell(125);
      $this->MultiCell(100, 3.5, utf8_decode(STORE_NAME_ADDRESS), 0, 'L');

// Email
      $this->SetX(0);
      $this->SetY(30);
      $this->SetFont('Arial', '', 8);
      $this->SetTextColor(INVOICE_RGB);
      $this->Ln(0);
      $this->Cell(-3);
      $this->MultiCell(100, 3.5, utf8_decode(CLICSHOPPING::getDef('entry_email')) . ' ' . STORE_OWNER_EMAIL_ADDRESS, 0, 'L');

// Website
      $this->SetX(0);
      $this->SetY(34);
      $this->SetFont('Arial', '', 8);
      $this->SetTextColor(INVOICE_RGB);
      $this->Ln(0);
      $this->Cell(-3);
      $this->MultiCell(100, 3.5, CLICSHOPPING::getDef('entry_http_site') . ' ' . HTTP::typeUrlDomain(), 0, 'L');
    }

    public function Footer()
    {
// Remerciement
      $this->SetY(-55);
      $this->SetFont('Arial', 'B', 8);
      $this->SetTextColor(INVOICE_RGB);
      $this->Cell(0, 10, utf8_decode(CLICSHOPPING::getDef('thank_you_customer')), 0, 0, 'C');
// Proprieties Legal
      $this->SetY(-45);
      $this->SetFont('Arial', '', 7);
      $this->SetTextColor(INVOICE_RGB);

      $this->Cell(0, 10, utf8_decode(CLICSHOPPING::getDef('reserve_propriete')), 0, 0, 'C');
      $this->SetY(-40);
      $this->SetFont('Arial', '', 7);
      $this->SetTextColor(INVOICE_RGB);

      $this->Cell(0, 10, utf8_decode(CLICSHOPPING::getDef('reserve_propriete_next')), 0, 0, 'C');
      $this->SetY(-35);
      $this->SetFont('Arial', '', 7);
      $this->SetTextColor(INVOICE_RGB);
      $this->Cell(0, 10, utf8_decode(CLICSHOPPING::getDef('reserve_propriete_next1', ['sell_conditions_url' => HTTP::getShopUrlDomain() . ' ' . SHOP_CODE_URL_CONDITIONS_VENTE])), 0, 0, 'C');
// Informations de la compagnie
      if (DISPLAY_DOUBLE_TAXE == 'false') {
        $this->SetY(-25);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(INVOICE_RGB);
        $this->Cell(0, 10, utf8_decode(CLICSHOPPING::getDef('entry_info_societe', ['shop_code_capital' => SHOP_CODE_CAPITAL, 'shop_code_rcs' => SHOP_CODE_RCS, 'shop_code_ape' => SHOP_CODE_APE])), 0, 0, 'C');

        $this->SetY(-20);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(INVOICE_RGB);
        $this->Cell(0, 10, utf8_decode(CLICSHOPPING::getDef('entry_info_societe_next', ['tva_shop_intracom' => TVA_SHOP_INTRACOM])), 0, 0, 'C');
      } else {
        $this->SetY(-25);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(INVOICE_RGB);
        $this->Cell(0, 10, utf8_decode(CLICSHOPPING::getDef('entry_info_societe1', ['shop_code_capital' => SHOP_CODE_CAPITAL, 'shop_code_rcs' => SHOP_CODE_RCS, 'shop_code_ape' => SHOP_CODE_APE])), 0, 0, 'C');

        $this->SetY(-20);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(INVOICE_RGB);
        $this->Cell(0, 10, utf8_decode(CLICSHOPPING::getDef('entry_info_societe_next1', ['tva_shop_provincial' => TVA_SHOP_PROVINCIAL, 'tva_shop_federal' => TVA_SHOP_FEDERAL])), 0, 0, 'C');
      }


// Autres informations (champ libre) sur la compagnie
      $this->SetY(-15);
      $this->SetFont('Arial', '', 8);
      $this->SetTextColor(INVOICE_RGB);
      $this->Cell(0, 10, utf8_decode(SHOP_DIVERS), 0, 0, 'C');
    }
  }