<?php
declare(strict_types=1);

ob_start();
ob_implicit_flush(0);
?>

<?php ob_start(); ob_implicit_flush(0); ?>

<a href="/add">追加</a>

<table>
<?php foreach ($todos as $todo): ?>
    <tr>
        <td>
            <?php safeEcho($todo->date->format('Y/m/d')); ?>
        </td>
        <td>
            <?php safeEcho($todo->text); ?>
        </td>
        <td>
            <form action="/delete" method="POST">
                <input type="hidden" name="id" value="<?php echo $todo->id ?>">
                <button type="submit">削除</button>
            </form>
        </td>
    </tr>
<?php endforeach; ?>
</table>

<?php
$title = 'ToDo一覧';
$body  = ob_get_clean();
require './views/layout.php';