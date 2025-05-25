<?php 
session_start();

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'db/db_connect.php';

// Default selected difficulty and language for form (on page load or after submission)
$selectedDifficulty = 'beginner';
$selectedLanguage = 'python';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $difficulty = htmlspecialchars(trim($_POST['difficulty']));
    $language = htmlspecialchars(trim($_POST['language']));
    $question = trim($_POST['question']);
    $answer = trim($_POST['answer']);

    $selectedDifficulty = $difficulty;
    $selectedLanguage = $language;

    $filepath = "puzzles/" . strtolower($difficulty) . "/" . strtolower($language) . ".txt";

    if (!file_exists(dirname($filepath))) {
        mkdir(dirname($filepath), 0777, true);
    }
    
    $existingPuzzles = file_exists($filepath) ? file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
    $puzzleCount = floor(count($existingPuzzles) / 3);
    $level = $puzzleCount + 1;
    
    $puzzleEntry = "\n" . "Level $level:\n" . "Q: " . $question . "\n" . "A: " . $answer . "\n";
    
    file_put_contents($filepath, $puzzleEntry, FILE_APPEND);

    $success_message = "Puzzle added successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css" />
    <style>
        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .puzzle-form {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            width: 1000px;
            max-width: 100%;
            margin: 0 auto;
        }

        .success-message {
            color: green;
            margin-bottom: 15px;
        }

        form div {
            margin-bottom: 15px;
            width: 100%;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        select,
        textarea,
        input {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            resize: none;
            box-sizing: border-box;
        }

        button[type="submit"],
        .btn,
        .lang-btn {
            width: auto;
            padding: 10px 16px;
            background-color: #007bff;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 4px 6px 8px 0;
            transition: background-color 0.3s;
        }

        button[type="submit"]:hover,
        .btn:hover,
        .lang-btn:hover {
            background-color: #0056b3;
        }

        .button {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 8px;
            background-color: rgba(0, 0, 0, 0.1);
            color: #000000;
            font-size: 18px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: background-color 0.3s;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span></h1>
            <div>
                <a href="adminpuzz.php" class="button" style="margin-right: 10px;">View Puzzles</a>
                <a href="admin.php" class="button">User Management</a>
                <a href="logout.php" class="button">Logout</a>
            </div>
        </div>

        <?php if (isset($success_message)) : ?>
            <div id="success-message" class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <div class="puzzle-form">
            <h2>Create New Puzzle</h2>
            <form method="POST" action="">
                <div>
                    <label for="difficulty">Difficulty:</label>
                    <select name="difficulty" id="difficulty" required>
                        <option value="beginner" <?php if ($selectedDifficulty == 'beginner') echo 'selected'; ?>>Beginner</option>
                        <option value="intermediate" <?php if ($selectedDifficulty == 'intermediate') echo 'selected'; ?>>Intermediate</option>
                        <option value="professional" <?php if ($selectedDifficulty == 'professional') echo 'selected'; ?>>Professional</option>
                    </select>
                </div>

                <div>
                    <label for="language">Language:</label>
                    <select name="language" id="language" required>
                        <option value="python" <?php if ($selectedLanguage == 'python') echo 'selected'; ?>>Python</option>
                        <option value="java" <?php if ($selectedLanguage == 'java') echo 'selected'; ?>>Java</option>
                        <option value="javascript" <?php if ($selectedLanguage == 'javascript') echo 'selected'; ?>>JavaScript</option>
                        <option value="php" <?php if ($selectedLanguage == 'php') echo 'selected'; ?>>PHP</option>
                    </select>
                </div>

                <div>
                    <label for="question">Question:</label>
                    <textarea name="question" id="question" rows="4" required></textarea>
                </div>

                <div>
                    <label for="answer">Correct Answer:</label>
                    <textarea name="answer" id="answer" rows="2" required></textarea>
                </div>

                <button type="submit">Add Puzzle</button>
            </form>
        </div>
    </div>

    <script>
        // Hide success message after 2 seconds
        setTimeout(() => {
            const msg = document.getElementById('success-message');
            if (msg) msg.style.display = 'none';
        }, 2000);
    </script>
</body>
</html>
