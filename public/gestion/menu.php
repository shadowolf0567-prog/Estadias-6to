<nav class="navbar navbar-expand-lg navbar-dark" style="background: #808080;">
    <div class="container">
        <img src="../assets/img/Logo.png" alt="Logo" style="height: 44px; width: auto;">
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'clientes') !== false ? 'active' : '' ?>" 
                       href="../clientes/clientes.php">Clientes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'equipos') !== false ? 'active' : '' ?>" 
                       href="../equipos/equipos.php">Equipos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'reportes') !== false ? 'active' : '' ?>" 
                       href="../reportes/reportes.php">Reportes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'backup') !== false ? 'active' : '' ?>" 
                       href="../respaldos/backup.php">Respaldos
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">
                        <i class="bi bi-box-arrow-right"></i> Salir
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>