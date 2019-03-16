<?php
namespace App\models;

use App\core\Mypdf as Mypdf;
use App\core\Model as Model;
use DateTime;

class VoucherModel extends Model{

    public static function sendVoucher($clientName, $quantity, $days, $arriveDate, $departureDate)
    {
        // create new PDF document
        $pdf = new Mypdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        //============================================================+
        // File name   : example_004.php
        // Begin       : 2008-03-04
        // Last Update : 2013-05-14
        //
        // Description : Example 004 for TCPDF class
        //               Cell stretching
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
         * @abstract TCPDF - Example: Cell stretching
         * @author Nicola Asuni
         * @since 2008-03-04
         */


        // create new PDF document
        $pdf = new Mypdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Nicola Asuni');
        $pdf->SetTitle('TCPDF Example 004');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

        // set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 004', PDF_HEADER_STRING);

        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        // ---------------------------------------------------------

        // set font
        $pdf->SetFont('times', '', 12);

        // add a page
        $pdf->AddPage();

        //Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')


        // set font
        $pdf->SetFont('times', '', 22);
        // test Cell stretching
        $pdf->Ln(5);
        $pdf->Cell(0, 0, 'VOUCHER', 0, 1, 'C', 0, '', 1);

        // set font
        $pdf->SetFont('times', '', 14);
        $pdf->Ln(2);
        $dateNow = new DateTime('NOW');
        $dateNow = $dateNow->format('d/m/Y');
        $pdf->Cell(0, 0, 'DATA DI EMISSIONE/DATE OF ISSUE :' . $dateNow, 0, 1, 'C', 0, '', 1);
        $pdf->Ln(2);
        $pdf->Cell(0, 0, 'NOME/NAME :' . $clientName, 0, 1, 'L', 0, '', 1);
        $pdf->Cell(0, 0, 'DATA POSTEGGIO / PARKING DATE :', 0, 1, 'L', 0, '', 1);
        $pdf->Cell(0, 0, 'IN : ..' . $arriveDate . '.. TIME : ...............', 0, 1, 'L', 0, '', 1);
        $pdf->Cell(0, 0, 'OUT: ..' . $departureDate . '.. TIME : ...............', 0, 1, 'L', 0, '', 1);
        $pdf->Cell(0, 0, 'TOTALE GIORNI / NUMBER OF DAYS :'. $days, 0, 1, 'L', 0, '', 1);
        $pdf->Cell(0, 0, 'AUTO/CAR : ............... TARGA/NUMBER OF PLATS : ...............', 0, 1, 'L', 0, '', 1);
        $pdf->Cell(0, 0, 'GARAGE CENTRAL – VIA CUMBO BORGIA, 60 - MILAZZO (ME)', 0, 1, 'L', 0, '', 1);
        $pdf->Cell(0, 0, 'TEL: 090 9287423 CELL.: 333 2580363 – CELL.: 334 2885335 -    ', 0, 1, 'L', 0, '', 1);
        $pdf->Ln(5);
        // Stretching, position and alignment example
// Image method signature:
// Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)

        $pdf->SetXY(10, 110);
        $pdf->Image(K_PATH_IMAGES.'central-maps.png', '', '', 190, 140, '', '', 'T', false, 300, '', false, false, 1, false, false, false);
        // add a page
        $pdf->AddPage();
        $pdf->Ln(8);
        // test some inline CSS
        $html = '<p>
        <br />
        a) La Clarissa Viaggi under Delegation of the "CLIENT", book the private car parking in the indicated GARAGE. This garage has all the requirements of the Law.<br /><br />
        b) The parking fee has to be paid to Clarissa Viaggi which has the PROXY of the customer and will issue the invoice.<br /><br />
        c) The customer in delivering the vehicle to the GARAGE, must point out all the conditions of the car and must ask another receipt. It is recommended that the CLIENT in the withdrawal of the vehicle, to check the condition of the vehicle CAREFULLY that are the same as delivery.<br /><br />
        d) La Clarissa Viaggi declines all responsibility for any claims made by the customer. Any complaint must be made against the PARKING where the vehicle has been assigned for secure parking.
        <br /><br />
        <span style="color: rgb(255, 0, 0);">
        e) The daily parking means 24 hours. After it must be paid € 1,00 for each hour (if you exceeded 24 hours).
        </span>
        <br /><br />
        f) No refund in case the customer decides to withdraw the car before.
        </p>';

        $pdf->writeHTML($html, true, false, true, false, '');


        // ---------------------------------------------------------

        //Close and output PDF document
        $pdf->Output(APP.'/uploads/vouchers/voucher_'.$clientName.'_central.pdf', 'f');
        return $pdf->Output('/uploads/vouchers/voucher_'.$clientName.'_central.pdf', 'I');

        //============================================================+
        // END OF FILE
        //============================================================+
        }
        public static function sendVoucherFerrari($clientName, $quantity, $days, $arriveDate, $departureDate)
        {
            // create new PDF document
            $pdf = new Mypdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            //============================================================+
            // File name   : example_004.php
            // Begin       : 2008-03-04
            // Last Update : 2013-05-14
            //
            // Description : Example 004 for TCPDF class
            //               Cell stretching
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
             * @abstract TCPDF - Example: Cell stretching
             * @author Nicola Asuni
             * @since 2008-03-04
             */


            // create new PDF document
            $pdf = new Mypdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Nicola Asuni');
            $pdf->SetTitle('TCPDF Example 004');
            $pdf->SetSubject('TCPDF Tutorial');
            $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

            // set default header data
            $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 004', PDF_HEADER_STRING);

            // set header and footer fonts
            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

            // set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

            // set margins
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

            // set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

            // set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            // set some language-dependent strings (optional)
            if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
                require_once(dirname(__FILE__).'/lang/eng.php');
                $pdf->setLanguageArray($l);
            }

            // ---------------------------------------------------------

            // set font
            $pdf->SetFont('times', '', 12);

            // add a page
            $pdf->AddPage();

            //Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')


            // set font
            $pdf->SetFont('times', '', 22);
            // test Cell stretching
            $pdf->Ln(5);
            $pdf->Cell(0, 0, 'VOUCHER', 0, 1, 'C', 0, '', 1);

            // set font
            $pdf->SetFont('times', '', 14);
            $pdf->Ln(2);
            $dateNow = new DateTime('NOW');
            $dateNow = $dateNow->format('d/m/Y');
            $pdf->Cell(0, 0, 'DATA DI EMISSIONE/DATE OF ISSUE :' . $dateNow, 0, 1, 'C', 0, '', 1);
            $pdf->Ln(2);
            $pdf->Cell(0, 0, 'NOME/NAME :' . $clientName, 0, 1, 'L', 0, '', 1);
            $pdf->Cell(0, 0, 'DATA POSTEGGIO / PARKING DATE :', 0, 1, 'L', 0, '', 1);
            $pdf->Cell(0, 0, 'IN : ..' . $arriveDate . '.. TIME : ...............', 0, 1, 'L', 0, '', 1);
            $pdf->Cell(0, 0, 'OUT: ..' . $departureDate . '.. TIME : ...............', 0, 1, 'L', 0, '', 1);
            $pdf->Cell(0, 0, 'TOTALE GIORNI / NUMBER OF DAYS :'. dirname(__FILE__), 0, 1, 'L', 0, '', 1);
            $pdf->Cell(0, 0, 'AUTO/CAR : ............... TARGA/NUMBER OF PLATS : ...............', 0, 1, 'L', 0, '', 1);
            $pdf->Cell(0, 0, 'GARAGE FERRARI – VIA TENENTE MINNITI, 77 - MILAZZO (ME)', 0, 1, 'L', 0, '', 1);
            $pdf->Cell(0, 0, 'TEL: 349.2893184 – 349.5826580 http://www.garageferrarimilazzo.com/   ', 0, 1, 'L', 0, '', 1);
            $pdf->Ln(5);
            // Stretching, position and alignment example
    // Image method signature:
    // Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)

            $pdf->SetXY(10, 110);
            $pdf->Image(K_PATH_IMAGES.'ferrari-maps.jpg', '', '', 190, 100, '', '', 'T', false, 300, '', false, false, 1, false, false, false);
            // add a page
            $pdf->AddPage();
            $pdf->Ln(8);
            // test some inline CSS
            $html = '<p>
            <br />
            a) La Clarissa Viaggi under Delegation of the "CLIENT", book the private car parking in the indicated GARAGE. This garage has all the requirements of the Law.<br /><br />
            b) The parking fee has to be paid to Clarissa Viaggi which has the PROXY of the customer and will issue the invoice.<br /><br />
            c) The customer in delivering the vehicle to the GARAGE, must point out all the conditions of the car and must ask another receipt. It is recommended that the CLIENT in the withdrawal of the vehicle, to check the condition of the vehicle CAREFULLY that are the same as delivery.<br /><br />
            d) La Clarissa Viaggi declines all responsibility for any claims made by the customer. Any complaint must be made against the PARKING where the vehicle has been assigned for secure parking.
            <br /><br />
            <span style="color: rgb(255, 0, 0);">
            e) The daily parking means 24 hours. After it must be paid € 1,00 for each hour (if you exceeded 24 hours).
            </span>
            <br /><br />
            f) No refund in case the customer decides to withdraw the car before.
            </p>';

            $pdf->writeHTML($html, true, false, true, false, '');


            // ---------------------------------------------------------

            //Close and output PDF document
            $pdf->Output(APP.'/uploads/vouchers/voucher_'.$clientName.'_ferrari.pdf', 'f');
            return $pdf->Output(APP.'/uploads/vouchers/voucher_'.$clientName.'_ferrari.pdf', 'I');

            //============================================================+
            // END OF FILE
            //============================================================+
            }
}
