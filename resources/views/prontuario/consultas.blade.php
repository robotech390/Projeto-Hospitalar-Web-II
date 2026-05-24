<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Consultas</title>
</head>
<body>
    <!-- lista de consultas com botões para deletar cada uma e botão para criar novas -->
    <h1>Lista de Consultas</h1>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Descrição</th>
                <th>Paciente</th>
                <th>Medico</th>
                <th>Data</th>
                <th>Hora Início</th>
                <th>Hora Fim</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($consultas as $consulta)
                <tr>
                    <td>{{ $consulta->id }}</td>
                    <td>{{ $consulta->descricao }}</td>
                    <td>{{ $consulta->paciente->nome ?? 'N/A' }}</td>
                    <td>{{ $consulta->medico->pessoa->nome ?? 'N/A' }}</td>
                    <td>{{ $consulta->data->format('d/m/Y') }}</td>
                    <td>{{ $consulta->hora_inicio->format('H:i:s') }}</td>
                    <td>{{ $consulta->hora_fim->format('H:i:s') }}</td>
                    <td>{{ $consulta->status }}</td>
                    <td>{{ $consulta->tipo_consulta->descricao ?? 'N/A' }}, {{ $consulta->tipo_consulta->valor ?? 'N/A' }}</td><!-- id_tipo_consulta -> "descricao, valor" -->
                    <td>
                        <!-- botão para criar diagnóstico -->
                        <button><a href="{{ route('diagnosticos.create', $consulta->id) }}">Diagnóstico</a></button>
                        <!-- botão para editar consulta -->
                        <button><a href="{{ route('consultas.edit', $consulta->id) }}">Editar</a></button>
                        <!-- botão para deletar consulta -->
                        <form action="{{ route('consultas.destroy', $consulta->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Deletar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <button><a href="{{ route('consultas.form') }}">Nova Consulta</a></button>
</body>
</html>