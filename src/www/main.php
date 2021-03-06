<?php
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/classes/TwistOAuth.phar';

@session_start();

$dotenv = new Dotenv\Dotenv(dirname(dirname(__DIR__)));
$dotenv->load();

if (!isset($_SESSION['logined'])) {
    $url = '/CompareFollows/index.php';
    header("Location: $url");
    header('Content-Type: text/plain; charset=utf-8');
    exit("Redirecting to $url ...");
}

try {
    $pdo = new PDO(
        sprintf('mysql:dbname=%s;host=%s;charset=utf8', $_ENV['DB_NAME'], $_ENV['DB_HOST']),
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    $stmt = $pdo->prepare('SELECT created_at FROM snapshots WHERE userid = ?');
    $stmt->execute([$_SESSION['userid']]);

    $dates = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
} catch (Exception $e) {
    $error = $e->getMessage();
}

header('Content-Type: text/html; charset=utf-8');

?>
<!DOCTYPE html>
<html>
<head>
    <title>CompareFollows</title>
</head>
<body>
    <form action="get.php">
        <input type="submit" value="取得">
    </form>
    <form action="comparison.php" method="post">
        <select name="date1">
            <?php foreach ($dates as $date): ?>
                <option value="<?php echo $date; ?>"><?php echo $date; ?></option>
            <?php endforeach; ?>
        </select>
        と
        <select name="date2">
            <?php foreach ($dates as $date): ?>
                <option value="<?php echo $date; ?>"><?php echo $date; ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <input type="submit" value="比較">
    </form>
    <br><br>
    <a href="/CompareFollows/logout.php">Logout</a>
</body>
</html>
