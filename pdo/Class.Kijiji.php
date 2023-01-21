<?php

class Kijiji
{
    private $crud;

    public function __construct($DB_con)
    {
        $this->crud = new Crud($DB_con);
    }

    /**
     * To add one new record into kijiji_settings
     * kijiji_settings table keeps the config for property manager(kijiji_settings_id in employee_info table references id in this table)
    */
    private function new_kijiji_settings_for_employee($employee_id){
        try{
            $this->crud->query("INSERT INTO kijiji_settings (slots_number, feed_by_price, feed_by_value) VALUES ('0', '0', '0')");
            $this->crud->execute();
            $kijiji_setting_id=$this->crud->lastInsertId();

            $this->crud->query("UPDATE employee_infos SET kijiji_settings_id = :kijiji_settings_id WHERE employee_id = :employee_id");
            $this->crud->bind(":kijiji_settings_id",$kijiji_setting_id);
            $this->crud->bind(":employee_id",$employee_id);
            $this->crud->execute();

            return $kijiji_setting_id;

        }catch(PDOException $e) {echo $e->getMessage();}
    }

    /**
     * To get the employee's config about global kijiji settings
     * If the property manager is new, create a new default settings for him
    */
    public function get_kijiji_settings_by_employee($employee_id){
        try{
            $this->crud->query('SELECT kijiji_settings_id FROM employee_infos WHERE employee_infos.employee_id =:employee_id');
            $this->crud->bind(':employee_id', $employee_id);
            $kijiji_setting_id = $this->crud->resultSingle()['kijiji_settings_id'];

            if($kijiji_setting_id== 0){
                $kijiji_setting_id=$this->new_kijiji_settings_for_employee($employee_id);
            }

            $this->crud->query("SELECT * FROM kijiji_settings WHERE kijiji_settings.id = (SELECT kijiji_settings_id FROM employee_infos WHERE employee_infos.employee_id =:employee_id)");
            $this->crud->bind(':employee_id', $employee_id);
            $result = $this->crud->resultSingle();

            return $result;

        }catch(PDOException $e) {echo $e->getMessage();}
    }


    /**
     * get the building infos for the buildings in which the employee's company has
    */
    public function get_building_settings_by_employee($employee_id){
        try{
            $this->crud->query("SELECT * FROM building_infos WHERE employee_id IN (SELECT employee_id FROM employee_infos WHERE company_id = (SELECT company_id FROM employee_infos WHERE employee_id = :employee_id))");
            $this->crud->bind(":employee_id",$employee_id);
            $result = $this->crud->resultSet();

            return $result;

        }catch (PDOException $e) {echo $e->getMessage();}
    }


    /**
     * update the kijiji settings for the specific building
     * view to operate the building settings is above of candidate list
    */
    public function update_building_setting($building_id,$kijiji_listing_priority_type,$kijiji_prioritize_media,$kijiji_listing_diff_num_room,$kijiji_showed_units_count){
        try{
            $this->crud->query("UPDATE building_infos SET kijiji_listing_priority_type=:kijiji_listing_priority_type,kijiji_prioritize_media=:kijiji_prioritize_media,kijiji_listing_diff_num_room=:kijiji_listing_diff_num_room,kijiji_showed_units_count=:kijiji_showed_units_count WHERE building_infos.building_id = :building_id");
            $this->crud->bind(":kijiji_listing_priority_type",$kijiji_listing_priority_type);
            $this->crud->bind(":kijiji_prioritize_media",$kijiji_prioritize_media);
            $this->crud->bind(":kijiji_listing_diff_num_room",$kijiji_listing_diff_num_room);
            $this->crud->bind(":kijiji_showed_units_count",$kijiji_showed_units_count);
            $this->crud->bind(":building_id",$building_id);
            $this->crud->execute();
            return TRUE;
        }catch (PDOException $e) {echo $e->getMessage();}
    }


