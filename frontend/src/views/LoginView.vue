<script setup>
import { vMaska } from 'maska'
import { reactive, ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router';

//In this login vue, we need to be able to redirect the user to maybe a dashboad/landing
//after they successfully login-hence we need the Router
const router = useRouter()

const credentials = reactive({
  phone: null,
  login_code: null
})

const waitingOnVerification = ref(false);
//const loginHeadingText = 'Enter your phone number';

onMounted(() => {
  if (localStorage.getItem('token')) {
      router.push({
          name: 'landing'
      })
  }
})

const getFormattedCredentials = () => {
  return {
      //clean the phone number by replacing all double blank spaces with one space, remove all parentheses & remove all hyphens 
      phone: credentials.phone.replaceAll(' ', '').replace('(', '').replace(')', '').replace('-', ''),
      login_code: credentials.login_code
  }
}

const handleLogin = () => {
  //axios's post() needs 2 params-the URL to reigger this call, & the data to submit (array or json obj needed)
  axios.post('http://127.0.0.1/api/login', getFormattedCredentials())
      .then((response) => {
          console.log(response.data)
          waitingOnVerification.value = true
      })
      .catch((error) => {
          console.error(error)
          alert(error.response.data.message)
      })
}

const handleVerification = () => {
  axios.post('http://127.0.0.1/api/login/verify', getFormattedCredentials())
      .then((response) => {
          console.log(response.data) // auth token
          localStorage.setItem('token', response.data)
          //redirect user after successful authentication to a landng (page) route
          router.push({
              name: 'landing'
          })
      })
      .catch((error) => {
          console.error(error)
          alert(error.response.data.message)
      })
}
</script>


<template>
  <div class="pt-16">
      <!--<h1 class="text-3xl font-semibold mb-4">loginHeadingText</h1>-->
      <h1 class="text-3xl font-semibold mb-4">Enter your phone number</h1>
      <form v-if="!waitingOnVerification" action="#" @submit.prevent="handleLogin">
          <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
              <div class="bg-white px-4 py-5 sm:p-6">
                  <div>
                      <!--Using "# (###) ###-####" with the maska package reads as: 1 digit, 3 digits, a space then 3 digits, a hyphen & 3 digits-->
                      <!--This is the format that maska will enforce in validation on the input for you-->
                      <!--It will auto insert the spaces, brackets, hyphen for the user, & because it expects numbers, -->
                      <!--It will not accepts non-numeric characters-->
                      <input type="text" v-maska data-maska="# (###) ###-####" v-model="credentials.phone" name="phone" id="phone" placeholder="1 (234) 567-8910"
                          class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-black focus:outline-none">
                  </div>
              </div>
              <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                  <button type="submit" @submit.prevent="handleLogin"
                      class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm 
                        hover:bg-gray-600 focus:outline-none">
                      Continue
                    </button>
              </div>
          </div>
      </form>

      <form v-else action="#" @submit.prevent="handleVerification">
          <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
              <div class="bg-white px-4 py-5 sm:p-6">
                  <div>
                      <!---Note that the mask for this field unlike the 'phone' field in the above form is only 6 xters.-->
                      <!---Thats because this time, we are expecting a 6 digit code-->
                      <input type="text" v-maska data-maska="######" v-model="credentials.login_code" name="login_code" id="login_code" placeholder="123456"
                          class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-black focus:outline-none">
                  </div>
              </div>
              <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                  <button type="submit" @submit.prevent="handleVerification"
                      class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm 
                        hover:bg-gray-600 focus:outline-none">
                      Verify
                    </button>
              </div>
          </div>
      </form>
  </div>
</template>
