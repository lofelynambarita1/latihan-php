<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Detail Todo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="card shadow-sm p-4">
      <h3><?= htmlspecialchars($todo['title']) ?></h3>
      <p><?= nl2br(htmlspecialchars($todo['description'])) ?></p>
      <p><b>Status:</b> <?= ($todo['is_finished']=='t' || $todo['is_finished']==1) ? '✅ Selesai' : '❌ Belum Selesai' ?></p>
      <p><small>Dibuat: <?= $todo['created_at'] ?><br>Update: <?= $todo['updated_at'] ?></small></p>
      <a href="/?page=index" class="btn btn-secondary mt-3">Kembali</a>
    </div>
  </div>
</body>
</html>