    /**
     * update the candidate status for all apartment belonging to the company where the employee in
     * Firstly : remove all status belonging to the company [kijiji_candidate = 0]
     * Then: set candidate status[kijiji_candidate = 1] for apartment that is satisfied the criteria now
    */
    public function update_candidates($employee_id){
        try{
            //delete all old candidates flag
            $this->crud->query("UPDATE apartment_infos SET kijiji_candidate=0 WHERE employee_id IN (SELECT employee_id FROM employee_infos WHERE company_id = (SELECT company_id FROM employee_infos WHERE employee_id = :employee_id))");
            $this->crud->bind(":employee_id",$employee_id);
            $this->crud->execute();

            //add all new candidates flag
            $new_candidates_list=$this->get_candidates_lists($employee_id);
            $new_candidates_ids=array();
            foreach ($new_candidates_list as $candidate){
                $apart_id=$candidate['apartment_id'];
                array_push($new_candidates_ids,$apart_id);
            }

            $in_criteria=' ( '.implode(',',$new_candidates_ids).' )';

            if(sizeof($new_candidates_ids)>0){
                $this->crud->query("UPDATE apartment_infos SET kijiji_candidate=1 WHERE apartment_id IN ".$in_criteria);
                $this->crud->execute();
            }
            return TRUE;
        }catch (PDOException $e) {echo $e->getMessage();}
    }


    /**
     * get the candidates list for the buildings in which the employee's company has
     * Firstly: get the building list and its building settings
     * Secondly: get the candidate satisfied with building settings rules
     * Thirdly: get the force list from apartment info table
     */
    public function get_candidates_lists($employee_id){

        $building_settings=$this->get_building_settings_by_employee($employee_id);
        $results=array();
        foreach ($building_settings as $building){
            $building_id=$building['building_id'];
            $kijiji_listing_priority_type=$building['kijiji_listing_priority_type'];
            $kijiji_prioritize_media=$building['kijiji_prioritize_media'];
            $kijiji_listing_diff_num_room=$building['kijiji_listing_diff_num_room'];
            $kijiji_showed_units_count=$building['kijiji_showed_units_count'];
            if($kijiji_showed_units_count!=0){

                //sub_candidates_lists only the units followed the setting rules / per building
                $temp = $this->get_sub_candidates_lists($building_id,$kijiji_listing_priority_type,$kijiji_prioritize_media,$kijiji_listing_diff_num_room,$kijiji_showed_units_count);
                for($i=0;$i<sizeof($temp);$i++){
                    array_push($results,$temp[$i]);
                }
            }

            // add force_posting units to candidates
            $temp1 = $this->get_sub_force_posting_list($building_id);
            for($i=0;$i<sizeof($temp1);$i++){
                array_push($results,$temp1[$i]);
            }

        }
        return $results;
    }

    /**
     * get the candidates list for the buildings in which the employee's company has, based on the building settings
     * Note: here only get the candidate satisfied with building settings rules
     */
    private function get_sub_candidates_lists($building_id,$listing_priority_type,$prioritize_media,$listing_diff_num_room,$showed_units_count){
        try{
            $order_strings=array();
            if($listing_priority_type=="lowest_price"){
                array_push($order_strings,"monthly_price ASC");
            }
            else if ($listing_priority_type="best_value"){
                array_push($order_strings,"(monthly_price/area) DESC");
            }

            if($prioritize_media=="1"){
                array_push($order_strings,"video DESC, pictures DESC , floor_plan DESC");
            }

            if($listing_diff_num_room=="1"){
                array_push($order_strings,"bedrooms ASC");
            }

            $order_by="";
            if(sizeof($order_strings)>0)
                $order_by=' ORDER BY '.implode(",",$order_strings);

            $limit="";
            if($showed_units_count!=9){
               $limit=" LIMIT $showed_units_count";
            }

            $this->crud->query("SELECT * FROM apartment_infos WHERE building_id=:building_id AND kijiji_force_list = 0 AND renovation_status = 4 AND apartment_status IN(5,7,8)  ".$order_by.$limit);
            $this->crud->bind(":building_id",$building_id);

            $results = $this->crud->resultSet();

            return $results;

        }catch (PDOException $e) {echo $e->getMessage();}
    }


    /**
     * get the candidates list for the buildings in which the employee's company has, based on the apartment settings
     * Note: here only get the candidate that is forced in apartment settings [apartment info table]
     */
    public function get_sub_force_posting_list($building_id){
        try{
            $this->crud->query(" SELECT * FROM apartment_infos WHERE kijiji_force_list= 1 AND building_id =:building_id");
            $this->crud->bind(':building_id',$building_id);
            $results = $this->crud->resultSet();
            return $results;

        }catch (PDOException $e){echo $e->getMessage();}

    }


