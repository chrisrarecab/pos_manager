import { createRouter, createWebHistory } from 'vue-router'
import Sample from '../components/Sample.vue';
import Login from '../components/auth/Login.vue';
import Register from '../components/auth/Register.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
        path: '/sample',
        name: 'sample',
        component: Sample
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
       // component: () => import('../components/auth/Register.vue')
        component: Register
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
    },
    
  ]
})

export default router