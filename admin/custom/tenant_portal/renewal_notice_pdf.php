<?
/*
require __DIR__.'/../../vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

try {
    ob_start();
    $content = ob_get_clean();
$content=file_get_contents('https://'.$_SERVER['HTTP_HOST'].'/admin/custom/tenant_portal/renewal_notice.php?id='.$_GET['id'].'&pdf=1'); //?id='.$_GET['id'].'&pdf=1'
$html2pdf = new Html2Pdf();
$html2pdf->writeHTML($content);
$html2pdf->output();
} catch (Html2PdfException $e) {
    $html2pdf->clean();
    $formatter = new ExceptionFormatter($e);
    echo $formatter->getHtmlMessage();

}

//$content=file_get_contents('https://'.$_SERVER['HTTP_HOST'].'/admin/custom/tenant_portal/renewal_notice.php?id='.$_GET['id'].'&pdf=1'); //?id='.$_GET['id'].'&pdf=1'
//echo $content;
*/
?>
<?php
$_GET['pdf']=1;
require __DIR__.'/../../vendor/autoload.php';
use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;
try {
    $html2pdf = new Html2Pdf('P', 'A4', 'en', true, 'UTF-8', array(10, 10, 10, 10));
    $html2pdf->pdf->SetDisplayMode('fullpage');
    ob_start();
    include 'renewal_notice.php';
    $content = ob_get_clean();
    $html2pdf->writeHTML($content);
    $html2pdf->output('renewal_notice.pdf');
} catch (Html2PdfException $e) {
    $html2pdf->clean();
    $formatter = new ExceptionFormatter($e);
    echo $formatter->getHtmlMessage();
    var_dump($e);
}
?>
