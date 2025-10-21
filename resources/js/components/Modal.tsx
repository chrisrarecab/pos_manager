import React from "react";
import type { ReactNode } from "react";
import ReactDOM from "react-dom";
import type { ModalProps } from "@/types/CommonProps";

const Modal: React.FC<ModalProps> = ({
  isModalOpen,
  onClose,
  title,
  children,
  footer,
  sizeClass = "max-w-md",
  Icon,
}) => {
  if (!isModalOpen) return null;

  return ReactDOM.createPortal(
    <div className="fixed inset-0 z-50 flex items-start justify-center bg-black/50 overflow-y-auto">
      <div
        className={`bg-white rounded-lg shadow-lg w-full ${sizeClass} max-h-[90vh] flex flex-col mt-10`}
      >
        {/* Header */}
        {title && (
          <div className="flex justify-between items-center px-6 py-3 border-b border-gray-200">
            <div className="flex items-center gap-2">
              {Icon && <Icon className="h-5 w-5 text-gray-700" />}
              <h2 className="text-lg font-semibold">{title}</h2>
            </div>
            <button
              onClick={onClose}
              className="text-gray-500 hover:text-gray-800 text-2xl leading-none"
            >
              &times;
            </button>
          </div>
        )}

    {/* Body (scrollable only here) */}
    <div className="px-6 py-4 flex-1 overflow-y-auto">
      <div className="flex items-center justify-center min-h-full">
        {children}
      </div>
    </div>

        {/* Footer (fixed at bottom) */}
        {footer && (
          <div className="px-6 py-3 border-t border-gray-200 flex justify-end gap-2">
            {footer}
          </div>
        )}
      </div>
    </div>,
    document.body
  );
};

export default Modal;