    /**
     * save the general kijiji settings for the property manager.
     * View to operate this is in top of the kijiji page
     */
    public function save_kijiji_settings($employee_id,$feed_by_price,$feed_by_price_strategy,$feed_by_value,$feed_by_value_strategy,$feed_carousel){
        try{
            $this->crud->query("UPDATE kijiji_settings SET feed_by_price =:feed_by_price, feed_by_price_order_strategy=:feed_by_price_order_strategy,feed_by_value=:feed_by_value,feed_by_value_order_strategy=:feed_by_value_order_strategy,feed_carousel=:feed_carousel WHERE id = (SELECT kijiji_settings_id FROM employee_infos WHERE employee_id =:employee_id)");
            $this->crud->bind(":feed_by_price",$feed_by_price);
            $this->crud->bind(":feed_by_price_order_strategy",$feed_by_price_strategy);
            $this->crud->bind(":feed_by_value",$feed_by_value);
            $this->crud->bind(":feed_by_value_order_strategy",$feed_by_value_strategy);
            $this->crud->bind(":feed_carousel",$feed_carousel);
            $this->crud->bind(":employee_id",$employee_id);
            $this->crud->execute();
            return TRUE;

        }catch (PDOException $e) {echo $e->getMessage();}
    }


    /**
     * update the feeding status for the apartments that the employee manage
     * 1. based on the number of slot the employee has
     * 2. based on the kijiji general settings of the employee
    */
    public function update_feeding_list($employee_id,$slots_number,$feed_by_price,$feed_by_price_order_strategy,$feed_by_value,$feed_by_value_order_strategy){
        try{
            $order_by="";
            if($feed_by_price>0 && $feed_by_value==0){
                $order_by=" ORDER BY monthly_price ".$feed_by_price_order_strategy;
            }
            if($feed_by_price==0 && $feed_by_value>0){
                $order_by=" ORDER BY (monthly_price/area) ".$feed_by_value_order_strategy;
            }
            if($feed_by_price>0 && $feed_by_value>0){
                if($feed_by_price>$feed_by_value)
                    $order_by=" ORDER BY monthly_price ".$feed_by_price_order_strategy." , (monthly_price/area) ".$feed_by_value_order_strategy;
                else
                    $order_by=" ORDER BY (monthly_price/area) ".$feed_by_value_order_strategy." , monthly_price ".$feed_by_price_order_strategy;
            }

            $this->crud->query("UPDATE apartment_infos SET kijiji_feeding = 1 WHERE employee_id IN (SELECT employee_id FROM employee_infos WHERE company_id = (SELECT company_id FROM employee_infos WHERE employee_id = :employee_id)) AND kijiji_candidate = 1  ".$order_by." LIMIT :limit");
            $this->crud->bind(":employee_id",$employee_id);
            $this->crud->bind(":limit", intval($slots_number), PDO::PARAM_INT);
            $this->crud->execute();
            return TRUE;
        }catch (PDOException $e) {echo $e->getMessage();}
    }


    /**
     * remove all feeding status [kijiji_feeding = 0] for the apartments the employee manage
    */
    public function remove_all_feeding_list($employee_id){
        try{
            //delete all feeding list
            $this->crud->query("UPDATE apartment_infos SET kijiji_feeding = 0 WHERE employee_id IN (SELECT employee_id FROM employee_infos WHERE company_id = (SELECT company_id FROM employee_infos WHERE employee_id = :employee_id))");
            $this->crud->bind(":employee_id",$employee_id);
            $this->crud->execute();
            return TRUE;
        }catch (PDOException $e) {echo $e->getMessage();}
    }


    //------------------------------  front end query --------------------------------

    /**
     * the apartment info list of the candidates belonging to the employee
     * View to show is in candidate list in kijiji page
    */
    public function get_candidates_info($employee_id){
        try{
            $this->crud->query("SELECT building_infos.city AS city,building_infos.building_name AS building_name,apartment_infos.unit_number AS unit_number,apartment_types_sub.name AS apartment_types_sub_name,apartment_infos.monthly_price AS monthly_price,apartment_infos.pictures AS pictures,apartment_infos.video AS video,apartment_infos.floor_plan AS floor_plan, apartment_infos.kijiji_force_list AS if_force FROM  apartment_infos,apartment_types_sub,building_infos WHERE apartment_types_sub.id = apartment_infos.apartment_type_sub_id AND building_infos.building_id = apartment_infos.building_id AND apartment_infos.employee_id IN (SELECT employee_id FROM employee_infos WHERE company_id = (SELECT company_id FROM employee_infos WHERE employee_id = :employee_id))  AND apartment_infos.kijiji_candidate = 1");
            $this->crud->bind(":employee_id",$employee_id);
            $results=$this->crud->resultSet();
            return$results;
        }catch (PDOException $e) {echo $e->getMessage();}
    }


