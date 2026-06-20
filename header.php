<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/functions.php';
$isAdminHeader = isset($_SESSION['level']) && (int)$_SESSION['level'] === 1;
?>
<!DOCTYPE html>
<html lang="hr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? e($pageTitle) : 'PWA News Portal' ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
  <div class="container header-inner">
    <a class="logo" href="index.php">PWA Vijesti</a>
    <nav class="main-nav" aria-label="Glavna navigacija">
      <a href="index.php">Početna</a>
      <a href="kategorija.php?id=sport">Sport</a>
      <a href="kategorija.php?id=kultura">Kultura</a>
      <a href="<?= $isAdminHeader ? 'administrator.php' : 'unos.php' ?>">Unos</a>
      <a href="administrator.php">Prijava</a>
      <?php if (!empty($_SESSION['username'])): ?>
        <a href="logout.php">Odjava</a>
      <?php else: ?>
        <a href="registracija.php">Registracija</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<main class="container page-content">
