<script setup>
import { ref } from 'vue'
import Loader from '@/components/Loader.vue'
import { onMounted } from 'vue'
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import { useTripStore } from '@/stores/trip'
import { useLocationStore } from '@/stores/location'
import http from '@/helpers/http'
import { useRouter } from 'vue-router'

const title = ref('Waiting for ride request...')
const gMap = ref(null)
const trip = useTripStore()
const location = useLocationStore()
const router = useRouter()

const handleDeclineTrip = () => {
    trip.reset()
    title.value = 'Waiting for ride request...'
}

const handleAcceptTrip = () => {
    http().post(`/api/trip/${trip.id}/accept`, {
        driver_location: location.current.geometry
    })
        .then((response) => {
            location.$patch({
                destination: {
                    name: 'Passenger',
                    geometry: response.data.origin
                }
            })

            router.push({
                name: 'driving'
            })
        })
        .catch((error) => {
            console.error(error)
        })
}

onMounted(async () => {
    await location.updateCurrentLocation()

    let echo = new Echo({
        //spec that here (on frontend) we are using pusher to broadcast, unlike websockets which was used on the backend
        broadcaster: 'pusher',
        //key val must match the the val of PUSHER_APP_KEY in your backend .env file
        key: 'myAppKey',
        cluster: 'mt1', //default
        //specify the websockets host-wh in this case is on same domain as this app
        wsHost: window.location.hostname,
        wsPort: 6001, //default websockets port
        forceTLS: false,
        disableStats: true,
        //enable info transfer over both websockets (ws) & secure web sockets (wss)
        //this allows us to deploy this feature on both http & https sites
        enabledTransports: ['ws', 'wss']
    })

    //Now you can subscribe to specifc channel & then listen for any particular event on that channel (there could be many events on a channel)
    //Here we subscribe to the channel 'drivers' that was broadcasted on by the 'backend/app/Events/TripStarted.php', then listen for 
    //a particular event 'TripStarted'. Note how in LV the convention is to name the event after the class where the channel is
    echo.channel('drivers')
        .listen('TripCreated', (e) => {
            title.value = 'Ride requested:'

            trip.$patch(e.trip);
            console.log('TripCreated', e);

            //gMap is initialized with null above, and there's a delay before the trip obj is patched, resulting in an 'gMap is null'
            //error when view is loaded. Solution is to intro a 2 secs delay before calling initMapFunctions()
            setTimeout(initMapDirections, 2000)
        })
})

const initMapDirections = () => {
    gMap.value.$mapPromise.then((mapObject) => {

        let originPoint = new google.maps.LatLng(trip.origin),
            destinationPoint = new google.maps.LatLng(trip.destination),
            directionsService = new google.maps.DirectionsService,
            directionsDisplay = new google.maps.DirectionsRenderer({
                map: mapObject
            })

        directionsService.route({
            origin: originPoint,
            destination: destinationPoint,
            avoidTolls: false,
            avoidHighways: false,
            travelMode: google.maps.TravelMode.DRIVING
        }, (res, status) => {
            if (status === google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(res)
            } else {
                console.error(status)
            }
        })
    })
}
</script>

<template>
    <div class="pt-16">
        <h1 class="text-3xl font-semibold mb-4">{{ title }}</h1>
        <div v-if="!trip.id" class="mt-8 flex justify-center">
            <Loader />
        </div>
        <div v-else>
            <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
                <div class="bg-white px-4 py-5 sm:p-6">
                    <div>
                        <GMapMap :zoom="14" :center="trip.destination" ref="gMap"
                            style="width:100%; height: 256px;"></GMapMap>
                    </div>
                    <div class="mt-2">
                        <p class="text-xl">Going to <strong>{{ trip.destination_name }}</strong></p>
                    </div>
                </div>
                <div class="flex justify-between bg-gray-50 px-4 py-3 text-right sm:px-6">
                    <button
                        @click="handleDeclineTrip"
                        class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium 
                            text-white shadow-sm hover:bg-gray-600 focus:outline-none">Decline</button>
                    <button
                        @click="handleAcceptTrip"
                        class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium 
                            text-white shadow-sm hover:bg-gray-600 focus:outline-none">Accept</button>
                </div>
            </div>
        </div>
    </div>
</template>