import React, { type FormEvent} from 'react';
import AuthLayout from '@/Layouts/AuthLayout';
import { ShieldIcon, UserIcon, LockIcon } from 'lucide-react';
import { Link, useForm } from '@inertiajs/react';

const Register = () => {

	const {data, setData, post, errors, clearErrors, processing, reset} = useForm({
		secretKey: '',
		fullname: '',
		username: '',
		password: ''
	})

	
	const handleSave = (e :FormEvent) => {
		e.preventDefault();
		clearErrors();
		post('/register')
	}
	
	return (
		<AuthLayout>
			<div className="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
				{/* Header */}
				<div className="py-10 px-6">
					<h2 className="text-[var(--color-primary)] text-2xl text-center font-medium">Create an account</h2>
				</div>
				{/* Registration Form */}
				<div className="px-6 pb-6">
					<form onSubmit={handleSave}>
					<div className="mb-4">
						<div className="relative">
						<div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
							<ShieldIcon className="h-5 w-5 text-gray-400" />
						</div>
						<input placeholder="Secret Key" type="text" className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#5a6a7f] focus:border-[#5a6a7f] transition-colors" required
							value={data.secretKey}
							onChange={(e) => setData('secretKey', e.target.value)}
						/>
						</div>
					</div>
					<div className="mb-4">
						<div className="relative">
						<div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
							<UserIcon className="h-5 w-5 text-gray-400" />
						</div>
						<input placeholder="Full Name" type="text" className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#5a6a7f] focus:border-[#5a6a7f] transition-colors" required
							value={data.fullname}
							onChange={(e) => setData('fullname', e.target.value)}
						/>
						</div>
					</div>
					<div className="mb-4">
						<div className="relative">
						<div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
							<UserIcon className="h-5 w-5 text-gray-400" />
						</div>
						<input placeholder="Username" type="text" className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#5a6a7f] focus:border-[#5a6a7f] transition-colors" required
							value={data.username}
							onChange={(e) => setData('username', e.target.value)}
						/>
						</div>
					</div>
					<div className="mb-6">
						<div className="relative">
						<div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
							<LockIcon className="h-5 w-5 text-gray-400" />
						</div>
						<input placeholder="Password" type="password" className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#5a6a7f] focus:border-[#5a6a7f] transition-colors" required
							value={data.password}
							onChange={(e) => setData('password', e.target.value)}
						/>
						</div>
					</div>
					<div>
						<button
						type="submit"
						className="w-full bg-[#e74c3c] hover:bg-[#c0392b] text-white py-2 px-4 rounded-md transition-colors font-medium"
						>
						Register
						</button>
					</div>
					<div className="mt-4 text-center text-sm text-gray-500">
						Already have an account?{' '}
						<Link
						href='/login'
						className="text-[var(--color-primary-light)] font-medium hover:underline hover:text-[var(--color-accent-hover)] transition-colors"
						>          Login
						</Link>
					</div>
					</form>
				</div>
			</div>
		</AuthLayout>
	);
};

export default Register;