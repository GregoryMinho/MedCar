<?php
require '../includes/classe_usuario.php';
use usuario\Usuario;

Usuario::verificarPermissao('empresa');

require '../includes/conexao_BdMotoristas.php';

$mensagem = '';
$classeMensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Inserir Motorista
        $stmt = $pdo->prepare("INSERT INTO Motoristas 
            (nome, cnh, status, cidade, estado, foto_url) 
            VALUES (?, ?, ?, ?, ?, ?)");
        
        $foto_url = uploadFoto($_FILES['foto']);
        
        $stmt->execute([
            $_POST['nome'],
            $_POST['cnh'],
            $_POST['status'],
            $_POST['cidade'],
            $_POST['estado'],
            $foto_url
        ]);
        
        $motoristaId = $pdo->lastInsertId();

        // Inserir Veículo se os dados foram fornecidos
        if (!empty($_POST['placa'])) {
            $stmtVeiculo = $pdo->prepare("INSERT INTO Veiculos 
                (placa, modelo, tipo, status, ultima_manutencao, proxima_manutencao, motorista_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            $stmtVeiculo->execute([
                $_POST['placa'],
                $_POST['modelo'],
                $_POST['tipo'],
                $_POST['status_veiculo'],
                $_POST['ultima_manutencao'],
                $_POST['proxima_manutencao'],
                $motoristaId
            ]);
        }

        $mensagem = "Motorista cadastrado com sucesso!";
        $classeMensagem = 'alert-success';

    } catch (PDOException $e) {
        $mensagem = "Erro ao cadastrar motorista: " . $e->getMessage();
        $classeMensagem = 'alert-danger';
    }
}

function uploadFoto($arquivo) {
    if ($arquivo['error'] !== UPLOAD_ERR_OK) return null;
    
    $diretorioUploads = '../uploads/motoristas/';
    $nomeUnico = uniqid() . '_' . basename($arquivo['name']);
    $caminhoCompleto = $diretorioUploads . $nomeUnico;
    
    if (move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
        return $caminhoCompleto;
    }
    return null;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCar - Cadastro de Motoristas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style/style_adicionar_motorista.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: var(--primary-color)">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-users-cog me-2"></i>
                MedCar - Cadastro de Motoristas
            </a>
        </div>
    </nav>

    <div class="container">
        <div class="registration-card">
            <?php if ($mensagem): ?>
            <div class="alert <?= $classeMensagem ?>"><?= $mensagem ?></div>
            <?php endif; ?>

            <h3 class="mb-4"><i class="fas fa-user-plus me-2"></i>Novo Motorista</h3>
            
            <form method="POST" enctype="multipart/form-data">
                <!-- Seção Dados Pessoais -->
                <div class="form-section">
                    <h5 class="mb-3 text-primary"><i class="fas fa-id-card me-2"></i>Dados Pessoais</h5>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" name="nome" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">CNH</label>
                            <input type="text" class="form-control" name="cnh" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="Ativo">Ativo</option>
                                <option value="Inativo">Inativo</option>
                                <option value="Em Serviço">Em Serviço</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Cidade</label>
                            <input type="text" class="form-control" name="cidade" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Estado</label>
                            <input type="text" class="form-control" name="estado" maxlength="2" required>
                        </div>
                    </div>
                </div>

                <!-- Seção Foto -->
                <div class="form-section">
                    <h5 class="mb-3 text-primary"><i class="fas fa-camera me-2"></i>Foto do Motorista</h5>
                    
                    <div class="file-upload" onclick="document.getElementById('foto').click()">
                        <div class="mb-2">
                            <i class="fas fa-cloud-upload-alt fa-2x text-muted"></i>
                        </div>
                        <span class="text-muted">Clique para enviar uma foto</span>
                        <input type="file" id="foto" name="foto" accept="image/*" hidden 
                            onchange="previewFoto(event)">
                    </div>
                    <img id="preview" class="preview-image">
                </div>

                <!-- Seção Veículo -->
                <div class="form-section">
                    <h5 class="mb-3 text-primary"><i class="fas fa-car me-2"></i>Dados do Veículo (Opcional)</h5>
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Placa</label>
                            <input type="text" class="form-control" name="placa">
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Modelo</label>
                            <input type="text" class="form-control" name="modelo">
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Tipo</label>
                            <select class="form-select" name="tipo">
                                <option value="">Selecione</option>
                                <option value="Ambulância">Ambulância</option>
                                <option value="UTI Móvel">UTI Móvel</option>
                                <option value="Van">Van</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Status do Veículo</label>
                            <select class="form-select" name="status_veiculo">
                                <option value="Disponível">Disponível</option>
                                <option value="Em Manutenção">Em Manutenção</option>
                                <option value="Em Uso">Em Uso</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Última Manutenção</label>
                            <input type="date" class="form-control" name="ultima_manutencao">
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Próxima Manutenção</label>
                            <input type="date" class="form-control" name="proxima_manutencao">
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-driver btn-lg">
                        <i class="fas fa-save me-2"></i>Cadastrar Motorista
                    </button>
                    <a href="motoristas.php" class="btn btn-secondary btn-lg">
                        <i class="fas fa-arrow-left me-2"></i>Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewFoto(event) {
            const preview = document.getElementById('preview');
            const file = event.target.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }

            if (file) {
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>