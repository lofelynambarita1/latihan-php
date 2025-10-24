<?php
require_once(__DIR__ . '/../config.php');

class TodoModel {
    private $conn;

    public function __construct() {
        $this->conn = pg_connect(
            "host=" . DB_HOST .
            " port=" . DB_PORT .
            " dbname=" . DB_NAME .
            " user=" . DB_USER .
            " password=" . DB_PASSWORD
        );
        if (!$this->conn) die("Koneksi database gagal");
    }

    // Ambil daftar dengan filter & pencarian
    public function getAllTodos($filter = 'all', $search = '') {
        $params = [];
        $idx = 1;
        $query = "SELECT * FROM todo WHERE 1=1";

        if ($filter === 'finished') $query .= " AND is_finished = TRUE";
        if ($filter === 'unfinished') $query .= " AND is_finished = FALSE";

        if (!empty($search)) {
            $query .= " AND (title ILIKE $" . $idx . " OR description ILIKE $" . $idx . ")";
            $params[] = '%' . $search . '%';
            $idx++;
        }

        $query .= " ORDER BY sort_order ASC, created_at DESC";

        $result = empty($params) ? pg_query($this->conn, $query) : pg_query_params($this->conn, $query, $params);

        $todos = [];
        while ($row = pg_fetch_assoc($result)) $todos[] = $row;
        return $todos;
    }

    // Create todo (explicit is_finished = FALSE)
    public function createTodo($title, $description) {
        // Validasi unik (case-insensitive)
        $check = pg_query_params($this->conn, "SELECT id FROM todo WHERE LOWER(title)=LOWER($1)", [$title]);
        if (pg_num_rows($check) > 0) return false;

        $query = "INSERT INTO todo (title, description, is_finished, sort_order)
                  VALUES ($1, $2, FALSE, (SELECT COALESCE(MAX(sort_order),0)+1 FROM todo))";
        return pg_query_params($this->conn, $query, [$title, $description]) !== false;
    }

    // Update semua field (dipakai untuk form edit)
    public function updateTodo($id, $title, $description, $is_finished) {
        // is_finished dipassing sebagai '1'/'0' atau boolean; cast di SQL
        $query = "UPDATE todo SET title=$1, description=$2, is_finished=$3::boolean WHERE id=$4";
        return pg_query_params($this->conn, $query, [$title, $description, $is_finished ? 'true' : 'false', $id]) !== false;
    }

    // Update hanya status (dipakai untuk toggle checkbox)
    public function updateStatus($id, $is_finished) {
        $query = "UPDATE todo SET is_finished=$1::boolean WHERE id=$2";
        return pg_query_params($this->conn, $query, [$is_finished ? 'true' : 'false', $id]) !== false;
    }

    public function deleteTodo($id) {
        return pg_query_params($this->conn, "DELETE FROM todo WHERE id=$1", [$id]) !== false;
    }

    public function getTodoById($id) {
        $res = pg_query_params($this->conn, "SELECT * FROM todo WHERE id=$1", [$id]);
        return pg_fetch_assoc($res);
    }

    public function updateOrder($id, $order) {
        return pg_query_params($this->conn, "UPDATE todo SET sort_order=$1 WHERE id=$2", [$order, $id]);
    }
}
