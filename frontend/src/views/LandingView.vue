<script setup>
import { useRouter } from 'vue-router'
import http from '@/helpers/http'

const router = useRouter()

const handleStartDriving = () => {
  //check if current user is already registered as a driver. The 'api/driver' backend route
  //will return any driver with the details of the current (authenticated) user
  http().get('/api/driver')
      .then((response) => {
          if (response.data.driver) {
              router.push({
                  //already a driver (their details are already registered in the DB drivers table),so take them to standby 
                  //screen where they can view and choose existing ride requests from passenger users 
                  name: 'standby'
              })
          } else {
              router.push({
                  //new request to be a driver. This user has no details in the 'drivers' DB table, so show them
                  //a form to enter their vehicle details to register as a driver
                  name: 'driver'
              })
          }
      })
      .catch((error) => {
          console.error(error)
      })
}

const handleFindARide = () => {
  router.push({
      name: 'location'
  })
}
</script>

<template>
  <div class="pt-16">
      <h1 class="text-3xl font-semibold mb-4">Gustav</h1>
      <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
          <div class="bg-white px-4 py-5 sm:p-6">
              <div class="flex justify-between">
                  <button
                      @click="handleStartDriving"
                      class="rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none">
                      Start Driving
                  </button>

                  <button
                      @click="handleFindARide"
                      class="rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none">
                      Find A Ride
                  </button>
              </div>
          </div>
      </div>
  </div>
</template>
