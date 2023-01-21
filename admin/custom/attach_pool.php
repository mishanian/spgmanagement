<html>
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
</head>
</html>
<?
session_start();
$dir = '../files/attachments/';
$files = scandir($dir,1);
//print_r($files);
include '../../pdo/dbconfig.php';
$Crud=new CRUD($DB_con);
$sql = "select * from attachment_infos WHERE company_id=" . $_SESSION['company_id'];
$Crud->query($sql);
$atts=$Crud->resultSet();
//print_r($attachments);
echo "<table id='myTable' class='table table-striped table-hover table-condense'>\n";
echo "<thead><tr><th>ID</th><th>Type</th><th>FileName</th><td>Comments</td></tr></thead><tbody>";
foreach ($atts as $att){
    $id=$att['attachment_id'];
    $filename=$att['file'];
    $remarks=html_entity_decode($att['remarks']);
    $ext=pathinfo($filename, PATHINFO_EXTENSION);

if(strtoupper($ext)=='PDF') {
    $icon = 'fa fa-file-pdf-o';
}elseif (strtoupper($ext)=='XLS' || strtoupper($ext)=='XLSX' ){
    $icon = 'fa fa-file-excel-o';
}elseif (strtoupper($ext)=='DOC' || strtoupper($ext)=='DOCX' ){
    $icon = 'fa fa-file-word-o';
    }elseif (strtoupper($ext)=='PNG' || strtoupper($ext)=='JPG' || strtoupper($ext)=='JPEG'){
    $icon = 'fa fa-file-image-o';
}else{
    $icon = 'fa fa-file';
}

    echo "<tr><td>".$id."</td><td><i class=\"$icon fa-2x\"></i></td><td><a href='../files/attachments/$filename' target='_blank'>".$filename."</td><td>$remarks</td></tr>\n";
}
?>
</tbody></table>
<script>
$('#myTable').DataTable( {
fixedHeader: true
} );
</script>
