import axios from "axios"

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
