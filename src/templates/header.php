<?php
// session_start() is called in index.php BEFORE this is included
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
    <h1>My Awesome Website</h1>
    <?php require_once __DIR__ . '/nav.php'; // Include navigation ?>
</header>
<main>