    /**
     * the apartment info list of the feeding apartments belonging to the employee
     * View to show is in feeding list in kijiji page
     */
    public function get_feeding_info($employee_id){
        try{
            $this->crud->query("SELECT building_infos.city AS city,building_infos.building_name AS building_name,apartment_infos.unit_number AS unit_number,apartment_types_sub.name AS apartment_types_sub_name,apartment_infos.monthly_price AS monthly_price,apartment_infos.pictures AS pictures,apartment_infos.video AS video,apartment_infos.floor_plan AS floor_plan ,apartment_infos.kijiji_force_list AS if_force FROM  apartment_infos,apartment_types_sub,building_infos WHERE apartment_types_sub.id = apartment_infos.apartment_type_sub_id AND building_infos.building_id = apartment_infos.building_id AND apartment_infos.employee_id IN (SELECT employee_id FROM employee_infos WHERE company_id = (SELECT company_id FROM employee_infos WHERE employee_id = :employee_id))  AND apartment_infos.kijiji_feeding = 1");
            $this->crud->bind(":employee_id",$employee_id);
            $results=$this->crud->resultSet();

            return $results;

        }catch (PDOException $e) {echo $e->getMessage();}
    }

    /**
     * get the number of total candidate apartment the employee has
     * Hint : candidate means the apartment that the employee want to post in kijiji
    */
    public function get_candidates_count($employee_id){
        try{
            $this->crud->query("SELECT count(*) AS count_number FROM apartment_infos WHERE kijiji_candidate = 1 AND employee_id IN (SELECT employee_id FROM employee_infos WHERE company_id = (SELECT company_id FROM employee_infos WHERE employee_id = :employee_id)) ");
            $this->crud->bind(":employee_id",$employee_id);
            $result=$this->crud->resultSingle();

            return $result['count_number'];

        }catch (PDOException $e) {echo $e->getMessage();}
    }

    /**
     * get the number of slot the employee has now
    */
    public function get_available_slots_number($employee_id){
        try{
            $this->crud->query("SELECT sum(buy_slots_count) as slots_count FROM kijiji_payments WHERE employee_id = :employee_id AND payment_status = 1  AND slots_due_time >= current_timestamp()");
            $this->crud->bind(":employee_id",$employee_id);
            $count = $this->crud->resultSingle()['slots_count'];
            $result = 0;
            if(!empty($count)){
                $result = $count;
            }
            return $result;
        }catch (PDOException $e) {echo $e->getMessage();}
    }

    /**
     * get the number of the employee
    */
    public function get_employee_name($employee_id){
        try{
            $this->crud->query("SELECT full_name FROM employee_infos WHERE employee_id = :employee_id ");
            $this->crud->bind(":employee_id",$employee_id);
            $result=$this->crud->resultSingle();

            return $result['full_name'];

        }catch (PDOException $e) {echo $e->getMessage();}
    }


    //------------------------------ feeding xml -------------------------------

    /**
     * get all building list (include building info) for the buildings in which there are feeding apartments in, from whole system
     * order by building id
    */
    public function get_all_kijiji_classified_by_building(){
        try{
            $this->crud->query("SELECT building_infos.building_id AS building_id,building_infos.building_name,building_infos.address,building_infos.province_id,building_infos.city,building_infos.postal_code,building_infos.public_comments, building_infos.feature_picture AS building_feature_pic,building_infos.pictures AS building_pic,employee_infos.mobile,employee_infos.email,company_infos.website,company_infos.logo,company_infos.sub_domain FROM apartment_infos,building_infos,employee_infos, company_infos WHERE  apartment_infos.kijiji_feeding = 1 AND apartment_infos.building_id = building_infos.building_id AND apartment_infos.employee_id = employee_infos.employee_id AND employee_infos.company_id = company_infos.id GROUP BY apartment_infos.building_id");
            $feeding_set=$this->crud->resultSet();

            return $feeding_set;

        }catch (PDOException $e) {echo $e->getMessage();}
    }


    /**
     * get all feeding apartment[kijiji_feeding = 1] from the specific building
     * The function will be run before Kijiji access the data from us.
    */
    public function get_all_kijiji_within_building($building_id){
        try{
            $this->crud->query("SELECT * FROM apartment_infos WHERE apartment_infos.kijiji_feeding = 1 AND apartment_infos.building_id = :building_id");
            $this->crud->bind(":building_id",$building_id);
            $feeding_set=$this->crud->resultSet();

            return $feeding_set;

        }catch (PDOException $e) {echo $e->getMessage();}
    }


