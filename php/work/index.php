<?php
declare(strict_types=1);

require_once './app/Response.php';
require_once './app/Util.php';

function connectDB(): PDO {
    $dbType = 'mysql';
    $dbHost = 'db';
    $charset = 'utf8mb4';
    $dbName = 'todo';
    $dbUser = 'mtkr3';
    $dbPass = 'ymmt1026';
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        return new PDO(
            "$dbType:dbname=$dbName;host=$dbHost;charset=$charset",
            $dbUser,
            $dbPass,
            $options
        );
    } catch (PDOException $e) {
        Response::internalServerError($e->getMessage());
    }
}

class Todo
{
    const DATE_FORMAT = 'Y-m-d H:i:s';

    public $id;

    public $date;

    public $text;

    public static function insert(PDO $db, Datetime $date, string $text): void
    {
        $stmt = $db->prepare('INSERT INTO todo (date, text) VALUES (?, ?)');
        $stmt->execute([$date->format(self::DATE_FORMAT), $text]);
    }

    public static function delete(PDO $db, int $id): void
    {
        $stmt = $db->prepare('DELETE FROM todo WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function exists(PDO $db, int $id): bool
    {
        $stmt = $db->prepare('SELECT * FROM todo WHERE id = ?');
        $stmt->execute([$id]);
        return empty($stmt->fetchAll()) === false;
    }

    public static function fetchAll(PDO $db): array
    {
        $stmt = $db->prepare('SELECT * FROM todo');
        $stmt->execute();
        return array_map(function($x) {
            $todo = new Todo();
            $todo->id = $x['id'];
            $todo->date = Datetime::createFromFormat(self::DATE_FORMAT, $x['date']);
            $todo->text = $x['text'];
            return $todo;
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}

// $content = render('./views/view.php', ['name' => 'world']);
// Response::statusOk($content)->send();

function todoList(): Response
{
    $db = connectDB();
    $todos = Todo::fetchAll($db);
    $content = render('./views/todoList.php', compact('todos'));
    return Response::statusOk($content);
}

function todoCreate(): Response
{
    $errors = [];
    $date = (new DateTime())->format('Y/m/d');
    $text = '';
    $content = render('./views/todoAdd.php', compact('errors', 'date', 'text'));
    return Response::statusOk($content);
}

function todoAdd(): Response
{
    $date = assertPost('date');
    $text = assertPost('text');

    $validate = new Validate();
    $validate->isTrue(empty($text) === false, 'テキストが空です');
    $validate->isTrue(mb_strlen($text) <= 100, 'テキストは100文字までです。');

    $datetime = DateTime::createFromFormat('Y/m/d', $date);
    $validate->isTrue($datetime !== false, '日付の形式が****/**/**の形式ではありません。');

    $errors = $validate->message;
    if (empty($errors)) {
        $db = connectDB();
        Todo::insert($db, $datetime, $text);
        return Response::redirect('/');
    } else {
        $content = render('./views/todoAdd.php', compact('errors', 'date', 'text'));
        return Response::badRequest($content);
    }
}

function todoDelete(): Response
{
    $id = (int) assertPost('id');
    $db = connectDB();
    sqlTransaction($db, function($db) use ($id) {
        assert400(Todo::exists($db, $id));
        Todo::delete($db, $id);
    });
    return Response::redirect('/');
}

function sqlTransaction(PDO $db, callable $func): void
{
    try {
        $db->beginTransaction();
        $func($db);
        $db->commit();
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
}

// function page1(string $text): Response
// {
//     $content = render('./views/page1.php', compact('text'));
//     return Response::statusOk($content);
// }

// function page2(string $text): Response
// {
//     $content = render('./views/page2.php', compact('text'));
//     return Response::statusOk($content);
// }

function onError(Exception $e): Response
{
    if ($e instanceof Exception400) {
        return Response::badRequest('400 Bad Request');
    } elseif ($e instanceof Exception404) {
        return Response::notFound('404 Not Found');
    } else {
        return Response::internalServerError('500 Internal Server Error'); 
    }
}

$routesGet = [
    '/' => 'todoList',
    '/add' => 'todoCreate'
];

$routesPost = [
    '/add' => 'todoAdd',
    '/delete' => 'todoDelete'
];

respond($_SERVER['REQUEST_URI'], $routesGet, $routesPost, 'onError');

function render(string $_file, array $_args): string
{
    extract($_args);

    ob_start();
    ob_implicit_flush(0);
    require $_file;
    return ob_get_clean();
}

function respond(
    string $pathInfo,
    array $routesGet,
    array $routesPost,
    callable $onError
): void {
    try {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $response = getResponse($pathInfo, $routesGet);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $response = getResponse($pathInfo, $routesPost);
        } 
        
        if (isset($response)) {
            $response->send();
        } else {
            throw new Exception404();
        }
    } catch (Exception $e) {
        $onError($e)->send();
    }
}

function getResponse(string $pathInfo, array $callableMap): ?Response
{
    foreach ($callableMap as $route => $f) {
        $matchers = matchRoute($route, $pathInfo);
        if (isset($matchers)) {
            return call_user_func_array($f, $matchers);
        }
    }

    return null;
}

function matchRoute(string $route, string $pathInfo): ?array
{
    $pattern = preg_replace('#:[^/]+#', '([^/]+)', $route);
    $matchers = [];
    if (preg_match("#^${pattern}$#", $pathInfo, $matchers) === 1) {
        array_shift($matchers);
        return $matchers;
    } else {
        return null;
    }
}

class Validate
{
    public $message = [];

    public function isTrue(bool $condition, string $message): void
    {
        if ($condition === false) {
            array_push($this->message, $message);
        }
    }
}

function assert400(bool $condition)
{
    if ($condition === false) {
        throw new Exception400();
    }
}

function assertPost(string $key)
{
    if (isset($_POST[$key])) {
        return $_POST[$key];
    } else {
        throw new Exception400();
    }
}

class Exception400 extends Exception{};
class Exception404 extends Exception{};