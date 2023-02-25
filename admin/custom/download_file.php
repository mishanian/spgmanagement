<?php
ignore_user_abort(true);
set_time_limit(0);
$path = "../files/bulletins/";
include_once('../../pdo/dbconfig.php');
$secret = 'Bulletin';
include_once('../../pdo/Class.Request.php');
$DB_Request = new Request($DB_con);
if (!isset($_GET['n'])) {
    $n = 0;
} else {
    $n = $_GET['n'];
}
if (isset($_GET['fid']) && preg_match('/^([a-f0-9]{32})$/', $_GET['fid'])) {
    $filenames = $DB_Request->getBulletin($secret, $_GET['fid']);
    // die("filenames=$filenames");
    if (!empty($filenames)) {
        $filenames = explode("|", $filenames);
        $filename = $filenames[$n];
        $fullPath = $path . $filename;
        if ($fd = fopen($fullPath, "r")) {
            $fsize = filesize($fullPath);
            $path_parts = pathinfo($fullPath);
            $ext = strtolower($path_parts["extension"]);


            switch ($ext) {
                case "pdf":
                    header("Content-type: application/pdf");
                    header("Content-Disposition: attachment; filename=\"" . $path_parts["basename"] . "\""); // use 'attachment' to force a file download
                    break;
                    // add more headers for other content types here
                default:
                    header("Content-type: application/octet-stream");
                    header("Content-Disposition: filename=\"" . $path_parts["basename"] . "\"");
                    break;
            }
            header("Content-length: $fsize");
            header("Cache-control: private"); //use this to open files directly

            while (!feof($fd)) {
                $buffer = fread($fd, 2048);
                echo $buffer;
            }
        }
        fclose($fd);

        //Update tenant History
        $user_id = $_GET['u'];
        $history_type_id = $_GET['h'];
        $table_id = $_GET['id'];
        if (!empty($_GET['e'])) {
            $user_email = $_GET['e'];
        } else {
            $user_email = "";
        }
        if (!empty($_GET['s'])) {
            $subject = $_GET['s'];
        } else {
            $subject = "";
        }
        if (!empty($_GET['f'])) {
            $file_name = $_GET['f'];
        } else {
            $file_name = "";
        }
        if (!empty($_GET['c'])) {
            $comments = $_GET['c'];
        } else {
            $comments = "";
        }

        include_once('GetClientData.php');
        $data = new GetDataPlugin();
        $client_data = array(
            $data->ip(), $data->os(), $data->browser(), $data->geo('country'),
            $data->geo('region'), $data->geo('continent'), $data->geo('city'), $data->agent(), $data->referer(),
            $data->height(), $data->width(), $data->javaenabled(), $data->cookieenabled(), $data->language(), $data->architecture(), $data->geo('logitude'), $data->geo('latitude'), $data->provetor(), $data->getdate()
        );
        $client_data = json_encode($client_data);


        $DB_Request->insertHistory($user_id, $history_type_id, $table_id, $user_email, $subject, $file_name, $comments, $client_data, '');
        //      die("Test2");
        exit;
    } else {
        die('no match');
    }
} else {
    die('missing file ID');
}
