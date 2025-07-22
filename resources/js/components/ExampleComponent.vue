<template>
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-8">
			<div class="card">
				<div class="card-header">Example Component</div>

				<div class="card-body">
					I'm an example component.
					<button @click="testLoginLink">Test Login Link</button>
				</div>
			</div>
			</div>
		</div>
	</div>
	</template>

<script setup>
import axios from 'axios';

const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

const API_HEADERS = {
	'Content-Type': 'application/json',
	'X-CSRF-TOKEN': token,
	Accept: 'application/json',
};

const payload = {
	domain: 'dannyiloilo.cirms.ph',
	userid: '123',
	username: 'admin',
	fullname: 'admin',
	password: 'admin123',
};


const register = async (payload) => {
	try {
		const response = await axios.post(`/api/register/cirms`, payload, {
			headers: API_HEADERS,
		});
		return response.data.values;
	} catch (error) {
		console.error('Error verifying user:', error.response?.data?.values || error);
		throw error;
	}
};

const testLoginLink = async () => {
	try {
		const result = await register(payload);
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

</script>

