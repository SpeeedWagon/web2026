<?php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Use the $pageTitle variable set in index.php -->
    <title><?php echo htmlspecialchars($pageTitle ?? 'My Website'); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <!-- Add other head elements -->
</head>

<body>
    <header>
        <?php require_once __DIR__ . '/nav.php';  ?>
    </header>
    <main>