import * as React from "react";
import { cva } from "class-variance-authority";
import clsx from "clsx";
import { twMerge } from "tailwind-merge";

function cn(...inputs) {
  return twMerge(clsx(inputs));
}

const labelVariants = cva("text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70");

const Label = React.forwardRef(function Label({ className, ...props }, ref) {
  return (
    <label ref={ref} className={cn(labelVariants(), className)} {...props} />
  );
});
Label.displayName = "Label";

export default Label;
