<?php
require_once(__DIR__ . '/../models/TodoModel.php');

class TodoController {
    private $model;

    public function __construct() {
        $this->model = new TodoModel();
    }

    public function index() {
        $filter = $_GET['filter'] ?? 'all';
        $search = $_GET['search'] ?? '';
        $todos = $this->model->getAllTodos($filter, $search);
        include(__DIR__ . '/../views/TodoView.php');
    }

    public function create() {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($title === '') {
            echo "<script>alert('Judul wajib diisi!');history.back();</script>"; return;
        }

        if (!$this->model->createTodo($title, $description)) {
            echo "<script>alert('Judul sudah ada, gunakan yang lain!');history.back();</script>";
            return;
        }

        header("Location: /?page=index"); exit;
    }

    public function update() {
        // update via form edit (full update)
        $id = $_POST['id'] ?? null;
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        // is_finished may come as '1' or 'true' or 'on' etc.
        $is_finished = isset($_POST['is_finished']) && ($_POST['is_finished'] === '1' || $_POST['is_finished'] === 'true' || $_POST['is_finished'] === 'on');

        if (!$id) { header("Location: /?page=index"); exit; }

        $this->model->updateTodo($id, $title, $description, $is_finished);
        header("Location: /?page=index"); exit;
    }

    // endpoint khusus hanya toggle status (dipanggil AJAX)
    public function toggle() {
        // terima POST form-encoded id & is_finished (1/0)
        parse_str(file_get_contents("php://input"), $data);
        $id = $data['id'] ?? null;
        $is_finished = isset($data['is_finished']) && ($data['is_finished'] === '1' || $data['is_finished'] === 'true');

        if ($id) {
            $ok = $this->model->updateStatus($id, $is_finished);
            header('Content-Type: application/json');
            echo json_encode(['success' => (bool)$ok]);
            return;
        }
        header('Content-Type: application/json', true, 400);
        echo json_encode(['success' => false, 'error' => 'Missing id']);
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) $this->model->deleteTodo($id);
        header("Location: /?page=index"); exit;
    }

    public function detail() {
        $todo = $this->model->getTodoById($_GET['id'] ?? 0);
        include(__DIR__ . '/../views/TodoDetailView.php');
    }

    public function reorder() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) {
            echo json_encode(['success' => false]); return;
        }
        foreach ($data as $item) {
            $this->model->updateOrder($item['id'], $item['order']);
        }
        echo json_encode(['success' => true]);
    }
}
