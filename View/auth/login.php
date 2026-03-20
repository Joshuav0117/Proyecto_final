<?php include __DIR__ . '/../layout/login_header.php'; ?>

<div class="login-page">
  <div class="login-overlay"></div>

  <div class="login-top">
    <div class="login-brand">
    <div class="login-brand-icon">
  <img src="assets/img/UPRA.png" alt="Logo UPRA">
</div>
      <div class="login-brand-text">
        <h1>UNIVERSIDAD</h1>
        <p>Portal Estudiantil</p>
      </div>
    </div>
  </div>

  <div class="login-card">
    <h2>Iniciar Sesión</h2>
    <div class="login-divider"></div>

    <?php if (!empty($error)): ?>
      <div class="login-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <form method="POST" action="index_login.php" class="login-form">
      <div class="login-field">
        <span class="login-icon">✉</span>
        <input
          type="email"
          name="email"
          placeholder="Correo electrónico"
          value="<?php echo htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8'); ?>"
          required
        >
      </div>

      <div class="login-field">
        <span class="login-icon">🔒</span>
        <input
          type="password"
          name="password"
          placeholder="Contraseña"
          required
        >
      </div>

      <button type="submit" class="login-btn">Iniciar Sesión</button>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../layout/login_footer.php'; ?>