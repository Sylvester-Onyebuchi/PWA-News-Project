<?php
session_start();

$timeout = 1800; // 30 minutes

if (isset($_SESSION['username'])) {
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout) {
        session_unset();
        session_destroy();

        session_start();
        $_SESSION['expired'] = true;

        header('Location: administrator.php');
        exit;
    }

    $_SESSION['LAST_ACTIVITY'] = time();
}

require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/functions.php';

$loginMessage = '';
$allowedRedirects = ['unos.php'];
$redirect = $_GET['redirect'] ?? $_POST['redirect'] ?? '';

if (!in_array($redirect, $allowedRedirects, true)) {
    $redirect = '';
}

if (isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = mysqli_prepare(
        $dbc,
        'SELECT id, ime, prezime, korisnicko_ime, lozinka, razina 
         FROM korisnik 
         WHERE korisnicko_ime = ?'
    );

    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['lozinka'])) {
        $_SESSION['username'] = $user['korisnicko_ime'];
        $_SESSION['ime'] = $user['ime'];
        $_SESSION['level'] = (int)$user['razina'];
        $_SESSION['LAST_ACTIVITY'] = time();

        if ((int)$user['razina'] === 1) {
            header('Location: administrator.php');
        } elseif ($redirect !== '') {
            header('Location: ' . $redirect);
        } else {
            header('Location: unos.php');
        }
        exit;
    } else {
        $loginMessage = 'Korisnik nije pronađen ili lozinka nije ispravna. Morate se prvo registrirati.';
    }
}

$isAdmin = isset($_SESSION['level']) && (int)$_SESSION['level'] === 1;

if ($isAdmin && isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];

    $article = getArticle($dbc, $id);

    if ($article) {
        if (!empty($article['slika'])) {
            deleteImageFile($article['slika']);
        }

        $stmt = mysqli_prepare($dbc, 'DELETE FROM vijesti WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
    }

    header('Location: administrator.php');
    exit;
}

if ($isAdmin && isset($_POST['update_id'])) {
    $id = (int)$_POST['update_id'];

    $title = trim($_POST['title'] ?? '');
    $about = trim($_POST['about'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category = $_POST['category'] ?? 'sport';
    $archive = isset($_POST['archive']) ? 1 : 0;

    $currentImage = $_POST['current_image'] ?? null;
    $newImage = uploadImage('pphoto');

    if ($newImage) {
        if ($currentImage) {
            deleteImageFile($currentImage);
        }

        $picture = $newImage;
    } else {
        $picture = $currentImage;
    }

    $stmt = mysqli_prepare(
        $dbc,
        'UPDATE vijesti
         SET naslov = ?,
             sazetak = ?,
             tekst = ?,
             slika = ?,
             kategorija = ?,
             arhiva = ?
         WHERE id = ?'
    );

    mysqli_stmt_bind_param(
        $stmt,
        'sssssii',
        $title,
        $about,
        $content,
        $picture,
        $category,
        $archive,
        $id
    );

    mysqli_stmt_execute($stmt);

    header('Location: administrator.php');
    exit;
}

$pageTitle = $isAdmin ? 'Administracija' : 'Prijava';
include __DIR__ . '/header.php';
?>

<section class="admin-page">
  <h1><?= $isAdmin ? 'Administracija' : 'Prijava' ?></h1>

  <?php if (!$isAdmin): ?>

    <?php if (!empty($_SESSION['username']) && (int)($_SESSION['level'] ?? 0) === 0): ?>

      <p class="success">
        Bok <?= e($_SESSION['ime'] ?? $_SESSION['username']) ?>, uspješno ste prijavljeni kao korisnik.
        Možete unijeti novu vijest.
      </p>

      <a class="button-link" href="unos.php">Unesi vijest</a>

    <?php else: ?>

      <?php if (!empty($_SESSION['expired'])): ?>
        <p class="alert">
          Vaša sesija je istekla zbog neaktivnosti. Molimo prijavite se ponovno.
        </p>
        <?php unset($_SESSION['expired']); ?>
      <?php endif; ?>

      <?php if ($loginMessage): ?>
        <p class="alert">
          <?= e($loginMessage) ?> <a href="registracija.php">Registracija</a>
        </p>
      <?php endif; ?>

      <form method="POST" class="form login-form">
        <?php if ($redirect !== ''): ?>
          <input type="hidden" name="redirect" value="<?= e($redirect) ?>">
        <?php endif; ?>

        <label>
          Korisničko ime
          <input type="text" name="username" required>
        </label>

        <label>
          Lozinka
          <input type="password" name="password" required>
        </label>

        <button type="submit" name="login">Prijava</button>
      </form>

      <p class="form-note">
        Nemate račun? <a href="registracija.php">Registrirajte se ovdje</a>.
      </p>

    <?php endif; ?>

  <?php else: ?>

    <p class="success">
      Prijavljeni ste kao administrator: <?= e($_SESSION['username']) ?>
    </p>

    <?php $result = mysqli_query($dbc, 'SELECT * FROM vijesti ORDER BY id DESC'); ?>

    <?php while ($row = mysqli_fetch_assoc($result)): ?>

      <form method="POST" enctype="multipart/form-data" class="admin-card">
        <input type="hidden" name="update_id" value="<?= (int)$row['id'] ?>">
        <input type="hidden" name="current_image" value="<?= e($row['slika']) ?>">

        <label>
          Naslov
          <input type="text" name="title" value="<?= e($row['naslov']) ?>" required>
        </label>

        <label>
          Sažetak
          <textarea name="about" rows="3" required><?= e($row['sazetak']) ?></textarea>
        </label>

        <label>
          Tekst
          <textarea name="content" rows="6" required><?= e($row['tekst']) ?></textarea>
        </label>

        <label>
          Kategorija
          <select name="category">
            <?php foreach (categories() as $key => $label): ?>
              <option value="<?= e($key) ?>" <?= $row['kategorija'] === $key ? 'selected' : '' ?>>
                <?= e($label) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </label>

        <label>
          Nova slika
          <input type="file" name="pphoto" accept="image/jpeg,image/png,image/gif,image/webp">
        </label>

        <?php if (!empty($row['slika'])): ?>
          <img class="admin-thumb" src="img/<?= e($row['slika']) ?>" alt="Slika">
        <?php endif; ?>

        <label class="checkbox">
          <input type="checkbox" name="archive" value="1" <?= (int)$row['arhiva'] === 1 ? 'checked' : '' ?>>
          Arhivirano
        </label>

        <div class="admin-actions">
          <button type="submit">Spremi promjene</button>
        </div>
      </form>

      <form method="POST" onsubmit="return confirm('Obrisati vijest?');" class="delete-form">
        <input type="hidden" name="delete_id" value="<?= (int)$row['id'] ?>">
        <button type="submit" class="danger">Obriši</button>
      </form>

    <?php endwhile; ?>

  <?php endif; ?>

</section>

<?php include 'footer.php'; ?>
