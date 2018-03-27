<?php
require_once '../../../lib/base.php';
$json = OC::$APPSROOTS[0]['path'].'/groupalert/settings.json';

$fields = ['texte', 'checked', 'groups'];

if (file_exists($json)) {

    $jsonContent = file_get_contents($json);
    $jsonExist = json_decode($jsonContent, true);

    foreach ($fields as $field) {
        if (isset($_GET[$field])) {
            $json_data[$field] = htmlentities($_GET[$field]);
        }else{
            $json_data[$field] = $jsonExist[$field];
        }
    }
    $fp = fopen($json, 'w');
    fwrite($fp, json_encode($json_data));
    fclose($fp);
}
?>