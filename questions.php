<?php
session_start();
require_once "db_connection.php";

// Questions adaptées aux données de la base
$questions = [
    [
        'question' => "What is your cooking level?",
        'type' => "choices",
        'options' => ["Easy", "Medium", "Hard"]
    ],
    [
        'question' => "You prefer:",
        'type' => "choices",
        'options' => ["Quick recipe", "Medium recipe", "Long recipe"]
    ],
    [
        'question' => "What are you looking for?",
        'type' => "checkboxes",
        'options' => ["Healthy", "Indulgent", "Vegetarian", "Cheap recipe"]
    ]
];

$currentQuestion = $_SESSION['currentQuestion'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['next'])) {
    $question = $questions[$currentQuestion];

    if ($question['type'] === 'choices') {
        if (!empty($_POST['answer'])) {
            $_SESSION['answers'][$question['question']] = $_POST['answer'];
        } else {
            $error = "Please select an option!";
        }
    } elseif ($question['type'] === 'checkboxes') {
        if (!empty($_POST['answers'])) {
            $_SESSION['answers'][$question['question']] = $_POST['answers'];
        } else {
            $error = "Please select at least one option!";
        }
    }

    if (!isset($error)) {
        $currentQuestion++;
        $_SESSION['currentQuestion'] = $currentQuestion;

        if ($currentQuestion >= count($questions)) {
            $level_name = $_SESSION['answers']["What is your cooking level?"] ?? "Easy";
            $preferences = $_SESSION['answers']["What are you looking for?"] ?? [];
            $type_name = is_array($preferences) && !empty($preferences) ? $preferences[0] : "Healthy";

            if (isset($_SESSION['user_name'])) {
                $user_name = $_SESSION['user_name'];

                try {
                    $pdo->prepare("INSERT IGNORE INTO level (level_name) VALUES (:level_name)")
                        ->execute([':level_name' => $level_name]);

                    $pdo->prepare("INSERT IGNORE INTO type (type_name) VALUES (:type_name)")
                        ->execute([':type_name' => $type_name]);

                    $pdo->prepare("UPDATE users SET level_name = :level_name, type_name = :type_name WHERE user_name = :user_name")
                        ->execute([
                            ':level_name' => $level_name,
                            ':type_name' => $type_name,
                            ':user_name' => $user_name
                        ]);

                    unset($_SESSION['currentQuestion'], $_SESSION['answers']);
                    header("Location: main.php");
                    exit;
                } catch (PDOException $e) {
                    $error = "Database error: " . $e->getMessage();
                }
            } else {
                $_SESSION['user_preferences'] = [
                    'level_name' => $level_name,
                    'type_name' => $type_name
                ];
                header("Location: login.php");
                exit;
            }
        }
    }
}

if (isset($_GET['reset'])) {
    $_SESSION['currentQuestion'] = 0;
    $_SESSION['answers'] = [];
    $currentQuestion = 0;
    header("Location: questionnaire.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Recipe Matcher - Questionnaire</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #fffaf5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .question-container {
            background: #ffffff;
            border: 2px solid #ffe5d0;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 480px;
            text-align: center;
        }

        .question-container h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 30px;
        }

        .options {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 25px;
        }

        .option-box {
            padding: 14px 16px;
            border: 1px solid #ffd6b0;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 16px;
            color: #333;
            text-align: left;
            background: #fff8f2;
        }

        .option-box:hover {
            background: #ffe8d9;
            border-color: #ff7f50;
        }

        input[type="radio"],
        input[type="checkbox"] {
            margin-right: 10px;
            accent-color: #ff7f50;
        }

        .next-btn {
            margin-top: 10px;
            padding: 12px 24px;
            background: #ff7f50;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 14px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .next-btn:hover {
            background: #ff6a3c;
        }

        .error {
            color: #ff0000;
            margin-bottom: 15px;
            font-size: 15px;
        }

        .progress-bar {
            width: 100%;
            height: 10px;
            background-color: #ffe3d0;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .progress {
            height: 100%;
            background-color: #d4a373;
            border-radius: 5px;
            transition: width 0.3s ease;
        }
    </style>
</head>
<body>

<div class="question-container">
    <?php if ($currentQuestion < count($questions)): ?>
        <div class="progress-bar">
            <div class="progress" style="width: <?= ($currentQuestion / count($questions)) * 100 ?>%"></div>
        </div>

        <h2><?= htmlspecialchars($questions[$currentQuestion]['question']) ?></h2>

        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="options">
                <?php if ($questions[$currentQuestion]['type'] === 'choices'): ?>
                    <?php foreach ($questions[$currentQuestion]['options'] as $option): ?>
                        <label class="option-box">
                            <input type="radio" name="answer" value="<?= htmlspecialchars($option) ?>">
                            <?= htmlspecialchars($option) ?>
                        </label>
                    <?php endforeach; ?>
                <?php elseif ($questions[$currentQuestion]['type'] === 'checkboxes'): ?>
                    <?php foreach ($questions[$currentQuestion]['options'] as $option): ?>
                        <label class="option-box">
                            <input type="checkbox" name="answers[]" value="<?= htmlspecialchars($option) ?>">
                            <?= htmlspecialchars($option) ?>
                        </label>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <button type="submit" name="next" class="next-btn">Next</button>
        </form>
    <?php else: ?>
        <h2>Thank you for completing the questionnaire!</h2>
        <p>Saving your preferences...</p>
        <meta http-equiv="refresh" content="2;url=main.php">
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const optionBoxes = document.querySelectorAll('.option-box');
    optionBoxes.forEach(box => {
        box.addEventListener('click', function(e) {
            if (e.target !== this.querySelector('input')) {
                const input = this.querySelector('input');
                if (input.type === 'radio') {
                    input.checked = true;
                } else if (input.type === 'checkbox') {
                    input.checked = !input.checked;
                }
            }
        });
    });
});
</script>

</body>
</html>
