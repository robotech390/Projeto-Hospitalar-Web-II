import React, { useState } from 'react';
import ResultTextEditor from '../../Components/Lab/ResultTextEditor';
import ResultFileUpload from '../../Components/Lab/ResultFileUpload';

export default function ResultEntryForm() {
  const [resultText, setResultText] = useState('');
  const [files, setFiles] = useState([]);

  const handleSave = () => {
    // Salvar resultado (mock)
    alert('Resultado salvo!');
  };

  return (
    <div className="p-6 bg-slate-50 min-h-screen">
      <div className="max-w-3xl mx-auto bg-white rounded-xl shadow-sm p-6">
        <h1 className="text-2xl font-bold mb-4">Lançamento de Resultado</h1>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label className="block font-semibold mb-2">Laudo Médico</label>
            <ResultTextEditor value={resultText} onChange={setResultText} />
          </div>
          <div>
            <label className="block font-semibold mb-2">Anexos (PDF/Imagem)</label>
            <ResultFileUpload files={files} setFiles={setFiles} />
          </div>
        </div>
        <button
          className="mt-6 bg-[#00767F] text-white px-6 py-2 rounded-xl shadow-sm hover:bg-[#005a61]"
          onClick={handleSave}
        >
          Salvar Resultado
        </button>
      </div>
    </div>
  );
}
