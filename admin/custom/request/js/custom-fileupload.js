$(function () {

    // Variable to store your files
    var files;
    var filesEdit;

    // Add events
    $('#newRequest_invoicefiles').on('change', prepareUpload);
    $('#editRequest_invoicefiles').on('change', prepareUploadEdit);

    // Grab the files and set them to our variable
    function prepareUpload(event) {
        $("#newRequest_invoicefiles_list").show();
        $("#newRequest_invoicefiles_alert").fadeOut();

        // in the details tab, empty the list of file names which are already attached
        // $("#invoicesAttachedNewRequest").empty();
        // $(".showOnlyIfInvoicesAttached").hide();

        files = event.target.files;

        // Show the file names as a ordered list
        $.each(files, function (key, value) {
            $("#newRequest_invoicefiles_list > ol").append("<li>" + value.name + "</li>");
        });

        uploadFiles(event);
    }

    // Grab the files and set them to our variable
    function prepareUploadEdit(event) {
        $("#editRequest_invoicefiles_list").show();
        $("#editRequest_invoicefiles_alert").fadeOut();

        // in the details tab, empty the list of file names which are already attached
        // $("#invoicesAttachedNewRequest").empty();
        // $(".showOnlyIfInvoicesAttached").hide();

        filesEdit = event.target.files;

        // Show the file names as a ordered list
        $.each(filesEdit, function (key, value) {
            $("#editRequest_invoicefiles_list > ol").append("<li>" + value.name + "</li>");
        });

        uploadFilesEdit(event);
    }

    // $('#newRequest_invoicefilesForm').on('submit', uploadFiles);

    $('#editRequest_invoicefilesForm').on('submit', uploadFilesEdit);

    // Catch the form submit and upload the files
    function uploadFiles(event) {
        event.stopPropagation(); // Stop stuff happening
        event.preventDefault(); // Totally stop stuff happening

        // START A LOADING SPINNER HERE

        // Create a formdata object and add the files
        var data = new FormData();
        data.append("action", "newrequestInvoiceFileUpload")
        $.each(files, function (key, value) {
            data.append(key, value);
        });

        $.ajax({
            url: 'custom/request/request_info_controller.php',
            type: 'POST',
            data: data,
            cache: false,
            dataType: "json",
            contentType: false,
            processData: false,
            success: function (data, textStatus, jqXHR) {
                if (typeof data.error === 'undefined') {

                    files = null; // empty the files variable for the next files upload
                    $("#newRequest_invoicefiles_list > ol").empty();

                    // Success so call function to process the form
                    $("#newRequest_invoicefiles_list").fadeOut();

                    // Display the list of files uploaded in the Details tab as "uploaded files list"
                    // Check if the files key in the response is > 0 - atleast 1 file is uploaded
                    if (data.files.length > 0) {
                        for (let fileIndex in data.files) {
                            let fileName = data.files[fileIndex];
                            inputHiddenForFile = "<input type='hidden' name='newRequest_invoicefiles_uploaded[]' value='" + fileName + "'>";
                            findexValue = "123456789101112131415".shuffle()
                            deleteButton = ' <a class="btn-xs deleteInvoiceFileNewRequest" href="#" data-findex="' + findexValue + '"> Delete <i class="fa fa-trash" aria-hidden="true"></i> </a>';
                            $("#invoicesAttachedNewRequest").append("<li class='newRequestDeletefile_" + findexValue + "'>" + fileName + deleteButton + inputHiddenForFile + "</li>");
                        }
                        $(".showOnlyIfInvoicesAttached").show();

                        /* Show the attached invoices list in the same space as the upload area - currently shownig in the Additional detail section */
                        attachedInvoicesHtml = $("#invoicesAttachedNewRequest").html();
                        $("#newRequest_invoicefiles_alertfilelist").html(attachedInvoicesHtml);
                        $("#newRequest_invoicefiles_alert").fadeIn();

                        window.setTimeout(function () {
                            $("#newRequest_invoicefiles_alert").fadeOut();
                        }, 1500);
                    }
                }
                else {
                    // Handle errors here
                    console.log('ERRORS: ' + data.error);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                // Handle errors here
                console.log('ERRORS: ' + textStatus);
                // STOP LOADING SPINNER
            }
        });
    }

    function uploadFilesEdit(event) {
        event.stopPropagation(); // Stop stuff happening
        event.preventDefault(); // Totally stop stuff happening

        request_id = $("body").find("#request_id_val").val();

        // Create a formdata object and add the files
        var data = new FormData();
        data.append("action", "editrequestInvoiceFileUpload");
        data.append("request_id", request_id);
        $.each(filesEdit, function (key, value) {
            data.append(key, value);
        });

        $.ajax({
            url: 'custom/request/request_info_controller.php',
            type: 'POST',
            data: data,
            cache: false,
            dataType: "json",
            contentType: false,
            processData: false,
            success: function (data, textStatus, jqXHR) {
                if (data) {
                    $("#added_report_edit_attachedInvoices_ol").empty();
                    $("#onloadExistingInvoicesPanel").remove();

                    for (var index in data) {
                        fileName = data[index];
                        fileListHtml = "<li> <span> " + fileName + " </span> ";
                        fileListHtml += "<a target='_blank' href='files/" + fileName + "' class='btn-xs btn-primary'>View</a> ";
                        fileListHtml += "<a class='btn-xs deleteAttachedInvoiceFile'  href='#' data-findex='" + index + "' > Delete <i class='fa fa-trash' aria-hidden='true'></i> </a> </li>";
                        $("#added_report_edit_attachedInvoices_ol").append(fileListHtml);
                    }

                    $("#editRequest_invoicefilesForm_filesattachedAgain").show();
                    filesEdit = null;
                    $("#editRequest_invoicefiles_list > ol").empty();
                    $("#editRequest_invoicefiles_list").hide();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log('ERRORS: ' + textStatus);
            }
        });
    }

    /* Existing request - Delete the attached invoice files - */
    $("body").on("click", ".deleteAttachedInvoiceFile", function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        element = $(this);
        // get the request ID
        request_id = $("#request_id_val").val();

        if (!request_id) {
            alert("Could not delete the file. Please reload the page or try again later");
            return;
        }

        // Get the file index - based on the index of the file - delete the index from the db value ( indexed array )
        fileIndex = $(this).attr("data-findex");

        data = {
            action: "deleteInvoiceAttachedFile",
            request_id: request_id,
            fileIndex: fileIndex.toString()
        };

        $.ajax({
            url: relative_path + "request_info_controller.php",
            type: "post",
            data: data,
            dataType: "json",
            success: function (response) {
                if (response) {
                    $(element).parent().remove(); // Remove the li of the file
                }
            }
        });


    });

    /* New request - Delete the attached invoice files */
    $("body").on("click", ".deleteInvoiceFileNewRequest", function (e) {
        e.preventDefault();

        fileIndex = $(this).attr("data-findex");
        className = "newRequestDeletefile_" + fileIndex;

        $("." + className).remove();

        /* hide the invoices attached label if there are no files after deleting this file */
        if ($("#invoicesAttachedNewRequest").children().length == 0) {
            $(".showOnlyIfInvoicesAttached").hide();
        }

    });

    /* Shuffle the letters in the string */
    String.prototype.shuffle = function () {
        var a = this.split(""),
            n = a.length;

        for (var i = n - 1; i > 0; i--) {
            var j = Math.floor(Math.random() * (i + 1));
            var tmp = a[i];
            a[i] = a[j];
            a[j] = tmp;
        }
        return a.join("");
    }

});
