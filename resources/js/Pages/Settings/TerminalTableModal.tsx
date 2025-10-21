import React from "react";
import { Loader2 } from "lucide-react";
import Modal from "@/Components/Modal";
import type { ModalProps } from "@/types/CommonProps";

interface TerminalTableModalProps extends ModalProps {
  data: Array<any>;
  selectedRow?: any | null; // add this
  modifiedSettings: Record<string, any>;
  setModifiedSettings: React.Dispatch<React.SetStateAction<Record<string, any>>>;
  loading?: boolean;
  onSave?: (action: string, data?: any) => Promise<void>;
}

const TerminalTableModal: React.FC<TerminalTableModalProps> = ({
  isModalOpen,
  onClose,
  title,
  Icon,
  sizeClass = "max-w-3xl",
  selectedRow,
  modifiedSettings,
  setModifiedSettings,
  loading = false,
  onSave,
}) => {
  if (!isModalOpen || !selectedRow) return null;

  return (
    <Modal
      isModalOpen={isModalOpen}
      onClose={onClose}
      title={title || "Terminal Connection"}
      Icon={Icon}
      sizeClass={sizeClass}
      footer={
        <div className="flex gap-2 justify-end">
          <button
            onClick={onClose}
            className="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300"
          >
            Close
          </button>
          <button
            onClick={() => onSave?.("saveTerminalConnection")}

            className="px-4 py-2 rounded text-white bg-[var(--button-success-active)] hover:bg-[var(--button-success-hover)]"
          >
            {loading ? <Loader2 className="h-4 w-4 mr-2 animate-spin" /> : "Proceed"}
          </button>
        </div>
      }
    >
	<div className="grid grid-cols-1 md:grid-cols-2 gap-4">
	{Object.entries(selectedRow).map(([key, value]) => {
		if (typeof value === "object") return null;
		if (key === "setting_id") return null;

		return (
		<div key={key} className="flex flex-col">
			<label className="text-sm font-medium text-gray-700 mb-1">{key}</label>
			<input
			type="text"
			className="w-70 px-3 py-2 border border-gray-300 rounded-md"
			value={modifiedSettings[`${selectedRow.terminalno}-${key}`] ?? value}
			onChange={(e) =>
				setModifiedSettings((prev) => ({
				...prev,
				[`${selectedRow.terminalno}-${key}`]: e.target.value,
				}))
			}
			/>
		</div>
		);
	})}
	</div>

    </Modal>
  );
};


export default TerminalTableModal;
