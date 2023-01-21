<?php
include_once("../../../pdo/dbconfig.php");

/**
 * Page for the checklist after the user enters credit check information for the credit bureau.
 * Page has actions for approval after all the checklist items are checked -
 * If the items are not checked - manager can send an email for the document/information from the potential tenant
 * After the updated information from the tenant - show it in this page with the respective title.
 */

if (!isset($_GET["pid"]) || !isset($_GET["type"])) {
	echo "Potential data missing.";
	exit;
}

$potentialId   = $_GET["pid"];
$visitQuestion = $_GET["type"];

$checkListData = $DB_tenant->getPotentialCreditChecklist($potentialId, $visitQuestion);
?>

<div class="container">
    <div class="panel panel-primary">
        <div class="panel-heading">Credit Checklist</div>
        <div class="panel-body">

            <div class="checklist-content">

                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#checklist_content">Checklist</a></li>
                    <li><a data-toggle="tab" href="#submitted_info">Submitted Details</a></li>
                </ul>

                <div class="tab-content">

                    <div id="checklist_content" class="tab-pane fade in active">

                        <br>
                        <div class="alert alert-info">
                            <i class="fas fa-exclamation-triangle"></i> Select the below detail if received from tenant
                                                                        / available. Tenant will be informed about the
                                                                        details needed if left unselected.
                        </div>

                        <form id="checklistForm">

                            <input type="hidden" name="visitOrQuestionValue" id="visitOrQuestionValue"
                                   value="<?php echo $visitQuestion; ?>" />
                            <input type="hidden" name="potentialIdValue" id="potentialIdValue"
                                   value="<?php echo $potentialId; ?>" />

                            <ul class="list-group">

								<?php
								$checkListItemsDisplay = array(
									"previous_landlord" => "Previous Landlord",
									"regiedulogement" => "Regie Du Logement",
									"payslip" => "Payslip",
									"guarantor" => "Guarantor",
									"bankstatement" => "Bank Statement",
									"voidcheck" => "Void Check",
									"idphoto" => "ID Photo"
								);

								$dataExists = 0;
								if (!$checkListData || !is_array($checkListData)) {
									$newChecklistData = array();
								}
								else {
									$checkListExistingData = $checkListData;
									$dataExists            = 1;
								}

								?>
                                <input type="hidden" name="checklistDataExists" id="checklistDataExists"
                                       value="<?php echo $dataExists; ?>" />
								<?php

								foreach ($checkListItemsDisplay as $key => $checkListItem) {
									$checked = "";
									?>

                                    <li class="list-group-item">
                                        <div class="checkbox">
                                            <label>
												<?php
												if (isset($checkListExistingData[$key])) {
													if ($checkListExistingData[$key] == 1) {
														$checked = "checked";
													}
												}
												?>
                                                <input id="<?php echo "id-" . $key; ?>" name="<?php echo $key; ?>"
                                                       type="checkbox" <?php echo $checked; ?>
                                                       value="1"><?php echo $checkListItem ?>
                                            </label>
                                        </div>
                                    </li>

								<?php }
								?>
                            </ul>
                        </form>

                        <div class="alert alert-success" id="saveChecklistAlert" style="display: none;"></div>

                        <div class="row">
                            <div class="col-md-1">
                                <button type="button" id="updateChecklist" class="btn btn-default">Update & Notify
                                                                                                   Tenant
                                </button>
                            </div>
                        </div>

                    </div>

                    <div id="submitted_info" class="tab-pane fade">
                        <br>
                        <div class="alert alert-info">
                            <i class="far fa-check-square"></i> This is the information submitted by the Tenant.
                        </div>

                        <ul class="list-group">
							<?php
							$checkListItemsDisplay = array(
								"previous_landlord" => "Previous Landlord",
								"regiedulogement" => "Regie Du Logement",
								"payslip" => "Payslip",
								"guarantor" => "Guarantor",
								"bankstatement" => "Bank Statement",
								"voidcheck" => "Void Check",
								"idphoto" => "ID Photo"
							);
							?>
							<?php
							$existingDataValues = json_decode($checkListData["data"], true);
							foreach ($checkListItemsDisplay as $key => $checkListItem) { ?>

                                <li class="list-group-item">
                                    <div class="checkbox">

                                        <div class="row">
                                            <div class="col-md-8">

                                                <h4 class="showDetail" data-toggle="collapse"
                                                    data-target="<?php echo "#" . $key; ?>"
                                                    style="cursor: pointer;"><?php echo $checkListItem; ?>
                                                    <i class="fas fa-chevron-circle-up toggleMe"></i>
                                                </h4>

                                                <div id="<?php echo $key; ?>" class="collapse in">
													<?php
													$data = $existingDataValues[$key];
													switch ($key) {
														case "previous_landlord":
															if (is_array($data)) {
																?>
                                                                <b>Landlord Name </b>
                                                                <span> <?php echo $data["full_name"]; ?> </span>
                                                                <br>
                                                                <b>Telephone </b>
                                                                <span> <?php echo $data["telephone"]; ?> </span>
																<?php
															}
															break;
														case "regiedulogement":
															?>
                                                            <b>Have you ever been opening a file in Regie du Logement of
                                                               Quebec?</b> : <span><?php echo $data; ?></span>
															<?php
															break;
														case "payslip":
															?>
                                                            <a href="<?php echo 'files/'.$data; ?>"> <?php echo $data ?> </a>
															<?php
															break;
														case "guarantor":
															?>
                                                            <b>Guarantor Name </b>
                                                            <span> <?php echo $data["full_name"]; ?> </span>
                                                            <br>
                                                            <b>Date Of Birth </b>
                                                            <span> <?php echo $data["dob"]; ?> </span>
                                                            <br>
                                                            <b>Full Address</b>
                                                            <span> <?php echo $data["address"]; ?> </span>
															<?php
															break;
														case "bankstatement":
															?>
                                                            <a href="<?php echo 'files/'.$data; ?>"> <?php echo $data ?> </a>
															<?php
															break;
														case "voidcheck":
															?>
                                                            <a href="<?php echo 'files/'.$data; ?>"> <?php echo $data ?> </a>
															<?php
															break;
														case "idphoto":
															?>  <a href="<?php echo 'files/'.$data; ?>"> <?php echo $data ?> </a>

															<?php
															break;
													}

													?>
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                </li>

							<?php }
							?>
                        </ul>


                    </div>

                </div>

            </div>

        </div>
    </div>
</div>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css"
      integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="js/credit_checklist.js"></script>