<?php 
include 'includes/header.php'; 
include 'includes/nav.php'; 
include 'includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_SESSION['username'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    updateUserDetails($username, $name, $email);
}

$user = getUserDetails($_SESSION['username']);
?>
<div class="settings-container">
    <h2>Configuración de Usuario</h2>
    <form method="post">
        <input type="text" name="name" value="<?php echo $user['name']; ?>" required>
        <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
        <button type="submit">Actualizar</button>
    </form>
</div>
<?php include 'includes/footer.php'; ?>
