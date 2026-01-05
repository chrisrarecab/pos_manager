import React, {useEffect, useState, useRef } from 'react';
import { useTypedPage } from '@/Config/useTypePage';
import type { TabType, SettingType } from '@/types/Settings/TerminalConfigType';
import axios from '@/Config/axios';
import {X, Table, UserRoundPen , TriangleAlert, Loader2 , SettingsIcon, Files, CopyCheck, Undo, Star, RefreshCw, SlidersVertical , SaveIcon, SearchIcon, TerminalIcon, AlertCircleIcon, InfoIcon, PlusIcon, XIcon } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { URL } from '@/Config/common'
import TabNavigation from '@/Pages/Settings/TabNavigation';
import Tooltip from '@/Components/Tooltip';
import TerminalTable from '@/Pages/Settings/TerminalTable';
import CopyModal from '@/Pages/Settings/CopyModal';
import ApplyToAllModal from '@/Pages/Settings/ApplyToAllModal';
import TerminalTableModal from '@/Pages/Settings/TerminalTableModal'; 
import FlashMessage from '@/Components/FlashMessage';
import { iconMap } from './TerminalConfigIcons';
import { useDebounce } from 'use-debounce';

const TerminalConfig = () => {
	/**
	 * Flash meesage / Toast Notification
	 */
	const [flash, setFlashMessage] = useState<{ message: string; type: 'success' | 'error' | 'info' } | null>(null);
	const [loading, setLoading] = useState(false);

  	const [openActions, setOpenActions] = useState(false);
	const actions = [
		{label: 'Copy to', Icon: Files, onClick: () => {setIsCopyModalOpen(true), setOpenActions(false)}},
		{label: 'Apply to all', Icon: CopyCheck,  onClick: () => {setIsApplyToAllModalOpen(true), setOpenActions(false)}},
		{label: 'Revert', Icon: Undo, onClick: () => alert("Apply to all")},
		{label: 'Default', Icon: Star, onClick: () => alert("Apply to all")},
		{label: 'Resync', Icon: RefreshCw, onClick: () => alert("Apply to all")}
	]

	const [isCopyModalOpen, setIsCopyModalOpen] = useState(false);
	const [isApplyToAllModalOpen, setIsApplyToAllModalOpen] = useState(false);
	
	/**
	 * Terminal Select
	 */
	const { clientTerminalDetails, auth } = useTypedPage().props;
	const [selectedTerminalId, setSelectedTerminalId] = useState<string | number>('');

	/**
	 *  Set the first fetched terminal as default
	 */
	useEffect(() => {
		if (clientTerminalDetails?.length) {
			const firstTerminal = clientTerminalDetails[0];
		
			if (firstTerminal?.terminalId !== undefined) {
				setSelectedTerminalId(firstTerminal.terminalId);
			}
		}
	}, [clientTerminalDetails]);

	const handleTerminalChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
		const id = e.target.value;
		const parsedId = isNaN(Number(id)) ? id : Number(id); 
		setSelectedTerminalId(parsedId);
	};

	/**
	 * Fetch Tabs 
	 */
	const [showTabs, setShowTabs] = useState<TabType[]| null>(null);
	useEffect (() => {
		axios.get(`${URL.TERMINAL_SETTINGS}/tabs`).then(res =>  {
			setShowTabs(res.data)
		});
	},[]);

	/**
	 *  Set the first fetched tab as default
	 */
	const [activeTab, setActiveTab] = useState<string>('');
	useEffect (() => {
		const firstTab = showTabs?.[0]?.id; 

		if (showTabs && showTabs.length > 0 && !activeTab) {
			setActiveTab(String(firstTab) ?? null);
		}
	}, [showTabs])

	/**
	 * Fetch terminal settings
	 */
	const [loadingSettings, setLoadingSettings] = useState(false);
	const [fetchedSettings, setFetchedSettings] = useState<SettingType[]| null>(null);
	const softwareId = auth?.user?.softwareId;

	useEffect(() => {
		if (!selectedTerminalId || !softwareId) return; 
		setLoadingSettings(true);
		setDisabledButton(true);

		axios.get(`${URL.TERMINAL_SETTINGS}/?terminalId=${selectedTerminalId}&softwareId=${softwareId}`)
		.then(res => {
			setFetchedSettings(res.data);
			setOriginalData(res.data);
			setLoadingSettings(false);
		})
		.catch(err => console.error("Failed to fetch settings:", err));
	}, [selectedTerminalId, auth?.user?.softwareId]);

	/**
	 * Fetch All terminal with settings
	 */
	const [terminalIdsWithSettings, setTerminalIdsWithSettings] = useState<any[]>([]);

	useEffect (() => {
		if (isCopyModalOpen || isApplyToAllModalOpen) {
			axios.get(`${URL.TERMINAL_SETTINGS}/list`).then(res =>  {
				setTerminalIdsWithSettings(res.data)
			});
		}
	}, [isCopyModalOpen, isApplyToAllModalOpen])
	
	/**
	 * Search terminal settings
	 */

	const [searchValue, setSearchValue ] = useState<string>('');
	const [originalData, setOriginalData ] = useState<SettingType[]| null>(null);
	const [debounceSearch] = useDebounce(searchValue, 500);

	const doSearch = async () => {
		if (debounceSearch.trim() === '') {
			setFetchedSettings(originalData);
			return;
		}

		try {
			const res = await axios.get(`${URL.TERMINAL_SETTINGS}/search/?terminalId=${selectedTerminalId}&softwareId=${softwareId}&value=${debounceSearch}`)
			setFetchedSettings(res.data);
		}
		catch(err) {
			console.log(err);
		}
	}
	
	const handleClearSearch = () => {
		setSearchValue('');
		refreshTerminalTable();
	}

	const handleKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
        if (e.key === "Enter") {
            doSearch();
        }
    };

	useEffect(() => {
		doSearch();
	}, [debounceSearch]);
		
	/**
	 * Save only modified terminal settings
	 */
	const [modifiedSettings, setModifiedSettings] = useState<Record<string, any>>({});
	useEffect(() => {
		if (!fetchedSettings) return;
		const initial: Record<string, any> = {}

		fetchedSettings.forEach(setting => {
			initial[setting.id] = setting.value ?? '';
		});
		setModifiedSettings(initial);
	}, [fetchedSettings]);

	const checkChangedSettings = () => {
		const changes: any[] = [];

		fetchedSettings?.forEach(setting => {
			const originalValue = setting.value ?? '';
			const currentValue = modifiedSettings?.[setting.id];

			if (originalValue !== currentValue){
				changes.push({
					setting_id: setting.id,
					value: currentValue
				})
			}
		})

		return changes;
	}
	
	/**
	 * Set active tab from fetched tabs
	 */
	const filteredSettings = fetchedSettings?.filter(
		setting => String(setting.setting_tab_id) === activeTab
	)

	/**
	 *  For customized tabs 
	 */
	const activeObj = showTabs?.find((obj) => 
		String(obj.id) === activeTab
	);

	/**
	 *  Refresh data
	 */
	const refreshTerminalTable = async () => {
		try {
			const softwareId = auth?.user?.softwareId;
			if (!selectedTerminalId || !softwareId) return;
			setLoadingSettings(true);

			const response = await axios.get(`${URL.TERMINAL_SETTINGS}/?terminalId=${selectedTerminalId}&softwareId=${softwareId}`);
			setFetchedSettings(response.data);
		} 
		catch (err) {
			console.error("Failed to refresh terminal table:", err);
			setFlashMessage({ message: 'Failed to refresh terminal table.', type: 'error' });
		} 
		finally {
			setLoadingSettings(false);
			setSearchValue('');
		}
	};

	/**
	 * Terminal Table
	 */
	const getTerminalConnections = (settings: SettingType[] | null): (Record<string, any> & { setting_id?: number })[] => {
		const terminalSetting = settings?.find(s => s.name === "terminalconnections");
		if (!terminalSetting) return [];
		
		const raw = terminalSetting.value;
		if (!raw) return [];

		let parsed: any[] = [];

		if (typeof raw === "string") {
			try {
				const json = JSON.parse(raw);
				if (Array.isArray(json)) parsed = json;
			} 
			catch (err) {
				console.error("Failed to parse terminalConnections JSON:", err);
				return [];
			}
		} 
		else if (Array.isArray(raw)) {
			parsed = raw;
		}

		return parsed
			.filter(item => typeof item === "object" && item !== null)
			.map(item => ({
				...item,
				setting_id: terminalSetting.terminal_setting?.setting_id,
			}));
	};

	const [terminalConnections, setTerminalConnections] = useState<Record<string, any>[]>([]);
	useEffect(() => {
		setTerminalConnections(getTerminalConnections(fetchedSettings));
	}, [fetchedSettings]);

	const maxPreviewColumns = 7;
	const [isTerminalTableModal, setIsTerminalTableModal] = useState(false);
	const [selectedTerminalRow, setSelectedTerminalRow] = useState<any | null>(null);
	const handleEditRow = (row: any) => {
		setSelectedTerminalRow(row);
		setIsTerminalTableModal(true);
	};

	/**
	 * 
	 * Handle modal data
	 */
	const [sourceTerminalSettings, setSourceTerminalSettings] = useState<SettingType[]>([]);
	const handleTerminalsChange = (source: any) => {
		if (source) {
			const softwareId = auth.user?.softwareId;

			axios.get(`${URL.TERMINAL_SETTINGS}/?terminalId=${source.terminalId}&softwareId=${softwareId}`)
			.then((res) => {
				const filteredSettings = res.data.filter(
					(s: any) => s.type === "admin_only|shared" 
				);
				setSourceTerminalSettings(filteredSettings);
			})
			.catch(err => console.error("Failed to fetch source terminal settings:", err));
		}
	};
	
	/**
	 * Save settings
	 */
	const [disabledButton, setDisabledButton] = useState(false);

	useEffect(() => {
		if (checkChangedSettings().length === 0) {
			setDisabledButton(true);
		} 
		else {
			setDisabledButton(false);
		}
	})

	const handleSave = async (action: string, data?: any) => {
		switch(action) {
			case 'saveSettings': {
				if (loading) return;
    			setLoading(true);

				const changed = checkChangedSettings();
				if (changed.length === 0) {
					setFlashMessage({ message: 'No changes', type: 'info' });
				}
				
				const payload = {
					func: 'save',
					terminalId: selectedTerminalId,
					settings: changed
				}

				try {
					await axios.post(`${URL.TERMINAL_SETTINGS}/update`, payload);

					setFlashMessage({ message: 'Settings saved successfully!', type: 'success' });
					setLoading(false);
					refreshTerminalTable();
				} 
				catch (err) {
					console.error('Failed to save settings:', err);
					setFlashMessage({ message: 'Failed to save settings.', type: 'error' });
				}
				finally {
					setLoading(false);
				}

				break;
			}
				
			case 'copySettings': {
				if (!data || loading) return; 
    			setLoading(true);

				const { targetTerminal, settings } = data;

				const payload = {
					func: "copy",
					terminalId: targetTerminal.terminalId,
					settings: settings.map((s: any) => ({
						setting_id: s.id,
						value: s.value,
					})),
				};

				try {
					await axios.post(`${URL.TERMINAL_SETTINGS}/update`, payload);
					setFlashMessage({ message: 'Settings copied successfully!', type: 'success' });
					setIsCopyModalOpen(false);
					refreshTerminalTable();
				}
				catch(err) {
					console.error("Error applying settings:", err);
					setFlashMessage({ message: 'Failed to apply settings.', type: 'error' });
					setIsCopyModalOpen(true);
				}
				finally {
					setLoading(false);
				}
				
				break;
			}

			case "applyToAllSettings": {
				if (!data || loading) return;
    			setLoading(true);

				const { selectedOption, selectedTargetBranch, sourceTerminal, settings } = data;

				if (!sourceTerminal) {
					setFlashMessage({ message: 'Please select a source terminal.', type: 'error' });
					setLoading(false);
					return;
				}

				if (!settings || settings.length === 0) {
					setFlashMessage({ message: 'No settings found for the selected source terminal.', type: 'error' });
					setLoading(false);
					return;
				}

				let targetTerminalIds: number[] = [];

				if (selectedOption === "Network") {
					targetTerminalIds = clientTerminalDetails
						.filter(item => item.clientGroupId === sourceTerminal.clientGroupId) 
						.filter(item => item.terminalId !== sourceTerminal.terminalId)
						.map(item => Number(item.terminalId));
				} 
				else if (selectedOption === "Branch") {
					targetTerminalIds = clientTerminalDetails
						.filter(item => Number(item.branchId) === Number(selectedTargetBranch))
						.map(item => Number(item.terminalId)); 
				} 
				else {
					setFlashMessage({ message: 'Please select a valid option (Network or Branch).', type: 'error' });
					setLoading(false);
					return;
				}

				if (targetTerminalIds.length === 0) {
					setFlashMessage({ message: 'No target terminals found.', type: 'error' });
					setLoading(false);
					return;
				}

				const payload = {
					func: "apply-to-all",
					terminalId: targetTerminalIds,
					settings: settings.map((s: any) => ({
						setting_id: s.id,
						value: s.value,
					})),
				};

				try {
					await axios.post(`${URL.TERMINAL_SETTINGS}/apply-to-all`, payload);
					setFlashMessage({ message: 'Settings applied successfully!', type: 'success' });
					setIsApplyToAllModalOpen(false);
					refreshTerminalTable();
				} 
				catch (err) {
					console.error("Error applying settings:", err);
					setFlashMessage({ message: 'Failed to apply settings.', type: 'error' });
					setIsApplyToAllModalOpen(true);
				}
				finally {
 					setLoading(false);
				}

				break;
			}
			
			case 'saveTerminalConnection': {
				const rowsToSave = terminalConnections.map((row) => {
					const newRow = { ...row };
					delete newRow.setting_id; 

					Object.entries(modifiedSettings).forEach(([key, value]) => {
						if (key.startsWith(`${row.terminalno}-`)) {
							const field = key.replace(`${row.terminalno}-`, "");
							newRow[field] = value;
						}
					});

					return newRow;
				});

				const payload = {
					func: 'save-terminal-table',
					terminalId: selectedTerminalId,
					settings: [
						{
							setting_id: selectedTerminalRow.setting_id,
							value: JSON.stringify(rowsToSave), 
						},
					],
				};
				try {
					await axios.post(`${URL.TERMINAL_SETTINGS}/update`, payload);
					setIsTerminalTableModal(false);
					refreshTerminalTable();
					setFlashMessage({ message: 'Terminal connection saved!', type: 'success' });
				} 
				catch (err) {
					console.error(err);
					setFlashMessage({ message: 'Failed to save terminal connection.', type: 'error' });
				}

				break;
			}

			default: 
				console.log('Unknown action:', action)
		}
	}

	return <AppLayout>
		<div className="flex flex-col min-h-screen bg-gray-100">
			<div className="flex flex-1 overflow-y">
				{/* Main Content */}
				<main className="flex-1 overflow-y-auto">
					<div className="container mx-auto px-4 py-6">
						<div className="flex justify-between items-center mb-6">
							<div>
								<h1 className="text-xl sm:text-2xl font-bold text-gray-800">
									Terminal Configuration
								</h1>
							</div>
						</div>
						{ flash && (
							<div className="absolute top-4 right-4 z-50">
								<FlashMessage
									type={flash.type}
									message={flash.message}
									onDismiss={() => setFlashMessage(null)}
								/>
							</div>
						)}
						<div className="bg-white rounded-lg shadow-md  mb-8 border border-gray-200">
							<div className="p-4 sm:p-6 border-b border-gray-100">
								<div className="flex flex-col sm:flex-row justify-between sm:items-center items-start gap-4">
									<div className="mb-2 basis-1/3">
										<label htmlFor="terminal" className=" text-gray-700 font-medium flex items-center text-[20px] pt-[10px] pb-[10px]">
											<TerminalIcon className="h-4 w-4 mr-2 text-[#e74c3c]" />
											Active Terminal
										</label>
									</div>
									<div className="mb-2 flex items-center gap-2">
										<button className={`px-2 py-2 text-white rounded-md transition-colors flex items-center justify-center text-m font-medium shadow-sm whitespace-nowrap
												${ disabledButton || loading
													? 'bg-[var(--button-success-light)] cursor-not-allowed opacity-70' 
													: 'bg-[var(--button-success-active)] hover:[var( --button-success-hover)]'
												}
											`}
											disabled={disabledButton}
											onClick={() => handleSave('saveSettings')}>
											{ loading ? (
												<>
													<Loader2 className="h-4 w-4 mr-2 animate-spin" />
													Saving...
												</>
												) : (
												<>
													<SaveIcon className="h-4 w-4 mr-2" />
													Save Changes
												</>
											)}
										</button>
										<div className="relative">
											{ openActions ? (
												<XIcon className="h-5 w-5 text-[#5a6a7f] cursor-pointer hover:text-gray-800"
													onClick={() => setOpenActions(false)}
												/>
											) : (
												<SlidersVertical className="h-5 w-5 text-[#5a6a7f] cursor-pointer hover:text-gray-800"
													onClick={() => setOpenActions(true)}
												/>
											)}

											{/* Popup with multiple icons */}
											{openActions && (
												<div className="absolute top-1/2 right-full -translate-y-1/2 mr-1 bg-gray-100 border  border-gray-200 shadow-lg rounded-md p-1 flex items-start z-10">
													{actions.map(({label, Icon, onClick}) => (
														<button className="flex flex-col items-center hover:bg-gray-200 rounded-md p-2 w-20"
															key={label}
															onClick={onClick}
														>
															<Icon className="h-6 w-6 text-gray-700 mb-1" />
															<p className="font-light text-xs text-center leading-tight">{label}</p>
														</button>
													))}
												</div>
											)}
										</div>
									</div>
								</div>
								<div className="flex flex-col sm:flex-row justify-between items-start gap-4">
									<div className="relative w-full sm:w-1/2">
										<select className="block w-full px-4 py-2 border border-gray-300 rounded-md appearance-none bg-white focus:outline-none focus:ring-1 focus:ring-[#5a6a7f] focus:border-[#5a6a7f] transition-colors"
											value={selectedTerminalId}  
											onChange={handleTerminalChange} 
										>
											{clientTerminalDetails?.length > 0 ? (
												clientTerminalDetails.map((terminal) =>
													<option key={terminal.terminalId} value={terminal.terminalId}>
														{`Branch ${terminal.branchId}: ${terminal.branchName} - Terminal# ${terminal.terminalNo}`}
													</option>
												)): (
													<option disabled>No terminals available</option>
											)}
										</select>
										<div className="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
											<svg className="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
												<path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
											</svg>
										</div>
									</div>
									 <div className="relative w-full sm:w-1/2">
									 
										<div className="absolute left-0 top-0 h-full px-3 flex items-center">
											<SearchIcon className="h-5 w-5 text-gray-400" />
										</div>
										<input type="text" placeholder="Search configurations..." className="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-[#5a6a7f] focus:border-[#5a6a7f] transition-colors pl-10" 
											value={searchValue}
											onChange={(e)=> setSearchValue(e.target.value)}
											onKeyDown={handleKeyDown}
										/>
									  {searchValue && (
											<button
											type="button"
											onClick={() => handleClearSearch()}
											className="absolute inset-y-0 right-0 flex items-center pr-3"
											>
											<XIcon className="h-5 w-5 text-gray-400 hover:text-gray-600" />
											</button>
										)}
																				
									</div>
								</div>
							</div>
							{/* Tab Navigation */}
							<TabNavigation activeTab={activeTab} setActiveTab={setActiveTab}
								 tabs={showTabs?.map(tab => ({
									id: String(tab.id),
									label: tab.name,
									icon: iconMap[tab.name] ?? <SettingsIcon />
								})) ?? []}
							 />
							{/* Tab Content */}
							<div className="p-6 sm:p-6 border-t border-gray-200 bg-white">
								{ loadingSettings ? (
									<div className="flex justify-center items-center py-16">
										<span className="animate-spin w-8 h-8 border-4 border-t-transparent border-gray-500 rounded-full"></span>
									</div>
								) : filteredSettings && filteredSettings.length > 0 ? (
									<div className="max-w-8xl mx-auto">
										<div className="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4">
											{ filteredSettings.map(setting => {
												if (activeObj?.name === "Terminal Connections") {
													return (
														<div key={setting.id} className="col-span-3 mx-auto w-full">
															<div className="flex items-center mb-1">
																<Tooltip key={setting.id} content={ 
																	<div className="w-max whitespace-pre-wrap ">
																		<strong>Last Modified By: </strong> {setting.user?.full_name || 'Unknown'} <br />
																		<strong>Last Modified Date: </strong> {setting.last_modified_date}
																	</div>} >
																	<UserRoundPen className="h-4 w-4 text-gray-400 ml-1 cursor-help" />
																</Tooltip>

																<label htmlFor={`setting-${setting.id}`} className="text-md font-medium text-gray-700 ml-1" >
																	{setting.name}
																</label>

																<Tooltip content="Enable to display completion status">
																	<InfoIcon className="h-4 w-4 text-gray-400 ml-1 cursor-help" />
																</Tooltip>

																{ setting.issues && setting.issues.length > 0
																	? setting.issues.map((issue, key) => 
																		<Tooltip key={key} content={issue.value}>
																			<TriangleAlert className="h-4 w-4 text-gray-400 ml-1 cursor-help" />
																		</Tooltip>
																	)
																: null }
															</div>
															<div className="mb-6">
																<div className="flex justify-between items-center mb-4">
																	<h2 className="text-lg font-medium text-gray-700"> </h2>
																	<button className="bg-[var(--color-accent-active)] hover:bg-[var(--color-add-hover)] text-white px-3 py-1.5 rounded text-sm flex items-center">
																		<PlusIcon className="h-4 w-4 mr-1" />
																		Add Connection
																	</button>
																</div>
																<div className="w-full overflow-x-auto">
																	<TerminalTable  data={terminalConnections}  maxColumns={maxPreviewColumns} onEdit={handleEditRow} />
																</div>
															</div>
														</div>
													);
												}
												return (
													<div key={setting.id} className="flex flex-col">
														<div className="flex items-center mb-1">
															<Tooltip key={setting.id} content={ 
																<div className="w-max whitespace-pre-wrap ">
																	<strong>Last Modified By: </strong> {setting.user?.full_name || 'Unknown'} <br />
																	<strong>Last Modified Date: </strong> {setting.last_modified_date}
																</div>} >
																<UserRoundPen className="h-4 w-4 text-gray-400 ml-1 cursor-help" />
															</Tooltip>

															<label htmlFor={`setting-${setting.id}`} className="text-sm font-medium text-gray-700 ml-1" >
																{setting.name}
															</label>

															<Tooltip content="Enable to display completion status">
																<InfoIcon className="h-4 w-4 text-gray-400 ml-1 cursor-help" />
															</Tooltip>

															{ setting.issues && setting.issues.length > 0
																? setting.issues.map((issue, key) => 
																<Tooltip key={key} content={issue.value}>
																	<TriangleAlert className="h-4 w-4 text-gray-400 ml-1 cursor-help" />
																</Tooltip>
															)
															: null}
														</div>
			
														{ setting.form_element === 'text' && (
															<input type="text" className="w-full px-3 py-2 border border-gray-300 rounded-md"
																id={`setting-${setting.id}`}
																value={modifiedSettings[setting.id] ?? ''}
																onChange={e => setModifiedSettings(prev => ({ ...prev, [setting.id]: e.target.value }))}
															/>
														)}
														
														{setting.form_element === 'radio_button' && (
															<div className="flex items-center space-x-4">
																{["true", "false"].map(opt => {
																	const mapValue = opt === "true" ? "1" : "0";
																	return (
																	<label key={opt} className="inline-flex items-center">
																		<input type="radio" className="h-4 w-4 text-[#34495e]"
																			name={`setting-${setting.id}`}
																			value={mapValue}
																			checked={String(modifiedSettings[setting.id] ?? '') === mapValue}
																			onChange={e => setModifiedSettings(prev => ({ ...prev, [setting.id]: e.target.value }))}
																		/>
																		<span className="ml-2 text-sm text-gray-700">{opt}</span>
																	</label>
																	);
																})}
															</div>
														)}
														
														{setting.form_element === 'dropdown' && (
															<select className="block w-full px-4 py-2 pr-8 border border-gray-300 rounded-md bg-white"
																id={`setting-${setting.id}`}
																value={modifiedSettings[setting.id] ?? ''}
																onChange={e => setModifiedSettings(prev => ({ ...prev, [setting.id]: e.target.value }))}
															>
																{ setting.options.length > 0 ? (
																	setting.options.map(opt => <option key={opt.id} value={opt.value}>{opt.name}</option>)
																) : (
																	<option disabled>No options available</option>
																)}
															</select>
														)}
													</div>
												)
											})}
										</div>
									</div>
								) : (
									<div className="text-center py-8">
										<div className="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
											<Tooltip content="No data available">
												<AlertCircleIcon className="h-8 w-8 text-gray-400" />
											</Tooltip>
										</div>
										<h3 className="text-lg font-medium text-gray-700 mb-2">
											No Configuration Data Available
										</h3>
										<p className="text-gray-500 max-w-md mx-auto mb-4">
											Select a tab above to configure your terminal settings or use the
											search to find specific options.
										</p>
										<button className="bg-[var(--color-info)] hover:bg-[var(--color-info-hover)] text-white px-4 py-2 rounded-md transition-colors inline-flex items-center">
											<AlertCircleIcon className="h-4 w-4 mr-2" />
											Configure Terminal
										</button>
									</div>
								)}
							</div>
						</div>
					</div>
				</main>
			</div>
		</div>;

		<CopyModal
			isModalOpen={isCopyModalOpen}
			onClose={() => setIsCopyModalOpen(false)}
			title="Copy to..."
			Icon={Files}
			listOfSourceTerminal={terminalIdsWithSettings}
			data={clientTerminalDetails}
        	onTerminalsChange={handleTerminalsChange}
			sourceTerminalSettings={sourceTerminalSettings} 
			showTabs={showTabs}      
			sizeClass="max-w-3xl"
			onSave={handleSave}
			loading={loading} 
      	/>

		<ApplyToAllModal
			isModalOpen={isApplyToAllModalOpen}
			onClose={() => setIsApplyToAllModalOpen(false)}
			title="Apply-to-All"
			Icon={CopyCheck}
			listOfSourceTerminal={terminalIdsWithSettings}
			data={clientTerminalDetails}
        	onTerminalsChange={handleTerminalsChange}
			sourceTerminalSettings={sourceTerminalSettings} 
			showTabs={showTabs}      
			sizeClass="max-w-3xl"
			onSave={handleSave}
			loading={loading}  
      	/>

		<TerminalTableModal
			isModalOpen={isTerminalTableModal}
			onClose={() => setIsTerminalTableModal(false)}
			title="Terminal Connections"
			Icon={Table}
			data={terminalConnections}
			modifiedSettings={modifiedSettings}
			setModifiedSettings={setModifiedSettings}
			selectedRow={selectedTerminalRow}
			sizeClass="max-w-3xl"
			onSave={handleSave}
			loading={loading} 
      	/>
	</AppLayout>
};
export default TerminalConfig;