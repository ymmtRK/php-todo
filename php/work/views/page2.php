<?php
declare(strict_types=1);

ob_start();
ob_implicit_flush(0);
?>

<h1>Page2</h1>
<p><?php safeEcho($text) ?></p>

<?php
$title = 'page2';
$body = ob_get_clean();
require './views/layout.php';