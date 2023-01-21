<?php
include_once ("../../../pdo/dbconfig.php");

if(true) {
	if (isset($_POST['create_event'])) {
		$building_id = $_POST['building_id'];
        $event_category = $_POST['event_category'];
		$event_name = $_POST['event_name'];
		$event_contact = $_POST['event_contact'];
		$event_contact_number=$_POST['contact_number'];

		// Notification $vars
		$notify_preference = $_POST['notification'];
        $notification_preference_values = array("sms" => 0, "email"=> 0, "voice" => 0);
        $notify_when_type = $_POST['notification_when_type']; // Possible values : Days, weeks , months, years
        $notify_when = $_POST['notification_when']; // Possible value : a valid number

        if($notify_preference) {
            foreach ($notify_preference as $notif) {
                if ($notif == "sms_notif") {
                    $notification_preference_values["sms"] = 1;
                }
                if ($notif == "email_notif") {
                    $notification_preference_values["email"] = 1;
                }
                if ($notif == "voice_notif") {
                    $notification_preference_values["voice"] = 1;
                }
            }
        }

		if(isset($_POST['is_regular'])){
		    $event_type="regular";
            $event_date = date('Y-m-d', strtotime(str_replace('/','-',$_POST['regular_start_date'])));
            $event_frequency_type = $_POST['regular_frequency'];
            $event_frequency = 1;
        }
        else{
            $event_type = "oncall";
		    $event_date= date('Y-m-d', strtotime(str_replace('/','-',$_POST['event_date'])));
            $event_frequency = 0;
            $event_frequency_type = null;
        }
        $event_info = $_POST['event_info'];
		$event_created_by=$_POST['employee_id'];
        $principals_assigned=array();
        if(isset($_POST['principals_assigned']))
            $principals_assigned=$_POST['principals_assigned'];
        $created_date=date("Y-m-d");

        //create event
		$event_id = $DB_calendar->create_office_maintenance_event($building_id,$event_category,$event_name,$event_date,$event_contact,$event_contact_number,$event_created_by,$event_frequency,$event_frequency_type,$event_type,$event_info,$created_date);

		// Notification code : Sharan
        // Set the notification preference : SMS, Voice, Email based on the selection
        $notificationEventDetails = array("event_id" => $event_id,
                                            "type"=> $event_type,
                                            "category" => $event_category,
                                            "frequency"=> $event_frequency,
                                            "date" => $event_date,
                                            "notify_when_type" => $notify_when_type,
                                            "notify_when" => $notify_when
                                    );

        $DB_calendar->setNotificationPreference($notificationEventDetails, $notification_preference_values);

		//if other principal
        if(isset($_POST['is_other_principal'])){
            $other_principal_name=$_POST['other_principal_name'];
            $other_principal_telephone=$_POST['other_principal_telephone'];
            $other_principal_email=$_POST['other_principal_email'];
            $principal_id=null;

            $r=$DB_calendar->get_principal_id($other_principal_name,$other_principal_email,$other_principal_telephone,$building_id);
            if(sizeof($r)>0)
                $principal_id=$r['id'];

            if($principal_id==null)//not exist before
                $principal_id=$DB_calendar->create_principal($building_id,$other_principal_name,$other_principal_email,$other_principal_telephone);

            //assign to other principal
            $DB_calendar->assign_event($event_id,null,null,null,null,$principal_id);
        }

        //assign event to staff
        for($i=0;$i<sizeof($principals_assigned);$i++){
            $DB_calendar->assign_event($event_id,$principals_assigned[$i],null,null,null,null);
        }

        if($event_id!=0) {
			header('Location: ../../event-list.php'."?building_id=$building_id"."&direct=inner_events");

			//notification
            include_once "sendSMSEmail.php";

            //self-stuff
            for($i=0;$i<sizeof($principals_assigned);$i++){
                $person_info=$DB_calendar->get_employee_info($principals_assigned[$i]);
                //sms
                $SMS_message="Dear ".$person_info['full_name'].",\nYou have been assigned an office event in Ilivein.xyz.\nTo more detailed, please check your calendar in https://www.spgmanagement.com/admin";
                SendSMS($telephone,$SMS_message);
                //email
                $email_sbj="Ilivein.xyz - You received an office event in Ilivein.xyz";
                $email_content="Dear".$customer_name.',';
                $email_content.='<p><b>You have been assigned an office event in Ilivein.xyz</b></p>';
                $email_content.='<p>Event Detail:</p>';
                $email_content.='<p>Event Name : '.$event_name.'</p>';
                $email_content.='<p>Event Time : '.$event_date.'</p>';
                $email_content.='<p>Event Information : '.$event_info.'</p>';
                $email_content.='<p>Best regards,</p><a href="https://www.spgmanagement.com/admin">Ilivein.xyz</a>';
                SendEmail('info@ilivein.xyz','Info - ILivein.xyz',$person_info['email'],$person_info['full_name'],$email_sbj,$email_content);
            }

            //other-third party
            if(isset($_POST['is_other_principal'])){
                //sms
                $SMS_message="Dear ".$other_principal_name.",\nYou have received an event in Ilivein.xyz. To more detailed, please contact with ".$event_contact."(telephone:".$event_contact_number.").";
                SendSMS($other_principal_name,$SMS_message);
                //email
                $email_sbj="Ilivein.xyz - You received an event in Ilivein.xyz";
                $email_content="Dear".$other_principal_name.',';
                $email_content.='<p><b>You have received an event in Ilivein.xyz</b></p>';
                $email_content.='<p>Task Detail:</p>';
                $email_content.='<p>Event Name : '.$event_name.'</p>';
                $email_content.='<p>Event Time : '.$event_date.'</p>';
                $email_content.='<p>Event Information : '.$event_info.'</p>';
                $email_content.='<p>Best regards,</p><a href="https://www.spgmanagement.com/admin">Ilivein.xyz</a>';
                SendEmail('info@ilivein.xyz','Info - ILivein.xyz',$other_principal_email,$other_principal_name,$email_sbj,$email_content);
            }
        }
		else {
			echo "Error!";
		}
	}

	else if (isset($_POST['delete_event'])) {
		$event_id = $_POST['event_id'];
		$building_id=$_POST['building_id'];
		$result = $DB_calendar->delete_office_maintenance_event($event_id);
		if($result) {
			header('Location: ../../event-list.php'."?building_id=$building_id".'&direct=inner_events');
		}
		else {
			echo "Error!";
		}
	}

	else if (isset($_POST['details_event'])) {

		$event_id = $_POST['event_id'];
		$building_id=$_POST['building_id'];

		header('Location: ../../office-event-details.php?event_id='.$event_id.'&building_id='.$building_id);

	}

	else if (isset($_POST['update_event'])) {
		$event_id = $_POST['event_id'];
		$building_id=$_POST['building_id'];

		$event_name = $_POST['event_name'];
        $contact_person = $_POST['contact_person'];
		$contact_number=$_POST['contact_number'];

        // Notification $vars
        $notify_preference = $_POST['notification'];
        $notification_preference_values = array("sms" => 0, "email"=> 0, "voice" => 0);
        $notify_when_type = $_POST['notification_when_type']; // Possible values : Days, weeks , months, years
        $notify_when = $_POST['notification_when']; // Possible value : a valid number

        if($notify_preference){
            foreach($notify_preference as $notif){
                if($notif == "sms_notif"){
                    $notification_preference_values["sms"] = 1;
                }
                if($notif == "email_notif"){
                    $notification_preference_values["email"] = 1;
                }
                if($notif == "voice_notif"){
                    $notification_preference_values["voice"] = 1;
                }
            }
        }

		if(isset($_POST['is_regular'])){
            $event_date=$_POST['regular_start_date'];
		    $event_type="regular";
            $event_frequency_type=$_POST['regular_frequency'];
            $event_frequency=1;
		}else{
            $event_date=$_POST['event_date'];
            $event_type="oncall";
            $event_frequency_type=null;
            $event_frequency=0;
        }
		$event_info = $_POST['event_info'];
        $principals_assigned=array();
        $principals_assigned=$_POST['principals_assigned'];

		$result = $DB_calendar->update_office_maintenance_event($event_id,$event_name,$event_date,$contact_person,$contact_number,$event_frequency,$event_frequency_type,$event_type,$event_info);

        // Notification code : Sharan
        // Set the notification preference : SMS, Voice, Email based on the selection
        $notificationEventDetails = array("event_id" => $event_id,
            "frequency"=> $event_frequency,
            "date" => $event_date,
            "notify_when_type" => $notify_when_type,
            "notify_when" => $notify_when
        );
        $DB_calendar->updateNotificationPreference($notificationEventDetails, $notification_preference_values);

		//update event's all assigntos
        $flag =false;

        if($result)
            $result_1=$DB_calendar->delete_event_all_assigntos($event_id);

        if($result_1){
            //assign to staff
            for($i=0;$i<sizeof($principals_assigned);$i++){
                $DB_calendar->assign_event($event_id,$principals_assigned[$i],null,null,null,null);
            }

            //if other principal,means third-part
            if(isset($_POST['is_other_principal'])){
                $other_principal_name=$_POST['other_principal_name'];
                $other_principal_telephone=$_POST['other_principal_telephone'];
                $other_principal_email=$_POST['other_principal_email'];
                $principal_id=null;

                $r=$DB_calendar->get_principal_id($other_principal_name,$other_principal_email,$other_principal_telephone,$building_id);
                if(sizeof($r)>0)
                    $principal_id=$r['id'];

                if($principal_id==null) //not exist before
                    $principal_id=$DB_calendar->create_principal($building_id,$other_principal_name,$other_principal_email,$other_principal_telephone);

                //assign to other principal
                $DB_calendar->assign_event($event_id,null,null,null,null,$principal_id);
            }

            $flag=true;
        }

		if($flag) {
			header('Location: ../../office-event-details.php?event_id='.$event_id.'&building_id='.$building_id);
		}
		else {
			echo "Error!";
		}
	}

	else if (isset($_POST['upload_file']) && $_FILES["file_to_upload"]["name"] != null) {

		$event_id = $_POST['event_id'];
		$building_id = $_POST['building_id'];
		$upload_date = date('Y-m-d H:i:s');
		$upload_name = basename($_FILES["file_to_upload"]["name"]);
		$upload_id = $DB_calendar->upload($event_id, $upload_name, $upload_date);

		if(!is_null($upload_id)) {
			$target_dir = "uploads/" . $upload_id . "_" . $event_id . "_" . $upload_name;
			move_uploaded_file($_FILES["file_to_upload"]["tmp_name"], $target_dir);
			header('Location: ../../office-event-details.php?event_id=' . $event_id . '&building_id='.$building_id);
		}
		else {
			echo "Error!";
		}

	}

	else if (isset($_POST['delete_upload'])) {
		$event_id = $_POST['event_id'];
		$building_id = $_POST['building_id'];
		$upload_id = $_POST['upload_id'];
		$upload_name = $_POST['upload_name'];
		$result = $DB_calendar->delete_upload($upload_id);

		if($result) {
			$upload_url = "./uploads/" . $upload_id . "_" . $event_id . "_" . $upload_name;
			unlink($upload_url);
			header('Location: ../../office-event-details.php?event_id=' . $event_id .'&building_id='.$building_id);
		}
		else {
			echo "Error!";
		}
	}
}