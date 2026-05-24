<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Solicitações de Exame</title>
</head>
<!-- 
solicitacao_exame:
id
data
justificativa
prioridade
id_consulta

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
    <h1>Lista de Solicitações de Exame</h1>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>data</th>
                <th>justificativa</th>
                <th>prioridade</th>
                <th>Consulta</th><!--id_consulta-->
            </tr>
        </thead>
        <tbody>
            @foreach($solicitacoes as $solicitacao)
                <tr>
                    <td>{{ $solicitacao->id }}</td>
                    <td>{{ $solicitacao->data }}</td>
                    <td>{{ $solicitacao->justificativa }}</td>
                    <td>{{ $solicitacao->prioridade }}</td>
                    <td><a href="{{ route('consultas.show', $solicitacao->id_consulta) }}">{{ $solicitacao->consulta->descricao ?? 'N/A' }}</a></td>
                    <td>
                        <!-- botão para editar solicitação de exame -->
                        <button><a href="{{ route('solicitacoesExame.edit', $solicitacao->id) }}">Editar</a></button>
                        <!-- botão para deletar solicitação de exame -->
                        <form action="{{ route('solicitacoesExame.destroy', $solicitacao->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Deletar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <button><a href="{{ route('solicitacoesExame.form') }}">Nova Solicitação de Exame</a></button>
</body>
</html>