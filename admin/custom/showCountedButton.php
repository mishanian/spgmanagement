<?

namespace PHPMaker2023\spgmanagement;

echo "/***** Show counted button ****/ \n";
$project_text = $vendor_text = $proposal_text = $contract_text = $invoice_text = $payment_text = "";
$project_css = $vendor_css = $proposal_css = $contract_css = $invoice_css = $payment_css = "btn-default";

$whereVendor = "true";
$whereProject = "true";
$whereContract = "true";
$whereInvoice = "true";
$wherePayment = "true";

$linkVendor = "";
$linkProject = "";
$linkProposal = "";
$linkContract = "";
$linkInvoice = "";
$linkPayment = "";
$linkMaster = "";
//die("cdt=".var_dump(CurrentPage()));
//die("cp=".var_dump(CurrentPage()->SearchWhere));
//die("dt=".var_dump(CurrentDetailTable()));
//die("mt=".var_dump(CurrentMasterTable()->project_id->CurrentValue));
if (!empty(CurrentMasterTable())) {
    $MasterTable = CurrentMasterTable()->TableName;
    $MasterFilter = CurrentTable()->DbMasterFilter;
    //die("Master Filter=".$MasterFilter);
    if (!empty($MasterFilter)) {
        list($MasterField, $Masterfieldkey) = explode("=", str_replace(" ", "", str_replace("`", "", $MasterFilter)));
        $$MasterField = $Masterfieldkey;
        $detailTable = CurrentDetailTable()->TableName;
        $detailFilter = CurrentTable()->DbDetailFilter;
        $typeOfPage = CurrentPageID(); // list
        $linkMaster = "showmaster=" . $MasterTable . "&$MasterField=$Masterfieldkey";
    }
}
if (!empty(CurrentPage())) {
    $table = CurrentPage()->TableName;
    $tablefilter = CurrentPage()->SearchWhere;

    $fieldrow = explode("=", str_replace(" ", "", str_replace("`", "", $tablefilter)));
    if (!empty($fieldrow[0])) {
        $field = $fieldrow[0];
    } else {
        $field = "";
    }
    if (!empty($fieldrow[1])) {
        $fieldkey = $fieldrow[1];
        $$field = $fieldkey;
    } else {
        $fieldkey = "";
    }
}

//die("MasterTable=".$MasterTable."  MasterFilter=$MasterFilter MasterField=$MasterField Masterfieldkey=$Masterfieldkey --- detailTable=$detailTable detailFilter=$detailFilter table=$table tablefilter=$tablefilter Field=$field Key=$fieldkey typeOfPage=$typeOfPage");
//die($linkMaster);
//if(!empty($this->getMasterFilter())){
//   echo $this->getMasterFilter();
//}
//echo "P=".Page("project_infos")->project_id->CurrentValue;
//   if((!empty($field) && $field=='project_id') || !empty(Page("project_infos")->project_id->CurrentValue) || !empty($_GET['project_id']) || !empty($_GET['x_project_id'])){
if (!empty($project_id)) {
    //       die("pi=".$project_id);

    if (empty($project_id)) {
        $project_id = (!empty(Page("view_projects_of_vendor")->project_id->CurrentValue) ? Page("view_projects_of_vendor")->project_id->CurrentValue : (!empty($_GET['project_id']) ? $_GET['project_id'] : $_GET['x_project_id']));
    }
    if (!empty(Page("view_projects_of_vendor")->project_id->CurrentValue)) {
        $project_id = Page("view_projects_of_vendor")->project_id->CurrentValue;
    }
    $whereProject = " MT.project_id=" . $project_id;
    //  die($project_id);
}

//	if(!empty($_GET['fk_contract_id']) || !empty($_GET['contract_id']) || !empty($_GET['x_contract_id'])){
if (!empty($contract_id)) {
    //die("ci=".$contract_id);
    //       $contract_id=(!empty($_GET['fk_contract_id'])?$_GET['fk_contract_id']:(!empty($_GET['contract_id'])?$_GET['contract_id']:$_GET['x_contract_id']));
    $whereContract = " MT.contract_id=" . $contract_id;
    $linkContract = "showmaster=contract_infos&contract_id=" . $contract_id;
    $whereProject = " MT.project_id=" . CurrentMasterTable()->project_id->CurrentValue;
    //   die($whereProject);
}

if (!empty($_GET['fk_invoice_id']) || !empty($_GET['invoice_id']) || !empty($_GET['x_invoice_id'])) {
    $invoice_id = (!empty($_GET['fk_invoice_id']) ? $_GET['fk_invoice_id'] : (!empty($_GET['invoice_id']) ? $_GET['invoice_id'] : $_GET['x_invoice_id']));
    $whereInvoice .= " MT.invoice_id=" . $invoice_id;
    $linkInvoice = "&invoice_id=" . $invoice_id;
}

if (!empty($_GET['fk_payment_id']) || !empty($_GET['payment_id']) || !empty($_GET['x_payment_id'])) {
    $payment_id = (!empty($_GET['fk_payment_id']) ? $_GET['fk_payment_id'] : (!empty($_GET['payment_id']) ? $_GET['payment_id'] : $_GET['x_payment_id']));
    $wherePayment .= " MT.payment_id=" . $payment_id;
    $linkPayment .= "&payment_id=" . $payment_id;
}

