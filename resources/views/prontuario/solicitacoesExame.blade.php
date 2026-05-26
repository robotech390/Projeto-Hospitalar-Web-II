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
                <th>Data de Criação</th>
                <th>Data de Alteração</th>
                <th>Consulta</th><!--id_consulta-->
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($solicitacoes as $solicitacao)
                <tr>
                    <td>{{ $solicitacao->id }}</td>
                    <td>{{ $solicitacao->data }}</td>
                    <td>{{ $solicitacao->justificativa }}</td>
                    <td>{{ $solicitacao->prioridade }}</td>
                    <td>{{ $solicitacao->data_criacao }}</td>
                    <td>{{ $solicitacao->data_alteracao }}</td>
                    <td>
                        @if($solicitacao->id_consulta)
                            <a href="{{ route('consultas.show', ['consulta' => $solicitacao->id_consulta]) }}">{{ $solicitacao->consulta->descricao ?? 'N/A' }}</a>
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        <!-- botão para visualizar itens de exame desta solicitação -->
                        
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
    <div style="margin-top:1rem;">
        <button id="btnListarItens">Listar Itens de Exame (API)</button>
        <button><a href="{{ route('solicitacoesExame.form') }}">Nova Solicitação de Exame</a></button>
    </div>

    <div id="apiResults" style="white-space:pre-wrap; margin-top:1rem; border:1px solid #ccc; padding:0.5rem; display:none;"></div>

    <script>
    document.getElementById('btnListarItens').addEventListener('click', function(){
        const out = document.getElementById('apiResults');
        out.style.display = 'block';
        out.textContent = 'Carregando...';
        fetch('/api/itens-exame', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        }).then(async response => {
            if(!response.ok){
                const txt = await response.text();
                out.textContent = 'Erro: ' + response.status + '\n' + txt;
                return;
            }
            const data = await response.json();
            out.textContent = JSON.stringify(data, null, 2);
        }).catch(err=>{
            out.textContent = 'Erro: ' + err.message;
        });
    });
    </script>
</body>
</html>