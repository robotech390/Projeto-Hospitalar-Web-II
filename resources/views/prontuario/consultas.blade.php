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
                    <td>{{ $consulta->data }}</td>
                    <td>{{ $consulta->hora_inicio }}</td>
                    <td>{{ $consulta->hora_fim }}</td>
                    <td>{{ $consulta->status }}</td>
                    <td>
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