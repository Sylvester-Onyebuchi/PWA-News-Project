<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/functions.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$article = $id ? getArticle($dbc, $id) : null;

$pageTitle = $article ? $article['naslov'] : 'Članak nije pronađen';

include __DIR__ . '/header.php';
?>

<?php if (!$article || (int)$article['arhiva'] === 1): ?>
  <section class="article-full">
    <h1>Članak nije pronađen</h1>
    <p>Vijest ne postoji ili je arhivirana.</p>
  </section>
<?php else: ?>
  <article class="article-full">
    <span class="date">
      <?= e(date('d.m.Y.', strtotime($article['datum']))) ?> · <?= e(categoryLabel($article['kategorija'])) ?>
    </span>

    <h1><?= e($article['naslov']) ?></h1>

    <p class="lead"><?= e($article['sazetak']) ?></p>

    <?php if (!empty($article['slika'])): ?>
      <img class="article-image" src="img/<?= e($article['slika']) ?>" alt="<?= e($article['naslov']) ?>">
    <?php endif; ?>

    <p><?= nl2br(e($article['tekst'])) ?></p>
  </article>
<?php endif; ?>

<?php include __DIR__ . '/footer.php'; ?>