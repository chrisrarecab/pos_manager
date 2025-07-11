<script>
import axios from 'axios';

const API_HEADERS = {
	'Content-Type': 'application/json',
	'Authorization': 'Basic bmVsc29mdDoxMjE1ODY='
};
const API_BASE = '/api/v1/terminal/settings';

/**
 * Fetches client terminal details
 * @param {number} clientId - CLient Group Id
 * @returns {Promise<Array>} - Array of client terminal details
 */
export const fetchClientDetails = async (clientId) => {
	try {
		const response = await axios.get(`/api/v1/client/details/${clientId}`, { headers: API_HEADERS });
		return response.data.data;
	} catch (error) {
		console.error('Error fetching client details:', error);
		throw error;
	}
};

/**
 * Fetches settings tabs
 * @returns {Promise<Array>} - Array of setting tabs
 */
export const fetchTerminalSettingsTabs = async () => {
	try {
		const response = await fetch(`${API_BASE}/fetch/tabs`);
		return await response.json();
	} catch (error) {
		console.error('Error fetching tabs:', error);
		throw error;
	}
};

/**
 * Fetches settings for a terminal
 * @param {num} terminalId - CLient terminal Id
 * @returns {Promise<Array>} - Array of settings
 */
export const fetchTerminalSettings = async (terminalId) => {
	try {
		const response = await fetch(`${API_BASE}/?client_terminal_id=${terminalId}`);
		return await response.json();
	} catch (error) {
		console.error('Error fetching settings:', error);
		throw error;
	}
};

/**
 * Saves settings for a terminal
 * @param {Object} payload - Settings payload (clientterminalid and setting value)
 * @returns {Promise<Object>} - API response
 */
export const updateTerminalSettings = async (payload) => {
	try {
		const response = await axios.post(`${API_BASE}/update`, payload, {
			headers: API_HEADERS
		});
		return response.data;
	} catch (error) {
		console.error('Error saving settings:', error);
		throw error;
	}
};

/**
 * Save settings to a target terminal
 * @param {num} selectedTargetTerminal.value - target client terminal id
 * @param {Array} settings - Array of settings to copy
 * @returns {Promise<Object>} - API response
 */
export const copyTerminalSettings = async (payload) => {
	try {
		const response = await axios.post(`${API_BASE}/update`, payload, {
			headers: API_HEADERS
		});
		return response.data;
	} catch (error) {
		console.error('Error copying settings:', error);
		throw error;
	}
};

/**
 * Save settings to a target terminal
 * @param {Array} targetTerminalIds - Array of client terminal ids
 * @param {Array} settings - Array of settings to copy
 * @returns {Promise<Object>} - API response
 */
export const applySettingsToMultipleTerminals = async (payload) => {
	try {
		const response = await axios.post(`${API_BASE}/apply-to-all`, payload, {
			headers: API_HEADERS
		});
		return response.data;
	} catch (error) {
		console.error('Error copying settings:', error);
		throw error;
	}
};
</script>