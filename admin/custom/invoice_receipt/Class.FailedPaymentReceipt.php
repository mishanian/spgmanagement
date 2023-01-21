<?php
/**
 * User: t.e.chen
 * Date: 2018-01-16
 */

class FailedPaymentReceipt{

    private $order_id;
    private $response_code;
    private $response_message;
    private $trans_amount;
    private $timestamp;
    private $reference_number;


    public function __construct($order_id,$reference_no,$response_code,$response_message,$trans_amount){
        $this->order_id = $order_id;
        $this->reference_number = $reference_no;
        $this->response_code = $response_code;
        $this->response_message =$response_message;
        $this->trans_amount = $trans_amount;
        $this->timestamp = date('Y-m-d H:i:s');
    }

    public function failed_payment_receipt_download(){

        if (strpos(getcwd(), "admin") != false){
            $path="custom/invoice_receipt/";
        }
        if (strpos(getcwd(), "custom") != false){
            $path="invoice_receipt/";
        }
        if (strpos(getcwd(), "invoice_receipt") != false){
            $path="";
        }
        require_once($path."fpdf/fpdf.php");

        $receipt = new FPDF();
        $receipt->AddPage();
        $receipt->SetAutoPageBreak(true,0);
        $receipt->SetTopMargin(15);
        $x=$receipt->GetX();
        $y=$receipt->GetY();
        $receipt->Image('../images/receipt_logo.jpg');

        $receipt->SetXY($x,$y+15);
        $receipt->SetFont('Arial','B',17);
        $receipt->SetTextColor(39,64,139);
        $receipt->Cell(190,15,"Receipt",0,1,'C');
        $y=$receipt->GetY()+15;


        $receipt->SetTextColor(54,54,54);
        $receipt->SetFont('Arial','',11);


        $receipt->SetX($x+15);
        $receipt->Cell(45,10,"Order ID",1,0,'C');
        $receipt->Cell(110,10,$this->order_id,1,1,'C');


        $receipt->SetX($x+15);
        $receipt->Cell(45,10,"Reference Number",1,0,'C');
        $receipt->Cell(110,10,$this->reference_number,1,1,'C');


        $receipt->SetX($x+15);
        $receipt->Cell(45,10,"Transaction Amount",1,0,'C');
        $receipt->Cell(110,10,'$'.number_format(round($this->trans_amount,2),2).' CAD',1,1,'C');


        $receipt->SetX($x+15);
        $receipt->Cell(45,10,"Payment Status",1,0,'C');
        $receipt->Cell(110,10,'FAILED',1,1,'C');


        $receipt->SetX($x+15);
        $receipt->Cell(45,10,"Response Code",1,0,'C');
        $receipt->Cell(110,10,$this->response_code,1,1,'C');

        $receipt->SetX($x+15);
        if(strlen($this->response_message)<=40){
            $receipt->Cell(45,10,"Response Message",1,0,'C');
            $receipt->Cell(110,10,$this->response_message,1,1,'C');
        }else{
            $y = $receipt->GetY();
            $receipt->Cell(45,14,"Response Message",1,0,'C');
            $receipt->MultiCell(110,7,$this->response_message,1);
            $receipt->SetY($y+14);
        }

        $receipt->SetX($x+15);
        $receipt->Cell(45,10,"Comments",1,0,'C');
        $receipt->SetFont('Arial','',9);
        $receipt->Cell(110,10,"The account was not charged (if it was charged, please contact with us).",1,1,'C');

        $receipt->SetX($x+15);
        $receipt->SetFont('Arial','',11);
        $receipt->Cell(45,10,"Payment Time",1,0,'C');
        $receipt->Cell(110,10,$this->timestamp,1,1,'C');


        //footer
        $receipt->SetY($y+170);
        $receipt->SetTextColor(207,207,207);
        $receipt->SetFont('Arial','B',8);
        $receipt->Cell(190,6,'spgmanagement.com -- 100-1650 boul. Rene Levesque West, Montreal, QC, H3H 2S1',0,1,'C');
        $receipt->SetFont('Arial','',7);
        $receipt->Cell(190,5,'2019 SPGManagement. All rights reserved. All trademarks, trade names, service marks and logos referenced herein belong to their respective companies.',0,1,'C');

        $receipt->Output('D','receipt_SPGManagement.pdf');
        exit;
    }

}