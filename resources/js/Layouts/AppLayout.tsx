import { useState } from 'react'
import type { ReactNode } from 'react'
import Sidebar from '../Components/Sidebar'
import Header from '../Components/Header'
import { XIcon, MenuIcon } from 'lucide-react'
import { useTypedPage } from '@/Config/useTypePage';

interface AppLayoutProps {
  children: ReactNode
}

export default function AppLayout({ children }: AppLayoutProps) {
  	const [sidebarOpen, setSidebarOpen] = useState(true)
	const { auth }: any =  useTypedPage().props;
	return (
		// Full viewport height
		<div className="flex flex-col h-screen bg-gray-100">
			{/* Header: fixed height row */}
			<div className="shrink-0">
				<Header auth={auth}  />
			</div>

			{/* Content row: fills remaining space below header */}
			<div className="flex flex-1 overflow-hidden">
				{/* Mobile Sidebar Toggle */}
				<div className="lg:hidden fixed bottom-4 right-4 z-30">
				<button
					onClick={() => setSidebarOpen(!sidebarOpen)}
					className="bg-[#b88c03] text-white p-3 rounded-full shadow-lg hover:bg-[#a37b03] transition-colors"
				>
					{sidebarOpen ? <XIcon className="h-6 w-6" /> : <MenuIcon className="h-6 w-6" />}
				</button>
				</div>

				{/* Sidebar: full height column */}
				<Sidebar sidebarOpen={sidebarOpen} setSidebarOpen={setSidebarOpen} auth={auth}

				/>

				{/* Main content: only this column scrolls */}
				<main className="flex-1 overflow-y-auto p-6">
				{children}
				</main>
			</div>
		</div>
  	)
}
