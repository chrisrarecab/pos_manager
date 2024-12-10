import { createRouter, createWebHistory } from 'vue-router'
import Dashboard from '../components/Dashboard.vue';
import Login from '../components/auth/Login.vue';
import Register from '../components/auth/Register.vue';
import RegisterCirms from '../components/auth/RegisterCirms.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'dashboard',
      component: Dashboard
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: Dashboard
    },
    {
        path: '/login',
        name: 'login',
        component: Login
    },
    {
      path: '/logout',
      name: 'logout'
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
        props: true,
    },
    {
      path: '/register/core',
      name: 'register-core',
      component: Register,
      props: true,
    },
    {
        path: '/register/cirms',
        name: 'register-cirms',
        component: RegisterCirms,
    },
  ]
})

export default router