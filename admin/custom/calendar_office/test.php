<?php

/**
 * Created by PhpStorm.
 * User: misha
 * Date: 2017-04-09
 * Time: 1:09 AM
 */
//ini_set("include_path", '/home/iliveinx/php:' . ini_get("include_path") );
include_once("db.php");
$sql = 'SELECT * FROM building_infos';
$result = $db->query($sql);
$resultError = $result->errorInfo();
$rowNumbers = $result->rowCount();
if ($resultError[0] != 00000) {
    var_dump($resultError);
}
echo "<p>Row Number=$rowNumbers</p>";
while ($row = $result->fetch()) {

    echo "<p>{$row['building_name']}</p>";
}

$statement = $db->prepare("SELECT * FROM building_infos");
$statement->execute();
$results = $statement->fetchAll(PDO::FETCH_ASSOC);
$json = json_encode($results);
//var_dump($json);
?>
<?
if (!function_exists("gettext")) {
    echo "gettext is not installed\n";
} else {
    echo "gettext is supported\n";
}
?>

<?php include 'i18n_setup.php' ?>
<div id="header">
    <h1><?= sprintf(gettext('Welcome, %s!'), $name) ?></h1>
    <!-- code indented this way only for legibility -->
    <?php if ($unread) : ?>
        <h2><?= sprintf(
                ngettext(
                    'Only one unread message',
                    '%d unread messages',
                    $unread
                ),
                $unread
            ) ?>
        </h2>
    <?php endif ?>
</div>

<h1><?= gettext('Introduction') ?></h1>
<p><?= gettext('We\'re now translating some strings') ?></p>
<?= date("Y-m-d H:i:s") ?>