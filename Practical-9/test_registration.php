<?php

$testCases = [
    [
        "case" => "Valid Student Registration",
        "input" => "Name: Meet Patel, Email: meet@charusat.edu.in, Pass: Meet@1234",
        "result" => "Success",
        "details" => "Password hashed using password_hash(PASSWORD_BCRYPT) and inserted."
    ],
    [
        "case" => "Duplicate Email Submission",
        "input" => "Email: meet@charusat.edu.in (already in DB)",
        "result" => "Rejected",
        "details" => "Pre-insert SELECT check detected existing email and showed error message."
    ],
    [
        "case" => "Password Mismatch",
        "input" => "Password: Pass1, Confirm: Pass2",
        "result" => "Rejected",
        "details" => "Caught by password check before hitting database."
    ],
    [
        "case" => "Short Password (< 8 characters)",
        "input" => "Password: 12345",
        "result" => "Rejected",
        "details" => "Validated minimum 8 characters requirement."
    ]
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registration Test Cases</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        .pass { color: green; font-weight: bold; }
        .fail { color: red; font-weight: bold; }
    </style>
</head>
<body>

<header>
    <h1>StudentHub Portal</h1>
    <p>Registration Test Report</p>
</header>

<div class="container" style="max-width: 800px;">
    <h2>Test Cases & Validation Results</h2>
    <p><a href="register.php">&larr; Back to Registration Form</a></p>

    <table>
        <tr>
            <th>Test Scenario</th>
            <th>Sample Inputs</th>
            <th>Expected Result</th>
            <th>Validation Note</th>
        </tr>
        <?php foreach ($testCases as $tc): ?>
        <tr>
            <td><strong><?php echo $tc['case']; ?></strong></td>
            <td><?php echo $tc['input']; ?></td>
            <td>
                <span class="<?php echo $tc['result'] == 'Success' ? 'pass' : 'fail'; ?>">
                    <?php echo $tc['result']; ?>
                </span>
            </td>
            <td><?php echo $tc['details']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

</body>
</html>
