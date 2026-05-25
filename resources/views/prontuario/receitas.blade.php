<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Receitas</title>
</head>
<!--
receita:
id
observacoes varchar
farmacia varchar
data_emissao date
id_consulta

medicamento_receita:
id
id_receita
id_medicamento

medicamento:
id
nome
dosagem
principio_ativo
id_tipo_medicamento
preco
-->
<body>
    <h1>Lista de Receitas</h1>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Observações</th>
                <th>Farmácia</th>
                <th>Data de Emissão</th>
                <th>Data de Criação</th>
                <th>Data de Alteração</th>
                <th>Consulta</th><!--id_consulta-->
            </tr>
        </thead>
        <tbody>
            @foreach($receitas as $receita)
                <tr>
                    <td>{{ $receita->id }}</td>
                    <td>{{ $receita->observacoes }}</td>
                    <td>{{ $receita->farmacia }}</td>
                    <td>{{ $receita->data_emissao }}</td>
                    <td>{{ $receita->data_criacao }}</td>
                    <td>{{ $receita->data_alteracao }}</td>
                    <td>
                        @if($receita->id_consulta)
                            <a href="{{ route('consultas.show', ['consulta' => $receita->id_consulta]) }}">{{ $receita->consulta->descricao ?? 'N/A' }}</a>
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        <!-- botão para editar receita -->
                        <button><a href="{{ route('receitas.edit', $receita->id) }}">Editar</a></button>
                        <!-- botão para deletar receita -->
                        <form action="{{ route('receitas.destroy', $receita->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Deletar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <button><a href="{{ route('receitas.form') }}">Nova Receita</a></button>
</body>
</html>