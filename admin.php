<?php

require_once __DIR__ . "/functions.php";

session_start();

date_default_timezone_set("Europe/Kyiv");

// Створення токена захисту
if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$error = "";

// Обробка входу
if ($_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST["login"])) {

    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";

    if (
        $username === ADMIN_USERNAME &&
        password_verify($password, ADMIN_PASSWORD_HASH)
    ) {
        session_regenerate_id(true);

        $_SESSION["admin_logged_in"] = true;

        header("Location: admin.php");
        exit;
    } else {
        $error = "Неправильний логін або пароль.";
    }
}

// Перевірка авторизації
$isAdmin = !empty($_SESSION["admin_logged_in"]);

// Видалення анкети
if (
    $isAdmin &&
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["delete_id"])
) {
    if (!hash_equals(
        $_SESSION["csrf_token"],
        $_POST["csrf_token"] ?? ""
    )) {
        die("Помилка перевірки запиту.");
    }

    $id = filter_var(
        $_POST["delete_id"],
        FILTER_VALIDATE_INT
    );

    if ($id !== false && $id > 0) {
        deleteResponse($id);
    }

    header("Location: admin.php");
    exit;
}

// Експорт у CSV
if ($isAdmin && isset($_GET["export"])) {

    $responses = getAllResponses();

    header("Content-Type: text/csv; charset=UTF-8");
    header("Content-Disposition: attachment; filename=responses.csv");

    $output = fopen("php://output", "w");

    // BOM для правильного відображення українських букв
    fwrite($output, "\xEF\xBB\xBF");

    fputcsv($output, [
        "ID",
        "Ім'я",
        "Електронна пошта",
        "Дата та час",
        "Відповіді"
    ], ";");

    foreach ($responses as $response) {

        fputcsv($output, [
            $response["id"],
            $response["name"],
            $response["email"],
            $response["submitted_at"],
            $response["answers_json"]
        ], ";");
    }

    fclose($output);
    exit;
}

$pageTitle = "Адміністративна панель — Ялинковий Світ";

require_once "header.php";

?>

<style>
    .admin-container {
        max-width: 1100px;
        margin: 40px auto;
        padding: 25px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }

    .admin-container h2 {
        text-align: center;
        color: #28613a;
    }

    .login-form {
        max-width: 400px;
        margin: 30px auto;
    }

    .login-form input {
        width: 100%;
        padding: 12px;
        margin: 10px 0 20px;
        border: 1px solid #ccc;
        border-radius: 7px;
        box-sizing: border-box;
    }

    .admin-button {
        background: #287342;
        color: white;
        border: none;
        padding: 11px 18px;
        border-radius: 7px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }

    .admin-button:hover {
        background: #1e572f;
    }

    .delete-button {
        background: #a83232;
    }

    .error {
        color: #b00020;
        text-align: center;
    }

    .response-card {
        background: #f3f8f1;
        border-left: 4px solid #39804a;
        padding: 18px;
        margin: 20px 0;
        border-radius: 8px;
        overflow-wrap: anywhere;
    }

    .response-card p {
        margin: 8px 0;
    }

    .answer-item {
        margin: 10px 0;
    }

    .admin-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 25px;
    }
</style>

<main class="admin-container">

<?php if (!$isAdmin): ?>

    <h2>Вхід адміністратора</h2>

    <form method="POST" class="login-form">

        <label for="username">Логін:</label>

        <input
            type="text"
            id="username"
            name="username"
            required
        >

        <label for="password">Пароль:</label>

        <input
            type="password"
            id="password"
            name="password"
            required
        >

        <button
            type="submit"
            name="login"
            class="admin-button"
        >
            Увійти
        </button>

    </form>

    <?php if ($error !== ""): ?>
        <p class="error"><?= e($error) ?></p>
    <?php endif; ?>

<?php else: ?>

    <h2>Адміністративна панель</h2>

    <p>Вітаємо, адміністраторе!</p>

    <div class="admin-actions">

        <a href="admin.php?export=1" class="admin-button">
            Експортувати відповіді CSV
        </a>

        <a href="logout.php" class="admin-button">
            Вийти
        </a>

    </div>

    <?php $responses = getAllResponses(); ?>

    <h3>Відповіді користувачів</h3>

    <p>
        Кількість заповнених анкет:
        <strong><?= count($responses) ?></strong>
    </p>

    <?php if (empty($responses)): ?>

        <p>Поки що немає заповнених анкет.</p>

    <?php else: ?>

        <?php foreach ($responses as $response): ?>

            <div class="response-card">

                <h3>
                    Анкета №<?= e($response["id"]) ?>
                </h3>

                <p>
                    <strong>Ім'я:</strong>
                    <?= e($response["name"]) ?>
                </p>

                <p>
                    <strong>Електронна пошта:</strong>
                    <?= e($response["email"]) ?>
                </p>

                <p>
                    <strong>Дата заповнення:</strong>
                    <?= e($response["submitted_at"]) ?>
                </p>

                <h4>Відповіді:</h4>

                <?php
                $answers = json_decode(
                    $response["answers_json"],
                    true
                );
                ?>

                <?php if (is_array($answers)): ?>

                    <?php foreach ($answers as $question => $answer): ?>

                        <div class="answer-item">
                            <strong><?= e($question) ?></strong>
                            <p><?= e($answer) ?></p>
                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <p>Не вдалося прочитати відповіді.</p>

                <?php endif; ?>

                <form method="POST"
                      onsubmit="return confirm('Видалити цю анкету?');">

                    <input
                        type="hidden"
                        name="delete_id"
                        value="<?= e($response["id"]) ?>"
                    >

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e($_SESSION["csrf_token"]) ?>"
                    >

                    <button
                        type="submit"
                        class="admin-button delete-button"
                    >
                        Видалити анкету
                    </button>

                </form>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>

<?php endif; ?>

</main>

<?php include "footer.php"; ?>