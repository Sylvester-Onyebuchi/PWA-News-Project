<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/functions.php';

$category = $_GET['id'] ?? 'sport';

if (!array_key_exists($category, categories())) {
    $category = 'sport';
}

$pageTitle = categoryLabel($category);

include __DIR__ . '/header.php';

$result = getNewsByCategory($dbc, $category);
?>

<section class="news-section">
  <h1><?= e(categoryLabel($category)) ?></h1>

  <div class="news-grid">
    <?php if (mysqli_num_rows($result) === 0): ?>
      <p class="empty">Nema vijesti za odabranu kategoriju.</p>
     <a href="unos.php" class="btn">Unesite vijesti</a>
    <?php endif; ?>

    <?php while ($row = mysqli_fetch_assoc($result)): ?>
      <article class="card">
        <a href="clanak.php?id=<?= (int)$row['id'] ?>">
          <?php if (!empty($row['slika'])): ?>
            <img src="img/<?= e($row['slika']) ?>" alt="<?= e($row['naslov']) ?>">
          <?php endif; ?>

          <span class="date"><?= e($row['datum']) ?></span>
          <h3><?= e($row['naslov']) ?></h3>
          <p><?= e($row['sazetak']) ?></p>
        </a>
      </article>
    <?php endwhile; ?>
  </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>