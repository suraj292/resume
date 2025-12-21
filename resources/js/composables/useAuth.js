import { ref, computed } from 'vue';
import api from '../plugins/axios';
import { useRouter } from 'vue-router';

const user = ref(null);
const token = ref(localStorage.getItem('auth_token'));

export function useAuth() {
    const router = useRouter();

    const isAuthenticated = computed(() => !!token.value && !!user.value);

    // Load user from localStorage on init
    if (token.value && !user.value) {
        const storedUser = localStorage.getItem('user');
        if (storedUser) {
            user.value = JSON.parse(storedUser);
        }
    }

    const setAuth = (authToken, userData) => {
        token.value = authToken;
        user.value = userData;
        localStorage.setItem('auth_token', authToken);
        localStorage.setItem('user', JSON.stringify(userData));
    };

    const clearAuth = () => {
        token.value = null;
        user.value = null;
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
    };

    const register = async (data) => {
        try {
            const response = await api.post('/api/register', data);
            setAuth(response.data.token, response.data.user);
            return { success: true, data: response.data };
        } catch (error) {
            return {
                success: false,
                error: error.response?.data?.message || 'Registration failed',
                errors: error.response?.data?.errors || {}
            };
        }
    };

    const login = async (credentials) => {
        try {
            const response = await api.post('/api/login', credentials);
            setAuth(response.data.token, response.data.user);
            return { success: true, data: response.data };
        } catch (error) {
            return {
                success: false,
                error: error.response?.data?.message || 'Login failed',
                errors: error.response?.data?.errors || {}
            };
        }
    };

    const logout = async () => {
        try {
            await api.post('/api/logout');
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            clearAuth();
            router.push('/auth');
        }
    };

    const fetchUser = async () => {
        try {
            const response = await api.get('/api/me');
            user.value = response.data.user;
            localStorage.setItem('user', JSON.stringify(response.data.user));
            return { success: true, data: response.data.user };
        } catch (error) {
            clearAuth();
            return { success: false, error: error.response?.data?.message };
        }
    };

    const updateProfile = async (data) => {
        try {
            const response = await api.put('/api/profile', data);
            user.value = response.data.user;
            localStorage.setItem('user', JSON.stringify(response.data.user));
            return { success: true, data: response.data };
        } catch (error) {
            return {
                success: false,
                error: error.response?.data?.message || 'Update failed',
                errors: error.response?.data?.errors || {}
            };
        }
    };

    const updatePassword = async (data) => {
        try {
            const response = await api.put('/api/password', data);
            return { success: true, data: response.data };
        } catch (error) {
            return {
                success: false,
                error: error.response?.data?.message || 'Password update failed',
                errors: error.response?.data?.errors || {}
            };
        }
    };

    const uploadAvatar = async (file) => {
        try {
            const formData = new FormData();
            formData.append('avatar', file);
            const response = await api.post('/api/avatar', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            });
            user.value = response.data.user;
            localStorage.setItem('user', JSON.stringify(response.data.user));
            return { success: true, data: response.data };
        } catch (error) {
            return {
                success: false,
                error: error.response?.data?.message || 'Avatar upload failed',
                errors: error.response?.data?.errors || {}
            };
        }
    };

    const socialLogin = (provider) => {
        const width = 600;
        const height = 700;
        const left = window.screen.width / 2 - width / 2;
        const top = window.screen.height / 2 - height / 2;

        const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000';
        const url = `${apiUrl}/api/auth/${provider}/redirect`;

        window.open(
            url,
            'OAuth Login',
            `width=${width},height=${height},left=${left},top=${top}`
        );
    };

    return {
        user,
        token,
        isAuthenticated,
        register,
        login,
        logout,
        fetchUser,
        updateProfile,
        updatePassword,
        uploadAvatar,
        socialLogin,
    };
}
