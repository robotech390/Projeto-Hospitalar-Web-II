import React, { useState, useEffect } from 'react';

const EXAM_TYPES = ['Sangue', 'Raio-X', 'Urina', 'Outro'];

export default function ExamFormModal({ open, onClose, onSave, exam }) {
  const [form, setForm] = useState({ name: '', type: 'Sangue', price: '', preparation: '' });

  useEffect(() => {
    if (exam) setForm(exam);
    else setForm({ name: '', type: 'Sangue', price: '', preparation: '' });
  }, [exam, open]);

  if (!open) return null;

  const handleChange = (e) => {
    const { name, value } = e.target;
    setForm({ ...form, [name]: value });
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    onSave({ ...form, price: parseFloat(form.price) });
  };

  return (
    <div className="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
      <div className="bg-white rounded-2xl shadow-lg p-8 w-full max-w-md">
        <h2 className="text-xl font-bold mb-4">{exam ? 'Editar Exame' : 'Novo Exame'}</h2>
        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="block mb-1 font-semibold">Nome</label>
            <input name="name" value={form.name} onChange={handleChange} required className="w-full border rounded px-3 py-2" />
          </div>
          <div>
            <label className="block mb-1 font-semibold">Tipo</label>
            <select name="type" value={form.type} onChange={handleChange} className="w-full border rounded px-3 py-2">
              {EXAM_TYPES.map(type => <option key={type} value={type}>{type}</option>)}
            </select>
          </div>
          <div>
            <label className="block mb-1 font-semibold">Preço (R$)</label>
            <input name="price" type="number" min="0" step="0.01" value={form.price} onChange={handleChange} required className="w-full border rounded px-3 py-2" />
          </div>
          <div>
            <label className="block mb-1 font-semibold">Instruções de preparo</label>
            <textarea name="preparation" value={form.preparation} onChange={handleChange} rows={3} className="w-full border rounded px-3 py-2 resize-y" />
          </div>
          <div className="flex justify-end gap-2 mt-4">
            <button type="button" onClick={onClose} className="px-4 py-2 rounded bg-gray-200">Cancelar</button>
            <button type="submit" className="px-4 py-2 rounded bg-[#00767F] text-white">Salvar</button>
          </div>
        </form>
      </div>
    </div>
  );
}
