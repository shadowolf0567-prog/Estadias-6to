<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
if(!isset($_SESSION['tip_usr']) || ($_SESSION['tip_usr'] !=1 && $_SESSION['tip_usr'] != 2)){
    header('Location: ../login.php?error=' .urlencode('Acceso denegado'));
    exit;
}

$todos_equipos = [];
$sql_equipos = "SELECT e.id_equipo, e.no_serie, e.modelo, c.nombre as cliente_nombre, e.id_cliente
                FROM equipos e
                LEFT JOIN clientes c ON e.id_cliente = c.id_cliente
                ORDER BY e.no_serie ASC";
$result_equipos = mysqli_query($conn,$sql_equipos);
if($result_equipos){
    while($row = mysqli_fetch_assoc($result_equipos)){
        $todos_equipos[] = $row;
    }
}
$clientes = [];
$sql_clientes = "SELECT c.*,
                        GROUP_CONCAT(DISTINCT ct.telefono SEPARATOR ', ') as telefonos,
                        GROUP_CONCAT(DISTINCT cc.correo SEPARATOR ', ') as correos
                FROM clientes c
                LEFT JOIN telefonos ct ON c.id_cliente = ct.id_cliente
                LEFT JOIN correos cc ON c.id_cliente = cc.id_cliente
                GROUP BY c.id_cliente
                ORDER BY c.nombre ASC";
$result_clientes = mysqli_query($conn,$sql_clientes);
if($result_clientes){
    while($row = mysqli_fetch_assoc($result_clientes)){
        $clientes[] = $row;
    }
}
$cliente = "SELECT c.*,r.* FROM clientes c
            INNER JOIN reportes r
            ON r.id_cliente = c.id_cliente";
$componentes = [];
$sql_comp = "SELECT id, componente, descripcion
            FROM componentes
            ORDER BY componente ASC";
