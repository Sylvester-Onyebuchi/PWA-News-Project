<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['username'])) {
    header('Location: administrator.php?redirect=unos.php');
    exit;
}

if (isset($_SESSION['level']) && (int)$_SESSION['level'] === 1) {
    header('Location: administrator.php');
    exit;
}

require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/functions.php';

requirePost();

$title = trim($_POST['title'] ?? '');
$about = trim($_POST['about'] ?? '');
$content = trim($_POST['content'] ?? '');
$category = $_POST['category'] ?? 'sport';
$archive = 0;
$date = date('Y-m-d H:i:s');
$errors = [];

if ($title === '') {
    $errors[] = 'Naslov je obavezan.';
}

if ($about === '') {
    $errors[] = 'Sažetak je obavezan.';
}

if ($content === '') {
    $errors[] = 'Sadržaj je obavezan.';
}

if (!array_key_exists($category, categories())) {
    $errors[] = 'Kategorija nije ispravna.';
}

$picture = uploadImage('pphoto');

$newId = null;

if (!$errors) {
    $stmt = mysqli_prepare(
        $dbc,
        'INSERT INTO vijesti 
        (datum, naslov, sazetak, tekst, slika, kategorija, arhiva) 
        VALUES (?, ?, ?, ?, ?, ?, ?)'
    );

    if (!$stmt) {
        die('SQL prepare error: ' . mysqli_error($dbc));
    }

    mysqli_stmt_bind_param(
        $stmt,
        'ssssssi',
        $date,
        $title,
        $about,
        $content,
        $picture,
        $category,
        $archive
    );

    if (!mysqli_stmt_execute($stmt)) {
        die('SQL execute error: ' . mysqli_stmt_error($stmt));
    }

    $newId = mysqli_insert_id($dbc);
}

$pageTitle = 'Obrada unosa';
include __DIR__ . '/header.php';
?>

<section class="article-full">
    <?php if ($errors): ?>
        <h1>Greška pri unosu</h1>

        <?php foreach ($errors as $error): ?>
            <p class="alert"><?= e($error) ?></p>
        <?php endforeach; ?>

        <a class="button-link" href="unos.php">Natrag na unos</a>

    <?php else: ?>
        <h1><?= e($title) ?></h1>

        <span class="date">
            <?= e($date) ?> · <?= e(categoryLabel($category)) ?>
        </span>

        <?php if (!empty($picture)): ?>
            <img class="article-image" src="img/<?= e($picture) ?>" alt="<?= e($title) ?>">
        <?php endif; ?>

        <p class="lead"><?= e($about) ?></p>
        <p><?= nl2br(e($content)) ?></p>

        <a class="button-link" href="clanak.php?id=<?= (int)$newId ?>">
            Otvori članak
        </a>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/footer.php'; ?>
