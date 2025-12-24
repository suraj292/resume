/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./index.html",
        "./resources/js/**/*.{vue,js,ts,jsx,tsx}",
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'sans-serif'],
                display: ['Plus Jakarta Sans', 'sans-serif'],
            },
            colors: {
                primary: { 50: '#eff6ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8' },
                slate: { 850: '#1e293b', 900: '#0f172a' }
            },
            animation: {
                'float': 'float 6s ease-in-out infinite',
                'blob': 'blob 7s infinite',
                'fade-in-up': 'fadeInUp 0.8s ease-out forwards',
                'slide-in-right': 'slideInRight 1s ease-out forwards',
            },
            keyframes: {
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-20px)' },
                },
                blob: {
                    '0%': { transform: 'translate(0px, 0px) scale(1)' },
                    '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                    '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                    '100%': { transform: 'translate(0px, 0px) scale(1)' },
                },
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideInRight: {
                    '0%': { opacity: '0', transform: 'translateX(50px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
                progressFill: {
                    to: { width: 'var(--w, 100%)' }
                }
            }
        },
    },
    plugins: [],
}