if (!empty($_GET['vendor_id']) || !empty($_GET['fk_vendor_id']) || !empty($_GET['x_vendor_id'])) {
    $vendor_id = (!empty($_GET['fk_vendor_id']) ? $_GET['fk_vendor_id'] : (!empty($_GET['vendor_id']) ? $_GET['vendor_id'] : $_GET['x_vendor_id']));
    $whereVendor .= " and MT.vendor_id=" . $vendor_id;
    $linkVendor .= "&vendor_id=" . $vendor_id;
    $linkProject .= "showmaster=view_projects_of_vendorlist.php?showmaster=vendor_infos&fk_vendor_id=$vendor_id";
    $linkProposal .= "&vendor_id=" . $vendor_id;
    $linkContract .= "&vendor_id=" . $vendor_id;
    $linkInvoice .= "&vendor_id=" . $vendor_id;
    $linkPayment .= "&vendor_id=" . $vendor_id;
    $VendorName = ExecuteScalar("select company_name from vendor_infos where vendor_id=" . $vendor_id);
} else {
    $linkVendor .= "cmd=reset";
}

$field_name = str_replace("_infos", "", CurrentPage()->TableName);
${$field_name . "_css"} = "btn-primary";

$sqlVendor = "SELECT  COUNT(DISTINCT MT.vendor_id) FROM view_project_vendor MT LEFT JOIN contract_infos CI ON MT.vendor_id=CI.vendor_id where $whereVendor and $whereProject and $whereContract and MT.company_id=" . $_SESSION['company_id'];
//echo("<br>SqlVendor=".$sqlVendor."<br>");
$vendor_count = ExecuteScalar($sqlVendor);
$vendor_text = "<div class=\"btn-group btn-group-sm ew-btn-group \"><a class=\"btn $vendor_css ew-row-link ew-detail\" data-action=\"list\" href=\"vendor_infoslist.php?cmd=reset$linkVendor\">Vendor&nbsp;<span class=\"badge badge-info ew-detail-count$vendor_count\">$vendor_count</span></a></div>";

$sqlProject = "SELECT  COUNT(DISTINCT project_name) FROM view_project_vendor MT LEFT JOIN contract_infos CI ON CI.project_id=MT.project_id WHERE $whereVendor and $whereProject and MT.company_id=" . $_SESSION['company_id'];
//echo("<br>sqlProject=".$sqlProject."<br>");
$project_count = ExecuteScalar($sqlProject);
$project_text = "<div class=\"btn-group btn-group-sm ew-btn-group \"><a class=\"btn $project_css ew-row-link ew-detail\" data-action=\"list\" href=\"view_projects_of_vendorlist.php?cmd=reset&$linkProject\">Project&nbsp;<span class=\"badge badge-info ew-detail-count$project_count\">$project_count</span></a></div>";

$sqlProposal = "select count(*) from contract_infos MT where $whereVendor and $whereProject and $whereContract and is_proposal=1 and company_id=" . $_SESSION['company_id'];
//echo("<br>sqlProposal=".$sqlProposal."<br>");
$proposal_count = ExecuteScalar($sqlProposal);
$proposal_text = "<div class=\"btn-group btn-group-sm ew-btn-group \"><a class=\"btn $proposal_css ew-row-link ew-detail\" data-action=\"list\" href=\"proposal_infoslist.php?cmd=reset&$linkProposal\">Proposal&nbsp;<span class=\"badge badge-info ew-detail-count$proposal_count\">$proposal_count</span></a></div>";

$sqlContract = "select count(*) from contract_infos MT where $whereVendor and $whereProject  and $whereContract  and is_proposal=0 and company_id=" . $_SESSION['company_id'];
//echo("<br>sqlContract=".$sqlContract."<br>");
$contract_count = ExecuteScalar($sqlContract);
$contract_text = "<div class=\"btn-group btn-group-sm ew-btn-group\"><a class=\"btn $contract_css ew-row-link ew-detail\" data-action=\"list\" href=\"contract_infoslist.php?cmd=reset&$linkProject&$linkContract\">Contract&nbsp;<span class=\"badge badge-info ew-detail-count$contract_count\">$contract_count</span></a></div>";

$sqlInvoice = "select count(*) from invoice_infos MT where $whereVendor and $whereProject  and $whereContract and company_id=" . $_SESSION['company_id'];
//echo("<br>sqlInvoice=".$sqlInvoice."<br>");
$invoice_count = ExecuteScalar($sqlInvoice);
$invoice_text = "<div class=\"btn-group btn-group-sm ew-btn-group\"><a class=\"btn $invoice_css ew-row-link ew-detail\" data-action=\"list\" href=\"invoice_infoslist.php?cmd=reset&$linkProject&$linkContract&$linkInvoice\">Invoice&nbsp;<span class=\"badge badge-info ew-detail-count$invoice_count\">$invoice_count</span></a></div>";

$sqlPayment = "select count(*) from payment_infos MT where $whereVendor and $whereProject  and $whereContract and $wherePayment and company_id=" . $_SESSION['company_id'];
//echo("<br>sqlPayment=".$sqlPayment."<br>");
$payment_count = ExecuteScalar($sqlPayment);
$payment_text = "<div class=\"btn-group btn-group-sm ew-btn-group\"><a class=\"btn $payment_css ew-row-link ew-detail\" data-action=\"list\" href=\"payment_infoslist.php?cmd=reset&$linkProject&$linkContract&$linkInvoice&$linkPayment\">Payment&nbsp;<span class=\"badge badge-info ew-detail-count$payment_count\">$payment_count</span></a></div>";

//$this->ListOptions->Items["Projects"]->Body = $ProjectsText;
?>
$(".ew-filter-option").append('<?= $vendor_text . $project_text . $proposal_text . $contract_text . $invoice_text . $payment_text ?>');