<?php include ("../../../pdo/dbconfig.php");
// Save notification preference controller
if(isset($_POST["action"]) && $_POST["action"] == "save_notif_pref"){
    if(isset($_POST["data"])){
        $notificationData = $_POST["data"];

        $preference["sms"] = $notificationData["sms"];
        $preference["email"] = $notificationData["email"];
        $preference["voice"] =  $notificationData["voice"];

        $preference["notify_when_type"] = $notificationData["notify_when_type"];
        $preference["notify_when"] = $notificationData["notify_when"];

        echo $DB_calendar->updateNotifs($_POST["event_id"], $preference);
    }
}
?>