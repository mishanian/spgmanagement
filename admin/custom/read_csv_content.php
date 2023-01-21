<?php
include_once '../pdo/dbconfig.php';
$Crud = new CRUD($DB_con);
if (!empty($_POST['submit']) && $_POST['submit'] == 'upload') {

    $fileName = $_FILES['file']['name'];
    $fileNameNoExt = pathinfo($_FILES['file']['name'], PATHINFO_FILENAME);
    $fileTmpPath = $_FILES['file']['tmp_name'];
    $fileSize = $_FILES['file']['size'];
    $fileType = $_FILES['file']['type'];
    $fileNameCmps = explode(".", $fileName);
    $end = end($fileNameCmps);
    $fileExtension = strtolower($end);

    $newFileName = $fileNameNoExt . '-' . date("Y-m-d") . '.' . $fileExtension;
    $allowedfileExtensions = array('csv');
    if (in_array($fileExtension, $allowedfileExtensions)) {
        $uploadFileDir = 'files/csv/';
        $dest_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $message = 'File is successfully uploaded.<br><hr>';
        } else {
            $message = 'There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.';
        }
    }
    $csv = array_map('str_getcsv', file($dest_path));
    array_walk($csv, function (&$a) use ($csv) {
        $a = array_combine($csv[0], $a);
    });
    array_shift($csv); # remove column header
//    print_r($csv);
    echo "$newFileName is uploaded.";
    echo "<table class='table thead-dark table-striped'>";
    echo "<tr><th>Payment Date</th><th>Amount</th><th>Description</th></tr>";
    foreach ($csv as $row) {
    //    print_r($row);
        $amount=$row['CAD$'];
        $payment_date=DateTime::createFromFormat('m/d/Y', $row['Transaction Date'])->format('Y-m-d');
        list($paid_owner_id,$paid_owner_account_ids)=explode("-",$_POST['owner_account_id']);
        $description=$row['Description 1']." ".$row['Description 2'];
        $company_id=$_SESSION['company_id'];
        $employee_id=$_SESSION['employee_id'];
        if ($row['CAD$']>0){$expence=$row['CAD$'];$income=0;$payment_inex_id=1;$payment_inex_type_id=2;}else{$expence=0;$income=$row['CAD$'];$payment_inex_id=2;$payment_inex_type_id=4;}
        $sql = "insert into payment_infos (is_book, payment_inex_id, payment_inex_type_id, payment_action_id, payment_date, amount, income, expence, cheque_no, paid_owner_id, paid_owner_account_ids , comments, company_id, employee_id) 
                values (4,$payment_inex_id,$payment_inex_type_id,7,'".$payment_date."','".$amount."','".$income."','".$expence."','".$row['Cheque Number']."','$paid_owner_id','$paid_owner_account_ids','".$description."',$company_id, $employee_id)";
//echo $sql;
        $Crud->query($sql);
        $Crud->execute();
        echo "<tr><td>$payment_date</td><td>$amount</td><td>$description</td></tr>";
//        die();
    }
