import React, { useRef } from 'react';

export default function ResultFileUpload({ files, setFiles }) {
  const inputRef = useRef();

  const handleDrop = (e) => {
    e.preventDefault();
    const droppedFiles = Array.from(e.dataTransfer.files).filter(file =>
      ['application/pdf', 'image/jpeg', 'image/png'].includes(file.type)
    );
    setFiles([...files, ...droppedFiles]);
  };

  const handleChange = (e) => {
    const selectedFiles = Array.from(e.target.files).filter(file =>
      ['application/pdf', 'image/jpeg', 'image/png'].includes(file.type)
    );
    setFiles([...files, ...selectedFiles]);
  };

  return (
    <div
      className="border-2 border-dashed border-slate-300 rounded-xl p-4 flex flex-col items-center justify-center cursor-pointer bg-slate-50 hover:bg-slate-100"
      onDrop={handleDrop}
      onDragOver={e => e.preventDefault()}
      onClick={() => inputRef.current.click()}
    >
      <svg xmlns="http://www.w3.org/2000/svg" className="h-8 w-8 text-slate-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M7 16v-4m0 0V8a4 4 0 018 0v4m-8 0h8" /></svg>
      <span className="text-slate-500 mb-2">Arraste e solte arquivos PDF ou Imagem aqui, ou clique para selecionar</span>
      <input
        ref={inputRef}
        type="file"
        accept="application/pdf,image/jpeg,image/png"
        multiple
        className="hidden"
        onChange={handleChange}
      />
      <div className="mt-2 w-full">
        {files.length > 0 && (
          <ul className="text-xs text-slate-700">
            {files.map((file, idx) => (
              <li key={idx}>{file.name}</li>
            ))}
          </ul>
        )}
      </div>
    </div>
  );
}
