<?php
declare(strict_types=1);

ob_start();
ob_implicit_flush();
?>

<?php if (empty($errors) === false) : ?>
<ul>
    <?php foreach ($errors as $error): ?>
    <li>
        <?php safeEcho($error) ?>
    </li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>

<form action="/add" method="POST">
    <ul>
        <li>
            <label>日付
                <input type="text" name="date" value="<?php safeEcho($date) ?>">
            </label>
        </li>
        <li>
            <label for="">テキスト
                <input type="text" name="text" value="<?php safeEcho($text) ?>">
            </label>
        </li>
        <li>
            <button type="submit">送信</button>
        </li> 
    </ul>
</form>

<?php
$title = 'ToDo追加';
$body = ob_get_clean();
require './views/layout.php';
