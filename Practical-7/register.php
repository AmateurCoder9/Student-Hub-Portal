<?php
declare(strict_types=1);

function escapeHtml(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function renderResponse(string $title, string $message, int $statusCode = 200, array $errors = []): void
{
    http_response_code($statusCode);
    header('Content-Type: text/html; charset=UTF-8');
    $resultClass = $statusCode >= 400 ? 'server-result is-error' : 'server-result';
    $messageRole = $statusCode >= 400 ? 'alert' : 'status';
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= escapeHtml($title) ?> | StudentHub</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <h1><?= escapeHtml($title) ?></h1>
  </header>
  <nav aria-label="Main navigation">
    <a href="home.html">Home</a>
    <a href="register.html">Register</a>
    <a href="events.html">Events</a>
    <a href="contact.html">Contact</a>
  </nav>
  <main>
    <section class="<?= $resultClass ?>" aria-labelledby="resultTitle">
      <h2 id="resultTitle"><?= escapeHtml($title) ?></h2>
      <p role="<?= $messageRole ?>"><?= escapeHtml($message) ?></p>
      <?php if ($errors !== []): ?>
        <ul>
          <?php foreach ($errors as $error): ?>
            <li><?= escapeHtml($error) ?></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
      <p><a href="register.html">Return to the registration form</a></p>
    </section>
  </main>
</body>
</html>
    <?php
    exit;
}

function postString(string $key): string
{
    $value = $_POST[$key] ?? '';
    return is_string($value) ? trim($value) : '';
}

function csvSafe(string $value): string
{
    return preg_match('/^\s*[=+\-@]/', $value) === 1 ? "'" . $value : $value;
}

function appendJsonRecord(string $path, array $record): bool
{
    $file = @fopen($path, 'c+');
    if ($file === false) {
        return false;
    }

    $locked = false;
    try {
        if (!flock($file, LOCK_EX)) {
            return false;
        }
        $locked = true;

        rewind($file);
        $contents = stream_get_contents($file);
        if ($contents === false) {
            return false;
        }

        if (trim($contents) === '') {
            $records = [];
        } else {
            try {
                $records = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $error) {
                return false;
            }
            if (!is_array($records) || !array_is_list($records)) {
                return false;
            }
        }

        $records[] = $record;
        try {
            $encodedRecords = json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        } catch (JsonException $error) {
            return false;
        }

        if (!rewind($file) || !ftruncate($file, 0)) {
            return false;
        }

        $length = strlen($encodedRecords);
        $written = 0;
        while ($written < $length) {
            $bytesWritten = fwrite($file, substr($encodedRecords, $written));
            if ($bytesWritten === false || $bytesWritten === 0) {
                return false;
            }
            $written += $bytesWritten;
        }

        return fflush($file);
    } finally {
        if ($locked) {
            flock($file, LOCK_UN);
        }
        fclose($file);
    }
}

function appendCsvRecord(string $path, array $record): bool
{
    $file = @fopen($path, 'c+');
    if ($file === false) {
        return false;
    }

    $locked = false;
    try {
        if (!flock($file, LOCK_EX)) {
            return false;
        }
        $locked = true;

        $fileStats = fstat($file);
        if ($fileStats === false || fseek($file, 0, SEEK_END) !== 0) {
            return false;
        }

        if ($fileStats['size'] === 0) {
            $header = ['Submitted at', 'Name', 'Email', 'Mobile', 'Password hash', 'Course', 'Year', 'Gender', 'Terms accepted'];
            if (fputcsv($file, $header, ',', '"', '') === false) {
                return false;
            }
        }

        $values = [
            $record['submitted_at'],
            csvSafe($record['name']),
            csvSafe($record['email']),
            $record['mobile'],
            $record['password_hash'],
            $record['course'],
            $record['year'],
            $record['gender'],
            'Yes'
        ];

        return fputcsv($file, $values, ',', '"', '') !== false && fflush($file);
    } finally {
        if ($locked) {
            flock($file, LOCK_UN);
        }
        fclose($file);
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    renderResponse('Registration request required', 'Open the registration form and submit it to continue.', 405);
}

$name = postString('name');
$email = postString('email');
$mobile = postString('mobile');
$passwordValue = $_POST['password'] ?? '';
$confirmPasswordValue = $_POST['confirmPassword'] ?? '';
$password = is_string($passwordValue) ? $passwordValue : '';
$confirmPassword = is_string($confirmPasswordValue) ? $confirmPasswordValue : '';
$course = postString('course');
$year = postString('year');
$gender = postString('gender');
$termsValue = $_POST['terms'] ?? '';
$termsAccepted = is_string($termsValue) && $termsValue === 'on';

$courseOptions = [
    'computer-engineering' => 'Computer Engineering',
    'information-technology' => 'Information Technology',
    'computer-science' => 'Computer Science',
    'other' => 'Other'
];
$yearOptions = ['1', '2', '3', '4'];
$genderOptions = ['female', 'male', 'other', 'prefer-not-to-say'];
$errors = [];

if (preg_match('/^[A-Za-z][A-Za-z .\'-]{1,59}$/', $name) !== 1) {
    $errors[] = 'Enter a name using at least 2 letters.';
}
if (strlen($email) > 254 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    $errors[] = 'Enter a valid email address.';
}
if (preg_match('/^[6-9][0-9]{9}$/', $mobile) !== 1) {
    $errors[] = 'Enter a valid 10-digit mobile number.';
}
$passwordIsStrong = strlen($password) >= 8
    && strlen($password) <= 128
    && preg_match('/[a-z]/', $password) === 1
    && preg_match('/[A-Z]/', $password) === 1
    && (preg_match('/[0-9]/', $password) === 1 || preg_match('/[^A-Za-z0-9]/', $password) === 1);
if (!$passwordIsStrong) {
    $errors[] = 'Use 8 to 128 characters with upper- and lowercase letters and a number or symbol.';
}
if ($password === '' || $password !== $confirmPassword) {
    $errors[] = 'Passwords do not match.';
}
if (!array_key_exists($course, $courseOptions)) {
    $errors[] = 'Select a valid course.';
}
if (!in_array($year, $yearOptions, true)) {
    $errors[] = 'Select a valid year.';
}
if (!in_array($gender, $genderOptions, true)) {
    $errors[] = 'Select a gender option.';
}
if (!$termsAccepted) {
    $errors[] = 'Accept the terms and conditions to continue.';
}

if ($errors !== []) {
    renderResponse('Please correct the registration details', 'The registration was not saved. Review these fields and try again.', 422, $errors);
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
if ($passwordHash === false) {
    renderResponse('Registration could not be completed', 'The password could not be processed. Please try again.', 500);
}

$record = [
    'submitted_at' => date(DATE_ATOM),
    'name' => $name,
    'email' => $email,
    'mobile' => $mobile,
    'password_hash' => $passwordHash,
    'course' => $courseOptions[$course],
    'year' => $year,
    'gender' => $gender,
    'terms_accepted' => true
];

$storageDirectory = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.studenthub-data';
if (!is_dir($storageDirectory) && !@mkdir($storageDirectory, 0700, true) && !is_dir($storageDirectory)) {
    renderResponse('Registration could not be completed', 'The local data directory could not be created.', 500);
}

$jsonPath = $storageDirectory . DIRECTORY_SEPARATOR . 'registrations.json';
$csvPath = $storageDirectory . DIRECTORY_SEPARATOR . 'registrations.csv';
if (!appendJsonRecord($jsonPath, $record) || !appendCsvRecord($csvPath, $record)) {
    renderResponse('Registration could not be completed', 'The registration could not be saved. Check the local data directory and try again.', 500);
}

renderResponse('Registration received', 'Your details passed server-side validation and were saved to CSV and JSON. The password is stored as a hash, not as plain text.', 201);