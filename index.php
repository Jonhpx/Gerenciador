<?php
// index.php
declare(strict_types=1);
require __DIR__ . '/config.php';

$filter = $_GET['filter'] ?? 'all';
$where = '';
$params = [];

if ($filter === 'done') {
  $where = 'WHERE is_done = :done';
  $params[':done'] = 1;
} elseif ($filter === 'pending') {
  $where = 'WHERE is_done = :done';
  $params[':done'] = 0;
}

$stmt = $pdo->prepare("SELECT * FROM tasks $where ORDER BY id DESC");
$stmt->execute($params);
$tasks = $stmt->fetchAll();
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>To-Do List (PHP + MySQL)</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <main class="container">
    <h1>To‑Do List</h1>

    <form class="add" method="post" action="actions.php">
      <input type="hidden" name="action" value="add">
      <input name="title" placeholder="Digite uma tarefa..." maxlength="255" required>
      <button type="submit">Adicionar</button>
    </form>

    <nav class="filters">
      <a class="<?= $filter === 'all' ? 'active' : '' ?>" href="?filter=all">Todas</a>
      <a class="<?= $filter === 'pending' ? 'active' : '' ?>" href="?filter=pending">Pendentes</a>
      <a class="<?= $filter === 'done' ? 'active' : '' ?>" href="?filter=done">Concluídas</a>
    </nav>

    <ul class="list">
      <?php foreach ($tasks as $task): ?>
        <li class="item <?= (int)$task['is_done'] === 1 ? 'done' : '' ?>">
          <form class="inline" method="post" action="actions.php">
            <input type="hidden" name="action" value="toggle">
            <input type="hidden" name="id" value="<?= (int)$task['id'] ?>">
            <button class="toggle" type="submit" title="Concluir/Desmarcar">
              <?= (int)$task['is_done'] === 1 ? '✓' : '○' ?>
            </button>
          </form>

          <div class="content">
            <div class="title"><?= htmlspecialchars($task['title']) ?></div>

            <details class="edit">
              <summary>Editar</summary>
              <form method="post" action="actions.php" class="edit-form">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" value="<?= (int)$task['id'] ?>">
                <input name="title" value="<?= htmlspecialchars($task['title']) ?>" maxlength="255" required>
                <button type="submit">Salvar</button>
              </form>
            </details>
          </div>

          <form class="inline" method="post" action="actions.php" onsubmit="return confirm('Excluir esta tarefa?')">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$task['id'] ?>">
            <button class="danger" type="submit">Excluir</button>
          </form>
        </li>
      <?php endforeach; ?>

      <?php if (count($tasks) === 0): ?>
        <li class="empty">Nenhuma tarefa para exibir.</li>
      <?php endif; ?>
    </ul>
  </main>
</body>
</html>