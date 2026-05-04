<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Consulta</title>
</head>
<body>
    <!-- formulário para criar nova consulta -->
    <button><a href="{{ route('consultas.index') }}">X</a></button>
    
    <h1>Formulário de Consulta</h1>

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('consultas.store') }}" method="POST">
        @csrf
        @method('POST')
        <label for="descricao">Descrição:</label>
        <input type="text" id="descricao" name="descricao" required><br><br>

        <label for="data">Data:</label>
        <input type="date" id="data" name="data" required><br><br>

        <label for="hora_inicio">Hora Início:</label>
        <input type="time" id="hora_inicio" name="hora_inicio" required><br><br>

        <label for="hora_fim">Hora Fim:</label>
        <input type="time" id="hora_fim" name="hora_fim" required><br><br>

        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <option value="">Selecione</option>
            <option value="agendada">Agendada</option>
            <option value="realizada">Realizada</option>
            <option value="cancelada">Cancelada</option>
        </select><br><br>

        <label for="tipo_consulta">Tipo de Consulta:</label>
        <select id="tipo_consulta" name="tipo_consulta" required>
            @foreach($tipos_consulta as $tipo)
                <option value="{{ $tipo->id }}">{{ $tipo->descricao }}</option>
            @endforeach
        </select><br><br>

        <!-- campos para selecionar paciente e médico -->
        <label for="id_paciente">Paciente:</label>
        <select id="id_paciente" name="id_paciente" required>
            <!-- opções de pacientes serão carregadas aqui -->
            @foreach($pacientes as $paciente)
                <option value="{{ $paciente->id }}">{{ $paciente->nome }}</option>
            @endforeach
        </select><br><br>

        <label for="id_medico">Médico:</label>
        <select id="id_medico" name="id_medico" required>
            <!-- opções de médicos serão carregadas aqui -->
            @foreach($medicos as $medico)
                <option value="{{ $medico->id }}">{{ $medico->pessoa->nome }}</option>
            @endforeach
        </select><br><br>

        <button type="submit">Cadastrar Consulta</button>
    </form>
</body>
</html>