import axios from "axios"

/**
 * 
 * This code is a generic single source of truth, a callable func that will be called by any code in your app 
 * making an HTTP request. Because for every request, the end URL will be different, you would modify the URL 
 * depending on any HTTP request being made rather than doing so in the script of all those individual requests. 
 * 
 * The second reason for having a central function for requests is the authentication via headers that need to 
 * be done with each call. It's best to do that in one place too.
 */
const http = () => { 
    let options = {
       ///// baseURL: 'http://localhost',
        baseURL: 'http://127.0.0.1',
        headers: {}
    }

    //we add Authorization on the header only if there is a token in localStorage
    //this is coz not all requests need the token authentication
    if (localStorage.getItem('token')) {
        options.headers.Authorization = `Bearer ${localStorage.getItem('token')}`
    }

    return axios.create(options)
}

export default http
