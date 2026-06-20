<?php
require_once 'connect.php';
$message = '';
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ime = trim($_POST['ime'] ?? '');
    $prezime = trim($_POST['prezime'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $pass = $_POST['pass'] ?? '';
    $pass2 = $_POST['pass2'] ?? '';
    if ($ime === '' || $prezime === '' || $username === '' || $pass === '' || $pass2 === '') {
        $message = 'Sva polja su obavezna.';
    } elseif ($pass !== $pass2) {
        $message = 'Lozinke nisu iste.';
    } else {
        $check = mysqli_prepare($dbc, 'SELECT id FROM korisnik WHERE korisnicko_ime = ?');
        mysqli_stmt_bind_param($check, 's', $username);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);
        if (mysqli_stmt_num_rows($check) > 0) {
            $message = 'Korisničko ime već postoji.';
        } else {
            $hash = password_hash($pass, PASSWORD_BCRYPT);
            $razina = 0;
            $stmt = mysqli_prepare($dbc, 'INSERT INTO korisnik (ime, prezime, korisnicko_ime, lozinka, razina) VALUES (?, ?, ?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'ssssi', $ime, $prezime, $username, $hash, $razina);
            $success = mysqli_stmt_execute($stmt);
            $message = $success ? 'Korisnik je uspješno registriran.' : 'Registracija nije uspjela.';
        }
    }
}
$pageTitle = 'Registracija';
include 'header.php';
?>
<section class="form-panel">
  <h1>Registracija korisnika</h1>
  <?php if ($message): ?><p class="<?= $success ? 'success' : 'alert' ?>"><?= e($message) ?></p><?php endif; ?>
  <form method="POST" class="form">
    <label>Ime<input type="text" name="ime" required></label>
    <label>Prezime<input type="text" name="prezime" required></label>
    <label>Korisničko ime<input type="text" name="username" required></label>
    <label>Lozinka<input type="password" name="pass" required></label>
    <label>Ponovi lozinku<input type="password" name="pass2" required></label>
    <button type="submit">Registriraj se</button>
  </form>

  <p class="form-note">
    Već imate račun? <a href="administrator.php">Prijavite se ovdje</a>.
  </p>
</section>
<?php include 'footer.php'; ?>
