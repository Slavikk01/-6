<?php
require_once "functions.php";

$data = loadSurveyData();
?>

<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Опитування — Ялинковий Світ</title>

    <link rel="stylesheet" href="style.css">

    <style>
        .survey-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .survey-container h1 {
            text-align: center;
            color: #28613a;
        }

        .survey-description {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }

        .survey-field {
            margin-bottom: 20px;
        }

        .survey-field input {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbdacb;
            border-radius: 7px;
            box-sizing: border-box;
            margin-top: 8px;
        }

        .question {
            background: #f3f8f1;
            border-left: 4px solid #39804a;
            border-radius: 8px;
            padding: 18px;
            margin: 20px 0;
        }

        .question p {
            color: #285b36;
            font-weight: bold;
        }

        .question label {
            display: block;
            margin: 10px 0;
        }

        .survey-button {
            width: 100%;
            padding: 14px;
            background: #287342;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }

        .survey-button:hover {
            background: #1e572f;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #287342;
        }
    </style>
</head>

<body>

<div class="survey-container">

    <h1><?= e($data["title"]) ?></h1>

    <p class="survey-description">
        <?= e($data["description"]) ?>
    </p>

    <form action="process.php" method="POST">

        <div class="survey-field">
            <label for="name">Ваше ім'я:</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div class="survey-field">
            <label for="email">Електронна пошта:</label>
            <input type="email" id="email" name="email" required>
        </div>

        <?php foreach ($data["questions"] as $question): ?>

            <div class="question">

                <p><?= e($question["question"]) ?></p>

                <?php foreach ($question["options"] as $option): ?>

                    <label>
                        <input
                            type="radio"
                            name="answers[<?= e($question["id"]) ?>]"
                            value="<?= e($option) ?>"
                            required
                        >

                        <?= e($option) ?>
                    </label>

                <?php endforeach; ?>

            </div>

        <?php endforeach; ?>

        <button type="submit" class="survey-button">
            Надіслати відповіді
        </button>

    </form>

    <a href="index.html" class="back-link">
        Повернутися на головну сторінку
    </a>

</div>

</body>
</html>