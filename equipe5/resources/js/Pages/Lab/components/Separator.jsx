import * as React from "react";
import * as SeparatorPrimitive from "@radix-ui/react-separator";

/**
 * Accessible Separator component using Radix primitive.
 * @param {"horizontal"|"vertical"} orientation - Direction of the separator.
 * @param {string} className - Additional classes.
 * @param {object} rest - Other props.
 */
const Separator = React.forwardRef(function Separator({ orientation = "horizontal", className = "", ...rest }, ref) {
  const base =
    orientation === "vertical"
      ? "w-px h-full"
      : "h-px w-full";
  return (
    <SeparatorPrimitive.Root
      ref={ref}
      decorative
      orientation={orientation}
      className={`shrink-0 bg-border ${base} ${className}`.trim()}
      {...rest}
    />
  );
});

export default Separator;
