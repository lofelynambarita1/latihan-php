<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Todo List</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
  <style>
    .todo-done { text-decoration: line-through; color: #6c757d; }
  </style>
</head>
<body class="bg-light">
  <div class="container py-5">
    <h1 class="mb-4 text-center text-primary">✨ Todo List</h1>

    <!-- Filter & Search -->
    <form method="get" class="d-flex mb-4 justify-content-center">
      <input type="hidden" name="page" value="index">
      <select name="filter" class="form-select w-auto me-2">
        <option value="all" <?= ($filter==='all') ? 'selected' : '' ?>>Semua</option>
        <option value="unfinished" <?= ($filter==='unfinished') ? 'selected' : '' ?>>Belum Selesai</option>
        <option value="finished" <?= ($filter==='finished') ? 'selected' : '' ?>>Selesai</option>
      </select>
      <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari judul atau deskripsi..." class="form-control w-25 me-2">
      <button class="btn btn-primary">🔍 Cari</button>
    </form>

    <!-- Form Add -->
    <form method="post" action="?page=create" class="card card-body mb-4 shadow-sm">
      <div class="row g-2">
        <div class="col-md-4"><input type="text" name="title" class="form-control" placeholder="Judul todo" required></div>
        <div class="col-md-6"><input type="text" name="description" class="form-control" placeholder="Deskripsi (opsional)"></div>
        <div class="col-md-2"><button class="btn btn-success w-100">Tambah</button></div>
      </div>
    </form>

    <!-- Todo list -->
    <ul id="todo-list" class="list-group shadow-sm">
      <?php if (!empty($todos)): ?>
        <?php foreach ($todos as $todo): ?>
          <li class="list-group-item d-flex justify-content-between align-items-center" data-id="<?= $todo['id'] ?>">
            <div>
              <input type="checkbox"
                     onchange="toggleFinish(<?= $todo['id'] ?>, this.checked)"
                     <?= ($todo['is_finished'] == 't' || $todo['is_finished'] == '1' || $todo['is_finished'] === true) ? 'checked' : '' ?>>
              <span class="<?= ($todo['is_finished'] == 't' || $todo['is_finished'] == '1' || $todo['is_finished'] === true) ? 'todo-done' : '' ?>">
                <?= htmlspecialchars($todo['title']) ?>
              </span>
              <div class="small text-secondary"><?= htmlspecialchars($todo['description']) ?></div>
            </div>
            <div>
              <a href="?page=detail&id=<?= $todo['id'] ?>" class="btn btn-info btn-sm me-1">Detail</a>
              <a href="?page=delete&id=<?= $todo['id'] ?>" onclick="return confirm('Yakin hapus?')" class="btn btn-danger btn-sm">Hapus</a>
            </div>
          </li>
        <?php endforeach; ?>
      <?php else: ?>
        <li class="list-group-item text-center text-muted">Belum ada data tersedia.</li>
      <?php endif; ?>
    </ul>
  </div>

  <script>
    // Sortable
    new Sortable(document.getElementById('todo-list'), {
      animation: 150,
      onEnd: async function(evt) {
        const data = Array.from(evt.to.children).map((el, i) => ({ id: el.dataset.id, order: i + 1 }));
        await fetch('?page=reorder', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify(data)
        });
      }
    });

    // Toggle status menggunakan endpoint khusus (tidak merubah title/description)
    async function toggleFinish(id, checked) {
      const body = `id=${encodeURIComponent(id)}&is_finished=${checked ? '1' : '0'}`;
      const res = await fetch('?page=toggle', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body
      });
      // optional: perbarui tampilan (langsung reload sederhana)
      if (res.ok) {
        location.reload();
      } else {
        alert('Gagal mengubah status');
      }
    }
  </script>
</body>
</html>
