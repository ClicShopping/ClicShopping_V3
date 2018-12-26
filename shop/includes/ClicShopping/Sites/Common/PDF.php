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

  namespace ClicShopping\Sites\Common;

  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class PDF extends FPDF {

    private $pdf;

    private static function getGlobalPdf() {
      global $pdf;

      return $pdf;
    }

    public function roundedRect($x, $y, $w, $h,$r, $style = '') {
      $k = $this->k;
      $hp = $this->h;

      if ($style=='F') {
        $op='f';
      } else if ($style=='FD' || $style=='DF') {
        $op='B';
      } else {
        $op='S';
      }

      $MyArc = 4/3 * (sqrt(2) - 1);
      $this->_out(sprintf('%.2f %.2f m',($x+$r)*$k,($hp-$y)*$k ));
      $xc = $x+$w-$r;
      $yc = $y+$r;
      $this->_out(sprintf('%.2f %.2f l', $xc*$k,($hp-$y)*$k ));
      $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);
      $xc = $x+$w-$r;
      $yc = $y+$h-$r;
      $this->_out(sprintf('%.2f %.2f l',($x+$w)*$k,($hp-$yc)*$k));
      $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);
      $xc = $x+$r;
      $yc = $y+$h-$r;
      $this->_out(sprintf('%.2f %.2f l',$xc*$k,($hp-($y+$h))*$k));
      $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);
      $xc = $x+$r;
      $yc = $y+$r;
      $this->_out(sprintf('%.2f %.2f l',($x)*$k,($hp-$yc)*$k ));
      $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
      $this->_out($op);
    }

    public function _Arc($x1, $y1, $x2, $y2, $x3, $y3) {
      $h = $this->h;
      $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c ', $x1*$this->k, ($h-$y1)*$this->k,
      $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
    }

    public function Header() {
      $CLICSHOPPING_Template = Registry::get('Template');

// Logo
      if (file_exists(CLICSHOPPING::getConfig('dir_root', 'Shop') . $CLICSHOPPING_Template->getDirectoryTemplateImages() . 'logos/invoice/'. INVOICE_LOGO)) {
        $this->Image(CLICSHOPPING::getConfig('http_server', 'Shop')  . $CLICSHOPPING_Template->getDirectoryShopTemplateImages() . 'logos/invoice/'. INVOICE_LOGO, 5, 10, 50);
      }

// Nom de la compagnie
        $this->SetX(0);
        $this->SetY(10);
        $this->SetFont('Arial','B',10);
        $this->SetTextColor(INVOICE_RGB);
        $this->Ln(0);
        $this->Cell(125);
        $this->MultiCell(100, 3.5, utf8_decode(STORE_NAME),0,'L');

// Adresse de la compagnie
        $this->SetX(0);
        $this->SetY(15);
        $this->SetFont('Arial','',8);
        $this->SetTextColor(INVOICE_RGB);
        $this->Ln(0);
        $this->Cell(125);
        $this->MultiCell(100, 3.5, utf8_decode(STORE_NAME_ADDRESS),0,'L');

// Email
        $this->SetX(0);
        $this->SetY(30);
        $this->SetFont('Arial','',8);
        $this->SetTextColor(INVOICE_RGB);
        $this->Ln(0);
        $this->Cell(-3);
        $this->MultiCell(100, 3.5, utf8_decode(CLICSHOPPING::getDef('entry_email')) . ' ' . STORE_OWNER_EMAIL_ADDRESS,0,'L');

// Website
        $this->SetX(0);
        $this->SetY(34);
        $this->SetFont('Arial','',8);
        $this->SetTextColor(INVOICE_RGB);
        $this->Ln(0);
        $this->Cell(-3);
        $this->MultiCell(100, 3.5, CLICSHOPPING::getDef('entry_http_site') . ' ' . HTTP::typeUrlDomain(),0,'L');

    }

    public function Footer() {
// Remerciement
      $this->SetY(-55);
      $this->SetFont('Arial','B',8);
      $this->SetTextColor(INVOICE_RGB);
      $this->Cell(0,10, utf8_decode(CLICSHOPPING::getDef('thank_you_customer')), 0,0,'C');

// Proprieties Legal
        $this->SetY(-45);
        $this->SetFont('Arial','',7);
        $this->SetTextColor(INVOICE_RGB);
        $this->Cell(0,10, utf8_decode(CLICSHOPPING::getDef('reserve_propriete')), 0,0,'C');

        $this->SetY(-40);
        $this->SetFont('Arial','',7);
        $this->SetTextColor(INVOICE_RGB);
        $this->Cell(0,10, utf8_decode(CLICSHOPPING::getDef('reserve_propriete_next')), 0,0,'C');

        $this->SetY(-35);
        $this->SetFont('Arial','',7);
        $this->SetTextColor(INVOICE_RGB);
        $this->Cell(0,10, utf8_decode(CLICSHOPPING::getDef('reserve_propriete_next1')), 0,0,'C');

// Informations de la compagnie
        $this->SetY(-25);
        $this->SetFont('Arial','',8);
        $this->SetTextColor(INVOICE_RGB);
        $this->Cell(0,10, utf8_decode(CLICSHOPPING::getDef('entry_info_societe')), 0,0,'C');

        $this->SetY(-20);
        $this->SetFont('Arial','',8);
        $this->SetTextColor(INVOICE_RGB);
        $this->Cell(0,10, utf8_decode(CLICSHOPPING::getDef('entry_info_societe_next')), 0,0,'C');

// Autres informations (champ libre) sur la compagnie
        $this->SetY(-15);
        $this->SetFont('Arial','',8);
        $this->SetTextColor(INVOICE_RGB);
        $this->Cell(0,10, utf8_decode(CLICSHOPPING::getDef('shop_divers')), 0,0,'C');
    }

