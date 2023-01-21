<?php
    
    function request_signature_on_lease(
        $lessor_email,
        $lessor_name,
        $lessee_1_email,
        $lessee_1_name,
        $lessee_2_email,
        $lessee_2_name,
        $documentName,
        $documentFileName){

        $more_lessee=false;
        if($lessee_2_email!=="" && $lessee_2_name!="")
            $more_lessee=true;


        // Set Authentication information
        // Set via a config file or just set here using constants.
        $email = "tianen.chen2016@gmail.com";  // your account email.
        $password = "For338838";      // your account password
        $integratorKey = "72823bd8-0db8-41e1-a5c7-435a2c30e5eb"; // your account integrator key, found on (Preferences -> API page)
        // api service point
        $url = "https://demo.docusign.net/restapi/v2/login_information"; // change for production

        // construct the authentication header:
        $header = "<DocuSignCredentials><Username>" . $email . "</Username><Password>" . $password . "</Password><IntegratorKey>" . $integratorKey . "</IntegratorKey></DocuSignCredentials>";


        // STEP 1 - Login (to retrieve baseUrl and accountId)
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-DocuSign-Authentication: $header"));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($status != 200) {
            return array('ok' => false, 'errMsg' => "Error calling DocuSign, status is: " . $status);
        }
        $response = json_decode($json_response, true);
        $accountId = $response["loginAccounts"][0]["accountId"];
        $baseUrl = $response["loginAccounts"][0]["baseUrl"];
        curl_close($curl);


        // STEP 2 - Create and send envelope with one recipient, one tab, and one document
        // the following envelope request body will place 1 signature tab on the document, located
        // 100 pixels to the right and 100 pixels down from the top left of the document
        $data=null;
        if(!$more_lessee) {
            $data = array(
                "emailSubject" => "Lease - Please sign " . $documentName,
                "documents" => array(
                    array("documentId" => "1", "name" => $documentName)
                ),
                "recipients" => array(
                    "signers" => array(
                        array(
                            "email" => $lessor_email,
                            "name" => $lessor_name,
                            "recipientId" => "1",
                            "routingOrder" => "1",
                            "tabs" => array(
                                "signHereTabs" => array(
                                    array(
                                        "anchorString" => "lessor1_name",
                                        "anchorXOffset" => "0",
                                        "anchorYOffset" => "10"
                                    )
                                ),
                                "initialHereTabs" => array(
                                    array(
                                        "anchorString" => "lessor1_init",
                                        "anchorXOffset" => "0",
                                        "anchorYOffset" => "11"
                                    )
                                ),
                                "dateTabs"=> array(
                                    array(
                                        "anchorString"=>"lessor_sign_date",
                                        "anchorXOffset"=>"10",
                                        "anchorYOffset"=>"-3",
                                        "width"=>"20",
                                        "bold"=>true
                                    )
                                )
                            )
                        ),
                        array(
                            "email" => "$lessee_1_email",
                            "name" => "$lessee_1_name",
                            "recipientId" => "2",
                            "routingOrder" => "2",
                            "tabs" => array(
                                "signHereTabs" => array(
                                    array(
                                        "anchorString" => "lessee1_name",
                                        "anchorXOffset" => "0",
                                        "anchorYOffset" => "10",
                                        "documentId" => "1"
                                    )
                                ),
                                "initialHereTabs" => array(
                                    array(
                                        "anchorString" => "lessee1_init",
                                        "anchorXOffset" => "0",
                                        "anchorYOffset" => "11"
                                    )
                                ),
                                "dateTabs"=> array(
                                    array(
                                        "anchorString"=>"lessee1_sign_date",
                                        "anchorXOffset"=>"10",
                                        "anchorYOffset"=>"-3",
                                        "width"=>"20",
                                        "bold"=>true
                                    )
                                )
                            )
                        )
                    )
                ),
                "status" => "sent"
            );
        }else{
            $data = array(
                "emailSubject" => "Lease - Please sign " . $documentName,
                "documents" => array(
                    array("documentId" => "1", "name" => $documentName)
                ),
                "recipients" => array(
                    "signers" => array(
                        array(
                            "email" => $lessor_email,
                            "name" => $lessor_name,
                            "recipientId" => "1",
                            "routingOrder" => "1",
                            "tabs" => array(
                                "signHereTabs" => array(
                                    array(
                                        "anchorString" => "lessor1_name",
                                        "anchorXOffset" => "0",
                                        "anchorYOffset" => "10",
                                    )
                                ),
                                "initialHereTabs" => array(
                                    array(
                                        "anchorString" => "lessor1_init",
                                        "anchorXOffset" => "0",
                                        "anchorYOffset" => "11",
                                    )
                                ),
                                "dateTabs"=> array(
                                    array(
                                        "anchorString"=>"lessor_sign_date",
                                        "anchorXOffset"=>"10",
                                        "anchorYOffset"=>"-3",
                                        "width"=>"20",
                                        "bold"=>true
                                    )
                                )
                            )
                        ),
                        array(
                            "email" => "$lessee_1_email",
                            "name" => "$lessee_1_name",
                            "recipientId" => "2",
                            "routingOrder" => "2",
                            "tabs" => array(
                                "signHereTabs" => array(
                                    array(
                                        "anchorString" => "lessee1_name",
                                        "anchorXOffset" => "0",
                                        "anchorYOffset" => "10",
                                        "documentId" => "1"
                                    )
                                ),
                                "initialHereTabs" => array(
                                    array(
                                        "anchorString" => "lessee1_init",
                                        "anchorXOffset" => "0",
                                        "anchorYOffset" => "11",
                                    )
                                ),
                                "dateTabs"=> array(
                                    array(
                                        "anchorString"=>"lessee1_sign_date",
                                        "anchorXOffset"=>"10",
                                        "anchorYOffset"=>"-3",
                                        "width"=>"20",
                                        "bold"=>true
                                    )
                                )
                            )
                        ),
                        array(
                            "email" => "$lessee_2_email",
                            "name" => "$lessee_2_name",
                            "recipientId" => "3",
                            "routingOrder" => "3",
                            "tabs" => array(
                                "signHereTabs" => array(
                                    array(
                                        "anchorString" => "lessee2_name",
                                        "anchorXOffset" => "0",
                                        "anchorYOffset" => "10",
                                        "documentId" => "1"
                                    )
                                ),
                                "initialHereTabs" => array(
                                    array(
                                        "anchorString" => "lessee2_init",
                                        "anchorXOffset" => "0",
                                        "anchorYOffset" => "11",
                                    )
                                ),
                                "dateTabs"=> array(
                                    array(
                                        "anchorString"=>"lessee2_sign_date",
                                        "anchorXOffset"=>"10",
                                        "anchorYOffset"=>"-3",
                                        "width"=>"20",
                                        "bold"=>true
                                    )
                                )
                            )
                        )
                    )
                ),
                "status" => "sent"
            );
        }
        $data_string = json_encode($data);
        $file_contents = file_get_contents($documentFileName);

        // Create a multi-part request. First the form data, then the file content
        $requestBody =
            "\r\n"
            . "\r\n"
            . "--myboundary\r\n"
            . "Content-Type: application/json\r\n"
            . "Content-Disposition: form-data\r\n"
            . "\r\n"
            . "$data_string\r\n"
            . "--myboundary\r\n"
            . "Content-Type:application/pdf\r\n"
            . "Content-Disposition: file; filename=\"$documentName\"; documentid=1 \r\n"
            . "\r\n"
            . "$file_contents\r\n"
            . "--myboundary--\r\n"
            . "\r\n";

        // Send to the /envelopes end point, which is relative to the baseUrl received above.
        $curl = curl_init($baseUrl . "/envelopes");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: multipart/form-data;boundary=myboundary',
                'Content-Length: ' . strlen($requestBody),
                "X-DocuSign-Authentication: $header")
        );
        $json_response = curl_exec($curl); // Do it!
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($status != 201) {
            echo "Error calling DocuSign, status is:" . $status . "\nerror text: ";
            print_r($json_response);
            echo "\n";
            exit(-1);
        }
        $response = json_decode($json_response, true);
        $envelopeId = $response["envelopeId"];

        return array(
            'ok' => true,
            'envelopeId' => $envelopeId,
            'accountId' => $accountId,
            'baseUrl' => $baseUrl
        );
    } // end of function request_signature_on_a_document
?>