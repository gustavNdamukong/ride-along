import { createRouter, createWebHistory } from 'vue-router'
//import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import LandingView from '../views/LandingView.vue'
import LocationView from '@/views/LocationView.vue'
import MapView from '@/views/MapView.vue'
import axios from 'axios'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'login',
      //component: HomeView
      component: LoginView
    },
    {
      path: '/landing',
      name: 'landing',
      component: LandingView
    },
    {
      path: '/location',
      name: 'location',
      component: LocationView
    },
    {
      path: '/trip',
      name: 'map',
      component: MapView
    },
  ]
})

router.beforeEach((to, from) => {
  //let them through if the target route is login
  //else make sure they are logged in
  if (to.name === 'login')
  { //alert('GOING TO LOGIN'); 
    return true;
  }

  //console.log('VALUES OF TO & FROM: '+to, from); /////
  //alert('TO IS NOT LOGIN, '+to.name+' - '+from.name);////////

  if (!localStorage.getItem('token'))
  { 
    return {
      name: 'login'
    }
  } 

  //check with the backend for the authenticity of their token
  //coz they could have made it up themselves
  checkTokenAuthenticity();
  
})

//If the backend returns a user with that token, then do nothing, otherwise, redirect them to the login view
const checkTokenAuthenticity = () => {
  axios.get('http://127.0.0.1/api/user', {
    headers: {
      //This token will be passed into the get request object, to be picked up on the backend
      Authorization: `Bearer ${localStorage.getItem('token')}`
    }
  })
  .then((response) => {})
  .catch((error) => {
    localStorage.removeItem('token')
    router.push({
      name: 'login'
    })
  })
}

export default router
