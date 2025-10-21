import { usePage, router } from "@inertiajs/react";
import AppLayout from '@/Layouts/AppLayout'

export default function Dashboard() {
    const { props } = usePage();
    console.log(props); 

    const handleLogout = () => {
      router.post('/logout');

    }   
    return (
        <AppLayout>
            <div className="flex flex-col items-center min-h-screen w-full bg-gray-50">
            <h1 className="mt-10"> DASHBOARD</h1>

                <button onClick={handleLogout} className="text-red-500">
                Logout
                </button>
            </div>
        </AppLayout>
    )

}

