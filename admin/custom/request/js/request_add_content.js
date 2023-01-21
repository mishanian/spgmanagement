$(function () {
    /* JS code for the functionality of Projects, Contracts
    * This JS will eventually have the JS code from the request_add_content.php
    * Date : 2018-06-12
    * */

    var relative_path = "custom/request/";

    /* Generic click event handler for all the buttons */
    $("body").on("click", ".btnClicker", function () {
        buttonId = $(this).attr("id");
        switch (buttonId) {
            case "createProjectNew":
                createNewProject();
                break;

            case "createContractNew":
                createNewContract();
                break;
        }
    });

    $("body").on("change", ".selectChanger", function () {
        selectId = $(this).attr("id");
        switch (selectId) {
            case "projectSelectDetail":
                /* Set the location_id of the selected project */
                location_id = $('option:selected', this).attr('data-location');
                $("#reportArea").val(location_id).change();
                $("#report-request-type").val("0").change();

                setContractView();
                break;
            case "contractSelectDetail":
                setProjectContractRequestDetail();
                break;

            case "projectLocation":

                selectedLocation = $('option:selected', this).val();
                if (selectedLocation == "3") {
                    $("#project_address_newproject_wrap").show().slideDown(1000);
                }else{
                    $("#project_address_newproject_wrap").hide().slideUp(1000);
                }
                break;
        }
    });

    /* Functions Defined */

    /* handle the view generation of the contract select
    * Get the selected project Id and fetch the contracts under the project
    * */
    function setContractView() {
        projectid = $("#projectSelectDetail").val();

        data = {
            action: "getContractsForProject",
            project_id: projectid
        }

        /* Send an AJAX request to get the contracts under a project */
        $.ajax({
            url: relative_path + "request_info_controller.php",
            type: "post",
            data: data,
            dataType: "json",
            success: function (response) {
                $("#contractSelectDetail").html("");
                if (response) {
                    if (response.length > 0) { // To check if there are any contracts for the project
                        optionhtml = "<option value='default'>Select a Contract</option>";
                        for (var index in response) {
                            contract = response[index];
                            optionhtml += "<option value='" + contract["contract_id"] + "'> " + contract["contract_desc"] + "</option>";
                        }
                        $("#contractSelectDetail").html(optionhtml);
                        $("#contractSelectWrap").show().slideDown(1000);
                    } else { // There are no contracts for the project - show the alert and ask the user to create a new contract
                        alert("No contracts found for the selected project.");
                    }
                }
            }
        });
    }

    /* Handle the creation of a Project
    * Check if all the inputs are correct and valid
    * Send an AJAX request to request_info_controller.php
    **/
    function createNewProject() {
        /* Values required to create a new Project */
        projectName = $("#project_name_newproject").val();
        projectLocation = $("#projectLocation").val();
        projectAddress = $("#project_address_newproject").val();
        company_id = $("#companyIdValue").val();
        error = false;

        if (projectName == null || projectLocation == "default") {
            error = true;
        }

        if (error) {
            alert("Missing required fields.");
            return;
        }

        data = {
            name: projectName,
            location: projectLocation,
            address: projectAddress,
            company_id:company_id,
            action: 'addNewProject'
        };

        /* Send an AJAX request to create a new project */
        $.ajax({
            url: relative_path + "request_info_controller.php",
            type: "post",
            data: data,
            dataType: "json",
            success: function (response) {
                if (response) {
                    TriggerAlertOpen("create_project_success", "Project created successfully!");
                    TriggerAlertClose("create_project_success");
                    $("#reportProject")[0].reset(); // Reset the form details
                    refreshProjectsList();
                }
            }
        });
    }

    /* Handle the creation of a Contract
   * Check if all the inputs are correct and valid
   * Send an AJAX request to request_info_controller.php
   **/
    function createNewContract() {
        /* Values required to create a new Contract */
        projectId = $("#projectIdDetail").val();
        contractDesc = $("#contract_description_detail").val();
        vendorId = $("body").find("#recipient-report-vendor").val();
        estimatedPrice = $("body").find("#recipient-vendor-estimatedprice").val();
        contractPrice = $("body").find("#recipient-vendor-contractprice").val();
        company_id = $("#companyIdValue").val();
        typeId = $("#contractIdtype").val();
        error = false;

        if (projectId == "default" || vendorId == "0" || typeId == "default") {
            error = true;
        }

        if (error) {
            alert("Missing required fields.");
            return;
        }

        data = {
            projectId: projectId,
            contractDesc: contractDesc,
            vendorId: vendorId,
            typeId: typeId,
            estimatedPrice: estimatedPrice,
            contractPrice: contractPrice,
            company_id:company_id,
            action: 'addNewContract'
        };

        /* Send an AJAX request to create a new project */
        $.ajax({
            url: relative_path + "request_info_controller.php",
            type: "post",
            data: data,
            dataType: "json",
            success: function (response) {
                if (response) {
                    TriggerAlertOpen("create_contract_success", "Contract created successfully!");
                    TriggerAlertClose("create_contract_success");
                    $("#reportContract")[0].reset(); // Reset the form details
                }
            }
        });
    }

    /* Refresh the Projects select dropdown in the new contracts tab and the new request tab
    * Do an AJAX request to get all the projects
    * */
    function refreshProjectsList() {
        $.ajax({
            url: relative_path + "request_info_controller.php",
            type: "post",
            data: {
                action: "getAllProjects"
            },
            dataType: "json",
            success: function (response) {
                if (response) {
                    /* Remove the data from the Projects Select in contracts tab and new request tab */
                    $("body").find("#projectIdDetail").empty();
                    $("body").find("#projectSelectDetail").empty();

                    optionhtml = "<option value='default'> Select a Project</option>";
                    for (var pIndex in response) {
                        project = response[pIndex];
                        optionhtml += "<option data-location='" + project["location_id"] + "' value='" + project["project_id"] + "'> " + project["name"] + "</option>";
                    }

                    // Append
                    $("body").find("#projectIdDetail").append(optionhtml); // Append new Projects to New Request Projects select list
                    $("body").find("#projectSelectDetail").append(optionhtml); // Append new Projects to New Contract Projects select list
                }
            }
        });
    }

    /* Open the alert div and show the message */
    function TriggerAlertOpen(alertId, text) {
        $("#" + alertId).html(text).fadeTo(1000, 1).slideDown(1000);
    }

    /* Close the alert div */
    function TriggerAlertClose(alertId) {
        window.setTimeout(function () {
            $("#" + alertId).fadeTo(1000, 0).slideUp(1000);
        }, 3000);
    }

    /* Get the Contract Detail */
    function setProjectContractRequestDetail() {
        contractSelected = $("body").find("#contractSelectDetail").val();
        if (contractSelected == "default") {
            return;
        }

        data = {
            action: "getContractDetail",
            contractId: contractSelected
        };

        $.ajax({
            url: relative_path + "request_info_controller.php",
            type: "post",
            data: data,
            dataType: "json",
            success: function (response) {
                if (response) {
                    /* Set the request type Id in the new request detail tab;
                    * */
                    $("#report-request-type").val(response.request_type_id);
                    // $("#contractvendorDetailWrap").
                    // $("#contractVendorDetailText").html(response.)
                }
            }
        });
    }

});