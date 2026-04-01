import React from 'react';

export default function ResultTextEditor({ value, onChange }) {
  return (
    <textarea
      className="w-full border rounded px-3 py-2 min-h-[120px] resize-y"
      value={value}
      onChange={e => onChange(e.target.value)}
      placeholder="Digite o laudo médico aqui..."
    />
  );
}
