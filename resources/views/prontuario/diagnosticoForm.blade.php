<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Diagnostico</title>
</head>
<body>
    <!-- formulário para criar/editar consultas -->
    <button><a href="{{ route('diagnosticos.index') }}">X</a></button>
    
    <h1>Formulário de Diagnóstico</h1>

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
        $isEditing = isset($diagnostico);
        $action = $isEditing ? route('diagnosticos.update', $diagnostico->id) : route('diagnosticos.store');
    @endphp
    
    <form action="{{ $action }}" method="POST">
        @csrf
        @if($isEditing)
            @method('PUT')
        @else
            @method('POST')
        @endif
        
        <label for="descricao">Descrição:</label>
        <input type="text" id="descricao" name="descricao" value="{{ $diagnostico->descricao ?? '' }}" required><br><br>

        <label for="cid">CID:</label>
        <input type="text" id="cid" name="cid" value="{{ $diagnostico->cid ?? '' }}" required><br><br>

        <label for="id_consulta">Consulta:</label>
        <select id="id_consulta" name="id_consulta" required>
            @foreach($consultas as $consulta)
                <option value="{{ $consulta->id }}" {{ ($isEditing && $diagnostico->id_consulta == $consulta->id) || (! $isEditing && isset($selectedConsulta) && $selectedConsulta == $consulta->id) ? 'selected' : '' }}>{{ $consulta->id }} - {{ $consulta->descricao }}</option>
            @endforeach
        </select><br><br>

        <button type="submit">{{ $isEditing ? 'Atualizar Diagnóstico' : 'Cadastrar Diagnóstico' }}</button>
    </form>
</body>
</html>