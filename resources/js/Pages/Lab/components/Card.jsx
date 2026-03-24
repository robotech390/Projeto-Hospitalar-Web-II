import * as React from "react";
import clsx from "clsx";
import { twMerge } from "tailwind-merge";

function cn(...inputs) {
  return twMerge(clsx(inputs));
}

const Card = React.forwardRef(function Card({ className, ...props }, ref) {
  return (
    <div ref={ref} className={cn("rounded-lg border bg-white text-gray-900 shadow-sm", className)} {...props} />
  );
});
Card.displayName = "Card";

const CardContent = React.forwardRef(function CardContent({ className, ...props }, ref) {
  return <div ref={ref} className={cn("p-6 pt-0", className)} {...props} />;
});
CardContent.displayName = "CardContent";

export { Card, CardContent };
