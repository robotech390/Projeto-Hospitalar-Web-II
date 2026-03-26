import React from "react";
import { ArrowUpRight, ArrowDownRight } from "lucide-react";

function formatTrend(value, percentual) {
  if (value > 0) {
    return percentual ? `+${value}%` : `+${value}`;
  } else if (value < 0) {
    return percentual ? `${value}%` : `${value}`;
  } else {
    return "-";
  }
}

function getIcon(value) {
  if (value > 0) {
    return <ArrowUpRight className="w-4 h-4" />;
  } else if (value < 0) {
    return <ArrowDownRight className="w-4 h-4" />;
  } else {
    return null;
  }
}

function getColor(value) {
  if (value > 0) {
    return "text-green-600";
  } else if (value < 0) {
    return "text-red-600";
  } else {
    return "text-gray-500";
  }
}

export default function Trend({ percentual, value }) {
  return (
    <span className={`inline-flex items-center gap-1 text-xs font-medium ${getColor(value)}`}>
      {getIcon(value)}
      {formatTrend(value, percentual)}
    </span>
  );
}
