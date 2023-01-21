$(function () {
    /* Doc ready*/

    var checkListItems = [
        "previous_landlord",
        "regiedulogement",
        "payslip",
        "guarantor",
        "bankstatement",
        "voidcheck",
        "idphoto",
    ];

    /* On click handler for the update checklist button */
    $("#updateChecklist").on("click", function () {

        visit_or_question = $("#visitOrQuestionValue").val();
        potential_id = $("#potentialIdValue").val();
        checklistDataExists = $("#checklistDataExists").val();

        formData = {};

        for (var checkListIndex in checkListItems) {
            // console.log(checkListItems[checkListIndex]);
            $checkedValue = $("#id-" + checkListItems[checkListIndex]).is(':checked');

            if ($checkedValue) {
                formData[checkListItems[checkListIndex]] = 1;
            } else {
                formData[checkListItems[checkListIndex]] = 0
            }
        }

        /* Send ajax request to the booking controller - to update the credit check checklist */
        $.ajax({
            url: "booking_controller.php",
            type: 'POST',
            dataType: 'json',
            data: {
                "action": "updateCreditCheckList",
                "data": formData,
                "potential_id": potential_id,
                "visit_question": visit_or_question,
                "checklistDataExists": checklistDataExists
            },
            success: function (response) {
                if (response) {
                    if (response.value) {

                        $("#saveChecklistAlert").html("<i class='fas fa-check-double'></i> Selected items details are saved. Potential Tenant will be notified about the necessary details.").fadeTo(1000, 1).slideDown(1000);

                        window.setTimeout(function () {
                            $("#saveChecklistAlert").fadeTo(1000, 0).slideUp(1000);
                        }, 6000);

                    }
                }
            }
        });

    });

    /* Show the details - collapse opens when this is clicked - collapse done using bootstrap */
    $(".showDetail").on("click",function () {
        $(this).find(".toggleMe").toggleClass("fa-chevron-circle-down");
        $(this).find(".toggleMe").toggleClass("fa-chevron-circle-up");
    });

});