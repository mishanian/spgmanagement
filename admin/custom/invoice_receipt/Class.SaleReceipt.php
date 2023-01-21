<?php

class SaleReceipt {
	private $sale_payment_id;
	private $payment_time;
	private $payment_amount;
	private $C_F_amount;
	private $invoice_number;
	private $employee_name;
	private $employee_email;
	private $total_amount;
	private $payment_method;
	private $payment_type;


	private $payment_type_switch;
	//paypal
	private $paypal_id;
	private $paypal_status;
	private $p_payer_id;
	//moneris (interac)
	private $issuer_name;
	private $issuer_confirm;
	private $trans_name;
	private $response_code;
	private $interac_bank_transaction_id;
	private $interac_bank_approval_code;
	//moneris (credit card)
	private $credit_card;
	private $credit_card_no;
	private $moneris_bank_transaction_id;
	private $moneris_bank_approval_code;


	public function __construct($sale_payment_id) {

		if (strpos(getcwd(), "admin") != false) {
			$path = "..";
		}
		if (strpos(getcwd(), "custom") != false) {
			$path = "../..";
		}
		if (strpos(getcwd(), "invoice_receipt") != false) {
			$path = "../../..";
		}

		require($path . "/pdo/dbconfig.php");

		$this->sale_payment_id = $sale_payment_id;

		$info = $DB_payment->get_sale_payment_info_by_invoice_number("SALE-" . $sale_payment_id);

		$table_payment_id     = $info['id'];
		$this->invoice_number = $info['invoice_number'];

		$user_id              = $info['employee_id'];
		$employee_info        = $DB_payment->get_user_info($user_id);
		$this->employee_name  = $employee_info['full_name'];
		$this->employee_email = $employee_info['email'];

		$this->payment_amount = $info['payment_amount'];
		$this->C_F_amount     = $info['C_F_amount'];
		$this->total_amount   = $info['total_amount'];
		$this->payment_time   = $info['payment_time'];

		$payment_type_info    = $DB_payment->get_sale_payment_method_type($table_payment_id);
		$this->payment_method = $payment_type_info['payment_method'];
		$this->payment_type   = $payment_type_info['payment_type'];

		if (!is_null($info['transactions_paypal_id'])) {
			$paypal_transaction_info   = $DB_payment->get_paypal_transaction_info($info['transactions_paypal_id']);
			$this->paypal_id           = $paypal_transaction_info['p_id'];
			$this->paypal_status       = $paypal_transaction_info['p_state'];
			$this->p_payer_id          = $paypal_transaction_info['p_payer_id'];
			$this->payment_type_switch = 0;
		}
		elseif (!is_null(trim($info['transactions_moneris_id'])) && strlen(trim($info['transactions_moneris_id'])) > 0 && $info['transactions_moneris_id'] != 0) {
			$moneris_transaction_info = $DB_payment->get_moneris_transaction_info($info['transactions_moneris_id']);

			if (!is_null($moneris_transaction_info['issuer_name'])) {
				$this->issuer_name                 = $moneris_transaction_info['issuer_name'];
				$this->issuer_confirm              = $moneris_transaction_info['issuer_confirm'];
				$this->trans_name                  = $moneris_transaction_info['trans_name'];
				$this->response_code               = $moneris_transaction_info['response_code'];
				$this->interac_bank_transaction_id = $moneris_transaction_info['bank_transaction_id'];
				$this->interac_bank_approval_code  = $moneris_transaction_info['bank_approval_code'];
				$this->payment_type_switch         = 1;
			}
			else {
				$this->credit_card                 = $moneris_transaction_info['creditcard'];
				$this->credit_card_no              = $moneris_transaction_info['creditcard_no'];
				$this->moneris_bank_approval_code  = $moneris_transaction_info['bank_approval_code'];
				$this->moneris_bank_transaction_id = $moneris_transaction_info['bank_transaction_id'];
				$this->payment_type_switch         = 2;
			}
		}
		else {
			/* Cash */
			$this->payment_type_switch = 3;
		}
	}

