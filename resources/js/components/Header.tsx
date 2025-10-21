import React from 'react';
import { UserIcon  } from 'lucide-react';

interface HeaderProps {
  auth: any; 
}

const Header = ({ auth }:HeaderProps) => {
	const client = auth?.user
    ? auth.user.softwareId === 2
      ? auth.user.domain
      : auth.user.clientGroupId
    : null;

	return( <div className="w-full h-14 bg-[#5a6a7f] shadow-md flex items-center px-4">
		<div className="container mx-auto flex justify-between items-center">
			<div className="flex items-center">
				<h1 className="text-xl font-bold text-white">CIRMS POS</h1>
				<span className="ml-2 text-xs bg-white/20 text-white px-2 py-0.5 rounded">
					Enterprise
				</span>
			</div>
			<div className="items-end">
				<div className="flex items-center">
					<div className="h-6 w-6 rounded-full bg-gray-200 flex items-center justify-center">
						<UserIcon className="h-5 w-5 text-gray-600" />
					</div>
					<div className="ml-3 hidden sm:block">
						<p className="text-sm font-medium text-white">
							{auth?.user?.username}  ({client})
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>)
};
export default Header;