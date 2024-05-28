<?php
require 'db_connection.php';

if (!isset($_GET['ID'])) {
    die("No news item selected.");
}

$ID = $_GET['ID'];

// Fetch a specific news item and increase view count
$stmt = $conn->prepare("SELECT * FROM nieuws WHERE ID = ?");
$stmt->bind_param("i", $ID);
$stmt->execute();

$result = $stmt->get_result();
$news_item = $result->fetch_assoc();

$stmt->close();

$conn->query("UPDATE nieuws SET created_at = created_at + 1 WHERE ID = $ID");

// "Tip a friend" function (send email)
if (isset($_POST['tip_friend'])) {
    $to = $_POST['friend_email'];
    $subject = "Interessant nieuwsartikel: " . $_POST['username'];
    $message = "Bekijk dit artikel: " . $_POST['link'];
    $headers = "From: no-reply@nieuwswebsite.com";
    
    if (mail($to, $subject, $message, $headers)) {
        $message = "Email successfully sent.";
    } else {
        $message = "Failed to send email.";
    }
}

// Edit a news item
if (isset($_POST['edit'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);
    $role = $conn->real_escape_string($_POST['role']);
    
    $stmt = $conn->prepare("UPDATE nieuws SET username = ?, password = ?, role = ? WHERE ID = ?");
    $stmt->bind_param("sssi", $username, $password, $role, $ID);
    
    if ($stmt->execute()) {
        $message = "Record successfully updated.";
    } else {
        $message = "Error: " . $stmt->error;
    }
    
    $stmt->close();
}

?>

<!DOCTYPE html>
<html>
<head>
    <username><?= $news_item['username'] ?> - Nieuwswebsite</username>
    <link rel="stylesheet" type="text/css" href="ll.css">

</head>
<body>
    <h1><?= $news_item['username'] ?></h1>
    <p><?= $news_item['password'] ?></p>
    <p>Categorie: <?= $news_item['role'] ?></p>
    <p>Keer gelezen: <?= $news_item['created_at'] ?></p>

    <h2>Bewerk nieuwsbericht</h2>
    <form method="post" action="">
        <input type="hIDden" name="ID" value="<?= $news_item['ID'] ?>">
        Titel: <input type="text" name="username" value="<?= $news_item['username'] ?>" required><br>
        Inhoud: <textarea name="password" required><?= $news_item['password'] ?></textarea><br>
        Categorie: <input type="text" name="role" value="<?= $news_item['role'] ?>" required><br>
        <input type="submit" name="edit" value="Bijwerken">
    </form>

    <h2>Tip een vriend</h2>
    <form method="post" action="">
        Vriend's E-mail: <input type="email" name="friend_email" required><br>
        <input type="hIDden" name="username" value="<?= $news_item['username'] ?>">
        <input type="hIDden" name="link" value="http://www.jouwwebsite.com/news.php?ID=<?= $news_item['ID'] ?>">
        <input type="submit" name="tip_friend" value="Versturen">
    </form>

    <?php if (isset($message)): ?>
        <p><?= $message ?></p>
    <?php endif; ?>
</body>
</html>

<?php
$conn->close();
?>
