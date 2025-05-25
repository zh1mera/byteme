<?php 
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle full puzzle update via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json')) {
    $data = json_decode(file_get_contents("php://input"), true);

    if ($data['action'] === 'update_full_puzzle') {
        $difficulty = strtolower(trim($data['difficulty']));
        $language = strtolower(trim($data['language']));
        $content = $data['content'];

        $filepath = "puzzles/$difficulty/$language.txt";

        if (file_exists($filepath)) {
            file_put_contents($filepath, $content);
            echo "Puzzles saved successfully!";
        } else {
            echo "Puzzle file not found.";
        }
        exit();
    }
}

// Handle puzzle fetch
if (isset($_GET['get_puzzles']) && $_GET['get_puzzles'] == 1) {
    $difficulty = isset($_GET['difficulty']) ? strtolower($_GET['difficulty']) : 'beginner';
    $language = isset($_GET['language']) ? strtolower($_GET['language']) : 'python';
    $filepath = "puzzles/$difficulty/$language.txt";

    if (file_exists($filepath)) {
        $puzzles = file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        echo implode("\n", $puzzles);
    } else {
        echo "No puzzles found for $difficulty / $language.";
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>View Puzzles - Admin</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f9f9f9;
        margin: 0;
        padding: 0;
        color: #333;
    }
    .container {
        max-width: 900px;
        margin: 40px auto;
        background: #fff;
        padding: 30px 40px;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    a.back-button {
        text-decoration: none;
        color: #007bff;
        font-weight: 600;
        display: inline-block;
        margin-bottom: 25px;
        transition: color 0.2s;
    }
    a.back-button:hover {
        color: #0056b3;
        text-decoration: underline;
    }
    h1 {
        margin-bottom: 25px;
        font-weight: 700;
    }
    .difficulty-btns, .language-btns {
        margin-bottom: 15px;
    }
    .language-btns {
        display: none;
    }
    .language-btns.active {
        display: block;
    }
    button.btn, button.lang-btn {
        padding: 10px 18px;
        background-color: #007bff;
        border: none;
        color: white;
        border-radius: 6px;
        cursor: pointer;
        margin: 5px 8px 10px 0;
        font-size: 15px;
        font-weight: 600;
        transition: background-color 0.25s ease;
        user-select: none;
    }
    button.btn:hover, button.lang-btn:hover {
        background-color: #0056b3;
    }
    button.active {
        background-color: #0056b3 !important;
        box-shadow: 0 0 6px #0056b3aa;
    }
    #puzzle-list {
        margin-top: 20px;
    }
    textarea#full-puzzle-text {
        width: 100%;
        min-height: 350px;
        font-family: 'Courier New', Courier, monospace;
        font-size: 15px;
        padding: 15px;
        border-radius: 6px;
        border: 1px solid #ccc;
        resize: vertical;
        box-shadow: inset 0 1px 3px #ddd;
        transition: border-color 0.3s;
    }
    textarea#full-puzzle-text:focus {
        border-color: #007bff;
        outline: none;
        box-shadow: 0 0 6px #007bff66 inset;
    }
    button#save-full-puzzle {
        margin-top: 15px;
        padding: 12px 25px;
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 700;
        font-size: 16px;
        transition: background-color 0.3s ease;
        user-select: none;
    }
    button#save-full-puzzle:hover {
        background-color: #218838;
    }
</style>
</head>
<body>
<div class="container">
    <a href="admin.php" class="back-button">&larr; Back to Admin Dashboard</a>
    <h1>View Puzzles</h1>

    <div class="difficulty-btns">
        <button class="btn" data-difficulty="beginner">Beginner</button>
        <button class="btn" data-difficulty="intermediate">Intermediate</button>
        <button class="btn" data-difficulty="professional">Professional</button>
    </div>

    <div class="language-btns" id="beginner-langs">
        <button class="lang-btn" data-language="python">Python</button>
        <button class="lang-btn" data-language="javascript">JavaScript</button>
        <button class="lang-btn" data-language="java">Java</button>
        <button class="lang-btn" data-language="php">PHP</button>
    </div>
    <div class="language-btns" id="intermediate-langs">
        <button class="lang-btn" data-language="python">Python</button>
        <button class="lang-btn" data-language="javascript">JavaScript</button>
        <button class="lang-btn" data-language="java">Java</button>
        <button class="lang-btn" data-language="php">PHP</button>
    </div>
    <div class="language-btns" id="professional-langs">
        <button class="lang-btn" data-language="python">Python</button>
        <button class="lang-btn" data-language="javascript">JavaScript</button>
        <button class="lang-btn" data-language="java">Java</button>
        <button class="lang-btn" data-language="php">PHP</button>
    </div>

    <div id="puzzle-list">
        Select a difficulty and language to view puzzles.
    </div>
</div>

<script>
    const difficultyButtons = document.querySelectorAll('.difficulty-btns .btn');
    const languageContainers = document.querySelectorAll('.language-btns');
    const puzzleList = document.getElementById('puzzle-list');

    let currentDifficulty = null;
    let currentLanguage = null;

    function clearActiveButtons(container) {
        container.querySelectorAll('button').forEach(btn => btn.classList.remove('active'));
    }

    difficultyButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            difficultyButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            languageContainers.forEach(div => div.classList.remove('active'));
            currentDifficulty = btn.getAttribute('data-difficulty');
            currentLanguage = null;

            const langDiv = document.getElementById(currentDifficulty + '-langs');
            if (langDiv) langDiv.classList.add('active');

            puzzleList.innerHTML = 'Select a language to view puzzles.';
        });
    });

    languageContainers.forEach(container => {
        container.querySelectorAll('button').forEach(langBtn => {
            langBtn.addEventListener('click', () => {
                if (!currentDifficulty) {
                    puzzleList.innerHTML = 'Please select a difficulty first.';
                    return;
                }

                const language = langBtn.getAttribute('data-language');

                if (currentLanguage === language) {
                    currentLanguage = null;
                    clearActiveButtons(container);
                    puzzleList.innerHTML = 'Select a language to view puzzles.';
                    return;
                }

                currentLanguage = language;
                clearActiveButtons(container);
                langBtn.classList.add('active');

                fetch(`?get_puzzles=1&difficulty=${currentDifficulty}&language=${language}`)
                    .then(response => response.text())
                    .then(data => {
                        puzzleList.innerHTML = `
                            <textarea id="full-puzzle-text">${data}</textarea>
                            <button id="save-full-puzzle">Save Changes</button>
                        `;

                        document.getElementById('save-full-puzzle').addEventListener('click', () => {
                            const content = document.getElementById('full-puzzle-text').value;

                            fetch('adminpuzz.php', {
                                method: 'POST',
                                headers: {'Content-Type': 'application/json'},
                                body: JSON.stringify({
                                    action: 'update_full_puzzle',
                                    difficulty: currentDifficulty,
                                    language: currentLanguage,
                                    content: content
                                })
                            })
                            .then(res => res.text())
                            .then(msg => alert(msg))
                            .catch(() => alert('Failed to save changes.'));
                        });
                    })
                    .catch(() => {
                        puzzleList.innerHTML = 'Failed to load puzzles.';
                    });
            });
        });
    });
</script>
</body>
</html>
