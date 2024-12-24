<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to SQLite database
try {
    $db = new PDO('sqlite:phishing_detector.sqlite3');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Create tables if they do not exist
$db->exec("CREATE TABLE IF NOT EXISTS phishing_keywords (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    keyword TEXT NOT NULL UNIQUE
)");

$db->exec("CREATE TABLE IF NOT EXISTS files (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    filename TEXT NOT NULL,
    filehash TEXT NOT NULL
)");

$db->exec("CREATE TABLE IF NOT EXISTS phishing_results (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    file_id INTEGER NOT NULL,
    phishing_score INTEGER NOT NULL,
    result TEXT NOT NULL,
    FOREIGN KEY(file_id) REFERENCES files(id)
)");

// Function to handle file upload and phishing detection
function handleFileUpload($db) {
    $resultMessage = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['file'])) {
        $email_file = $_FILES['file'];

        // Validate file upload
        if ($email_file['error'] == 0 && ($email_file['type'] == 'text/plain' || pathinfo($email_file['name'], PATHINFO_EXTENSION) == 'eml')) {
            // Read the uploaded file
            $email_content = file_get_contents($email_file['tmp_name']);

            // Fetch phishing keywords from the database
            $phishing_keywords = array();
            $sql = "SELECT keyword FROM phishing_keywords";
            $result = $db->query($sql);
            foreach ($result as $row) {
                $phishing_keywords[] = $row['keyword'];
            }

            // Check for phishing keywords in the email body
            $phishing_score = 0;
            foreach ($phishing_keywords as $keyword) {
                if (stripos($email_content, $keyword) !== false) {
                    $phishing_score++;
                }
            }

            // Determine if the email is phishing or not
            if ($phishing_score > 1) {
                $resultMessage = 'Phishing Email!';
            } else {
                $resultMessage = 'Legitimate Email!';
            }

            // Insert file metadata into the database
            $sql = "INSERT INTO files (filename, filehash) VALUES (:filename, :filehash)";
            $stmt = $db->prepare($sql);
            $filehash = md5_file($email_file['tmp_name']);
            $stmt->bindParam(':filename', $email_file['name']);
            $stmt->bindParam(':filehash', $filehash);
            $stmt->execute();

            $file_id = $db->lastInsertId();

            // Insert phishing result into the database
            $sql = "INSERT INTO phishing_results (file_id, phishing_score, result) VALUES (:file_id, :phishing_score, :result)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':file_id', $file_id);
            $stmt->bindParam(':phishing_score', $phishing_score);
            $stmt->bindParam(':result', $resultMessage);
            $stmt->execute();

            // Display the result
            $resultClass = $phishing_score > 1 ? 'phishing' : 'legitimate';
            $resultMessage = "<div id='result' class='$resultClass'>
                        <p>Email uploaded successfully!</p>
                        <p>Phishing Score: $phishing_score</p>
                        <p>Result: $resultMessage</p>
                      </div>";
        } else {
            $resultMessage = '<h1>Phishing Detector</h1>
                              <p>Error uploading file or invalid file type!</p>';
        }
    }
    return $resultMessage;
}

// Handle the file upload and phishing detection
$resultMessage = handleFileUpload($db);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Phishing Detector - Main</title>
    <link rel="stylesheet" href="style1.css">
</head>
<body>

<header class="header-box">
    <div class="logo">MailDTect</div> 
    <div class="buttons">
        <form action="detector.php"><button class="detectbtn">Phishing Detector</button></form>
        <form action="Edu.html"><button class="edubtn">Phishing Education</button></form>
        <form action="main.html"><button class="logoutbtn">Main Menu</button></form>
    </div>
</header>

<main class="container">
    <h2 class="section-header">Upload Email File</h2>
    <p class="description">
        Please upload an email file (.eml or .txt) to check for phishing.
    </p>
    <form id="upload-form" action="detector.php" method="POST" enctype="multipart/form-data">
        <div id="file-drop-area" class="file-drop-area">
            <p>Drag & Drop files here or double-click to select</p>
            <input type="file" id="file-input" name="file" accept=".eml,.txt">
        </div>
        <button type="submit" id="submit-button" class="submit-button">Detect</button>
    </form>
    <div id="result"><?php echo $resultMessage; ?></div>
</main>

<script>
    // JavaScript for file drag and drop functionality
    const fileDropArea = document.getElementById('file-drop-area');
    const fileInput = document.getElementById('file-input');

    fileDropArea.addEventListener('dragover', (event) => {
        event.preventDefault();
        fileDropArea.classList.add('highlight');
    });

    fileDropArea.addEventListener('dragleave', () => {
        fileDropArea.classList.remove('highlight');
    });

    fileDropArea.addEventListener('drop', (event) => {
        event.preventDefault();
        fileDropArea.classList.remove('highlight');
        const files = event.dataTransfer.files;
        handleFiles(files);
    });

    fileDropArea.addEventListener('dblclick', () => {
        fileInput.click();
    });

    fileInput.addEventListener('change', () => {
        const files = fileInput.files;
        handleFiles(files);
    });

    function handleFiles(files) {
        console.log(files);
    }
</script>

</body>
</html>
