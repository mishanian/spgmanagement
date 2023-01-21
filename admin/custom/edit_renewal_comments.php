<?php session_start();

if (isset($_SESSION["company_id"])) {
    $company_id = $_SESSION["company_id"];
}
if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../pdo/";
}
$file = $path . 'dbconfig.php';
include_once($file);

// Check if the renew form is submitted
if (!empty($_POST['submitted'])) {
    $lease_id = $_POST['lease_id'];
    $comments = $_POST['comments'];
    $sql = "UPDATE lease_infos SET comments = :comments WHERE id = :lease_id";
    $stmt = $DB_con->prepare($sql);
    $stmt->bindParam(':comments', $comments);
    $stmt->bindParam(':lease_id', $lease_id);
    $stmt->execute();
    echo "The comment is saved.\r\n";
    // header("Location: ../../admin/custom/edit_renewal_comments.php?id=$renewal_id");
    exit();
} else { // if it is NOT submit show the form
    $lease_id  = $_GET['id'];
    $SelectSql = "select * from lease_infos LI LEFT JOIN apartment_infos APP ON APP.apartment_id=LI.apartment_id";
    $SelectSql .= " where LI.id=$lease_id ";

    $statement = $DB_con->prepare($SelectSql);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $row    = $result[0];
    $comments = $row['comments'];
}
?>
<form method="post" class="ewForm ewEditForm form-horizontal" id="modalform" name="modalform">
    <input type="hidden" name="lease_id" id="lease_id" value="<?= $lease_id ?>">
    Comments:<br> <textarea name="comments" id="comments" class="form-control" rows="5"
        cols="50"><?= $comments ?></textarea>

    <input type="hidden" name="submitted" id="submitted" value="1">
    <button id="form-submit" class="btn btn-primary">Save</button>
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
</form>

<script>
$(function() {
    $("#form-submit").click(function() {
        $.ajax({
            type: "POST",
            data: $('#modalform').serialize(),
            url: "custom/edit_renewal_comments.php",
            async: true,
            cache: true,
            timeout: 20000,
            success: function(response) {
                alert(response);
                $('#MyModal').modal('hide');
                location.reload();
                // window.location.href =
                //     "/view_renew_detailslist.php";
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert("Status" + textStatus);
            }
        });
        return false;
    });
});
</script>