<?
require __DIR__.'/../../vendor/autoload.php';
use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;
$pdf=1;
$stid=$_GET['stid'];
if($stid==1){$page_size='USLEGAL';}else{$page_size='USLETTER';}
include('../../../pdo/dbconfig.php');
$query = "select * from tenant_sign_types where sign_type_id=$stid";
$stmt = $DB_con->prepare($query);
$stmt->execute();
$row = $stmt->fetch(\PDO::FETCH_ASSOC);
$short_file_name=$row['short_file_name'];
// print_r($row);

try {
    $html2pdf = new Html2Pdf('P', $page_size, 'en', true, 'UTF-8', array(3, 2, 3, 2));
    $html2pdf->pdf->SetDisplayMode('fullpage');
    $html2pdf->addFont('Courgette', 'regular', 'Courgette-Regular.php');
    // $html2pdf->setDefaultFont("arial");
    $html2pdf->setTestTdInOnePage(false);
    ob_start();
    include ('lease_'.$short_file_name.'.php');
    $content = ob_get_clean();
    $html2pdf->writeHTML($content);
    $html2pdf->output('lease_'.$short_file_name.'.pdf');
} catch (Html2PdfException $e) {
    $html2pdf->clean();
    $formatter = new ExceptionFormatter($e);
    echo $formatter->getHtmlMessage();
    var_dump($e);
}





?>