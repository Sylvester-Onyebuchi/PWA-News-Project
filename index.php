<?php
require_once 'connect.php';
$pageTitle = 'Početna';
include 'header.php';
?>
<section class="hero">
  <h1>Najnovije vijesti</h1>
  <p>Portal izrađen prema fazama projekta: HTML/CSS, PHP forme, MySQL i sigurnost.</p>
</section>
<?php foreach (categories() as $key => $label): ?>
<section class="news-section">
  <div class="section-title">
    <h2><?= e($label) ?></h2>
    <a href="kategorija.php?id=<?= e($key) ?>">Prikaži sve</a>
  </div>
  <div class="news-grid">
    <?php $result = getNewsByCategory($dbc, $key, 4); ?>
    <?php if (mysqli_num_rows($result) === 0): ?>
      <p class="empty">Nema objavljenih vijesti u ovoj kategoriji.</p>
    <?php endif; ?>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
      <article class="card">
        <a href="clanak.php?id=<?= (int)$row['id'] ?>">
          <?php if (!empty($row['slika'])): ?><img src="img/<?= e($row['slika']) ?>" alt="<?= e($row['naslov']) ?>"><?php endif; ?>
          <span class="date"><?= e($row['datum']) ?></span>
          <h3><?= e($row['naslov']) ?></h3>
          <p><?= e($row['sazetak']) ?></p>
        </a>
      </article>
    <?php endwhile; ?>
  </div>
</section>
<?php endforeach; ?>
<?php include 'footer.php'; ?>
