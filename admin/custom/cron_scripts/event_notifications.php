<?php
/* * *
 * CRON script to send notifications to the events users
 * based on the event_notification table data.
 * * */
if (strpos(getcwd(), "cron_scripts") == false) {
    $path = "../../pdo/";
} else {
    $path = "../../../pdo/";
}
$file = $path . 'dbconfig.php';
include_once($file);

include_once("sendSMSEmail.php");

$preferences = $DB_calendar->getAllNotificationPreferences();

// Iterate over all the notifications for events and send the notif when the dates match
foreach ($preferences as $notification) {
    $eventId = $notification["event_id"]; // Event ID for the notification row
    $eventDate =  $notification["event_date"];

    // Get the type of the notification to send ; possible options : email,sms,voice
    // Get the WHEN TO SEND notification value ; possible options :
    // Get the WHEN TO SEND TYPE notification value ; possible options : days,months, weeks,year
    $notification_when_type = $notification["notify_when_type"];
    $notification_when_type_value = $notification["notify_when"];

    // Based on the "notification_when_type" - compute the SQL
    $notifTypeValueForSQL = strtoupper($notification_when_type);
    $date_to_send_notification = "SELECT DATE_SUB(event_date, INTERVAL $notification_when_type_value $notifTypeValueForSQL) AS notification_date FROM event_notifications WHERE event_id = $eventId";

    $todayDate = date("Y-m-d");

    // If Today's date is not equal to the notification date - do not send any notification
    if (strtotime($todayDate) !== strtotime($eventDate)) {
        return;
    }

    //*** Process the below code only if the notification date matches the date today ***//

    // Based on the type of Notification - Call the method to send event notification
    // Get the event Details to send in the Notification
    $eventDetails = $DB_calendar->get_office_maintenance_event_detail($eventId);
    $notification_text = "This is a reminder message for your event : '$eventDetails[event_name]' on date $eventDetails[event_date].";

    // Send SMS with the notification text above
    if (intval($notification["sms_notify"]) == 1) {
        $telephone = $eventDetails["contact_number"];
        if ($telephone && !is_null($telephone)) {
            $formattedTelephoneNumber  = formalize_telephone($telephone);
            MySendSMS($formattedTelephoneNumber, $notification_text);
        }
    }

    // Send EMAIL with the notification text above
    if ($notification["email_notify"] == 1) {
        $assignedToDetails = $DB_calendar->get_event_assigntos_details($eventId);
        MySendEmail("info@mgmgmt.ca", "Info - spgmanagement.com", $assignedToDetails["email"], $assignedToDetails["full_name"], "Event - Reminder", $notification_text);
        //        SendEmail("info@mgmgmt.ca", "Info - spgmanagement.com" , "chitta.sharan@gmail.com" , $assignedToDetails["full_name"] , "Event - Reminder" , $notification_text);
    }

    if ($notification["voice_notify"] == 1) {
        // Do nothing as of now
    }
}

function formalize_telephone($original_tele)
{
    $formal_tele = trim($original_tele);
    $formal_tele = str_replace(' ', '', $formal_tele);
    $formal_tele = str_replace('-', '', $formal_tele);
    if (strlen($formal_tele) == 10)
        $formal_tele = '1' . $formal_tele;
    return $formal_tele;
}