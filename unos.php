<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['username'])) {
    header('Location: administrator.php?redirect=unos.php');
    exit;
}

$isAdmin = isset($_SESSION['level']) && (int)$_SESSION['level'] === 1;

if ($isAdmin) {
    header('Location: administrator.php');
    exit;
}

$pageTitle = 'Unos vijesti';
include 'header.php';
?>
<section class="form-panel">
  <h1>Unos nove vijesti</h1>
  <form name="unosVijesti" action="skripta.php" method="POST" enctype="multipart/form-data" class="form">
    <label>Naslov vijesti<input type="text" name="title" required maxlength="255"></label>
    <label>Kratki sadržaj vijesti<textarea name="about" rows="4" required maxlength="500"></textarea></label>
    <label>Sadržaj vijesti<textarea name="content" rows="10" required></textarea></label>
    <label>Kategorija vijesti<select name="category" required><?php foreach(categories() as $key=>$label): ?><option value="<?= e($key) ?>"><?= e($label) ?></option><?php endforeach; ?></select></label>
    <label>Slika<input type="file" name="pphoto" accept="image/jpeg,image/png,image/gif,image/webp"></label>
    <button type="submit">Spremi vijest</button>
  </form>
</section>
<?php include 'footer.php'; ?>
