<?php include '../includes/header.php'; ?>
<main>
    <h1>Contacto</h1>
    <form action="contact_submit.php" method="POST">
        <label for="name">Nombre:</label>
        <input type="text" id="name" name="name" required>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <label for="message">Mensaje:</label>
        <textarea id="message" name="message" required></textarea>
        <button type="submit">Enviar</button>
    </form>
</main>
<?php include '../includes/footer.php'; ?>
