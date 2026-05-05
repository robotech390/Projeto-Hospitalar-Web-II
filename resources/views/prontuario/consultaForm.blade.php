<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Consulta</title>
</head>
<body>
    <!-- formulário para criar/editar consultas -->
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

    @php
        $isEditing = isset($consulta);
        $action = $isEditing ? route('consultas.update', $consulta->id) : route('consultas.store');
    @endphp
    
    <form action="{{ $action }}" method="POST">
        @csrf
        @if($isEditing)
            @method('PUT')
        @else
            @method('POST')
        @endif
        
        <label for="descricao">Descrição:</label>
        <input type="text" id="descricao" name="descricao" value="{{ $consulta->descricao ?? '' }}" required><br><br>

        <label for="data">Data:</label>
        <input type="date" id="data" name="data" value="{{ $isEditing ? $consulta->data->format('Y-m-d') : '' }}" required><br><br>

        <label for="hora_inicio">Hora Início:</label>
        <input type="time" id="hora_inicio" name="hora_inicio" value="{{ $isEditing ? $consulta->hora_inicio->format('H:i') : '' }}" required><br><br>

        <label for="hora_fim">Hora Fim:</label>
        <input type="time" id="hora_fim" name="hora_fim" value="{{ $isEditing ? $consulta->hora_fim->format('H:i') : '' }}" required><br><br>

        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <option value="">Selecione</option>
            <option value="agendada" {{ $isEditing && $consulta->status === 'agendada' ? 'selected' : '' }}>Agendada</option>
            <option value="em_espera" {{ $isEditing && $consulta->status === 'em_espera' ? 'selected' : '' }}>Em Espera</option>
            <option value="em_andamento" {{ $isEditing && $consulta->status === 'em_andamento' ? 'selected' : '' }}>Em Andamento</option>
            <option value="concluida" {{ $isEditing && $consulta->status === 'concluida' ? 'selected' : '' }}>Concluída</option>
            <option value="cancelada" {{ $isEditing && $consulta->status === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
        </select><br><br>

        <label for="id_tipo_consulta">Tipo de Consulta:</label>
        <select id="id_tipo_consulta" name="id_tipo_consulta" required>
            @foreach($tipos_consulta as $tipo)
                <option value="{{ $tipo->id }}" {{ $isEditing && $consulta->id_tipo_consulta === $tipo->id ? 'selected' : '' }}>{{ $tipo->descricao }}</option>
            @endforeach
        </select><br><br>

        <!-- campos para selecionar paciente e médico -->
        <label for="id_paciente">Paciente:</label>
        <select id="id_paciente" name="id_paciente" required>
            @foreach($pacientes as $paciente)
                <option value="{{ $paciente->id }}" {{ $isEditing && $consulta->id_paciente === $paciente->id ? 'selected' : '' }}>{{ $paciente->nome }}</option>
            @endforeach
        </select><br><br>

        <label for="id_medico">Médico:</label>
        <select id="id_medico" name="id_medico" required>
            @foreach($medicos as $medico)
                @if($medico->pessoa)
                    <option value="{{ $medico->id }}" {{ $isEditing && $consulta->id_medico === $medico->id ? 'selected' : '' }}>{{ $medico->pessoa->nome }}</option>
                @endif
            @endforeach
        </select><br><br>

        <button type="submit">{{ $isEditing ? 'Atualizar Consulta' : 'Cadastrar Consulta' }}</button>
    </form>
</body>
</html>