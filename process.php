<?php

require_once __DIR__ . "/functions.php";

// Перевірка способу надсилання
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: survey.php");
    exit;
}

// Завантаження запитань
$data = loadSurveyData();

// Отримання даних форми
$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");

$submittedAnswers = $_POST["answers"] ?? [];

// Перевірка імені та пошти
if ($name === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Перевірте правильність імені та електронної пошти.");
}

// Перевірка відповідей
$answers = [];

foreach ($data["questions"] as $question) {

    $id = $question["id"];
    $answer = $submittedAnswers[$id] ?? "";

    if (!in_array($answer, $question["options"], true)) {
        die("Будь ласка, дайте відповідь на всі запитання.");
    }

    $answers[$question["question"]] = $answer;
}

// Дата та час
date_default_timezone_set("Europe/Kyiv");

$submittedAt = date("Y-m-d H:i:s");

// Формування запису
$response = [
    "name" => $name,
    "email" => $email,
    "submitted_at" => $submittedAt,
    "answers" => $answers
];

// Збереження в базу даних
saveResponse($response);

$pageTitle = "Дякуємо — Ялинковий Світ";

require_once "header.php";

?>

<main class="survey-container">

    <h2>Дякуємо за вашу відповідь!</h2>

    <p>Ваші відповіді успішно збережено.</p>

    <p>
        <strong>Ім'я:</strong>
        <?= e($name) ?>
    </p>

    <p>
        <strong>Електронна пошта:</strong>
        <?= e($email) ?>
    </p>

    <p>
        <strong>Дата та час:</strong>
        <?= e(date("d.m.Y H:i:s", strtotime($submittedAt))) ?>
    </p>

    <h3>Ваші відповіді:</h3>

    <?php foreach ($answers as $question => $answer): ?>

        <div class="question">
            <strong><?= e($question) ?></strong>
            <p><?= e($answer) ?></p>
        </div>

    <?php endforeach; ?>

    <p>
        <a href="survey.php">Повернутися до опитування</a>
    </p>

    <p>
        <a href="index.html">На головну сторінку</a>
    </p>

</main>

<?php include "footer.php"; ?>