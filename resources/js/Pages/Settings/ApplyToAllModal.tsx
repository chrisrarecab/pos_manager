import React, { useRef, useState, useEffect } from "react";
import Modal from "@/Components/Modal";
import MultiStepForm from "@/Components/MultiStepForm";
import type { ModalProps, MultiStepFormRef } from "@/types/CommonProps";
import type { SettingType, TabType } from "@/types/Settings/TerminalConfigType";
import { Loader2 } from "lucide-react";

interface CopyModalProps extends ModalProps {
	data: Array<any>;
	onTerminalsChange?: (source: any, target: any) => void;
	sourceTerminalSettings?: SettingType[];
	showTabs?: TabType[] | null;
	onSave: (action: string, data?: any) => void;
	loading?: boolean;
	listOfSourceTerminal: any[]; 
}

const ApplyToAllModal: React.FC<CopyModalProps> = ({
	isModalOpen,
	onClose,
	title = "Apply-to-All Modal",
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

	const [sourceTerminalId, setSourceTerminalId] = useState<string | number>("");
	const [targetTerminalId, setTargetTerminalId] = useState<string | number>("");

	const [selectedOption, setSelectedOption] = useState<string>("Network");
	const [selectedTargetBranch, setSelectedTargetBranch] = useState<string | number>("");

	const handleStepUpdate = () => {
		const step = multiStepRef.current?.getCurrentStep() ?? 1;
		setCurrentStep(step);
	};

	const sourceTerminal = data?.find(t => t.terminalId == sourceTerminalId);
	const targetTerminal = data?.find(t => t.terminalId == targetTerminalId);
	const [expandedTab, setExpandedTab] = useState<string | null>(null);

	const terminalsWithSettings = data.filter(t => listOfSourceTerminal.includes(t.terminalId));

	const groupedSettings = sourceTerminalSettings.reduce(
		(acc: Record<string, SettingType[]>, setting) => {
			const tabId = String(setting.setting_tab_id);
			if (!acc[tabId]) acc[tabId] = [];
			acc[tabId].push(setting);
			return acc;
		}, {}
	);
	
	const filteredBranches = React.useMemo(() => {
		return data.reduce((acc: any[], t: any) => {
			if (!acc.find((b) => b.branchId === t.branchId)) {
			acc.push({ branchId: t.branchId, branchName: t.branchName });
			}
			return acc;
		}, []);
	}, [data]);

	const resetModal = () => { 
		setSourceTerminalId(""); 
		setTargetTerminalId(""); 
		setSelectedOption("Network"); 
		setSelectedTargetBranch(""); 
		setExpandedTab(null); 
		setCurrentStep(1); 
	};

	useEffect(() => {
		if (!isModalOpen) {
			resetModal();
		}
	}, [isModalOpen]);

	useEffect(() => {
		if (sourceTerminalId && onTerminalsChange) {
			onTerminalsChange(sourceTerminal, targetTerminal);
		}
	}, [sourceTerminalId]);

	useEffect(() => {
		if (!multiStepRef.current) return;
	
		if(selectedOption == 'Network' && sourceTerminalId == '') {
			multiStepRef.current?.disableNextStep(true);
		}
		else if (selectedOption == 'Branch' && sourceTerminalId == '' || selectedTargetBranch == '') {
			console.log('This from branch', selectedOption);
			multiStepRef.current?.disableNextStep(true);
		}
		else {
			console.log('This from else', selectedOption);
			multiStepRef.current?.disableNextStep(false);
		}
	});

	return (
		<Modal
			isModalOpen={isModalOpen}
			onClose={onClose}
			title={title}
			Icon={Icon}
			sizeClass={sizeClass}
			footer={
			currentStep === stepLabels.length
			? [<div key="footer" className="flex gap-2 justify-end">
					<button className="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300"
						onClick={onClose}
					>
						Close
					</button>
					<button className="px-4 py-2 rounded text-white bg-[var(--button-success-active)] hover:bg-[var(--button-success-hover)]"
						onClick={() => {
							onSave("applyToAllSettings", {
								selectedOption,
								selectedTargetBranch,
								sourceTerminal,
								settings: sourceTerminalSettings ?? [],
							});
						}}
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
				</div>,
			] : undefined
			}
		>
		{data?.length ? (
			<MultiStepForm
				ref={multiStepRef}
				stepLabels={stepLabels}
				onStepNext={() => setTimeout(handleStepUpdate, 0)}
				onStepBack={() => setTimeout(handleStepUpdate, 0)}
				onFinished={() => setTimeout(handleStepUpdate, 0)}
			>
				{/* Step 1: Choose Copy Mode */}
				<div className="step1">
					<p className="text-sm font-medium text-gray-700 mb-4 mt-3 ">
						Copy Terminal Settings By:
					</p>

					{/* Tab Switch */}
					<div className="flex justify-center mb-6">
						<div className="inline-flex rounded-xl border border-gray-300 bg-gray-50 p-1 shadow-sm">
						{["Network", "Branch"].map((option) => (
							<button
								key={option}
								type="button"
								onClick={() => setSelectedOption(option)}
								className={`w-70 py-2 text-sm font-medium rounded-lg transition-all duration-200 ${
									selectedOption === option
									? "bg-[var(--color-accent-active)] text-white shadow"
									: "text-gray-600 hover:text-gray-800 hover:bg-gray-100"
								}`}
							>
							{option}
							</button>
						))}
						</div>
					</div>

					{/* Network Selection */}
					{selectedOption === "Network" && (
						<div className="flex justify-center">
							<div className="w-full max-w-md space-y-3">
								<label className="block text-sm font-semibold text-gray-700">
									Copy Settings From:
								</label>
								<select className="w-full border border-gray-300 text-gray-800 rounded-lg h-10 px-3 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-[var(--primary)] transition duration-200 ease-in-out"
									value={sourceTerminalId}
									onChange={(e) => {
										setSourceTerminalId(e.target.value);
									}}
								>
									<option>Select a source terminal</option>
									{terminalsWithSettings.map((terminal: any, index: number) => (
										<option key={index} value={terminal.terminalId}>
										{`B${terminal.branchId}: ${terminal.branchName} - Terminal #${terminal.terminalNo}`}
										</option>
									))}
									</select>
							</div>
						</div>
					)}

					{/* Branch Selection */}
					{selectedOption === "Branch" && (
						<div className="flex justify-center">
							<div className="w-full max-w-md space-y-3">
								<label className="block text-sm font-semibold text-gray-700">
									Copy From:
								</label>
								<select className="w-full border border-gray-300 text-gray-800 rounded-lg h-10 px-3 focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-[var(--primary)] transition"
									value={sourceTerminalId}
									onChange={(e) => {
										setSourceTerminalId(e.target.value);
									}}
								>
									<option>Select a source terminal</option>
									{terminalsWithSettings.map((terminal: any, index: number) => (
										<option key={index} value={terminal.terminalId}>
										{`B${terminal.branchId}: ${terminal.branchName} - Terminal #${terminal.terminalNo}`}
										</option>
									))}
								</select>

								<label className="block text-sm font-semibold text-gray-700">
									Copy To:
								</label>
								<select className="w-full border border-gray-300 text-gray-800 rounded-lg h-10 px-3 focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-[var(--primary)] transition"
									value={selectedTargetBranch}
									onChange={(e) => {
										setSelectedTargetBranch(e.target.value);
									}}
								>
									<option>Select a branch</option>
									{filteredBranches.map((branch, index) => (
										<option key={index} value={branch.branchId}>
										{`B${branch.branchId}: ${branch.branchName}`}
										</option>
									))}
								</select>
							</div>
						</div>
					)}
				</div>
				
				{/* Step 2: Review */}
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
					{Object.keys(groupedSettings).map((tabId) => {
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

				{/* Step 3: Confirm  */}
				<div className="step3 mt-4  w-xl ">
					<div className="d-flex mt-5">
						{sourceTerminal && (
							<p className="mt-3 text-center text-sm">
								You're about to apply settings from{" "}
								<span className="font-mono text-[0.85em] bg-gray-100 rounded-md px-2 py-[2px] inline-flex items-center gap-[6px] border border-gray-300">
								Branch {sourceTerminal.branchId ?? "N/A"}: {sourceTerminal.branchName ?? "N/A"} - Terminal #{sourceTerminal.terminalNo ?? "N/A"}
								</span>{" "}
								to{" "}
								{selectedOption === "Network" ? (
								<span className="font-mono text-[0.85em] bg-gray-100 rounded-md px-2 py-[2px] inline-flex items-center gap-[6px] border border-gray-300">
									all terminals in the same network
								</span>
								) : selectedOption === "Branch" && selectedTargetBranch ? (
								<span className="font-mono text-[0.85em] bg-gray-100 rounded-md px-2 py-[2px] inline-flex items-center gap-[6px] border border-gray-300">
									all terminals under Branch {selectedTargetBranch}
								</span>
								) : (
								<span className="font-mono text-[0.85em] bg-gray-100 rounded-md px-2 py-[2px] inline-flex items-center gap-[6px] border border-gray-300">
									(please select a target option)
								</span>
								)}
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

export default ApplyToAllModal;