	function download_receipt() {

//		echo "<pre>";
//		print_r(get_object_vars($this));
//		exit;

		require_once('fpdf/fpdf.php');
		$receipt = new FPDF();
		$receipt->AddPage();
		$receipt->SetAutoPageBreak(true, 0);
		$receipt->SetTopMargin(15);
		$x = $receipt->GetX();
		$y = $receipt->GetY();
		$receipt->Image('../images/receipt_logo.jpg');

		$receipt->SetXY($x, $y);
		$receipt->SetFont('Arial', 'B', 17);
		$receipt->SetTextColor(39, 64, 139);
		$receipt->Cell(190, 15, "Receipt", 0, 1, 'C');
		$y = $receipt->GetY() + 15;

		//table
		$receipt->SetXY($x + 15, $y);
		$receipt->SetTextColor(54, 54, 54);
		$receipt->SetFont('Arial', '', 11);

		$receipt->SetX($x + 15);
		$receipt->Cell(110, 10, 'From : spgmanagement.com', 0, 1, 'L');

		$receipt->SetX($x + 15);
		$receipt->Cell(45, 10, "Order No.", 1, 0, 'C');
		$receipt->Cell(110, 10, $this->invoice_number, 1, 1, 'C');

		$reference_no = 'SALE-' . $this->sale_payment_id;

		$receipt->SetX($x + 15);
		$receipt->Cell(45, 10, "Amount", 1, 0, 'C');
		$receipt->Cell(110, 10, '$' . number_format($this->payment_amount, 2) . 'CAD', 1, 1, 'C');

		$receipt->SetX($x + 15);
		$receipt->Cell(45, 10, "Service Charge", 1, 0, 'C');
		$receipt->Cell(110, 10, '$' . number_format($this->C_F_amount, 2) . 'CAD', 1, 1, 'C');

		$receipt->SetX($x + 15);
		$receipt->Cell(45, 10, "Total Paid Amount", 1, 0, 'C');
		$receipt->Cell(110, 10, '$' . number_format($this->total_amount, 2) . 'CAD', 1, 1, 'C');

		$receipt->SetX($x + 15);
		$receipt->Cell(45, 10, "Payment Type", 1, 0, 'C');
		$receipt->Cell(110, 10, $this->payment_type, 1, 1, 'C');

		$receipt->SetX($x + 15);
		$receipt->Cell(45, 10, "Payment Method", 1, 0, 'C');
		$receipt->Cell(110, 10, $this->payment_method, 1, 1, 'C');

		if(strlen($this->payment_time) < 1){
			$this->payment_time = "-";
		}
		$receipt->SetX($x + 15);
		$receipt->Cell(45, 10, "Processed Time", 1, 0, 'C');
		$receipt->Cell(110, 10, $this->payment_time, 1, 1, 'C');

		if ($this->payment_type_switch != 3) {    // show only if not cash
			$receipt->SetX($x + 15);
			$receipt->Cell(155, 10, str_repeat(' ', 8) . "Transaction Details", 1, 1, 'L');
		}

		if ($this->payment_type_switch == 0) {    //paypal
			$receipt->SetX($x + 15);
			$receipt->Cell(45, 10, "Paypay ID", 1, 0, 'C');
			$receipt->Cell(110, 10, $this->paypal_id, 1, 1, 'C');

			$receipt->SetX($x + 15);
			$receipt->Cell(45, 10, "Transaction Status", 1, 0, 'C');
			$receipt->Cell(110, 10, $this->paypal_status, 1, 1, 'C');

			$receipt->SetX($x + 15);
			$receipt->Cell(45, 10, "Paypay Payer ID", 1, 0, 'C');
			$receipt->Cell(110, 10, $this->p_payer_id, 1, 1, 'C');
		}
		else if ($this->payment_type_switch == 1) {      //interac
			$receipt->SetX($x + 15);
			$receipt->Cell(45, 10, "Issuer Name", 1, 0, 'C');
			$receipt->Cell(110, 10, $this->issuer_name, 1, 1, 'C');

			$receipt->SetX($x + 15);
			$receipt->Cell(45, 10, "Issuer Confirmation", 1, 0, 'C');
			$receipt->Cell(110, 10, $this->issuer_confirm, 1, 1, 'C');


			$trans_name_arr = explode('_', $this->trans_name);
			$card_type      = '';
			if ($trans_name_arr[0] == 'idebit')
				$card_type = 'Debit';
			$trans_type = ucwords($trans_name_arr[1]);

			$receipt->SetX($x + 15);
			$receipt->Cell(45, 10, "Card Type", 1, 0, 'C');
			$receipt->Cell(110, 10, $card_type, 1, 1, 'C');

			$receipt->SetX($x + 15);
			$receipt->Cell(45, 10, "Transaction Type", 1, 0, 'C');
			$receipt->Cell(110, 10, $trans_type, 1, 1, 'C');

			$receipt->SetX($x + 15);
			$receipt->Cell(45, 10, "Response Code", 1, 0, 'C');
			$receipt->Cell(110, 10, $this->response_code, 1, 1, 'C');

			$receipt->SetX($x + 15);
			$receipt->Cell(45, 10, "Bank Tranaction No", 1, 0, 'C');
			$receipt->Cell(110, 10, $this->interac_bank_transaction_id, 1, 1, 'C');

			$receipt->SetX($x + 15);
			$receipt->Cell(45, 10, "Bank Approval Code", 1, 0, 'C');
			$receipt->Cell(110, 10, $this->interac_bank_approval_code, 1, 1, 'C');
		}
		else if ($this->payment_type_switch == 2) {      //moneris credit card
			$receipt->SetX($x + 15);
			$receipt->Cell(45, 10, "Credit Card", 1, 0, 'C');
			$receipt->Cell(110, 10, $this->credit_card, 1, 1, 'C');

			$receipt->SetX($x + 15);
			$receipt->Cell(45, 10, "Card No", 1, 0, 'C');
			$receipt->Cell(110, 10, $this->credit_card_no, 1, 1, 'C');

			$receipt->SetX($x + 15);
			$receipt->Cell(45, 10, "Bank Tranaction No", 1, 0, 'C');
			$receipt->Cell(110, 10, $this->moneris_bank_transaction_id, 1, 1, 'C');

			$receipt->SetX($x + 15);
			$receipt->Cell(45, 10, "Bank Approval Code", 1, 0, 'C');
			$receipt->Cell(110, 10, $this->moneris_bank_approval_code, 1, 1, 'C');
		}

		//footer
		$receipt->SetY($y + 225);
		$receipt->SetTextColor(207, 207, 207);
		$receipt->SetFont('Arial', 'B', 8);
		$receipt->Cell(190, 6, 'spgmanagement.com -- 100-1650 boul. Rene Levesque West, Montreal, QC, H3H 2S1', 0, 1, 'C');
		$receipt->SetFont('Arial', '', 7);
		$receipt->Cell(190, 5, '2019 SPGManagement. All rights reserved. All trademarks, trade names, service marks and logos referenced herein belong to their respective companies.', 0, 1, 'C');
//		$receipt->Output('D', 'receipt_SPGManagement.pdf');
		$receipt->Output();
		exit;
	}

}