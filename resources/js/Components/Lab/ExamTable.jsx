import React from 'react';

export default function ExamTable({ exams, onEdit, onDelete }) {
  return (
    <table className="min-w-full bg-white rounded-xl shadow-sm">
      <thead>
        <tr>
          <th className="px-4 py-2 text-left">Tipo</th>
          <th className="px-4 py-2 text-left">Nome</th>
          <th className="px-4 py-2 text-left">Preço</th>
          <th className="px-4 py-2 text-left">Instruções</th>
          <th className="px-4 py-2 text-left">Ações</th>
        </tr>
      </thead>
      <tbody>
        {exams.map((exam) => (
          <tr key={exam.id} className="border-b last:border-b-0">
            <td className="px-4 py-2">{exam.type}</td>
            <td className="px-4 py-2">{exam.name}</td>
            <td className="px-4 py-2">{exam.price.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })}</td>
            <td className="px-4 py-2">{exam.preparation}</td>
            <td className="px-4 py-2 flex gap-2">
              <button className="text-blue-700 hover:underline" onClick={() => onEdit(exam)}>Editar</button>
              <button className="text-red-600 hover:underline" onClick={() => onDelete(exam.id)}>Excluir</button>
            </td>
          </tr>
        ))}
      </tbody>
    </table>
  );
}
