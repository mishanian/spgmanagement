<?php
// die("check sub domain file");
$locallist = array(
  '127.0.0.1',
  '::1',
  'localhost'
);


if(!in_array($_SERVER['REMOTE_ADDR'], $locallist) && !checkSubdomain($DB_con)){
  $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

  if (strpos($url,'admin') == false && strpos($url,'tasks') == false) {
        $domain = StripSubdomain("$_SERVER[HTTP_HOST]");
        header("Location: https://$domain/admin/home.php");
  }
}else{
  //$_SESSION['company_id']=9; //SPG default
  //header("Location: https://$domain/admin/home.php");
}

function StripSubdomain($Domain) {

    $domain_array = explode('.', $Domain);
    $domain_array = array_reverse($domain_array);

    return $domain_array[1] . '.' . $domain_array[0];
}

function checkSubdomain($DB_con){
  $HTTP_HOST = explode('.', $_SERVER['HTTP_HOST']);
  $subDomain = $HTTP_HOST[0];

  if(in_array($subDomain,array("www","beaveraittesting"))){
    // Navigate to Admin
    return false;
  }

  if(isset($_SESSION['company_id']) && isset($_SESSION["companySubdomain"])){

    if(isset($_SESSION['companySubdomain']) && $_SESSION['companySubdomain'] != $subDomain){
          // Set the company Session with the request company sub domain and the company id
          $query = "SELECT *  FROM `company_infos` where upper(sub_domain)='" . strtoupper($subDomain) . "'";
          if ($res = $DB_con->query($query)) {
              if ($res->fetchColumn() > 0) {
                  $statement = $DB_con->prepare($query);
                  $statement->execute();
                  $results = $statement->fetchAll(PDO::FETCH_ASSOC);

                  foreach ($results as $line) {
                      $Company_ID = $line['id'];
                  }

                  // Setting the company ID and Sub Domain in the session for later use and for the domain check
                  $_SESSION['company_id'] = $Company_ID;
                  $_SESSION['companySubdomain'] = $subDomain;

                  echo "<hr> Company session is set now : ". $_SESSION['companySubdomain'];
                  return true;
              } else {
                  return false;
              }
          }
    }else{
      
        return true;
    }
  }else{
    // Set the company Session with the request company sub domain and the company id
    $query = "SELECT *  FROM `company_infos` where upper(sub_domain)='" . strtoupper($subDomain) . "'";
    if ($res = $DB_con->query($query)) {
        if ($res->fetchColumn() > 0) {
            $statement = $DB_con->prepare($query);
            $statement->execute();
            $results = $statement->fetchAll(PDO::FETCH_ASSOC);

            foreach ($results as $line) {
                $Company_ID = $line['id'];
            }

            // Setting the company ID and Sub Domain in the session for later use and for the domain check
            $_SESSION['company_id'] = $Company_ID;
            $_SESSION['companySubdomain'] = $subDomain;
            return true;
        } else {
          $domain = StripSubdomain("$_SERVER[HTTP_HOST]");
          header("Location: https://$domain/admin/home.php");
            return false;
        }
    }
  }

}
if(!empty($_SESSION['company_id'])){
  $company_id=$_SESSION['company_id'];
}