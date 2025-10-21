import React, { useState } from 'react';
import { BarChartIcon, UsersIcon, StoreIcon, HelpCircleIcon, SearchIcon, BellIcon, UserIcon, PlusIcon, MenuIcon, XIcon, ServerIcon, WifiIcon, GitCommitIcon, RefreshCwIcon, HardDriveIcon, ClockIcon } from 'lucide-react';
import { Link, usePage, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout'
const StatCard = ({
  title,
  value,
  icon,
  status
}) => {
  return <div className="bg-white rounded-lg p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
      <div className="flex justify-between items-start mb-3">
        <h3 className="text-gray-500 text-sm font-medium">{title}</h3>
        {icon && <div>{icon}</div>}
      </div>
      <div className="flex items-end justify-between">
        <div>
          <div className="text-lg font-bold">{value}</div>
          {status && <div className={`flex items-center text-xs ${status.positive ? 'text-green-500' : 'text-red-500'}`}>
              {status.positive ? '●' : '○'} {status.value}
            </div>}
        </div>
      </div>
    </div>;
};
const TerminalCard = ({
  id,
  active,
  uuid,
  version
}) => {
  return <div className="bg-white rounded-lg shadow-sm p-4 flex flex-col border border-gray-100 hover:shadow-md transition-shadow">
      <div className="flex justify-between items-center mb-3">
        <h3 className="font-medium">Terminal {id}</h3>
        <div className={`flex items-center ${active ? 'text-green-500' : 'text-red-500'}`}>
          <div className={`h-2 w-2 rounded-full ${active ? 'bg-green-500' : 'bg-red-500'} mr-2`}></div>
          <span className="text-sm">{active ? 'Online' : 'Offline'}</span>
        </div>
      </div>
      <div className="grid grid-cols-1 gap-2 mt-2">
        <div>
          <div className="text-xs text-gray-500">Version</div>
          <div className="font-medium">{version}</div>
        </div>
        <div>
          <div className="text-xs text-gray-500">Last Sync</div>
          <div className="font-medium text-xs">2025-03-01 09:10:23</div>
        </div>
      </div>
      <div className="mt-4 pt-3 border-t border-gray-100">
        <Link to="/terminal" className="text-[#b88c03] hover:underline transition-colors text-sm">
          Configure
        </Link>
      </div>
    </div>;
};
const Dashboard = () => {
	const [sidebarOpen, setSidebarOpen] = useState(false);
	return <AppLayout>
	<div className="flex flex-col min-h-screen bg-gray-100">
		<div className="flex flex-1 overflow-hidden">
			{/* Main Content */}
			<main className="flex-1 overflow-y-auto">
				<div className="p-4 sm:p-6 max-w-7xl mx-auto">
					<div className="flex justify-between items-center mb-6">
						<div>
							<h1 className="text-xl sm:text-2xl font-bold text-gray-800">
								System Overview
							</h1>
						</div>
					</div>
					{/* System Overview */}
					<div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
						<StatCard title="System Version" value="v.0.0.0.0" icon={<GitCommitIcon className="h-5 w-5 text-[#b88c03]" />} status={{
						value: 'Up to date',
						positive: true
					}} />
						<StatCard title="Auto Sync Status" value="Enabled" icon={<RefreshCwIcon className="h-5 w-5 text-[#b88c03]" />} status={{
						value: 'Active',
						positive: true
					}} />
						<StatCard title="Connected Terminals" value="8/10" icon={<ServerIcon className="h-5 w-5 text-[#b88c03]" />} />
						<StatCard title="System Health" value="Excellent" icon={<HardDriveIcon className="h-5 w-5 text-[#b88c03]" />} />
					</div>
					{/* Downtown Branch */}
					<div className="mb-8 bg-white rounded-lg shadow-sm p-4 sm:p-6 border border-gray-100">
						<div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
						<div>
							<h2 className="text-xl font-semibold text-gray-800">
							Downtown Branch
							</h2>
							<p className="text-sm text-gray-500">
							3 of 4 terminals online • Branch ID: BRANCH-DT-001
							</p>
						</div>
						<button className="px-3 py-1.5 bg-[#e74c3c] text-white rounded text-sm flex items-center hover:bg-[#c0392b] transition-colors">
							<PlusIcon className="h-4 w-4 mr-1" />
							Add Terminal
						</button>
						</div>
						<div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
						<TerminalCard id="1" active={true} uuid="7f8d9a2e-b3c4-5d6e-8f9g-1h2i3j4k5l6m" version="v.0.0.0.0" />
						<TerminalCard id="2" active={true} uuid="9a8b7c6d-5e4f-3g2h-1i9j-8k7l6m5n4o3p" version="v.0.0.0.0" />
						<TerminalCard id="3" active={true} uuid="1a2b3c4d-5e6f-7g8h-9i0j-1k2l3m4n5o6p" version="v.0.0.0.0" />
						<TerminalCard id="4" active={false} uuid="3c4d5e6f-7g8h-9i0j-1k2l-3m4n5o6p7q8r" version="v.0.0.0.0" />
						</div>
					</div>
					{/* Westside Mall */}
					<div className="mb-8 bg-white rounded-lg shadow-sm p-4 sm:p-6 border border-gray-100">
						<div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
						<div>
							<h2 className="text-xl font-semibold text-gray-800">
							Westside Mall
							</h2>
							<p className="text-sm text-gray-500">
							3 of 3 terminals online • Branch ID: BRANCH-WS-002
							</p>
						</div>
						<button className="px-3 py-1.5 bg-[#e74c3c] text-white rounded text-sm flex items-center hover:bg-[#c0392b] transition-colors">
							<PlusIcon className="h-4 w-4 mr-1" />
							Add Terminal
						</button>
						</div>
						<div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
						<TerminalCard id="1" active={true} uuid="5e6f7g8h-9i0j-1k2l-3m4n-5o6p7q8r9s0t" version="v.0.0.0.0" />
						<TerminalCard id="2" active={true} uuid="2b3c4d5e-6f7g-8h9i-0j1k-2l3m4n5o6p7q" version="v.0.0.0.0" />
						<TerminalCard id="3" active={true} uuid="8h9i0j1k-2l3m-4n5o-6p7q-8r9s0t1u2v3w" version="v.0.0.0.0" />
						</div>
					</div>
				</div>
			</main>
		</div>
	</div>
	</AppLayout>
	;
    
};
export default Dashboard;