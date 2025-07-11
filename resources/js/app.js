/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue';
import BootstrapVue3 from 'bootstrap-vue-3';

import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue-3/dist/bootstrap-vue-3.css';
import '../css/app.css';

/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

const app = createApp({});
app.use(BootstrapVue3);
app.use(router);

import CheckAuth from './components/auth/CheckAuth.vue';
import Login from './components/auth/Login.vue';
import Register from './components/auth/Register.vue';
import RegisterCirms from './components/auth/RegisterCirms.vue';
import Dashboard from './components/Dashboard.vue';
import router from './router';

app.component('session-component', CheckAuth);
app.component('login', Login);
app.component('register-core', Register);
app.component('register-cirms', RegisterCirms);
app.component('dashboard-component', Dashboard);

import ExampleComponent from './components/ExampleComponent.vue';
import UserListComponent from './components/UserListComponent.vue';
import UserDetailsComponent from './components/UserDetailsComponent.vue';
import UserBranchComponent from './components/UserBranchComponent.vue';

app.component('example-component', ExampleComponent);
app.component('user-list-component', UserListComponent);
app.component('user-details-component', UserDetailsComponent);
app.component('user-branch-component', UserBranchComponent);
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
//     app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
// });

/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */

app.mount('#app');
