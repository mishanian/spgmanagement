<?php

if(true){

    if(isset($_POST['download_receipt'])){
        $kijiji_payment_id = $_POST['$kijiji_payment_id'];
        download_receipt($kijiji_payment_id);
    }

    else if(isset($_GET['download_kijiji_receipt'])){
        $kijiji_payment_id = $_GET['download_kijiji_receipt'];
        download_receipt($kijiji_payment_id);
    }

}


function download_receipt($kijiji_payment_id){
    include_once("../../../pdo/dbconfig.php");
    $info = $DB_kijiji->get_payment_info_for_receipt($kijiji_payment_id);
    $inovice_number = $info['inovice_number'];
    $employee_name = $info['employee_name'];
    $buy_slots_count = $info['buy_slots_count'];
    $buy_slots_price = $info['buy_slots_price'];
    $payment_time = $info['payment_time'];
    $slots_due_time = $info['slots_due_time'];
    $payment_amount =$info['payment_amount'];
    $C_F_amount = $info['C_F_amount'];
    $total_amount = $info['total_amount'];

    require_once('fpdf/fpdf.php');

    $receipt = new FPDF();
    $receipt->AddPage();
    $receipt->SetAutoPageBreak(true,0);
    $receipt->SetTopMargin(15);
    $x=$receipt->GetX();
    $y=$receipt->GetY();
    $receipt->Image('logo.png');

    $receipt->SetXY($x,$y);
    $receipt->SetFont('Arial','B',17);
    $receipt->SetTextColor(39,64,139);
    $receipt->Cell(190,15,"Receipt",0,1,'C');
    $y=$receipt->GetY()+15;

    //table
    $receipt->SetXY($x+15,$y);
    $receipt->SetTextColor(54,54,54);
    $receipt->SetFont('Arial','',11);

    $receipt->SetX($x+15);
    $receipt->Cell(110,10,'From : spgmanagement.com',0,1,'L');

    $receipt->SetX($x+15);
    $receipt->Cell(45,10,"Invoice Number",1,0,'C');
    $receipt->Cell(110,10,$inovice_number,1,1,'C');

    $receipt->SetX($x+15);
    $receipt->Cell(45,10,"Price($/slot/mon.)",1,0,'C');
    $receipt->Cell(110,10,$buy_slots_price,1,1,'C');

    $receipt->SetX($x+15);
    $receipt->Cell(45,10,"Slots Count",1,0,'C');
    $receipt->Cell(110,10,$buy_slots_count,1,1,'C');

    $receipt->SetX($x+15);
    $receipt->Cell(45,10,"Slot Due Time",1,0,'C');
    $receipt->Cell(110,10,$slots_due_time,1,1,'C');

    $receipt->SetX($x+15);
    $receipt->Cell(45,10,"Payment Time",1,0,'C');
    $receipt->Cell(110,10,$payment_time,1,1,'C');

    $receipt->SetX($x+15);
    $receipt->Cell(45,10,"Amount",1,0,'C');
    $receipt->Cell(110,10,number_format($payment_amount,2).'CAD',1,1,'C');

    $receipt->SetX($x+15);
    $receipt->Cell(45,10,"Convenience Fee",1,0,'C');
    $receipt->Cell(110,10,number_format($C_F_amount,2).'CAD',1,1,'C');

    $receipt->SetX($x+15);
    $receipt->Cell(45,10,"Total amount",1,0,'C');
    $receipt->Cell(110,10,number_format($total_amount,2).' CAD',1,1,'C');


    //footer
    $receipt->SetY($y+225);
    $receipt->SetTextColor(207,207,207);
    $receipt->SetFont('Arial','B',8);
    $receipt->Cell(190,6,'spgmanagement.com -- 100-1650 boul. Rene Levesque West, Montreal, QC, H3H 2S1',0,1,'C');
    $receipt->SetFont('Arial','',7);
    $receipt->Cell(190,5,'© 2019 SPGManagement. All rights reserved. All trademarks, trade names, service marks and logos referenced herein belong to their respective companies.',0,1,'C');
    $receipt->Output('D','receipt_SPGManagement.pdf');
    exit;
}