import { cn } from "@/lib/utils";

type ExamStatus = "Pendente" | "Coletado" | "Em Análise" | "Concluído";

const statusMap: Record<ExamStatus, string> = {
  Pendente: "status-pendente",
  Coletado: "status-coletado",
  "Em Análise": "status-analise",
  Concluído: "status-concluido",
};

const StatusBadge = ({ status }: { status: ExamStatus }) => (
  <span
    className={cn(
      "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold",
      statusMap[status]
    )}
  >
    {status}
  </span>
);

export default StatusBadge;
export type { ExamStatus };
