import { useState, useEffect } from "react";
import type { ReactNode } from "react";
import { X, CheckCircle, Info, AlertCircle } from "lucide-react";

// 1️⃣ Freeze keys & values
const typeStyles = {
  success: {
    bg: "bg-green-100",
    text: "text-green-800",
    border: "border-green-300",
    icon: <CheckCircle className="h-5 w-5 text-green-600" />,
  },
  error: {
    bg: "bg-red-100",
    text: "text-red-800",
    border: "border-red-300",
    icon: <AlertCircle className="h-5 w-5 text-red-600" />,
  },
  info: {
    bg: "bg-blue-100",
    text: "text-blue-800",
    border: "border-blue-300",
    icon: <Info className="h-5 w-5 text-blue-600" />,
  },
} as const;

// 2️⃣ Build a type from the keys
type FlashType = keyof typeof typeStyles;

// 3️⃣ Props
interface FlashMessageProps {
  type?: FlashType;          // "success" | "error" | "info"
  message?: string;
  showIcon?: boolean;
  dismissible?: boolean;
  customIcon?: ReactNode;
  onDismiss?: () => void;
  duration?: number;          // auto-hide in ms
}

// 4️⃣ Component
export default function FlashMessage({
  type = "info",
  message = "Default message",
  showIcon = true,
  dismissible = true,
  customIcon = null,
  onDismiss = () => {},
  duration = 3000,            // default 3 seconds
}: FlashMessageProps) {
  const [visible, setVisible] = useState(true);

  // ✅ Auto-hide effect
  useEffect(() => {
    if (!duration) return;
    const timer = setTimeout(() => {
      setVisible(false);
      onDismiss();
    }, duration);
    return () => clearTimeout(timer);
  }, [duration, onDismiss]);

  if (!visible) return null;

  const styles = typeStyles[type];

  const handleClose = () => {
    setVisible(false);
    onDismiss();
  };

  return (
    <div
      className={`flex items-center gap-3 p-4 mb-4 border rounded-lg ${styles.bg} ${styles.text} ${styles.border}`}
      role="alert"
    >
      {showIcon && (customIcon || styles.icon)}

      <div className="flex-1 text-sm">{message}</div>

      {dismissible && (
        <button
          type="button"
          onClick={handleClose}
          className="ml-auto inline-flex items-center justify-center rounded-full p-1 hover:bg-black/10 focus:outline-none"
        >
          <X className="h-4 w-4" />
          <span className="sr-only">Close</span>
        </button>
      )}
    </div>
  );
}
