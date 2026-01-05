import React, { useState } from 'react';
import { Link, usePage, router } from '@inertiajs/react';
import { useTypedPage } from '@/Config/useTypePage';
import { 
    LayoutDashboardIcon, 
    SettingsIcon, 
    HardDriveIcon, 
    FileTextIcon, 
    UserIcon, 
    LogOutIcon, 
    StoreIcon, 
    ChevronsLeftIcon, 
    ChevronsRightIcon, 
    ChevronDownIcon, 
    BuildingIcon } from 'lucide-react';

interface SidebarProps {
    sidebarOpen: boolean
    setSidebarOpen: React.Dispatch<React.SetStateAction<boolean>>
    auth: any;
}

const Sidebar = ({ sidebarOpen, setSidebarOpen, auth }: SidebarProps) => {

    const { clientTerminalDetails } = useTypedPage().props;
    const [sidebarCollapsed, setSidebarCollapsed] = useState(false)
    const [storeDropdownOpen, setStoreDropdownOpen] = useState(false)

    const toggleCollapse = () => setSidebarCollapsed(!sidebarCollapsed)
    const toggleStoreDropdown = () => setStoreDropdownOpen(!storeDropdownOpen)

    return <>
        {/* Sidebar Overlay */}
        { sidebarOpen && <div className="fixed inset-0 bg-black bg-opacity-50 z-20 lg:hidden" onClick={() => setSidebarOpen(false)}></div>}

        {/* Sidebar */}
        <div className={`bg-[#34495e] border-r border-gray-700 flex-shrink-0 overflow-y-auto fixed lg:static inset-y-0 left-0 z-20 transform transition-all duration-300 ease-in-out flex flex-col ${sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'} ${sidebarCollapsed ? 'w-16 lg:w-16' : 'w-64 lg:w-64'}`}>
            {/* Store Selection - Moved Above Branches */}
            {!sidebarCollapsed ? <div className="px-4 py-4 border-b border-gray-700">
                <div className="flex items-center justify-between mb-2">
                    <h2 className="text-xs font-semibold text-gray-300 uppercase tracking-wider px-1">
                        COMPANY
                    </h2>
                    <button onClick={toggleCollapse} className="text-gray-300 hover:text-[#e74c3c] transition-colors">
                        <ChevronsLeftIcon className="h-5 w-5" />
                    </button>
                </div>
                <div className="relative mb-2">
                    <button onClick={toggleStoreDropdown} className="w-full flex items-center justify-between text-sm font-medium text-gray-200 px-3 py-2 hover:bg-[#2c3e50] rounded-md transition-colors">
                        <div className="flex items-center">
                            <BuildingIcon className="h-5 w-5 text-gray-400 mr-3" />
                            {clientTerminalDetails?.[0]?.clientGroupName ?? 'Client Group'}
                        </div>
                        <ChevronDownIcon className="h-4 w-4 text-gray-400" />
                    </button>
                    { storeDropdownOpen && 
                        <div className="absolute left-0 right-0 mt-1 bg-[#2c3e50] border border-gray-700 rounded-md shadow-lg z-10">
                            <div className="py-1">
                                <button className="w-full text-left px-4 py-2 text-sm text-gray-200 hover:bg-[#34495e]">
                                    Save N Fresh Cartimart
                                </button>
                                <button className="w-full text-left px-4 py-2 text-sm text-gray-200 hover:bg-[#34495e]">
                                    Downtown Branch
                                </button>
                                <button className="w-full text-left px-4 py-2 text-sm text-gray-200 hover:bg-[#34495e]">
                                    Westside Mall
                                </button>
                            </div>
                        </div>
                    }
                </div>
            </div> : <div className="px-3 py-4 flex items-center justify-between border-b border-gray-700">
                <div className="p-1.5 rounded-full">
                    <BuildingIcon className="h-5 w-5 text-gray-400" />
                </div>
                <button onClick={toggleCollapse} className="text-gray-300 hover:text-[#e74c3c] transition-colors">
                    <ChevronsRightIcon className="h-5 w-5" />
                </button>
            </div>
            }

            {/* Branches Section */}
            {/* {!sidebarCollapsed ? <div className="px-4 py-2 border-b border-gray-700">
                <h2 className="text-xs font-semibold text-gray-300 uppercase tracking-wider px-3 mb-2">
                    BRANCHES
                </h2>
                <nav className="space-y-1">
                    <Link href="#" className="flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md text-gray-200 hover:bg-[#2c3e50] transition-colors">
                        <div className="flex items-center">
                            <StoreIcon className="mr-3 h-5 w-5 text-gray-400" />
                            Downtown Branch
                        </div>
                        <span className="bg-yellow-900 text-yellow-200 text-xs px-2 py-0.5 rounded">
                            3/4
                        </span>
                    </Link>
                    <Link href="#" className="flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md text-gray-200 hover:bg-[#2c3e50] transition-colors">
                        <div className="flex items-center">
                            <StoreIcon className="mr-3 h-5 w-5 text-gray-400" />
                            Westside Mall
                        </div>
                        <span className="bg-green-900 text-green-200 text-xs px-2 py-0.5 rounded">
                            3/3
                        </span>
                    </Link>
                </nav>
            </div> : <div className="px-1 py-4 flex flex-col items-center space-y-4 border-b border-gray-700">
                <div className="rounded-md p-2 hover:bg-[#2c3e50] cursor-pointer transition-colors">
                    <StoreIcon className="h-5 w-5 text-gray-400" />
                </div>
                <div className="rounded-md p-2 hover:bg-[#2c3e50] cursor-pointer transition-colors">
                    <StoreIcon className="h-5 w-5 text-gray-400" />
                </div>
            </div>} */}

            {/* Navigation Links */}
            <div className="px-4 py-2 flex-grow">
                <nav className="space-y-1">
                    <Link href="/dashboard" className={`flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors ${location.pathname === '/dashboard' ? 'bg-[#2c3e50] text-[#e74c3c]' : 'text-gray-200 hover:bg-[#2c3e50]'} ${sidebarCollapsed ? 'justify-center' : ''}`}>
                        <LayoutDashboardIcon className={`${sidebarCollapsed ? 'h-5 w-5' : 'mr-3 h-5 w-5'} ${location.pathname === '/dashboard' ? 'text-[#e74c3c]' : 'text-gray-400'}`} />
                        {!sidebarCollapsed && <span>Dashboard</span>}
                    </Link>
                    <Link href="/terminal/config" className={`flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors ${location.pathname === '/terminal/config' ? 'bg-[#2c3e50] text-[#e74c3c]' : 'text-gray-200 hover:bg-[#2c3e50]'} ${sidebarCollapsed ? 'justify-center' : ''}`}>
                        <SettingsIcon className={`${sidebarCollapsed ? 'h-5 w-5' : 'mr-3 h-5 w-5'} ${location.pathname === '/terminal/config' ? 'text-[#e74c3c]' : 'text-gray-400'}`} />
                        {!sidebarCollapsed && <span>Terminal Config</span>}
                    </Link>
                    <Link href="/backup" className={`flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors ${location.pathname === '/backup' ? 'bg-[#2c3e50] text-[#e74c3c]' : 'text-gray-200 hover:bg-[#2c3e50]'} ${sidebarCollapsed ? 'justify-center' : ''}`}>
                        <HardDriveIcon className={`${sidebarCollapsed ? 'h-5 w-5' : 'mr-3 h-5 w-5'} ${location.pathname === '/backup' ? 'text-[#e74c3c]' : 'text-gray-400'}`} />
                        {!sidebarCollapsed && <span>Backup</span>}
                    </Link>
                    <Link href="/logs" className={`flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors ${location.pathname === '/logs' ? 'bg-[#2c3e50] text-[#e74c3c]' : 'text-gray-200 hover:bg-[#2c3e50]'} ${sidebarCollapsed ? 'justify-center' : ''}`}>
                        <FileTextIcon className={`${sidebarCollapsed ? 'h-5 w-5' : 'mr-3 h-5 w-5'} ${location.pathname === '/logs' ? 'text-[#e74c3c]' : 'text-gray-400'}`} />
                        {!sidebarCollapsed && <span>Logs</span>}
                    </Link>
                </nav>
            </div>

            {/* User Info Section */}
            <div className="mt-auto border-t border-gray-700">
            {!sidebarCollapsed ? <div className="px-6 py-4">
                <div className="flex items-center justify-between mb-1">
                    <div className="flex items-center">
                    <div className="bg-[#e74c3c] p-1.5 rounded-full shadow mr-2">
                        <UserIcon className="h-5 w-5 text-white" />
                    </div>
                    <p className="text-gray-200 font-medium">Welcome, {auth?.user?.username}</p>
                    </div>
                    <Link href='/logout' method="post" className="flex items-center text-red-400 hover:text-red-300 transition-colors">
                    <LogOutIcon className="h-5 w-5" />
                    </Link>
                </div>
                {/* <p className="text-xs text-gray-400 pl-9">
                    User ID: <span className="text-[#e74c3c]">1998</span>
                </p> */}
                </div> : <div className="flex flex-col items-center py-4 space-y-3">
                <div className="bg-[#e74c3c] p-1.5 rounded-full shadow">
                    <UserIcon className="h-5 w-5 text-white" />
                </div>
                <Link href='/logout' method="post" className="text-red-400 hover:text-red-300 transition-colors">
                    <LogOutIcon className="h-5 w-5" />
                </Link>
                </div>}
            </div>
        </div>
        </>;
};
export default Sidebar;