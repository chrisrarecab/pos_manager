import React, { useEffect, useState, useRef } from 'react';
import { ChevronLeftIcon, ChevronRightIcon } from 'lucide-react';

// UI Types 
interface Tab {
	id: string;
	icon: React.ReactElement;
	label: string;
}

interface TabNavigationProps {
	activeTab: string;
	setActiveTab: (tabId: string) => void;
	tabs: Tab[];
}

const TabNavigation = ({ activeTab, setActiveTab, tabs }: TabNavigationProps) => {
	const tabsContainerRef = useRef<HTMLDivElement>(null);
	const [showLeftScroll, setShowLeftScroll] = useState(false);
	const [showRightScroll, setShowRightScroll] = useState(false);

	const checkForScrollButtons = () => {
		const container = tabsContainerRef.current;
		if (container) {
			setShowLeftScroll(container.scrollLeft > 0);
			setShowRightScroll(container.scrollLeft < container.scrollWidth - container.clientWidth);
		}
	};

	const scrollTabs = (direction: 'left' | 'right') => {
		const container = tabsContainerRef.current;
		if (container) {
		const scrollAmount = 200;
		container.scrollBy({
			left: direction === 'left' ? -scrollAmount : scrollAmount,
			behavior: 'smooth',
		});
		setTimeout(checkForScrollButtons, 300);
		}
	};

	useEffect(() => {
		checkForScrollButtons();
		window.addEventListener('resize', checkForScrollButtons);
		return () => window.removeEventListener('resize', checkForScrollButtons);
	}, []);

	return (
		<div className="relative border-t border-b border-gray-200 bg-gray-50">
		{showLeftScroll && (
			<button onClick={() => scrollTabs('left')} className="absolute left-0 top-0 bottom-0 bg-gradient-to-r from-gray-50 to-transparent px-2 z-10 flex items-center justify-center">
			<div className="bg-[#5a6a7f] text-white p-1 rounded-full hover:bg-[#34495e] transition-colors">
				<ChevronLeftIcon className="h-5 w-5" />
			</div>
			</button>
		)}

		{showRightScroll && (
			<button onClick={() => scrollTabs('right')} className="absolute right-0 top-0 bottom-0 bg-gradient-to-l from-gray-50 to-transparent px-2 z-10 flex items-center justify-center">
			<div className="bg-[#5a6a7f] text-white p-1 rounded-full hover:bg-[#34495e] transition-colors">
				<ChevronRightIcon className="h-5 w-5" />
			</div>
			</button>
		)}

		<div ref={tabsContainerRef} className="flex overflow-x-auto scrollbar-hide" onScroll={checkForScrollButtons} style={{ scrollbarWidth: 'none', msOverflowStyle: 'none' }}>
			<div className="flex min-w-max">
			{tabs.map((tab) => (
				<button key={tab.id} className={`px-5 py-4 text-sm whitespace-nowrap transition-colors flex items-center gap-1 ${
					activeTab === tab.id
					? 'bg-white text-[#e74c3c] border-t-2 border-[#e74c3c] font-medium -mt-px'
					: 'text-gray-600 hover:text-[#e74c3c] hover:bg-gray-100'
				}`}
				onClick={() => setActiveTab(tab.id)}
				> 
				{tab.icon}
				{tab.label}
				</button>
			))}
			</div>
		</div>
		</div>
	);
};

export default TabNavigation;
