import axios from 'axios';

/*
|--------------------------------------------------------------------------
| Basic defaults
|--------------------------------------------------------------------------
*/
axios.defaults.withCredentials = true;              // send/receive cookies
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/*
|--------------------------------------------------------------------------
| Get a fresh CSRF token cookie
| This sets the `XSRF-TOKEN` cookie which axios will automatically
| send back as `X-XSRF-TOKEN` header on every POST/PUT/DELETE.
|--------------------------------------------------------------------------
*/
export async function initCsrf(): Promise<void> {
    await axios.get('/sanctum/csrf-cookie');
}

/*
|--------------------------------------------------------------------------
| Global error handler
| (helpful to catch 419s, 401s etc.)
|--------------------------------------------------------------------------
*/
axios.interceptors.response.use(
    response => response,
    async (error) => {
        if (error.response?.status === 419) {
            console.warn('CSRF token mismatch (419). Maybe call initCsrf() again.');
            // await initCsrf();

            return axios(error.config)
        } else if (error.response?.status === 401) {

            console.warn('Unauthorized.');
        }
        return Promise.reject(error);
    }
);

export default axios;
