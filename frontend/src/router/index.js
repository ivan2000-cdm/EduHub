import { createRouter, createWebHistory } from 'vue-router'
import DefaultLayout from '../layouts/DefaultLayout.vue'
import Login from '../pages/Login.vue'
import Home from '../pages/Home.vue'
import SchoolClasses from '../pages/directories/schoolClasses/index.vue' // Импортируешь компонент

const routes = [
  {
    path: '/',
    component: DefaultLayout, // 👈 Общий layout
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'Home',
        component: Home
      },
      {
        path: 'users',
        name: 'Users',
        component: Home
      },
      {
        path: 'directories/classes',
        name: 'SchoolClasses',
        component: SchoolClasses
      }
    ]
  },
  {
    path: '/login',
    name: 'Login',
    component: Login
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

// Защита маршрутов
router.beforeEach((to, from, next) => {
  const isAuthenticated = !!localStorage.getItem('auth_token')

  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login')
  } else if (to.path === '/login' && isAuthenticated) {
    next('/')
  } else {
    next()
  }
})

export default router