echo "</table>";
} else {
    ?>
    <style>


        .box {
            font-size: 1.25rem; /* 20 */
            background-color: #c8dadf;
            position: relative;
            padding: 100px 20px;
        }

        .box.has-advanced-upload {
            outline: 2px dashed #92b0b3;
            outline-offset: -10px;

            -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
            transition: outline-offset .15s ease-in-out, background-color .15s linear;
        }

        .box.is-dragover {
            outline-offset: -20px;
            outline-color: #c8dadf;
            background-color: #fff;
        }

        .box__dragndrop,
        .box__icon {
            display: none;
        }

        .box.has-advanced-upload .box__dragndrop {
            display: inline;
        }

        .box.has-advanced-upload .box__icon {
            width: 100%;
            height: 80px;
            fill: #92b0b3;
            display: block;
            margin-bottom: 40px;
        }

        .box.is-uploading .box__input,
        .box.is-success .box__input,
        .box.is-error .box__input {
            visibility: hidden;
        }

        .box__uploading,
        .box__success,
        .box__error {
            display: none;
        }

        .box.is-uploading .box__uploading,
        .box.is-success .box__success,
        .box.is-error .box__error {
            display: block;
            position: absolute;
            top: 50%;
            right: 0;
            left: 0;

            -webkit-transform: translateY(-50%);
            transform: translateY(-50%);
        }

        .box__uploading {
            font-style: italic;
        }

        .box__success {
            -webkit-animation: appear-from-inside .25s ease-in-out;
            animation: appear-from-inside .25s ease-in-out;
        }

        @-webkit-keyframes appear-from-inside {
            from {
                -webkit-transform: translateY(-50%) scale(0);
            }
            75% {
                -webkit-transform: translateY(-50%) scale(1.1);
            }
            to {
                -webkit-transform: translateY(-50%) scale(1);
            }
        }

        @keyframes appear-from-inside {
            from {
                transform: translateY(-50%) scale(0);
            }
            75% {
                transform: translateY(-50%) scale(1.1);
            }
            to {
                transform: translateY(-50%) scale(1);
            }
        }

        .box__restart {
            font-weight: 700;
        }

        .box__restart:focus,
        .box__restart:hover {
            color: #39bfd3;
        }

        .js .box__file {
            width: 0.1px;
            height: 0.1px;
            opacity: 0;
            overflow: hidden;
            position: absolute;
            z-index: -1;
        }

        .js .box__file + label {
            max-width: 80%;
            text-overflow: ellipsis;
            white-space: nowrap;
            cursor: pointer;
            display: inline-block;
            overflow: hidden;
        }

        .js .box__file + label:hover strong,
        .box__file:focus + label strong,
        .box__file.has-focus + label strong {
            color: #39bfd3;
        }

        .js .box__file:focus + label,
        .js .box__file.has-focus + label {
            outline: 1px dotted #000;
            outline: -webkit-focus-ring-color auto 5px;
        }

        .js .box__file + label * {
            /* pointer-events: none; */ /* in case of FastClick lib use */
        }

        .no-js .box__file + label {
            display: none;
        }

        .no-js .box__button {
            display: block;
        }

        .box__button {
            font-weight: 700;
            color: #e5edf1;
            background-color: #39bfd3;
            display: none;
            padding: 8px 16px;
            margin: 40px auto 0;
        }

        .box__button:hover,
        .box__button:focus {
            background-color: #0f3c4b;
        }

    </style>
    <form method="post" action="read_csv.php" enctype="multipart/form-data" novalidate class="box">
        <div class="box__input">
            <svg class="box__icon" xmlns="http://www.w3.org/2000/svg" width="50" height="43" viewBox="0 0 50 43">
                <path
                        d="M48.4 26.5c-.9 0-1.7.7-1.7 1.7v11.6h-43.3v-11.6c0-.9-.7-1.7-1.7-1.7s-1.7.7-1.7 1.7v13.2c0 .9.7 1.7 1.7 1.7h46.7c.9 0 1.7-.7 1.7-1.7v-13.2c0-1-.7-1.7-1.7-1.7zm-24.5 6.1c.3.3.8.5 1.2.5.4 0 .9-.2 1.2-.5l10-11.6c.7-.7.7-1.7 0-2.4s-1.7-.7-2.4 0l-7.1 8.3v-25.3c0-.9-.7-1.7-1.7-1.7s-1.7.7-1.7 1.7v25.3l-7.1-8.3c-.7-.7-1.7-.7-2.4 0s-.7 1.7 0 2.4l10 11.6z"/>
            </svg>
            <br><br>
            Select the Account Name to be uploaded:
            <select name="owner_account_id">
                <?
                $sql = "select owner_account_id, OI.owner_id, full_name as owner_name, `name` as account_name from owner_accounts OA left join owner_infos OI on OA.owner_id=OI.owner_id where OA.company_id=" . $_SESSION['company_id']." order by full_name,`name` ";
                $result = $Crud->query($sql);
                $rows = $Crud->resultSet();
                foreach ($rows as $row) {
                    echo "<option value='" . $row['owner_id']."-".$row['owner_account_id'] . "'>" . $row['owner_name'] . " - " . $row['account_name'] . "</option>";
                }
                ?>



            </select><br><br>
            <input type="file" name="file" id="file" class="box__file" data-multiple-caption="{count} files selected"
                   multiple/>
            <br>
            <label for="file"><strong>Choose a file</strong><span
                        class="box__dragndrop"> or drag it here</span>.</label>
            <button class="btn btn-default" type="submit" class="box__button" name="submit" value="upload">Upload
            </button>
        </div>

    </form>


    <script>

        'use strict';

        ;(function (document, window, index) {
            // feature detection for drag&drop upload
            var isAdvancedUpload = function () {
                var div = document.createElement('div');
                return (('draggable' in div) || ('ondragstart' in div && 'ondrop' in div)) && 'FormData' in window && 'FileReader' in window;
            }();


            // applying the effect for every form
            var forms = document.querySelectorAll('.box');
            Array.prototype.forEach.call(forms, function (form) {
                var input = form.querySelector('input[type="file"]'),
                    label = form.querySelector('label'),
                    errorMsg = form.querySelector('.box__error span'),
                    restart = form.querySelectorAll('.box__restart'),
                    droppedFiles = false,
                    showFiles = function (files) {
                        label.textContent = files.length > 1 ? (input.getAttribute('data-multiple-caption') || '').replace('{count}', files.length) : files[0].name;
                    },
                    triggerFormSubmit = function () {
                        var event = document.createEvent('HTMLEvents');
                        event.initEvent('submit', true, false);
                        form.dispatchEvent(event);
                    };

                // letting the server side to know we are going to make an Ajax request
                var ajaxFlag = document.createElement('input');
                ajaxFlag.setAttribute('type', 'hidden');
                ajaxFlag.setAttribute('name', 'ajax');
                ajaxFlag.setAttribute('value', 1);
                form.appendChild(ajaxFlag);

                // automatically submit the form on file select
                input.addEventListener('change', function (e) {
                    showFiles(e.target.files);


                    triggerFormSubmit();


                });

                // drag&drop files if the feature is available
                if (isAdvancedUpload) {
                    form.classList.add('has-advanced-upload'); // letting the CSS part to know drag&drop is supported by the browser

                    ['drag', 'dragstart', 'dragend', 'dragover', 'dragenter', 'dragleave', 'drop'].forEach(function (event) {
                        form.addEventListener(event, function (e) {
                            // preventing the unwanted behaviours
                            e.preventDefault();
                            e.stopPropagation();
                        });
                    });
                    ['dragover', 'dragenter'].forEach(function (event) {
                        form.addEventListener(event, function () {
                            form.classList.add('is-dragover');
                        });
                    });
                    ['dragleave', 'dragend', 'drop'].forEach(function (event) {
                        form.addEventListener(event, function () {
                            form.classList.remove('is-dragover');
                        });
                    });
                    form.addEventListener('drop', function (e) {
                        droppedFiles = e.dataTransfer.files; // the files that were dropped
                        showFiles(droppedFiles);


                        triggerFormSubmit();

                    });
                }


                // restart the form if has a state of error/success
                Array.prototype.forEach.call(restart, function (entry) {
                    entry.addEventListener('click', function (e) {
                        e.preventDefault();
                        form.classList.remove('is-error', 'is-success');
                        input.click();
                    });
                });

                // Firefox focus bug fix for file input
                input.addEventListener('focus', function () {
                    input.classList.add('has-focus');
                });
                input.addEventListener('blur', function () {
                    input.classList.remove('has-focus');
                });

            });
        }(document, window, 0));

    </script>


    <?php
}
?>