<?php
include "aliexpress_api.php";

echo "<h2>API Status Check</h2><pre>";
print_r( aliAPI("search?query=tshirt&page=1") );
echo "</pre>";
