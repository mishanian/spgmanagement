<?
session_start();
include 'pdo/dbconfig.php';
$HTTP_HOST = explode('.', $_SERVER['HTTP_HOST']);
//die(var_dump($HTTP_HOST));
if (($HTTP_HOST[0]!="spgmanagement" && $HTTP_HOST[0]!="www") || ($HTTP_HOST[0]!="beaveraittesting" && $HTTP_HOST[0]!="www")){$Menu_FriendlyURL=$HTTP_HOST[0];}
if (!empty($_GET['CN'])){$Menu_FriendlyURL=$_GET['CN'];}
if(!$Menu_FriendlyURL){
    $REQUEST_URI = $_SERVER['REQUEST_URI'];
    $request = rtrim(ltrim($REQUEST_URI, "/"), "/");
//	die( $request);
    #split the path by '/'
    $params = explode("/", $request);
    $Menu_FriendlyURL = $params[0];
}
if ($HTTP_HOST[0]=="localhost"){$params=explode("/",$_SERVER['REQUEST_URI']);$Menu_FriendlyURL = $params[2];}
//if(!empty($params[1])){$Menu_Sub_FriendlyURL=$params[1];}
//die(var_dump($params));
//var_dump($HTTP_HOST);
//var_dump($REQUEST_URI);
//var_dump($_SERVER['REQUEST_URI']);
//die("Menu_FriendlyURL=".$Menu_FriendlyURL);

    $query = "SELECT *  FROM `company_infos` where upper(sub_domain)='" . strtoupper($Menu_FriendlyURL) . "'";
//die($query);
    if ($res = $DB_con->query($query)) {
        if ($res->fetchColumn() > 0) {


            $statement = $DB_con->prepare($query);
            $statement->execute();
            $results = $statement->fetchAll(PDO::FETCH_ASSOC);
            foreach ($results as $line) {
                $Company_ID = $line['id'];
            }

            $_SESSION['company_id'] = $Company_ID;
            //   die("Company_ID=".$_SESSION['company_id']);
        } else {
            die("Not Found!");
            header('Location: /404.php');
        }
    }


//if ($params[0]=="Home"){$Link='../index.php';}
//	die($Menu_ID."-".$Menu_Sub_ID."-".$params[0]."-".$Link);
//header("Location: $Link");

// $url =  "http://".$Menu_FriendlyURL.".beaveraittesting.site";
// header("Location: $url");
header("Location: index.php");
//include ('index.php');
?>
