<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Diagnosticos</title>
</head>
<body>
    <!-- lista de diagnosticos com botões para deletar cada um e botão para criar novos -->
    <h1>Lista de Diagnosticos</h1>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Descrição</th>
                <th>Cid</th>
                <th>Data de Criação</th>
                <th>Data de Alteração</th>
                <th>Consulta</th><!--id_consulta-->
            </tr>
        </thead>
        <tbody>
            @foreach($diagnosticos as $diagnostico)
                <tr>
                    <td>{{ $diagnostico->id }}</td>
                    <td>{{ $diagnostico->descricao }}</td>
                    <td>{{ $diagnostico->cid }}</td>
                    <td>{{ $diagnostico->data_criacao->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $diagnostico->data_alteracao->format('d/m/Y H:i:s') }}</td>
                    <td><a href="{{ route('consultas.show', $diagnostico->id_consulta) }}">{{ $diagnostico->consulta->descricao ?? 'N/A' }}</a></td>
                    <td>
                        <!-- botão para editar diagnóstico -->
                        <button><a href="{{ route('diagnosticos.edit', $diagnostico->id) }}">Editar</a></button>
                        <!-- botão para deletar diagnóstico -->
                        <form action="{{ route('diagnosticos.destroy', $diagnostico->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Deletar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <button><a href="{{ route('diagnosticos.form') }}">Novo Diagnóstico</a></button>
</body>
</html>