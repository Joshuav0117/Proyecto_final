<?php
$step = 1;
$error = '';
$success = '';
$h = fn($v) => htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');

include __DIR__ . '/../layout/admin_header.php';
?>


<style>

.admin-page{
  margin-top: 10px;
}

.admin-title{
  text-align: center;
  font-size: 28px;
  margin: 0 0 28px 0;
}

.admin-button-grid{
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-top: 10px;
}

.admin-btn-box{
display: flex;
align-items: center;
gap: 14px;
width: 100%;
min-height: 90px;
padding: 22px 24px;
background: #000000;
color: #ffffff !important;
text-decoration: none !important;
border-radius: 14px;
box-shadow: 0 4px 10px rgba(0,0,0,.15);
font-size: 22px;
font-weight: 700;
transition: .2s ease;
}

.admin-btn-box:hover{
  background: #2ea6bc;
  transform: translateY(-2px);
}

.admin-btn-icon{
  font-size: 30px;
  line-height: 1;
  min-width: 34px;
  text-align: center;
}

@media (max-width: 700px){
  .admin-button-grid{
    grid-template-columns: 1fr;
  }
}

.pending-title{
  text-align:center;
  font-size:26px;
  margin-bottom:20px;
  width:100%;
}

.pending-empty{
  text-align:center;
  font-size:18px;
  color:#777;
  margin-top:8px;
}
</style>


<div class="admin-page">


  <div class="admin-button-grid">

    <a href="#" class="admin-btn-box">
      <span class="admin-btn-icon">✎</span>
      <span>Editar Salón</span>
    </a>

    <a href="#" class="admin-btn-box">
      <span class="admin-btn-icon">＋</span>
      <span>Añadir Salón</span>
    </a>

    <a href="#" class="admin-btn-box">
      <span class="admin-btn-icon">👥</span>
      <span>Añadir Administrador</span>
    </a>

    <a href="#" class="admin-btn-box">
      <span class="admin-btn-icon">🗂</span>
      <span>Añadir Archivo</span>
    </a>

  </div>


  
  <div class="pending-section">
  <br>
    <h2 class="pending-title">Reservaciones Pendientes</h2>

    <div class="pending-empty">
      No hay reservaciones pendientes
    </div>

  </div>

</div>

<?php include __DIR__ . '/../layout/admin_footer.php'; ?>