$result_comp = mysqli_query($conn,$sql_comp);
if($result_comp){
    while($row = mysqli_fetch_assoc($result_comp)){
        $componentes[] = $row;
    }
}
$error=isset($_GET['error']) ? $_GET['error'] : '';
$mensaje = isset($_GET['msg']) ? $_GET['msg'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportar en Masa</title>
    <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/responsives.css">
    <style>
        .form-section{
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .info-equipo{
            background: #e3f2fd;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }
        .cliente-card{
            cursor: pointer;
            transition: all 0.3s;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .cliente-card:hover{
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .cliente-seleccionado{
            background-color: #d4edda;
            border: 2px solid #28a745;
        }
        .filtro-busqueda{
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .resultados-busqueda{
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../gestion/menu.php'; ?>
    <div class="container mt-4">
        <h2 class="mb-4">Nuevos Reportes</h2>
        <p><span class="text-muted">Utiliza esta página si hay muchos servicios preventivos/correctivos de un mismo cliente</span></p>
        <a href="agregar_reporte.php" class="btn btn-sm btn-primary">
            <i class="bi bi-arrow-left"></i> Agregar Reporte
        </a>
        <?php if($mensaje): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle"></i> <?= htmlspecialchars($mensaje) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <form action="../lib/gestion_reportes.php" method="post">
            <input type="hidden" name="accion" value="agregar_muchos">
            <input type="hidden" name="id_cliente" id="idCliente" value="0">
            <div class="form-section">
                <h5>Cliente</h5>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Seleccionar Cliente</label>
                        <div class="filtro-busqueda">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control"  id="buscarCliente">
                                <button type="button" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Limpiar
                                </button>
                            </div>
                        </div>
                        <div class="resultados-busqueda" id="resultadosBusqueda" style="overflow-y: auto;max-height: 400px">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"> Escribe al menos 2 caracteres para comenzar a buscar</i>
                            </div>
                        </div>
                        <div class="alert alert-succcess mt-3" id="clienteSeleccionado" style="display:none;">
                            <div class="d-flex justify-content-between align-item-center">
                                <div>
                                    <i class="bi bi-check-circle"></i>
                                    <strong>Cliente Seleccionado: </strong><span id="nombreClienteSeleccionado"></span>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="limpiarSeleccion()">
                                    <i class="bi bi-x-circle"></i> Cambiar
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="telefono_cliente" id="telefono_cliente">
                        <input type="hidden" name="contacto_cliente" id="contacto_cliente">
                    </div>
                </div>
                <div class="form-section">
                    <h5>Detalles de los Reportes</h5>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label">Servicio</label>
                            <select name="servicio" class="form-select">
                                <option value="SER-01">SER-01</option>
                                <option value="SER-02">SER-02</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="" class="form-label">Fecha</label>
                            <input type="date" name="fecha" value="<?= date('Y-m-d') ?>" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="" class="form-label">Técnico</label>
                            <input type="text" name="tecnico" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="form-section">
                    <h5>Equipos</h5>
                    <div id="equiposContainer">
                        <div class="equipo-item" id="equipo_0">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <select name="id_equipo" id="id_equipo" class="form-select">
                                        <option value="">-- Seleccione un cliente primero --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="info-equipo" id="infoEquipo" style="display: none;">
                                <strong><i class="bi bi-info-circle"></i> Información del Equipo:</strong>
                                <span id="infoEquipoTexto"></span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-circle"></i> Agregar Equipo
                    </button>
                </div>
                <div class="mt-3 mb-3">
                    <button type="submit" class="btn btn-primary" id="btnGuardar">
                        <i class="bi bi-save"></i> Guardar Reportes
                    </button>
                    <a href="reportes.php" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded',function(){
            const buscarInput = document.getElementById('buscarCliente');
                if(buscarInput){
                    buscarInput.addEventListener('keydown',function(e){
                    if(e.key === 'Enter'){
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                        }
                    })
                }
            });
        const todosEquipos = <?= json_encode($todos_equipos) ?>;
        let clienteSeleccionado = null;
        let clienteIDSeleccionado = null;
        let clientesData = <?= json_encode($clientes) ?>;
        let elementoSeleccionado = null;
        let contadorComponentes = 1;
        const buscarClienteInput = document.getElementById('buscarCliente');
        const idClienteInput = document.getElementById('idCliente');
        const resultadosDiv = document.getElementById('resultadosBusqueda');
        const clienteSeleccionadoDiv = document.getElementById('clienteSeleccionado');
        const nombreClienteSpan = document.getElementById('nombreClienteSeleccionado');
        const equipoSelect = document.getElementById('id_equipo');
        const infoEquipoDiv = document.getElementById('infoEquipoTexto');
        const btnGuardar = document.getElementById('btnGuardar');

        function escapeHtml(text){
            if(!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        function buscarClientes(termino){
            if(!resultadosDiv) return;
            if(termino.length < 2){
                resultadosDiv.innerHTML = `
                    <div class="alert alert-info">
                        <div class="bi bi-info-circle"> Escribe al menos 2 caracteres para comenzar a buscar
                    </div>
                `;
                return;
            }
            const terminoLower = termino.toLowerCase();
            const resultados = clientesData.filter(cliente =>
                cliente.nombre.toLowerCase().includes(terminoLower) ||
                (cliente.no_cuenta && cliente.no_cuenta.toLowerCase().includes(terminoLower)) ||
                (cliente.correo && cliente.correo.toLowerCase().includes(terminoLower))
            );
            if(resultados.length === 0){
                resultadosDiv.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                `;
                return;
            }
            let html = `<div class="row">`;
            resultados.foreach(cliente =>{
                const isSelected = (clienteIDSeleccionado === cliente.id_cliente);
                html += `
                    <div class="col-md-6 mb-3">
                        <div class="card cliente-card ${isSelected ? 'cliente-seleccionado' : ''}"
                            data-cliente-id="${cliente.id_cliente}"
                            onclick="seleccionarCliente(${cliente.id_cliente}, '${escapeHtml(cliente.nombre)}')">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6 class="card-title">
                                        <i class="bi bi-person-circle"></i>
                                        ${escapeHtml(cliente.nombre)}
                                    </h6>
                                    ${isSelected ? '<i class="bi bi-check-circle-fill text-success"></i>' : ''}
                                </div>
                                ${cliente.no_cuenta ? `
                                    <p class="card-text small mb-1">
                                        Cuenta: ${escapeHtml(cliente.no_cuenta)}
                                    </p>
                                `:''}
                                ${cliente.contrato ? `
                                    <p class="card-text small mb-1">
                                        Contrato: ${escapeHtml(cliente.contrato)}
                                    </p>
                                `:''}
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '<7div>';
            resultadosDiv.innerHTML = html;
        }
    </script>
</body>
</html>