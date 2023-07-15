/** @type {import('tailwindcss').Config} */
module.exports = {
  //content will be looked into for tailwind classes to apply to the project
  content: [
    "./index.html",
    //scan the src...dir and apply tailwind to any files that end in '.vue' or '.js'
    "./src/**/*.{vue, js}"
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}

