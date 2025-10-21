import Card from "../../Components/Card";
import axios from "@/Config/axios"; 
import { usePage } from "@inertiajs/react";
export default function Sample() {
  const { props } = usePage();
  console.log(props);
  const payload = {
    domain: 'dannyiloilo.cirms.ph',
    userid: '123',
    username: 'admin',
    fullname: 'admin',
    password: 'admin123',
  };

  const bypassLogin = async (payload: Record<string, any>) => {
    try {
      const res = await axios.post('/api/register/cirms', payload);
		  return res.data.values;
    } catch (error: any) {
      console.error("Axios error:", error.res?.data || error.message);
    }
  }
  const testLoginLink = async () => {
	try {
		const result = await bypassLogin(payload);
   
		const { token, url } = result;

		const response = await fetch(url, {
			method: 'POST',
			headers: {
			'Authorization': `Bearer ${token}`,
			'Content-Type': 'application/json',
		},
			credentials: 'include',
			redirect: 'manual',   
		});

		const data = await response.json();
		if (data.isSuccessful && data.values?.redirect) {
		  window.location.href = data.values.redirect;
		} else {
			console.error(data.message, data.error);
		}
	} catch (err) {
		alert('An error occurred during login.');
		console.error(err);
	}
};

  return (
  <div className="min-h-screen flex items-center justify-center bg-gray-100 p-6">
    <Card
      title="Large Card"
      description="This card is 400px wide."
      className="w-[800px] h-[250px]"
    >
      <button onClick={testLoginLink}
        className="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
      >
        Bypass Login
      </button>
    </Card>
  </div>

  );
}
