loadjs.ready('jsloaded', function() {
    $("document").ready(function () {

    $("#process_form").trigger("reset"); // reset the form to avoid any data issue

    var relative_path = "custom/payment/";

    // Checkbox to select if the customer is a tenant ?
    $("#customer_tenant_toggle").change(function () {
        let is_checked = $(this).is(":checked");
        if(is_checked){
            $("#item_sale_tenant_toggle").removeClass("hidden");
            $("#item_sale_customer_toggle").addClass("hidden");
        }else{
            $("#item_sale_tenant_toggle").addClass("hidden");
            $("#item_sale_customer_toggle").removeClass("hidden");
        }
        // $("#item_sale_tenant_toggle,#item_sale_customer_toggle").toggleClass("hidden");
    });

    // Open the modal to view the tenants list
    $("#browsetenant_select").on("click",function () {
        $("#tenantSelectModal").modal("show");
    });

    let company_id = $("#companyIdValue").val(); // Current company ID

    // get the tenant data as json - make a table with a checkbox for the user to select
    var tenantSelectTable = $('#tenantSelecttable').DataTable( {
        "pagingType": "full_numbers",
        
        "ajax": {
            url: relative_path+"payment_standalone_controller.php",
            "type": "GET",
            "data": {
                "action" : "getTenants",
                "company_id":company_id
            }
        },
        "columns": [
            { "data": "full_name" },
            { "data": "email" },
            { "data": "building_name" },
            { "data": "" },
        ],
        "columnDefs": [ {
            "targets": -1,
            "data": null,
            "defaultContent": "<button class='btn btn-primary'> Select Tenant </button>"
        } ],
        "rowCallback": function( row, data ) {
            // Custom data attributes for the row items
            $(row).attr('data-id',data.tenant_id);
        }
    } );

    $('#tenantSelecttable tbody').on( 'click', 'button', function () {
        var data = tenantSelectTable.row( $(this).parents('tr') ).data();
        let tenantSelectedId = data.tenant_id;
        let tenantSelectedName = data.full_name;

        $("#item_sale_tenant").val(tenantSelectedName);
        $("#item_sale_tenant_id").val(tenantSelectedId);

        // Close the modal - Show the tenant Name in the selected tenant box
        $("#tenantSelectModal").modal("hide");
    } );

    $("#item_sale_amt").on("blur",function (e) {
        e.stopImmediatePropagation();
        let amount_entered = $(this).val();
        // if (amount_entered.indexOf('$') > -1){
        //     let amtOnly = amount_entered.split("$").pop();
        // }else{
        //     $(this).val("$" + amount_entered);
        // }

        if(isNaN(amount_entered)){
            let strippedValue = amount_entered.replace(/\D/g,''); // Remove all the non digits from the entered value
            amount_entered = strippedValue;
            $(this).val(strippedValue);
        }

        // Update the entered amount in the Payment tab for the calculation of the payment and the outstanding
        $("#payment_amount").html(amount_entered);

        // Update the product name of the sale
        let saleitemName = $("#item_name").val();
        $("#payment_item_name").html(saleitemName);

        // Set the hidden form element values with the amount entered and filtered
        $("#pay_payment_amount_form,#payment_amount_form").val(amount_entered);
    });

    $("#item_name").on("blur",function (e) {
        e.stopImmediatePropagation();
        let name = $(this).val();

        // Put the name value in the payments tab
        $("#payment_item_name").html(name);
    });

    // When the building is selected - fetch units in the building and show in the select
    $("#building_filterpayment").on("change",function () {
        let buildingId = $(this).val();

        $.ajax({
            url: relative_path+"payment_standalone_controller.php",
            type : 'GET',
            dataType:'json',
            data : {
                action:'getApts',
                building_id:buildingId
            },
            success : function(response) {
                if(response){
                    $("#item_unit_id").empty().append('<option value="0">Select a Unit</option>');
                    for(var index in response){
                        let unit = response[index];

                        // Append the Unit info as a OPTION to the select
                        let optionHtml = "<option value='" + unit.apartment_id + "'> " + unit.unit_number + " </option>";
                        $("#item_unit_id").append(optionHtml);
                    }
                    $("#item_unit_id_display").fadeIn();
                }
            }
        });

    });

    // When the payment method radio button is clicked - set the payment amount in the hidden field based on the type selected
    // Unnecessary code - not needed
    $(".payment_method_radio").on("click",function () {
        // let amount_value = $(this).attr("data-amount"); // price to be paid if this payment method is used for the payment
        //
        // // Set the amount_value to the hidden input fields which will be used for the form POST valus
        // $("#pay_payment_amount_form,#payment_amount_form").val(amount_value);
    });

    // When the next button is clicked in the product details page - open the payments tab
    // Update the payment amount value and calculate it for all the payment methods
    $("#submit_payment_item").on("click",function () {
        console.log("submit next");
        let outstanding =  $("#item_sale_amt").val();
        if(outstanding.length < 1 || outstanding == ""){
            return;
        }
        // Get the payment value and update in the respective fields in the payment details tab content
        $.ajax({
            type: "GET",
            url: relative_path+"payment_standalone_controller.php",
            data: {
                action: "getPaymentValue",
                outstanding: outstanding
            },
            dataType: "json",
            success: function (result) {
                // Open the payment tab
                $("#payment_tab").trigger("click");

                // iterate all the elements who has class : needs_dataamount_value
                $(".needs_dataamount_value").each(function(i,v){
                    // get the payment method for this element - and put the result value as the attribute value
                    let paymentMethod = $(this).attr("data-method");
                    $(v).attr("data-amount", result[paymentMethod]);
                });

                // iterate all the elemnts who has class : needs_html_value
                $(".needs_html_value").each(function(i,v){
                    // get the payment method for this element - and put the result value as the attribute value
                    let paymentMethod = $(this).attr("data-method");
                    $(v).html(result[paymentMethod]);
                });

                // iterate all the elemnts who has class : needs_input_value
                $(".needs_input_value").each(function(i,v){
                    // get the payment method for this element - and put the result value as the attribute value
                    let paymentMethod = $(this).attr("data-method");
                    $(v).val(result[paymentMethod]);
                });

            }
        });

    });

    // submit the form - based opn the active tab - submit the form to forward payment or normal payment
    $("#sale_form_submit").on("click",function (e) {
        e.stopPropagation();
        // If the forw3ard payment is selected - change the form action and submit
        if($("#fwd_pay_tag").hasClass("active")){
            $("#process_form").attr("action",relative_path+"forward_payment_request.php").submit();
        }
    });
});
});