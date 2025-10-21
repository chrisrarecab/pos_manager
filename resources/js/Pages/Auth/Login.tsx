import React, { useState, type FormEvent, useEffect  } from 'react';
import {useForm} from "@inertiajs/react"
import { UserIcon, KeyIcon, Link } from 'lucide-react';

const LoginPage = () => {
	const [activeTab, setActiveTab] = useState('core')

	const { data, setData, post, errors, clearErrors, processing, reset } = useForm({
		softwareId: '',
		domainName: '',
		username: '',
		password: '',
	})

	const handleLogin = async (e: FormEvent) => {
		e.preventDefault();
		clearErrors();
		post('/login');
	}

	useEffect(() => {
		setData('softwareId', activeTab === 'core' ? '1' : '2' );
		clearErrors();
   		reset('username', 'password');
	}, [activeTab]);
	

return <div className="min-h-screen flex items-center justify-center bg-gray-100 p-4">
	<div className="w-full max-w-md">
		<div className="text-center mb-6">
			<h1 className="text-2xl font-bold text-[#5a6a7f]">CIRMS POS</h1>
			<p className="text-gray-600">Enterprise Management System</p>
		</div>
		<div className="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
			{/* Tabs */}
			<div className="flex">
				<button className={`py-3 px-10 text-center font-medium transition-colors 
					${activeTab === 'core' ? 'bg-[#5a6a7f] text-white' : 'bg-white text-gray-700 hover:bg-gray-50'}`} 
					onClick={() => setActiveTab('core')}>
					Core
				</button>
				<button className={`py-3 px-10 text-center font-medium transition-colors 
					${activeTab === 'cirms' ? 'bg-[#5a6a7f] text-white' : 'bg-white text-gray-700 hover:bg-gray-50'}`} 
					onClick={() => setActiveTab('cirms')}>
					CIRMS
				</button>
			</div>
			{/* Login Form */}
			<div className="p-6">
				<form onSubmit={handleLogin}>
					{/* Login Form Header */}
					{activeTab === 'core' && (
						<div className="bg-gray-100 py-3 px-4 mb-6">
							<h2 className="text-gray-800 font-medium text-center">CORE LOGIN</h2>
						</div>
					)}
					{activeTab == 'cirms' && (
						<>
							<div className="bg-gray-100 py-3 px-4 mb-6">
								<h2 className="text-gray-700 font-medium text-center">CIRMS LOGIN</h2>
							</div>
							<div className="mb-4">
								<div className="relative">
									<span className="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
										<Link size={18} />
									</span>
									<input type="text" placeholder="Domain" className="w-full py-2 pl-10 pr-3 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-[#5a6a7f] focus:border-[#5a6a7f]"
										value={data.domainName} required
										onChange={(e) => setData('domainName', e.target.value)}
									/>
									
								</div>
								{errors.domainName && <div className='text-red-500'> {errors.domainName}</div>}
							</div>
						</>
					)}
					<div className="mb-4">
						<div className="relative">
							<input type="hidden" name="softwareId"
								value={data.softwareId}
								onChange={e => setData('softwareId', e.target.value)}
							/>
						<div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
							<UserIcon className="h-5 w-5 text-gray-400" />
						</div>
							<input type="text" className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#5a6a7f] focus:border-[#5a6a7f] transition-colors" placeholder="Username"
								value={data.username} required
								onChange={(e) => setData('username', e.target.value)}  
							/>
						</div>
						{errors.username && <div className='text-red-500'> {errors.username}</div>}
					</div>
					<div className="mb-6">
						<div className="relative">
							<div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
								<KeyIcon className="h-5 w-5 text-gray-400" />
							</div>
							<input type="password" className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#5a6a7f] focus:border-[#5a6a7f] transition-colors" placeholder="Password" 
								value={data.password}required
								onChange={(e) => setData('password', e.target.value)}
							/>
						</div>
						{errors.password && <div className='text-red-500'> {errors.password}</div>}
					</div>
					<div>
						<button type="submit" disabled={processing} className={`w-full bg-[#e74c3c] hover:bg-[#c0392b] text-white py-2 px-4 rounded-md transition-colors font-medium 
							${
								processing ? 'animate-pulse' : ''
							}`}
							>
							{processing ? 'Logging in…' : 'Login'}
						</button>
					</div>
					<div className="mt-4 text-center text-sm text-gray-500">
						<a href="#" className="text-[#5a6a7f] hover:text-[#e74c3c] hover:underline transition-colors">
							Forgot password?
						</a>
					</div>
				</form>
			</div>
		</div>
		<div className="mt-6 text-center text-sm text-gray-500">
			<p>© 2025 CIRMS POS Enterprise. All rights reserved.</p>
		</div>
	</div>
	</div>;
};
export default LoginPage;