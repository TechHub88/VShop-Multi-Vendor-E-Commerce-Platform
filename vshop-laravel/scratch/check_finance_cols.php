<?php
try {
    $cols = \DB::select("SHOW COLUMNS FROM seller_finances");
    foreach ($cols as $col) {
        echo $col->Field . " (" . $col->Type . ")\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
