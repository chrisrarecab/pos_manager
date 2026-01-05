import React, { useRef, useState, useEffect } from "react";
import { Loader2 } from "lucide-react";
import Modal from "@/Components/Modal";
import MultiStepForm from "@/Components/MultiStepForm";
import type { ModalProps, MultiStepFormRef } from "@/types/CommonProps";
import type { SettingType, TabType } from "@/types/Settings/TerminalConfigType";

interface CopyModalProps extends ModalProps {
	data: Array<any>;
	onTerminalsChange?: (source: any, target: any) => void;
	sourceTerminalSettings?: SettingType[];
	showTabs?: TabType[] | null;
	onSave: (action: string, data?: any) => void;
	loading?: boolean;
	listOfSourceTerminal: any[]; 
}

const CopyModal: React.FC<CopyModalProps> = ({
	isModalOpen,
	onClose,
	title = "Copy Modal",
	data,
	sizeClass = "max-w-md",
	Icon,
	onTerminalsChange,
	sourceTerminalSettings = [],
	showTabs = null,
	onSave,
	loading = false, 
	listOfSourceTerminal = []
}) => {

	const multiStepRef = useRef<MultiStepFormRef>(null);
	const stepLabels = ['Choose source and target terminals', 'Review new POS settings', 'Save new POS settings'];
	const [currentStep, setCurrentStep] = useState(1);

	const handleStepUpdate = () => {
		const step = multiStepRef.current?.getCurrentStep() ?? 1;
		setCurrentStep(step);
	};

	const [sourceTerminalId, setSourceTerminalId] = useState<string | number>("");
	const [targetTerminalId, setTargetTerminalId] = useState<string | number>("");

	const sourceTerminal = data?.find(t => t.terminalId === sourceTerminalId);
	const targetTerminal = data?.find(t => t.terminalId === targetTerminalId);

	const terminalsWithSettings = data.filter(t => listOfSourceTerminal.includes(t.terminalId));

	const [expandedTab, setExpandedTab] = useState<string | null>(null);

	const groupedSettings = sourceTerminalSettings.reduce(
		(acc: Record<string, SettingType[]>, setting) => {
			const tabId = String(setting.setting_tab_id);
			if (!acc[tabId]) {
				acc[tabId] = [];
			}
			
			acc[tabId].push(setting);
			return acc;
		},
		{}
	);

	const resetModal = () => { 
		setSourceTerminalId(""); 
		setTargetTerminalId(""); 
		setExpandedTab(null);
		setCurrentStep(1); 
	};

	useEffect(() => {
		if (!isModalOpen) {
			resetModal();
		}
	}, [isModalOpen]);

	useEffect(() => {
		if (sourceTerminalId && targetTerminalId && onTerminalsChange) {
			onTerminalsChange(sourceTerminal, targetTerminal);
		}
	}, [sourceTerminalId, targetTerminalId]);

	/**
	 * Input Validation and disble next button
	 */
	
	useEffect(() => {
		if (!multiStepRef.current) return;

		if (!sourceTerminalId || !targetTerminalId || sourceTerminalId == targetTerminalId) {
			multiStepRef.current?.disableNextStep(true);
		} 
		else {
			multiStepRef.current?.disableNextStep(false);
		}
	});

	const [errMessage, setErrMessage] = useState("");
	const handleValidation =() => {
		if (!sourceTerminalId) {
			setErrMessage('Please select a source terminal before proceeding'); 
			return false;
		} 

		if (!targetTerminalId) {
			setErrMessage('Please select a target terminal before proceeding');
			return false;
		}

		setTimeout(handleStepUpdate, 0);
		return true;
	}

	return (
		<Modal
			isModalOpen={isModalOpen}
			onClose={onClose}
			title={title}
			Icon={Icon}
			sizeClass={sizeClass}
			footer={
				currentStep === stepLabels.length && (
					<div className="flex gap-2 justify-end">
						<button className="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300"
							onClick={onClose}
						>
							Close
						</button>
						<button className="px-4 py-2 rounded text-white bg-[var(--button-success-active)] hover:bg-[var(--button-success-hover)]"
							onClick={() =>
								onSave("copySettings", {
									sourceTerminal,
									targetTerminal,
									settings: sourceTerminalSettings,
								})
							}
						>
						{loading ? (
							<>
							<Loader2 className="h-4 w-4 mr-2 animate-spin" />
							</>
						) : (
							<>
							Proceed
							</>
						)}
						</button>
					</div>
				)
			}
		>
		{data?.length ? (
			<MultiStepForm
				ref={multiStepRef}
				stepLabels={stepLabels}
				onStepNext={() => handleValidation()}
				onStepBack={() => setTimeout(handleStepUpdate, 0)}
				onFinished={() => setTimeout(handleStepUpdate, 0)}
			>
			{/* Step 1: Select Terminals */}
			<div className="step1 ">
				<label className="block font-medium mb-1">Copy Settings From:</label>
				<div className="relative">
					<select
						className="block w-full h-9 px-2 py-1 border border-gray-300 rounded-md appearance-none pr-8"
						value={sourceTerminalId}
						onChange={e => setSourceTerminalId(Number(e.target.value))}
					>
						<option>Select source terminal</option>
						{terminalsWithSettings.map((terminal: any) => (
							<option key={terminal.terminalId} value={terminal.terminalId}>
								{`Branch ${terminal.branchId}: ${terminal.branchName} - Terminal# ${terminal.terminalNo}`}
							</option>
						))}
					</select>

					{errMessage && (
						<p className="text-red-500 text-sm mt-2">{errMessage}</p>
					)}

					<div className="pointer-events-none absolute inset-y-0 right-2 flex items-center">
						<svg className="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" >
							<path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
						</svg>
					</div>
				</div>

    			<label className="block font-medium mt-2 mb-1">Copy Settings To:</label>
				<div className="relative">
					<select
					className="block w-full h-9 px-2 py-1 border border-gray-300 rounded-md appearance-none pr-8"
					value={targetTerminalId}
					onChange={e => setTargetTerminalId(Number(e.target.value))}
					>
					<option>Select target terminal</option>
					{data.filter((t: any) => t.terminalId !== sourceTerminalId)
						.map((terminal: any) => (
						<option key={terminal.terminalId} value={terminal.terminalId}>
							{`Branch ${terminal.branchId}: ${terminal.branchName} - Terminal# ${terminal.terminalNo}`}
						</option>
						))}
					</select>

					{errMessage && (
						<p className="text-red-500 text-sm mt-2">{errMessage}</p>
					)}
					<div className="pointer-events-none absolute inset-y-0 right-2 flex items-center">
						<svg className="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" >
							<path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
						</svg>
					</div>
				</div>
			</div>
			<div className="step2 mt-4">
				<p className="mb-3">
					<em>
						{sourceTerminal ? (
							<>
							These are the updated settings for
							<strong>
								{" "}
								Branch {sourceTerminal.branchId}: {sourceTerminal.branchName} - Terminal #{sourceTerminal.terminalNo}
							</strong>
							. Please review them carefully.
							</>
						) : (
							"No source terminal"
						)}
				
					</em>
				</p>
				{ Object.keys(groupedSettings).map((tabId) => {
					const tabName = showTabs?.find((t) => String(t.id) === tabId)?.name ?? `Tab ${tabId}`;
					return (
						<div key={tabId} className="border border-gray-200 rounded mb-2">
							<button type="button" className="w-full text-left px-4 py-2 bg-gray-100 hover:bg-gray-200 font-medium"
								onClick={() =>
									setExpandedTab(expandedTab === tabId ? null : tabId)
								}
							>
								{tabName}
							</button>

							{expandedTab === tabId && ( 
							<div className="p-4 border-t border-gray-200">
								<table className="table-auto w-full border-collapse border border-gray-200">
								<thead>
									<tr>
									<th className="border border-gray-300 p-1 ">Setting</th>
									<th className="border border-gray-300 p-1 ">Value</th>
									</tr>
								</thead>
								<tbody>
									{(groupedSettings[tabId] ?? []).map((setting) => (
										<tr key={setting.id}>
											<td className="border border-gray-300 p-1">{setting.name}</td>
											<td className="border border-gray-300 p-1">{String(setting.value)}</td>
										</tr>
										))}

								</tbody>
								</table>
							</div>
							)}
						</div>
					);
				})}
			</div>

			{/* Step 3: Review */}
			<div className="step3 mt-4  w-xl ">
				<div className="d-flex mt-5">
					{sourceTerminal && targetTerminal && (
						<p className="mt-3 text-center text-sm">
						You're about to apply new settings to {" "}
						<span className="font-mono text-[0.85em] bg-gray-100 rounded-md px-2 py-[2px] inline-flex items-center gap-[6px] border border-gray-300 mb-2">
							Branch {targetTerminal.branchId}: {targetTerminal.branchName} - Terminal #{targetTerminal.terminalNo}
						</span>
						, copied from{" "}
						<span className="font-mono text-[0.85em] bg-gray-100 rounded-md px-2 py-[2px] inline-flex items-center gap-[6px] border border-gray-300">
							Branch {sourceTerminal.branchId}: {sourceTerminal.branchName} - Terminal #{sourceTerminal.terminalNo}
						</span>

						. Click <strong>Proceed</strong> to continue.
						</p>
					)}
					</div>
			</div>
			</MultiStepForm>
		) : (
			<p>No terminals available.</p>
		)}
		</Modal>
	);
};

export default CopyModal;
