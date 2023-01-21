<?
$crud->query("SELECT count(*) as project_count FROM project_infos WHERE company_id = $company_id");
$project_count = $crud->resultField();
$crud->query("SELECT count(*) as proposal_count FROM proposal_infos WHERE company_id = $company_id and is_proposal=1");
$proposal_count = $crud->resultField();
$crud->query("SELECT count(*) as contract_count FROM contract_infos WHERE company_id = $company_id and is_proposal=0");
$contract_count = $crud->resultField();
$crud->query("SELECT count(*) as invoice_count FROM invoice_infos WHERE company_id = $company_id");
$invoice_count = $crud->resultField();
$crud->query("SELECT count(*) as vendor_count FROM vendor_infos WHERE company_id = $company_id");
$vendor_count = $crud->resultField();
$crud->query("SELECT count(*) as attachment_count FROM attachment_infos WHERE company_id = $company_id");
$attachment_count = $crud->resultField();
?>
<div class="row">
  <div class="col-lg-2 col-6">
    <!-- small box -->
    <div class="small-box bg-info">
      <div class="inner">
        <h3><?= $project_count ?></h3>

        <p>Projects</p>
      </div>
      <div class="icon">
        <i class="fas fa-building"></i>
      </div>
      <a href="projectinfoslist?cmd=resetall" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <!-- ./col -->
  <div class="col-lg-2 col-6">
    <!-- small box -->
    <div class="small-box bg-success">
      <div class="inner">
        <h3><?= $proposal_count ?></h3>

        <p>Proposals</p>
      </div>
      <div class="icon">
        <i class="fas fa-file-signature fa-2x"></i>
      </div>
      <a href="proposal_infoslist.php?cmd=resetall" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <!-- ./col -->
  <div class="col-lg-2 col-6">
    <!-- small box -->
    <div class="small-box bg-warning">
      <div class="inner">
        <h3><?= $contract_count ?></h3>

        <p>Contracts</p>
      </div>
      <div class="icon">
        <i class="fas fa-file-contract fa-2x"></i>
      </div>
      <a href="contract_infoslist.php?cmd=resetall" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <!-- ./col -->
  <div class="col-lg-2 col-6">
    <!-- small box -->
    <div class="small-box bg-danger">
      <div class="inner">
        <h3><?= $invoice_count ?></h3>

        <p>Invoices</p>
      </div>
      <div class="icon">
        <i class="fas fa-file-invoice  fa-2x"></i>
      </div>
      <a href="invoice_infoslist.php?cmd=resetall" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <!-- ./col -->
  <div class="col-lg-2 col-6">
    <!-- small box -->
    <div class="small-box bg-success">
      <div class="inner">
        <h3><?= $vendor_count ?></h3>

        <p>Vendors</p>
      </div>
      <div class="icon">
        <i class="fa fa-briefcase fa-2x"></i>
      </div>
      <a href="vendor_infoslist.php?cmd=resetall" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <!-- ./col -->
  <!-- ./col -->
  <div class="col-lg-2 col-6">
    <!-- small box -->
    <div class="small-box bg-info">
      <div class="inner">
        <h3><?= $attachment_count ?></h3>

        <p>File Center</p>
      </div>
      <div class="icon">
        <i class="fa fa-file-pdf-o fa-2x"></i>
      </div>
      <a href="attachment_infoslist.php?cmd=resetall" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <!-- ./col -->
</div>
<!-- /.row -->
<!-- Main row -->