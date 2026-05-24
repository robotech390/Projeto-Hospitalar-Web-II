<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Receita</title>
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
    <button><a href="{{ route('receitas.index') }}">X</a></button>
    
    <h1>Formulário de Receita</h1>

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
        $isEditing = isset($receita);
        $action = $isEditing ? route('receitas.update', $receita->id) : route('receitas.store');
    @endphp
    
    <form action="{{ $action }}" method="POST">
        @csrf
        @if($isEditing)
            @method('PUT')
        @else
            @method('POST')
        @endif
        
        <label for="observacoes">Observações:</label>
        <input type="text" id="observacoes" name="observacoes" value="{{ $receita->observacoes ?? '' }}" required><br><br>

        <label for="farmacia">Farmácia:</label>
        <input type="text" id="farmacia" name="farmacia" value="{{ $receita->farmacia ?? '' }}" required><br><br>

        <label for="data_emissao">Data de Emissão:</label>
        <input type="date" id="data_emissao" name="data_emissao" value="{{ $receita->data_emissao ?? '' }}" required><br><br>

        <label for="id_consulta">Consulta:</label>
        <select id="id_consulta" name="id_consulta" required>
            @foreach($consultas as $consulta)
                <option value="{{ $consulta->id }}" {{ ($isEditing && $receita->id_consulta == $consulta->id) || (! $isEditing && isset($selectedConsulta) && $selectedConsulta == $consulta->id) ? 'selected' : '' }}>{{ $consulta->id }} - {{ $consulta->descricao }}</option>
            @endforeach
        </select><br><br>

        <label>Medicamentos:</label>
        @php
            $medicamentoRows = old('medicamentos', []);
            if (empty($medicamentoRows) && $isEditing && isset($receita)) {
                $medicamentoRows = $receita->medicamentos->map(function ($item) {
                    return [
                        'id_medicamento' => $item->id_medicamento,
                        'posologia'      => $item->posologia,
                        'quantidade'     => $item->quantidade,
                    ];
                })->toArray();
            }
            if (empty($medicamentoRows)) {
                $medicamentoRows = [[
                    'id_medicamento' => '',
                    'posologia'      => '',
                    'quantidade'     => 1,
                ]];
            }
        @endphp

        <div id="medicamento-list">
            @foreach($medicamentoRows as $index => $item)
                <div class="medicamento-row" style="margin-bottom: 1rem; display:flex; gap:0.5rem; flex-wrap:wrap; align-items:flex-end;">
                    <div>
                        <label>Medicamento</label><br>
                        <select data-name="medicamentos[INDEX][id_medicamento]" name="medicamentos[{{ $index }}][id_medicamento]" required>
                            <option value="">Selecione um medicamento</option>
                            @if(!empty($medicamentos))
                                @foreach($medicamentos as $medicamento)
                                    <option value="{{ $medicamento->id }}" {{ (string)($item['id_medicamento'] ?? '') === (string)$medicamento->id ? 'selected' : '' }}>{{ $medicamento->nome ?? 'Medicamento ' . $medicamento->id }}</option>
                                @endforeach
                            @else
                                <option value="" disabled>Sem medicamentos disponíveis</option>
                            @endif
                        </select>
                    </div>
                    <div>
                        <label>Posologia</label><br>
                        <input type="text" data-name="medicamentos[INDEX][posologia]" name="medicamentos[{{ $index }}][posologia]" value="{{ $item['posologia'] ?? '' }}" placeholder="Posologia">
                    </div>
                    <div>
                        <label>Quantidade</label><br>
                        <input type="number" data-name="medicamentos[INDEX][quantidade]" name="medicamentos[{{ $index }}][quantidade]" value="{{ $item['quantidade'] ?? 1 }}" min="1" style="width:5rem;">
                    </div>
                    <div>
                        <button type="button" class="remove-med" style="margin-top:1.75rem;">Remover</button>
                    </div>
                </div>
            @endforeach
        </div>

        <button type="button" id="add-medicamento" style="margin-bottom:1rem;">Adicionar medicamento</button>

        <div id="medicamento-row-template" style="display:none;">
            <div class="medicamento-row" style="margin-bottom: 1rem; display:flex; gap:0.5rem; flex-wrap:wrap; align-items:flex-end;">
                <div>
                    <label>Medicamento</label><br>
                    <select data-name="medicamentos[INDEX][id_medicamento]" required>
                        <option value="">Selecione um medicamento</option>
                        @if(!empty($medicamentos))
                            @foreach($medicamentos as $medicamento)
                                <option value="{{ $medicamento->id }}">{{ $medicamento->nome ?? 'Medicamento ' . $medicamento->id }}</option>
                            @endforeach
                        @else
                            <option value="" disabled>Sem medicamentos disponíveis</option>
                        @endif
                    </select>
                </div>
                <div>
                    <label>Posologia</label><br>
                    <input type="text" data-name="medicamentos[INDEX][posologia]" placeholder="Posologia">
                </div>
                <div>
                    <label>Quantidade</label><br>
                    <input type="number" data-name="medicamentos[INDEX][quantidade]" value="1" min="1" style="width:5rem;">
                </div>
                <div>
                    <button type="button" class="remove-med" style="margin-top:1.75rem;">Remover</button>
                </div>
            </div>
        </div>

        <button type="submit">{{ $isEditing ? 'Atualizar Receita' : 'Cadastrar Receita' }}</button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const medicamentoList = document.getElementById('medicamento-list');
            const templateWrapper = document.getElementById('medicamento-row-template');
            const addButton = document.getElementById('add-medicamento');

            function updateIndexes() {
                medicamentoList.querySelectorAll('.medicamento-row').forEach((row, index) => {
                    row.querySelectorAll('[data-name]').forEach((input) => {
                        input.name = input.getAttribute('data-name').replace('INDEX', index);
                    });
                });
            }

            function addMedicamentoRow() {
                const clone = templateWrapper.firstElementChild.cloneNode(true);
                clone.style.display = 'flex';
                attachRemoveHandler(clone);
                medicamentoList.appendChild(clone);
                updateIndexes();
            }

            function attachRemoveHandler(row) {
                const removeButton = row.querySelector('.remove-med');
                if (removeButton) {
                    removeButton.addEventListener('click', function () {
                        if (medicamentoList.querySelectorAll('.medicamento-row').length > 1) {
                            row.remove();
                            updateIndexes();
                        }
                    });
                }
            }

            medicamentoList.querySelectorAll('.medicamento-row').forEach(attachRemoveHandler);
            addButton.addEventListener('click', addMedicamentoRow);
            updateIndexes();
        });
    </script>
</body>
</html>