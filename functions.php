<?php

require_once __DIR__ . "/config.php";

// Безпечне відображення тексту
function e($value) {
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        "UTF-8"
    );
}

// Завантаження запитань з JSON
function loadSurveyData() {

    $file = __DIR__ . "/data.json";

    if (!file_exists($file)) {
        die("Файл data.json не знайдено.");
    }

    $json = file_get_contents($file);
    $data = json_decode($json, true);

    if (!is_array($data) || !isset($data["questions"])) {
        die("Помилка читання даних опитування.");
    }

    return $data;
}

// Збереження відповідей у MySQL
function saveResponse($response) {

    global $pdo;

    $sql = "INSERT INTO responses
            (name, email, submitted_at, answers_json)
            VALUES
            (:name, :email, :submitted_at, :answers_json)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":name" => $response["name"],
        ":email" => $response["email"],
        ":submitted_at" => $response["submitted_at"],
        ":answers_json" => json_encode(
            $response["answers"],
            JSON_UNESCAPED_UNICODE
        )
    ]);
}
// Отримання всіх відповідей
function getAllResponses() {
    global $pdo;

    $sql = "SELECT * FROM responses ORDER BY id DESC";

    $stmt = $pdo->query($sql);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Видалення відповіді
function deleteResponse($id) {
    global $pdo;

    $sql = "DELETE FROM responses WHERE id = :id";

    $stmt = $pdo->prepare($sql);

    return $stmt->execute([
        ":id" => $id
    ]);
}