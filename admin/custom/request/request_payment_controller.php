    <?php
    include_once('../../../pdo/dbconfig.php');

    if (isset($_POST["action"]) && !empty($_POST["action"])) {

        // Upload the invoice attachments to the server - send back the uploaded file path
        if ($_POST["action"] == "upload_invoice") {
            if (!empty($_FILES['upload_inv'])) {
                $img_file = $_FILES['upload_inv'];
                if ($img_file['error'] <= 0) {
                    $file_name = $img_file['name'];
                    while (file_exists("../../files/requests/" . $file_name)) {
                        $file_name = str_replace(".", "_a.", $file_name);
                    }
                    $file_tmp_name = $img_file['tmp_name'];
                    move_uploaded_file($file_tmp_name, "../../files/requests/" . $file_name);
                    $img_path = $file_name;
                    echo json_encode(array("result" => true, "name" => $img_path));
                } else {
                    echo json_encode(array("result" => false));
                }
            }
        }

        if ($_POST["action"] == "update_payment") {
            $data = $_POST["data"];
            $request_id = $_POST["requestid"];
            $vendor_id = $_POST["vendorid"];
            parse_str($data, $payment_details);

            // Payment details - INSERT into table
            $DB_vendor->setPaymentDetails($payment_details, $request_id, $vendor_id);
        }

        if ($_POST["action"] == "getPaymentDetails") {
            $request_id = $_POST["request_id"];

            // get the estimated price from the requesT_infos table for the request_id given
            $estimatedPrice = $DB_request->getEstimatedPrice($request_id);
            echo json_encode(array("estimated" => $estimatedPrice["vendor_estimated_price"], "value" => $DB_vendor->getPaymentDetails($request_id)));
        }

        if ($_POST["action"] == "approve_payment_details") {
            $request_id = $_POST["requestid"];
            $comment = $_POST["comment"];
            $amount = $_POST["amount"];
            echo json_encode($DB_vendor->approvePayment($request_id, $comment, $amount));
        }

        // Check if a vendor is assigned the the request id specified
        if ($_POST["action"] == "check_assigned") {
            // Get the assigned recipients for the request
            $request_id = $_POST["request_id"];
            $assignedUsers = $DB_request->get_assigned_recipients($request_id);
            $allVendorIds = $DB_vendor->getVendorsId();
            $allVendorIds = explode(",", $allVendorIds["vendors"]);
            $vendorCount = 0;

            $vendors = array();
            foreach ($assignedUsers as $assignedUser) {
                if (in_array($assignedUser["user_id"], $allVendorIds)) {
                    $assignedUser["name"] = $DB_vendor->getVendorName($assignedUsers["user_id"]);
                    $assignedUser["wage"] = $DB_vendor->getVendorWage($assignedUsers["user_id"]);
                    array_push($vendors, $assignedUser);
                }
            }

            if (count($vendors) > 0) {
                echo json_encode(array("result" => true, value => $vendors));
            } else {
                echo json_encode(array("result" => false));
            }
        }
    }