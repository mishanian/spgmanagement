<?

$payment = <<< RESPONSE
{
    "id": "PAY-3XE46392W5329073PLF7YYYA",
    "intent": "sale",
    "state": "approved",
    "cart": "9K143272HA1036158",
    "payer": {
        "payment_method": "paypal",
        "status": "VERIFIED",
        "payer_info": {
            "email": "mishanian-b@outlook.com",
            "first_name": "Mehran New",
            "last_name": "Buyer",
            "payer_id": "YR33JAWDWG746",
            "shipping_address": {
                "recipient_name": "Mehran New Buyer",
                "line1": "1 Maire-Victorin",
                "city": "Toronto",
                "state": "Ontario",
                "postal_code": "M5A 1E1",
                "country_code": "CA"
            },
            "phone": "6132858673",
            "country_code": "CA"
        }
    },
    "transactions": [
        {
            "amount": {
                "total": "5.00",
                "currency": "CAD",
                "details": {
                    "subtotal": "5.00"
                }
            },
            "payee": {
                "merchant_id": "VCMG9GSZWE4SA"
            },
            "description": "Rent Payment for: Glenview Court  (2050 DCR) - Unit: 2050-001 - Due Date: Sep 01, 2017",
            "invoice_number": "INV-Sep 01, 2017/1/2050-001/1",
            "item_list": {
                "items": [
                    {
                        "name": "Rent",
                        "sku": "5",
                        "price": "5.00",
                        "currency": "CAD",
                        "tax": "0.00",
                        "quantity": 1
                    }
                ],
                "shipping_address": {
                    "recipient_name": "Mehran New Buyer",
                    "line1": "1 Maire-Victorin",
                    "city": "Toronto",
                    "state": "Ontario",
                    "postal_code": "M5A 1E1",
                    "country_code": "CA"
                }
            },
            "related_resources": [
                {
                    "sale": {
                        "id": "1LA16762JR185281C",
                        "state": "completed",
                        "amount": {
                            "total": "5.00",
                            "currency": "CAD",
                            "details": {
                                "subtotal": "5.00"
                            }
                        },
                        "payment_mode": "INSTANT_TRANSFER",
                        "protection_eligibility": "ELIGIBLE",
                        "protection_eligibility_type": "ITEM_NOT_RECEIVED_ELIGIBLE,UNAUTHORIZED_PAYMENT_ELIGIBLE",
                        "transaction_fee": {
                            "value": "0.45",
                            "currency": "CAD"
                        },
                        "parent_payment": "PAY-3XE46392W5329073PLF7YYYA",
                        "create_time": "2017-07-31T20:00:42Z",
                        "update_time": "2017-07-31T20:00:42Z",
                        "links": [
                            {
                                "href": "https://api.sandbox.paypal.com/v1/payments/sale/1LA16762JR185281C",
                                "rel": "self",
                                "method": "GET"
                            },
                            {
                                "href": "https://api.sandbox.paypal.com/v1/payments/sale/1LA16762JR185281C/refund",
                                "rel": "refund",
                                "method": "POST"
                            },
                            {
                                "href": "https://api.sandbox.paypal.com/v1/payments/payment/PAY-3XE46392W5329073PLF7YYYA",
                                "rel": "parent_payment",
                                "method": "GET"
                            }
                        ]
                    }
                }
            ]
        }
    ],
    "create_time": "2017-07-31T20:00:42Z",
    "links": [
        {
            "href": "https://api.sandbox.paypal.com/v1/payments/payment/PAY-3XE46392W5329073PLF7YYYA",
            "rel": "self",
            "method": "GET"
        }
    ]
}
RESPONSE;

var_dump(json_decode($payment));
echo "\n<hr>";


$pp_response=json_decode($payment);
$pp_id=$pp_response->id;
$pp_state=$pp_response->state;
$pp_create_time=$pp_response->create_time;
$pp_invoice_number=$pp_response->transactions[0]->invoice_number;
$pp_description=$pp_response->transactions[0]->description;
$pp_payer_id=$pp_response->payer->payer_info->payer_id;
$pp_merchant_id=$pp_response->transactions[0]->payee->merchant_id;
$pp_total=$pp_response->transactions[0]->amount->total;
$pp_sku=$pp_response->transactions[0]->item_list->items[0]->sku;
echo "$pp_id<br>$pp_state<br>$pp_create_time<br>$pp_invoice_number<br>$pp_description<br>$pp_payer_id<br>$pp_merchant_id<br>$pp_total<br>$pp_sku";


?>