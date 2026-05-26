import { useState, useMemo } from 'react'
import { useQuery } from '@tanstack/react-query'
import { ChevronLeft, ChevronRight, CalendarClock, Calendar as CalendarIcon } from 'lucide-react'
import { meuPerfilApi } from '../api/meuPerfil'
import { formatarData } from '../utils/datas'

const NOMES_DIAS    = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb']
const NOMES_MESES   = [
  'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
  'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
]

/**
 * Tela "Minha Agenda" do médico.
 * Mostra um calendário mensal com os plantões marcados e a lista dos próximos.
 */
export default function MinhaAgenda() {
  const hoje = new Date()
  const [mesAtual, setMesAtual] = useState({ ano: hoje.getFullYear(), mes: hoje.getMonth() })

  const primeiroDoMes  = new Date(mesAtual.ano, mesAtual.mes, 1)
  const ultimoDoMes    = new Date(mesAtual.ano, mesAtual.mes + 1, 0)
  const inicioPeriodo  = primeiroDoMes.toISOString().slice(0, 10)
  const fimPeriodo     = ultimoDoMes.toISOString().slice(0, 10)

  const { data: plantoes = [], isLoading } = useQuery({
    queryKey: ['minha-agenda', inicioPeriodo, fimPeriodo],
    queryFn:  () => meuPerfilApi.minhaAgenda({
      data_inicio: inicioPeriodo,
      data_fim:    fimPeriodo,
    }).then((r) => r.data),
  })

  // Indexa os plantões por data (YYYY-MM-DD) para lookup rápido
  const plantoesPorData = useMemo(() => {
    const map = {}
    plantoes.forEach((p) => {
      const chave = p.data_disponibilidade
      if (!map[chave]) map[chave] = []
      map[chave].push(p)
    })
    return map
  }, [plantoes])

  // Gera a grade do calendário (6 semanas)
  const diasDaGrade = useMemo(() => {
    const dias = []
    const inicio = new Date(primeiroDoMes)
    inicio.setDate(inicio.getDate() - inicio.getDay()) // Volta até o domingo anterior

    for (let i = 0; i < 42; i++) {
      const d = new Date(inicio)
      d.setDate(inicio.getDate() + i)
      dias.push(d)
    }
    return dias
  }, [primeiroDoMes])

  const irParaMes = (delta) => {
    setMesAtual((atual) => {
      const novo = new Date(atual.ano, atual.mes + delta, 1)
      return { ano: novo.getFullYear(), mes: novo.getMonth() }
    })
  }

  const dataIso = (d) => d.toISOString().slice(0, 10)
  const ehHoje = (d) => dataIso(d) === dataIso(hoje)

  const proximos = plantoes
    .filter((p) => p.data_disponibilidade >= dataIso(hoje))
    .slice(0, 8)

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-xl font-semibold text-slate-800">Minha agenda</h1>
        <p className="text-sm text-slate-400 mt-0.5">Seus plantões e turnos cadastrados</p>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {/* Calendário */}
        <div className="card lg:col-span-2">
          {/* Cabeçalho do calendário */}
          <div className="flex items-center justify-between mb-5">
            <h2 className="text-base font-semibold text-slate-800">
              {NOMES_MESES[mesAtual.mes]} {mesAtual.ano}
            </h2>
            <div className="flex items-center gap-1">
              <button onClick={() => irParaMes(-1)}
                className="p-1.5 rounded-lg hover:bg-slate-100 text-slate-500 transition-colors">
                <ChevronLeft size={16} />
              </button>
              <button onClick={() => setMesAtual({ ano: hoje.getFullYear(), mes: hoje.getMonth() })}
                className="px-3 py-1 text-xs rounded-lg hover:bg-slate-100 text-slate-600 transition-colors">
                Hoje
              </button>
              <button onClick={() => irParaMes(1)}
                className="p-1.5 rounded-lg hover:bg-slate-100 text-slate-500 transition-colors">
                <ChevronRight size={16} />
              </button>
            </div>
          </div>

          {/* Dias da semana */}
          <div className="grid grid-cols-7 mb-1">
            {NOMES_DIAS.map((d) => (
              <div key={d} className="text-center text-[11px] font-medium text-slate-400 uppercase tracking-wider py-2">
                {d}
              </div>
            ))}
          </div>

          {/* Grade do mês */}
          <div className="grid grid-cols-7 gap-1">
            {diasDaGrade.map((dia, i) => {
              const noMesAtual    = dia.getMonth() === mesAtual.mes
              const plantoesNoDia = plantoesPorData[dataIso(dia)] || []
              const temPlantao    = plantoesNoDia.length > 0

              return (
                <div key={i}
                  className={`relative min-h-[64px] rounded-lg p-1.5 border ${
                    !noMesAtual
                      ? 'border-transparent text-slate-300'
                      : ehHoje(dia)
                        ? 'border-brand bg-brand/5'
                        : temPlantao
                          ? 'border-teal-100 bg-teal-50/40 hover:bg-teal-50'
                          : 'border-slate-100 hover:bg-slate-50'
                  } transition-colors`}
                >
                  <p className={`text-xs font-medium ${
                    ehHoje(dia) ? 'text-brand-dark' : noMesAtual ? 'text-slate-600' : 'text-slate-300'
                  }`}>
                    {dia.getDate()}
                  </p>

                  {temPlantao && noMesAtual && (
                    <div className="mt-1 space-y-0.5">
                      {plantoesNoDia.slice(0, 2).map((p) => (
                        <div key={p.id}
                          className={`text-[9px] px-1 py-0.5 rounded truncate ${
                            p.plantao
                              ? 'bg-purple-100 text-purple-700'
                              : 'bg-teal-100 text-teal-700'
                          }`}>
                          {p.hora_inicio?.slice(0, 5)}
                        </div>
                      ))}
                      {plantoesNoDia.length > 2 && (
                        <p className="text-[9px] text-slate-400 px-1">
                          +{plantoesNoDia.length - 2}
                        </p>
                      )}
                    </div>
                  )}
                </div>
              )
            })}
          </div>

          {/* Legenda */}
          <div className="flex items-center gap-4 mt-4 pt-3 border-t border-slate-100">
            <div className="flex items-center gap-1.5">
              <div className="w-3 h-3 rounded bg-teal-100" />
              <span className="text-xs text-slate-500">Turno</span>
            </div>
            <div className="flex items-center gap-1.5">
              <div className="w-3 h-3 rounded bg-purple-100" />
              <span className="text-xs text-slate-500">Plantão</span>
            </div>
            <div className="flex items-center gap-1.5">
              <div className="w-3 h-3 rounded border border-brand bg-brand/5" />
              <span className="text-xs text-slate-500">Hoje</span>
            </div>
          </div>
        </div>

        {/* Lista de próximos plantões */}
        <div className="card">
          <div className="flex items-center gap-2 mb-4">
            <CalendarClock size={16} className="text-brand" />
            <p className="text-sm font-medium text-slate-700">Próximos plantões</p>
          </div>

          {isLoading ? (
            <p className="text-center py-6 text-slate-400 text-sm">Carregando...</p>
          ) : proximos.length === 0 ? (
            <div className="text-center py-8">
              <CalendarIcon size={28} className="mx-auto text-slate-200 mb-2" />
              <p className="text-slate-400 text-sm">Sem plantões futuros.</p>
            </div>
          ) : (
            <div className="space-y-2">
              {proximos.map((p) => (
                <div key={p.id}
                  className="flex items-center gap-3 p-2.5 rounded-lg bg-slate-50/60 border border-slate-100">
                  <div className={`w-9 h-9 rounded-lg flex flex-col items-center justify-center shrink-0 ${
                    p.plantao ? 'bg-purple-100 text-purple-700' : 'bg-teal-100 text-teal-700'
                  }`}>
                    <span className="text-[10px] font-medium uppercase leading-none">
                      {new Date(p.data_disponibilidade + 'T00:00:00')
                        .toLocaleDateString('pt-BR', { month: 'short' })
                        .replace('.', '')}
                    </span>
                    <span className="text-sm font-semibold leading-none mt-0.5">
                      {new Date(p.data_disponibilidade + 'T00:00:00').getDate()}
                    </span>
                  </div>
                  <div className="flex-1 min-w-0">
                    <p className="text-xs text-slate-500">{formatarData(p.data_disponibilidade)}</p>
                    <p className="text-sm font-medium text-slate-800">
                      {p.hora_inicio?.slice(0, 5)} — {p.hora_fim?.slice(0, 5)}
                    </p>
                  </div>
                  {p.plantao && (
                    <span className="text-[10px] px-1.5 py-0.5 rounded bg-purple-100 text-purple-700 font-medium shrink-0">
                      Plantão
                    </span>
                  )}
                </div>
              ))}
            </div>
          )}
        </div>
      </div>
    </div>
  )
}
