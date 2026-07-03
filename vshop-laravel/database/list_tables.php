<?php
$db = new PDO('sqlite:database/database.sqlite');
$res = $db->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
while($row = $res->fetch(PDO::FETCH_ASSOC)) {
    echo $row['name'] . "\n";
}
