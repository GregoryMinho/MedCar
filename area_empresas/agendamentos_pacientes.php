<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedQ - Agendamentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a365d;
            --secondary-color: #2a4f7e;
            --accent-color: #38b2ac;
            --confirmed-color: #28a745;
            --pending-color: #ffc107;
            --cancelled-color: #dc3545;
        }

        body {
            background: #f8f9fa;
        }

        .schedule-dashboard {
            background: #f8f9fa;
            min-height: 100vh;
        }

        .schedule-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s;
            margin-bottom: 20px;
            padding: 20px;
            position: relative;
        }

        .schedule-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .status-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            position: absolute;
            top: 15px;
            right: 15px;
        }

        .status-confirmed { background: var(--confirmed-color); color: white; }
        .status-pending { background: var(--pending-color); color: black; }
        .status-cancelled { background: var(--cancelled-color); color: white; }

        .schedule-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--accent-color);
            color: white;
        }

        .timeline {
            border-left: 3px solid var(--primary-color);
            padding-left: 1rem;
            margin: 1rem 0;
        }

        .btn-schedule {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-schedule:hover {
            background: #2c7a7b;
            color: white;
        }

        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
            margin-bottom: 20px;
        }

        .calendar-day {
            background: white;
            border-radius: 10px;
            padding: 15px;
            min-height: 100px;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }

        .calendar-day:hover {
            background: #f8f9fa;
            transform: scale(1.02);
        }

        .calendar-day.active {
            border: 2px solid var(--accent-color);
        }

        .event-dot {
            width: 8px;
            height: 8px;
            background: var(--accent-color);
            border-radius: 50%;
            position: absolute;
            bottom: 5px;
            left: 50%;
            transform: translateX(-50%);
        }

        .schedule-details {
            display: none;
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: var(--primary-color);">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-ambulance me-2"></i>
                MedQ Transportes
            </a>
            <div class="d-flex align-items-center">
                <div class="text-white me-3">Transportadora Saúde Total</div>
                <img src="https://source.unsplash.com/random/40x40/?icon" class="rounded-circle" alt="Perfil">
            </div>
        </div>
    </nav>

    <div class="schedule-dashboard">
        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-md-3 p-4" style="background: var(--secondary-color); color: white; min-height: 100vh;">
                    <h5 class="mb-4"><i class="fas fa-filter me-2"></i>Filtros</h5>
                    
                    <div class="mb-4">
                        <label class="form-label">Status</label>
                        <select class="form-select">
                            <option value="all">Todos</option>
                            <option value="pending">Pendentes</option>
                            <option value="confirmed">Confirmados</option>
                            <option value="cancelled">Cancelados</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Período</label>
                        <input type="month" class="form-control" id="monthPicker">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Tipo de Serviço</label>
                        <select class="form-select">
                            <option value="all">Todos</option>
                            <option value="routine">Rotina</option>
                            <option value="exam">Exames</option>
                            <option value="emergency">Emergência</option>
                        </select>
                    </div>

                    <button class="btn btn-schedule w-100 mb-3">
                        <i class="fas fa-sync me-2"></i>Aplicar Filtros
                    </button>

                    <button class="btn btn-light w-100">
                        <i class="fas fa-plus me-2"></i>Novo Agendamento
                    </button>
                </div>

                <!-- Conteúdo Principal -->
                <div class="col-md-9 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3><i class="fas fa-calendar-alt me-2"></i>Agendamentos</h3>
                        <div class="d-flex gap-2">
                            <input type="month" class="form-control" id="calendarMonth">
                        </div>
                    </div>

                    <!-- Calendário -->
                    <div class="calendar">
                        <!-- Dias da semana -->
                        <div class="text-center fw-bold">Dom</div>
                        <div class="text-center fw-bold">Seg</div>
                        <div class="text-center fw-bold">Ter</div>
                        <div class="text-center fw-bold">Qua</div>
                        <div class="text-center fw-bold">Qui</div>
                        <div class="text-center fw-bold">Sex</div>
                        <div class="text-center fw-bold">Sáb</div>

                        <!-- Dias do mês -->
                        <div class="calendar-day" onclick="showScheduleDetails(1)">
                            <div>1</div>
                            <div class="event-dot"></div>
                        </div>
                        <!-- Adicione mais dias conforme necessário -->
                    </div>

                    <!-- Detalhes do Agendamento -->
                    <div class="schedule-details" id="scheduleDetails">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5>Agendamentos - <span id="selectedDate">15/03/2024</span></h5>
                            <button class="btn btn-close" onclick="hideScheduleDetails()"></button>
                        </div>
                        
                        <div class="timeline">
                            <div class="mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="schedule-icon">
                                        <i class="fas fa-user-injured"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">João da Silva</h6>
                                        <small class="text-muted">14:00 - Hospital Santa Maria</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Funções para mostrar/ocultar detalhes
        function showScheduleDetails(day) {
            document.getElementById('scheduleDetails').style.display = 'block';
            document.getElementById('selectedDate').textContent = day + '/03/2024';
        }

        function hideScheduleDetails() {
            document.getElementById('scheduleDetails').style.display = 'none';
        }

        // Adiciona interação com os dias do calendário
        document.querySelectorAll('.calendar-day').forEach(day => {
            day.addEventListener('mouseenter', function() {
                this.classList.add('active');
            });
            
            day.addEventListener('mouseleave', function() {
                this.classList.remove('active');
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>