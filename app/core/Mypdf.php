<?php

namespace App\core;

use TCPDF as TCPDF;
//============================================================+
// File name   : example_003.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 003 for TCPDF class
//               Custom Header and Footer
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Custom Header and Footer
 * @author Nicola Asuni
 * @since 2008-03-04
 */

// Include the main TCPDF library (search for installation path).
//require_once('tcpdf_include.php');

// Extend the TCPDF class to create custom Header and Footer

class Mypdf extends TCPDF {

    //Page header
    public function Header() {
        // Logo
        $image_file = K_PATH_IMAGES.'clarissa-viaggi.png';
        $this->Image($image_file, 0, 0, 110, 30, 'PNG', '', 'C', false, 300, 'C', false, false, 0, false, false, false);
        // Set font
        $this->SetFont('helvetica', 'B', 22);
        // Title
        //$this->Cell(0, 0, 'Clarissa Viaggi', 0, 1, 'C', 0, '', 1, false, 'M', 'M');
        // Line break
      
    }


    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-25);
        // Set font
        $this->SetFont('helvetica', 'I', 8);         
        $this->Cell(0, 0, 'CLARISSA VIAGGI di Alacqua Gloria Veronica - Via Luigi Rizzo, 21 - 98057 Milazzo – ME - ITALIA', 0, 0, 'C');
        $this->Ln();         
        $this->Cell(0,0,'Tel. (+39) 0909240248 - Fax (+39) 0909243253 – W: www.clarissaviaggi.it – E: info@clarissaviaggi.it', 0, false, 'C', 0, '', 0, false, 'T', 'M');
        $this->Ln();         
        $this->Cell(0,0,'Licenza Cat. A/ILL DDS 1950/S9Tur/2011– Polizza R.C. 189669 Mondial Assistance', 0, false, 'C', 0, '', 0, false, 'T', 'M'); 
        $this->Ln(); 
        $this->Cell(0,0,'CCIAA Messina REA 218539 - P.Iva 03176160830 - CF LCQGRV90B59F158T', 0, false, 'C', 0, '', 0, false, 'T', 'M');                 
        $this->Ln();   
        $this->Ln();                     
        // Page number        
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'C', 'M');
        
    }
  
}