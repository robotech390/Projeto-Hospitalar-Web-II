<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Solicitações de Exame</title>
</head>
<!-- 
solicitacao_exame:
id
data datetime
justificativa varchar
prioridade int
id_consulta int

itens_exame:
id
id_solicitacao
id_tipo_exame
status
laudo
arquivo
data_resultado

tipo_exame:
id
nome
tipo
preco
preparo
-->
<body>
    <button><a href="{{ route('solicitacoes.index') }}">X</a></button>
    
    <h1>Formulário de Solicitações de Exame</h1>

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
        $isEditing = isset($solicitacao);
        $action = $isEditing ? route('solicitacoes.update', $solicitacao->id) : route('solicitacoes.store');
    @endphp
    
    <form action="{{ $action }}" method="POST">
        @csrf
        @if($isEditing)
            @method('PUT')
        @else
            @method('POST')
        @endif
        
        <label for="data">Data:</label>
        <input type="datetime-local" id="data" name="data" value="{{ $solicitacao->data ?? '' }}" required><br><br>

        <label for="justificativa">Justificativa:</label>
        <input type="text" id="justificativa" name="justificativa" value="{{ $solicitacao->justificativa ?? '' }}" required><br><br>

        <label for="prioridade">Prioridade:</label>
        <select id="prioridade" name="prioridade" required>
            <option value="1" {{ (isset($solicitacao) && $solicitacao->prioridade == '1') || (!isset($solicitacao) && old('prioridade') == '1') ? 'selected' : '' }}>Baixa</option>
            <option value="2" {{ (isset($solicitacao) && $solicitacao->prioridade == '2') || (!isset($solicitacao) && old('prioridade') == '2') ? 'selected' : '' }}>Média</option>
            <option value="3" {{ (isset($solicitacao) && $solicitacao->prioridade == '3') || (!isset($solicitacao) && old('prioridade') == '3') ? 'selected' : '' }}>Alta</option>
        </select><br><br>

        <label for="id_consulta">Consulta:</label>
        <select id="id_consulta" name="id_consulta" required>
            @foreach($consultas as $consulta)
                <option value="{{ $consulta->id }}" {{ ($isEditing && $solicitacao->id_consulta == $consulta->id) || (! $isEditing && isset($selectedConsulta) && $selectedConsulta == $consulta->id) ? 'selected' : '' }}>{{ $consulta->id }} - {{ $consulta->descricao }}</option>
            @endforeach
        </select><br><br>

        <button type="submit">{{ $isEditing ? 'Atualizar Solicitação' : 'Cadastrar Solicitação' }}</button>
    </form>
</body>
</html>