    //----------------------------- carousel auto -----------------------

    /**
     * get employee_id & his slot_number & c_index for the employees who open carousel function
     * Hint: c_index is the index to record which apartment in candidate list is in end of feeding list now, next run should be started from here
    */
    public function get_carousel_infos(){
        try{
            $this->crud->query("SELECT employee_infos.employee_id AS employee_id , slots_number AS c_index FROM kijiji_settings, employee_infos WHERE employee_infos.kijiji_settings_id = kijiji_settings.id AND feed_carousel = 1");
            $results = $this->crud->resultSet();

            return $results;

        }catch (PDOException $e) {echo $e->getMessage();}
    }


    /**
     * update the feeding status of apartments for the employee, based on c_index, his candidate list, slot_number
    */
    public function carousel_update_feeding($employee_id,$c_index,$slots_number){
        try{
            $excluded_sql = '';
            if($c_index > 0) {
                $this->crud->query("SELECT apartment_id AS id FROM apartment_infos WHERE employee_id = :employee_id AND kijiji_candidate = 1 LIMIT :c_index");
                $this->crud->bind(":employee_id", $employee_id);
                $this->crud->bind(":c_index", intval($c_index), PDO::PARAM_INT);
                $excludes = $this->crud->resultSet();
                $excluded_ids = array();
                foreach ($excludes as $r) {
                    array_push($excluded_ids, $r['id']);
                }
                $excluded_str = implode(',',$excluded_ids);
                $excluded_sql = "AND apartment_id NOT IN (".$excluded_str.")";
            }

            $this->crud->query("UPDATE apartment_infos SET kijiji_feeding = 1 WHERE employee_id = :employee_id AND kijiji_candidate = 1 ".$excluded_sql."LIMIT ".$slots_number);
            $this->crud->bind(":employee_id",$employee_id);
            $this->crud->execute();
            return true;
        } catch (PDOException $e) {echo $e->getMessage();}
    }


    /**
     * update the c_index value for the employee
     * It will be run after one round finish
    */
    public function update_c_index($employee_id,$c_index){
        try{
            $this->crud->query("UPDATE kijiji_settings SET slots_number = :index WHERE id = ( SELECT kijiji_settings_id FROM employee_infos WHERE employee_infos.employee_id = :employee_id)");
            $this->crud->bind(":employee_id",$employee_id);
            $this->crud->bind(":index",$c_index);
            $this->crud->execute();
            return true;
        }catch (PDOException $e) {echo $e->getMessage();}
    }


    /**
     * get the convenience fee rate [system level]
    */
    public function get_convenience_rules(){
        try{
            $this->crud->query("SELECT CF_PP_Balance_F,CF_PP_CC_P,CF_M_CC_P,CF_M_Interac_F FROM settings WHERE id = 1");
            $result = $this->crud->resultSingle();
            return $result;
        }catch (PDOException $e){echo $e->getMessage();}
    }


    //--------   accessibility ------------
    /**
     * get admin level of the employee
    */
    public function get_admin_level($employee_id){
        try{
            $this->crud->query("SELECT admin_id FROM employee_infos WHERE employee_id = :employee_id");
            $this->crud->bind(":employee_id",$employee_id);
            $level = $this->crud->resultSingle()['admin_id'];

            return $level;
        }catch (PDOException $e){echo $e->getMessage();}
    }


    /**
     * used in cron job and front end to update the feeding list and candidate list
     * The reason why update when enter kijiji page : some employees may check to force to posting  -> update the newest
    */
    public function update_to_latest_feeding_listing($employee_id){
        //update candidates
        $this->update_candidates($employee_id);

        //remove old the feeding list
        $this->remove_all_feeding_list($employee_id);

        $slots_number = $this->get_available_slots_number($employee_id);

        $settings=$this->get_kijiji_settings_by_employee($employee_id);
        $feed_by_price=$settings['feed_by_price'];
        $feed_by_price_order_strategy=$settings['feed_by_price_order_strategy'];
        $feed_by_value=$settings['feed_by_value'];
        $feed_by_value_order_strategy=$settings['feed_by_value_order_strategy'];

        //update new feeding list
        $this->update_feeding_list($employee_id,$slots_number,$feed_by_price,$feed_by_price_order_strategy,$feed_by_value,$feed_by_value_order_strategy);
    }
}
