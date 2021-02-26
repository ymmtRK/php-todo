<?php
declare(strict_types=1);

ob_start();
ob_implicit_flush(0);
?>

<h1>Page1</h1>
<p><?php safeEcho($text) ?></p>

<?php
$title = 'page1';
$body = ob_get_clean();
require './views/layout.php';