import React, { useState,  type FormEvent, useEffect  } from 'react'
import { UserIcon, KeyIcon, Link } from 'lucide-react'
import {useForm} from "@inertiajs/react"

export default function LoginPage() {
	const [activeTab, setActiveTab] = useState('core')

	const { data, setData, post } = useForm({
		softwareId: '',
		domain: '',
		username: '',
		password: '',
	})

	const handleLogin = (e: FormEvent) => {
		e.preventDefault()
		console.log("Form data to send:", data);
		post('login')
	}

	useEffect(() => {
		setData('softwareId', activeTab === 'core' ? '1' : '2' );
	}, [activeTab]);

	return (
		<div className="flex flex-col items-center justify-center min-h-screen w-full bg-gray-50">
			<div className="text-center mb-6">
			<h1 className="text-2xl font-bold text-amber-600">CIRMS POS</h1>
			<p className="text-gray-600">Enterprise Management System</p>
			</div>
			<div className="bg-white rounded-md shadow-md w-full max-w-md overflow-hidden">
			{/* Tabs */}
			<div className="flex">
				<button
				className={`py-3 px-6 flex-1 font-medium ${
					activeTab === 'core' 
					? 'bg-amber-600 text-white' 
					: 'bg-white text-gray-700'
				}`}
				onClick={() => setActiveTab('core')}
				>
				Core
				</button>
				<button
				className={`py-3 px-6 flex-1 font-medium ${activeTab === 'cirms' ? 'bg-amber-600 text-white' : 'bg-white text-gray-700'}`}
				onClick={() => setActiveTab('cirms')}
				>
				CIRMS
				</button>
			</div>
			<div className="p-6">
				<form onSubmit={handleLogin}>
				{/* Login Form Header */}
				{activeTab === 'core' && (
					<div className="bg-gray-100 py-3 px-4 mb-6">
						<h2 className="text-gray-700 font-medium">Core Login</h2>
					</div>
				)}
				{activeTab == 'cirms' && (
					<>
						<div className="bg-gray-100 py-3 px-4 mb-6">
							<h2 className="text-gray-700 font-medium">CIRMS Login</h2>
						</div>
						<div className="mb-4">
							<div className="relative">
								<span className="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
								<Link size={18} />
								</span>
								<input
								type="text"
								placeholder="Domain"
								className="w-full py-2 pl-10 pr-3 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-amber-500"
								value={data.domain}
								onChange={(e) => setData('domain', e.target.value)}
								required
								/>
								{/* {errors.domain && <div className='text-red-500'> {errors.domain}</div>} */}
							</div>
						</div>
					</>
					)}
					<div className="mb-4">
						
						<div className="relative">
							<input
								type="hidden"
								name="softwareId"
								value={data.softwareId}
								onChange={e => setData('softwareId', e.target.value)}
								/>

							<span className="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
							<UserIcon size={18} />
							</span>
							<input
							type="text"
							placeholder="Username"
							className="w-full py-2 pl-10 pr-3 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-amber-500"
							value={data.username}
							onChange={(e) => setData('username', e.target.value)}
							required
							/>
							{/* {errors.username && <div className='text-red-500'> {errors.username}</div>} */}
						</div>
					</div>
					<div className="mb-6">
						<div className="relative">
							<span className="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
							<KeyIcon size={18} />
							</span>
							<input
							type="password"
							placeholder="Password"
							className="w-full py-2 pl-10 pr-3 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-amber-500"
							value={data.password}
							onChange={(e) => setData('password', e.target.value)}
							required
							/>
							{/* {errors.password && <div className='text-red-500'> {errors.password}</div>} */}
						</div>
					</div>
					<button
					type="submit"
					className="w-full py-2 bg-amber-600 text-white rounded hover:bg-amber-700 transition-colors"
					>
					Login
					</button>
				</form>
				{/* Forgot Password */}
				<div className="mt-4 text-center">
				<a href="#" className="text-amber-600 text-sm hover:underline">
					Forgot password?
				</a>
				</div>
			</div>
			</div>
			{/* Footer */}
			<div className="mt-6 text-center text-xs text-gray-500">
			© 2025 CIRMS POS Enterprise. All rights reserved.
			</div>
		</div>
	)
}
