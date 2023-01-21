<?
/*
include 'dbconfig.php';
$Crud=new CRUD($con);
$sql="select Price from parts where PartId=".$_GET['p'];
//echo $sql;
$Crud->query($sql);
$Price = $Crud->resultField();
echo $Price;
*/
?>
<?
include '../../pdo/dbconfig.php';
$Crud=new CRUD($DB_con);
$s='<li class="rcbItem ">Autres pays / Other countries</li><li class="rcbItem ">Autres provinces / Other provinces</li><li class="rcbItem ">&#201;tats-Unis / USA</li>';
$s=str_replace('<li class="rcbItem ">','',$s);
$s=str_replace('</li>',',',$s);
$s=rtrim($s,',');
$array=explode(",",$s);
//die(var_dump($array));
//$array=array("Abercorn","Acton Vale","Akwesasne","Ange-Gardien","Beauharnois","Bedford - Canton","Bedford - Ville","Beloeil","B&#233;thanie","Bolton-Ouest","Boucherville","Brigham","Brome","Bromont","Brossard","Calixa-Lavall&#233;e","Candiac","Carignan","Chambly","Ch&#226;teauguay","Contrecoeur","Coteau-du-Lac","Cowansville","Delson","Dundee","Dunham","East Farnham","Elgin","Farnham","Franklin","Frelighsburg","Godmanchester","Granby","Greenfield Park (Longueuil)","Havelock","Hemmingford - Canton","Hemmingford - Village","Henryville","Hinchinbrooke","Howick","Hudson","Huntingdon","Kahnawake","Lac-Brome","Lacolle","La Prairie","La Pr&#233;sentation","L&#233;ry","Les C&#232;dres","Les Coteaux","Le Vieux-Longueuil (Longueuil)","L&#39;&#206;le-Cadieux","L&#39;&#206;le-Perrot","Marieville","Massueville","McMasterville","Mercier","Mont-Saint-Gr&#233;goire","Mont-Saint-Hilaire","Napierville","Notre-Dame-de-l&#39;&#206;le-Perrot","Notre-Dame-de-Stanbridge","Noyan","Ormstown","Otterburn Park","Pike River","Pincourt","Pointe-des-Cascades","Pointe-Fortune","Richelieu","Rigaud","Rivi&#232;re-Beaudette","Rougemont","Roxton","Roxton Falls","Roxton Pond","Saint-Aim&#233;","Saint-Alexandre","Saint-Alphonse-de-Granby","Saint-Amable","Saint-Anicet","Saint-Antoine-sur-Richelieu","Saint-Armand","Saint-Barnab&#233;-Sud","Saint-Basile-le-Grand","Saint-Bernard-de-Lacolle","Saint-Bernard-de-Michaudville","Saint-Blaise-sur-Richelieu","Saint-Bruno-de-Montarville","Saint-C&#233;saire","Saint-Charles-sur-Richelieu","Saint-Chrysostome","Saint-Clet","Saint-Constant","Saint-Cyprien-de-Napierville","Saint-Damase","Saint-David","Saint-Denis-sur-Richelieu","Saint-Dominique","Sainte-Ang&#232;le-de-Monnoir","Sainte-Anne-de-Sabrevois","Sainte-Anne-de-Sorel","Sainte-Barbe","Sainte-Brigide-d&#39;Iberville","Sainte-Catherine","Sainte-C&#233;cile-de-Milton","Sainte-Christine","Sainte-Clotilde","Saint-&#201;douard","Sainte-H&#233;l&#232;ne-de-Bagot","Sainte-Julie","Sainte-Justine-de-Newton","Sainte-Madeleine","Sainte-Marie-Madeleine","Sainte-Marthe","Sainte-Martine","Sainte-Sabine","Saint-&#201;tienne-de-Beauharnois","Sainte-Victoire-de-Sorel","Saint-Georges-de-Clarenceville","Saint-G&#233;rard-Majella","Saint-Hubert (Longueuil)","Saint-Hugues","Saint-Hyacinthe","Saint-Ignace-de-Stanbridge","Saint-Isidore","Saint-Jacques-le-Mineur","Saint-Jean-Baptiste","Saint-Jean-sur-Richelieu","Saint-Joachim-de-Shefford","Saint-Joseph-de-Sorel","Saint-Jude","Saint-Lambert","Saint-Lazare","Saint-Liboire","Saint-Louis","Saint-Louis-de-Gonzague","Saint-Marcel-de-Richelieu","Saint-Marc-sur-Richelieu","Saint-Mathias-sur-Richelieu","Saint-Mathieu","Saint-Mathieu-de-Beloeil","Saint-Michel","Saint-Nazaire-d&#39;Acton","Saint-Ours","Saint-Patrice-de-Sherrington","Saint-Paul-d&#39;Abbotsford","Saint-Paul-de-l&#39;&#206;le-aux-Noix","Saint-Philippe","Saint-Pie","Saint-Polycarpe","Saint-R&#233;mi","Saint-Robert","Saint-Roch-de-Richelieu","Saint-S&#233;bastien","Saint-Simon","Saint-Stanislas-de-Kostka","Saint-T&#233;lesphore","Saint-Th&#233;odore-d&#39;Acton","Saint-Urbain-Premier","Saint-Valentin","Saint-Val&#233;rien-de-Milton","Saint-Zotique","Salaberry-de-Valleyfield","Shefford","Sorel-Tracy","Stanbridge East","Stanbridge Station","Sutton","Terrasse-Vaudreuil","Tr&#232;s-Saint-R&#233;dempteur","Tr&#232;s-Saint-Sacrement","Upton","Varennes","Vaudreuil-Dorion","Vaudreuil-sur-le-Lac","Venise-en-Qu&#233;bec","Verch&#232;res","Warden","Waterloo","Yamaska");
foreach ($array as $key) {
    $sql = "insert into house_municipalitys (house_region_id,name) VALUES (18,'$key')";
echo "$sql<br>\n";
    $Crud->query($sql);
    $Crud->execute();
}
?>