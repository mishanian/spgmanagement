<?
session_start();
include_once '../../db.php';
       
    //step -1	
        //establish connection to your database - Code to be included by your programmers
         	//include 'dataconnection.php'  
  
    //step 2


	//Get data from your database for all the unit that need to be posted - data to be retrieved for posting (view or tables)
		$unitdatakijiji = mysql_query("select * from kijijitables");
		
    //step 3 - form the xml document by looping on unitdatakijiji - to be discussed or explained to your programmers

	//xml eclosure 
	$xml = new DOMDocument("1.0","utf-8");
        $xml->version  = "1.0";
	$xml->preserveWhiteSpace = false;
	$xml->formatOutput = true;


	

        //forming xml document
	$Locations = $xml->createElement("Locations");
        $xml->appendChild($Locations);

	//Loop at locations to create each location
	$Location = $xml->createElement("Location");
        $Locations->appendChild($Location);

	//get Client Location Id
	$ClientLocationId = $xml->createElement("ClientLocationId","683456");
	// syntax for your code $ClientLocationId = $xml->createElement("ClientLocationId",$kijidata->row['fieldName']);
        $Location->appendChild($ClientLocationId);

	//Building Name
	$BuildingName = $xml->createElement("BuildingName","GlenView Apartments");
        $Location->appendChild($BuildingName);

	//StreetAddress
	$StreetAddress = $xml->createElement("StreetAddress","2050 Decarie");
        $Location->appendChild($StreetAddress);
	

	//city
	$City = $xml->createElement("City","Montreal");
        $Location->appendChild($City);

	//Province
	$Province = $xml->createElement("Province","Quebec");
        $Location->appendChild($Province);


	//PostalCode
	$PostalCode = $xml->createElement("PostalCode","H4A 3G3");
        $Location->appendChild($PostalCode);


	//PhoneNumber
	$PhoneNumber = $xml->createElement("PhoneNumber","5145627588");
        $Location->appendChild($PhoneNumber);


	//Website
	$Website = $xml->createElement("Website","www.vaidacare.com");
        $Location->appendChild($Website);


	//CatchPhrase
	$CatchPhrase = $xml->createElement("CatchPhrase","Apartment for rent");
        $Location->appendChild($CatchPhrase);

	//kijiji_location_id - Optional
	$kijiji_location_id = $xml->createElement("kijiji_location_id","1700281");
        $Location->appendChild($kijiji_location_id);


	//Logo - Optional
	$Logo = $xml->createElement("Logo");
        $Location->appendChild($Logo);

	//Small - Optional
	$Small = $xml->createElement("Small");
        $Logo->appendChild($Small);

	//Medium - Optional
	$Medium = $xml->createElement("Medium");
        $Logo->appendChild($Medium);

	//Large - Optional
	$Large = $xml->createElement("Large");
        $Logo->appendChild($Large);

	//EmailRecipients - Optional
	$EmailRecipients = $xml->createElement("EmailRecipients");
        $Location->appendChild($EmailRecipients);

	//EmailRecipient - Optional - Loop
	$EmailRecipient = $xml->createElement("EmailRecipient","yuhong.yan1@gmail.com");
        $EmailRecipients->appendChild($EmailRecipient);


	//Units - Optional
	$Units = $xml->createElement("Units");
        $Location->appendChild($Units);


	//Unit - Optional
	$Unit = $xml->createElement("Unit");
        $Units->appendChild($Unit);


	//ClientUnitId - O
	$ClientUnitId = $xml->createElement("ClientUnitId","1001");
        $Unit->appendChild($ClientUnitId);


	//RentOrSale - O
	$RentOrSale = $xml->createElement("RentOrSale","rent");
        $Unit->appendChild($RentOrSale);

	//OfferedBy - O
	$OfferedBy = $xml->createElement("OfferedBy","professional");
        $Unit->appendChild($OfferedBy);


	//title - O
	//$Title = $xml->createElement("Title");
        //$Unit->appendChild($Title);



	//UnitType - O
	$UnitType = $xml->createElement("UnitType","apartment");
        $Unit->appendChild($UnitType);


	//Bedrooms - O
	$Bedrooms = $xml->createElement("Bedrooms","4");
        $Unit->appendChild($Bedrooms);


	//Bathrooms - O
	$Bathrooms = $xml->createElement("Bathrooms","2");
        $Unit->appendChild($Bathrooms);


	//SquareFootage - O
	$SquareFootage = $xml->createElement("SquareFootage","1000");
        $Unit->appendChild($SquareFootage);


	//Price - O
	$Price = $xml->createElement("Price","1200");
        $Unit->appendChild($Price);

	//Furnished - O
	$Furnished = $xml->createElement("Furnished","no");
        $Unit->appendChild($Furnished);


	//PetsAllowed - O
	$PetsAllowed = $xml->createElement("PetsAllowed","no");
        $Unit->appendChild($PetsAllowed);


	//Description - O
	$Description = $xml->createElement("Description","Clean 4 1/2 apartment for rent. Close to Decarie Blvd and Metro. Near bus stop and all other amenities.");
        $Unit->appendChild($Description);


	//Size-acres - O
	//$Size-acres = $xml->createElement("Size-acres");
        //$Unit->appendChild($Size-acres);



	//Images - O
	$Images = $xml->createElement("Images");
        $Unit->appendChild($Images);


	//Image - Loop
	$Image  = $xml->createElement("Image");
        $Images->appendChild($Image);


	//Image Name -
	$Name  = $xml->createElement("Name","Main Picture");
        $Image->appendChild($Name);


	//Image SourceUrl -
	$SourceUrl  = $xml->createElement("SourceUrl","http://www.limpsys.com/schema/example/toronto.jpg");
        $Image->appendChild($SourceUrl);


	//Video - O
	$Video = $xml->createElement("Video","eUdCA6tbdVI");
        $Unit->appendChild($Video);


   //step - 4 write the xml to local directory

                //Write feed file to local directory  
		echo $xml->save("F:/Business/Yan Montreal Project/Kijiji Feed/Feeds/FeedFileP.xml");

	
   // step - 5 transfer/ftp file to directory on server


	 $file = 'F:/Business/Yan Montreal Project/Kijiji Feed/Feeds/FeedFileP.xml';
	 $remote_file = 'readme.xml';
   
         $ftp_server = "ftp.vaidacare.com";
	 $ftp_user_name = "user1235";
	 $ftp_user_pass = "ftp!user1";

	// set up basic connection
	 $conn_id = ftp_connect($ftp_server);
         //$conn_id = ftp_ssl_connect($ftp_server, 22) or die("Could not connect to $ftp_server");

	// 71 login with username and password
	$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

	// upload a file
	if (ftp_put($conn_id, $remote_file, $file, FTP_ASCII)) {
 	echo "successfully uploaded $file\n";
	} else {
 	echo "There was a problem while uploading $file\n";
        	}

	//close the connection
	ftp_close($conn_id);

	




?>


