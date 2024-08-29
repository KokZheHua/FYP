<?php
    session_start();
    include("connection.php");

    $id = $_SESSION['id'];

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["theme"])) {
        $selectedTheme = $_POST["theme"];

        $themeFilePath = "color_theme.php";
        $themeContent = '<link id="defaultTheme" rel="stylesheet" href="' . $selectedTheme . '">';
        file_put_contents($themeFilePath, $themeContent);

        $query = "UPDATE users SET color_theme = '$selectedTheme' WHERE id = $id";
    
        mysqli_query($con, $query);
    }
?>
