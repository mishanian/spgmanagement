<?php
if (isset($_POST['submit'])) {
    $countfiles = count($_FILES['file']['name']);
    for ($i = 0; $i < $countfiles; $i++) {
        $filename = $_FILES['file']['name'][$i];
        move_uploaded_file($_FILES['file']['tmp_name'][$i], '..files/gov_files/' . $filename);
        //         $sql = "INSERT INTO fileup(id,name) VALUES ('$filename','$filename')";
        // $db->query($sql);
    }
}
?>
<form method='post' action='' enctype='multipart/form-data'>
    <input type='text' name='prefix' value="RL-31.CS" />
    <input type='text' name='year' value="<?= date('Y') - 1 ?>" />
    <input type="file" name="file[]" id="file" multiple>
    <input type='submit' name='submit' value='Upload'>
</form>
?>