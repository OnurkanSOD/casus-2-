<?php
require 'db_connection.php';

$message = "";

// Add a new news item
if (isset($_POST['submit'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);
    $role = $conn->real_escape_string($_POST['role']);
    
    $stmt = $conn->prepare("INSERT INTO nieuws (username, password, role, created_at) VALUES (?, ?, ?, 0)");
    $stmt->bind_param("sss", $username, $password, $role);
    
    if ($stmt->execute()) {
        $message = "New record successfully added.";
    } else {
        $message = "Error: " . $stmt->error;
    }
    
    $stmt->close();
}

// Delete a news item
if (isset($_GET['delete'])) {
    $ID = $_GET['delete'];
    
    $stmt = $conn->prepare("DELETE FROM nieuws WHERE ID = ?");
    $stmt->bind_param("i", $ID);
    
    if ($stmt->execute()) {
        $message = "Record successfully deleted.";
    } else {
        $message = "Error: " . $stmt->error;
    }
    
    $stmt->close();
}

// Fetch all news items
$result = $conn->query("SELECT * FROM nieuws");

?>

<!DOCTYPE html>
<html>
<head>
    <username>Nieuwswebsite</username>
    <link rel="stylesheet" type="text/css" href="ll.css">

</head>
<body>
    <h1>Nieuwswebsite</h1>
    
    <?php if ($message): ?>
        <p><?= $message ?></p>
    <?php endif; ?>

    <h2>Voeg een nieuw nieuwsbericht toe</h2>
    <form method="post" action="">
        Titel: <input type="text" name="username" required><br>
        Inhoud: <textarea name="password" required></textarea><br>
        Categorie: <input type="text" name="role" required><br>
        <input type="submit" name="submit" value="Toevoegen">
    </form>

    <h2>Nieuwsberichten</h2>
    <ul>
        <?php while($row = $result->fetch_assoc()): ?>
            <li>
                <a href="news.php?ID=<?= $row['ID'] ?>"><?= $row['username'] ?></a>
                (<?= $row['role'] ?>) - <?= $row['created_at'] ?> keer gelezen
                <a href="?delete=<?= $row['ID'] ?>" onclick="return confirm('Weet je zeker dat je dit bericht wilt verwijderen?')">Verwijderen</a>
            </li>
        <?php endwhile; ?>
    </ul>
</body>
</html>

<?php
$conn->close();
?>
