<?php
// Check if boosted_listings has seller_id column and if boost_amount column exists
try {
    $cols = \DB::select("SHOW COLUMNS FROM boosted_listings");
    foreach ($cols as $col) {
        echo $col->Field . " (" . $col->Type . ")\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
