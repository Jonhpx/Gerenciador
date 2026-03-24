<?php
// actions.php
declare(strict_types=1);
require __DIR__ . '/config.php';

function redirect_back(): void {
  $back = $_SERVER['HTTP_REFERER'] ?? 'index.php';
  header("Location: $back");
  exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'add') {
  $title = trim($_POST['title'] ?? '');
  if ($title !== '') {
    $stmt = $pdo->prepare("INSERT INTO tasks (title) VALUES (:title)");
    $stmt->execute([':title' => $title]);
  }
  redirect_back();
}

if ($action === 'toggle') {
  $id = (int)($_POST['id'] ?? 0);
  $stmt = $pdo->prepare("UPDATE tasks SET is_done = 1 - is_done WHERE id = :id");
  $stmt->execute([':id' => $id]);
  redirect_back();
}

if ($action === 'delete') {
  $id = (int)($_POST['id'] ?? 0);
  $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
  $stmt->execute([':id' => $id]);
  redirect_back();
}

if ($action === 'edit') {
  $id = (int)($_POST['id'] ?? 0);
  $title = trim($_POST['title'] ?? '');
  if ($id > 0 && $title !== '') {
    $stmt = $pdo->prepare("UPDATE tasks SET title = :title WHERE id = :id");
    $stmt->execute([':title' => $title, ':id' => $id]);
  }
  redirect_back();
}

redirect_back();