/***************************************************
 Catalog
****************************************************/



// Création entête du tableau des produits pour les factures
    /*
     * output_table_heading
     */
    public function outputTableHeadingPdf($Y_Fields_Name_position){
      $pdf = static::getGlobalPdf();

      $pdf->SetFillColor(245);
      $pdf->SetFont('Arial','B',8);
      $pdf->SetY($Y_Fields_Name_position);
      $pdf->SetX(6);
      $pdf->Cell(9,6, utf8_decode(CLICSHOPPING::getDef('table_heading_qte')), 1, 0, 'C', 1);
      $pdf->SetX(15);
      $pdf->Cell(27,6, utf8_decode(CLICSHOPPING::getDef('table_heading_products_model')), 1, 0, 'C', 1);
      $pdf->SetX(40);
      $pdf->Cell(103,6,CLICSHOPPING::getDef('table_heading_products'), 1, 0, 'C', 1);
      $pdf->SetX(143);
      $pdf->Cell(15,6,CLICSHOPPING::getDef('table_heading_tax'), 1, 0, 'C', 1);
      $pdf->SetX(158);
      $pdf->Cell(20,6,CLICSHOPPING::getDef('table_heading_price_excluding_tax'), 1, 0, 'C', 1);
      /*
          $pdf->SetX(138);
          $pdf->Cell(20,6,TABLE_HEADING_PRICE_INCLUDING_TAX, 1, 0, 'C', 1);
      */
      $pdf->SetX(178);
      $pdf->Cell(20,6,CLICSHOPPING::getDef('table_heading_total_excluding_tax'), 1, 0, 'C', 1);
      /*
          $pdf->SetX(178);
          $pdf->Cell(20,6,TABLE_HEADING_TOTAL_INCLUDING_TAX, 1, 0, 'C', 1);
      */
      $pdf->Ln();
    }

// Création entête du tableau des produits pour les bons de livraison
//  output_table_heading_packingslip
    public function outputTableHeadingPackingslip($Y_Fields_Name_position){
      $pdf = static::getGlobalPdf();

      $pdf->SetFillColor(245);
      $pdf->SetFont('Arial','B',8);
      $pdf->SetY($Y_Fields_Name_position);
      $pdf->SetX(6);
      $pdf->Cell(14,6,utf8_decode(CLICSHOPPING::getDef('table_heading_qte')), 1, 0, 'C', 1);
      $pdf->SetX(20);
      $pdf->Cell(40,6,utf8_decode(CLICSHOPPING::getDef('table_heading_products_model')), 1, 0, 'C', 1);
      $pdf->SetX(60);
      $pdf->Cell(138,6,CLICSHOPPING::getDef('table_heading_products'), 1, 0, 'C', 1);
      $pdf->Ln();
    }

// output_table_suppliers
    public function outputTableSuppliers($Y_Fields_Name_position){
      $pdf = $this->pdf;

      $pdf->SetFillColor(245);
      $pdf->SetFont('Arial','B',8);
      $pdf->SetY($Y_Fields_Name_position);
      $pdf->SetX(6);
      $pdf->Cell(9,6,utf8_decode(CLICSHOPPING::getDef('table_heading_qte')), 1, 0, 'C', 1);
      $pdf->SetX(15);
      $pdf->Cell(27,6,utf8_decode(CLICSHOPPING::getDef('table_heading_products_model')), 1, 0, 'C', 1);
      $pdf->SetX(40);
      $pdf->Cell(78,6,CLICSHOPPING::getDef('table_heading_products'), 1, 0, 'C', 1);
      $pdf->SetX(105);
      $pdf->Cell(45,6,CLICSHOPPING::getDef('table_heading_options'), 1, 0, 'C', 1);
      $pdf->SetX(150);
      $pdf->Cell(45,6,CLICSHOPPING::getDef('values'), 1, 0, 'C', 1);

      $pdf->Ln();
    }

//output_table_customers_suppliers
//
    public function outputTableCustomersSuppliers($Y_Fields_Name_position){
      $pdf = static::getGlobalPdf();

      $pdf->SetFillColor(245);
      $pdf->SetFont('Arial','B',8);
      $pdf->SetY($Y_Fields_Name_position);
      $pdf->SetX(6);
      $pdf->Cell(9,6,utf8_decode(CLICSHOPPING::getDef('table_heading_qte')), 1, 0, 'C', 1);
      $pdf->SetX(15);
      $pdf->Cell(13,6,CLICSHOPPING::getDef('table_heading_customers_id'), 1, 0, 'C', 1);
      $pdf->SetX(28);
      $pdf->Cell(25,6,CLICSHOPPING::getDef('table_heading_customers_name'), 1, 0, 'C', 1);
      $pdf->SetX(53);
      $pdf->Cell(30,6,utf8_decode(CLICSHOPPING::getDef('table_heading_products_model')), 1, 0, 'C', 1);
      $pdf->SetX(83);
      $pdf->Cell(60,6,CLICSHOPPING::getDef('table_heading_products'), 1, 0, 'C', 1);
      $pdf->SetX(143);
      $pdf->Cell(40,6,CLICSHOPPING::getDef('table_heading_options'), 1, 0, 'C', 1);
      $pdf->SetX(183);
      $pdf->Cell(20,6,CLICSHOPPING::getDef('values'), 1, 0, 'C', 1);

      $pdf->Ln();
    }
  }