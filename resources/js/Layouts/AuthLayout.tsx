import type { ReactNode } from 'react';

interface AuthLayoutProps {
	children: ReactNode
}

export default function AuthLayout({children} : AuthLayoutProps) {
	return ( 
	<div className="min-h-screen flex items-center justify-center bg-gray-100 p-4">
		<div className="w-full max-w-md">
			<div className="text-center mb-6">
				<h1 className="text-2xl font-bold text-[#5a6a7f]">POS Manager</h1>
				<p className="text-gray-600">Enterprise Management System</p>
			</div>
			{children}
		</div>
	</div>
	)
}