<template>
    <div class="flex flex-col min-h-screen bg-slate-50">
        <!-- Header -->
        <header class="glass-header sticky top-0 z-50">
            <div class="container mx-auto px-6 h-16 flex items-center justify-between">
                <NuxtLink to="/" class="flex items-center gap-2.5 group">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-600 to-blue-600 flex items-center justify-center text-white text-sm shadow-md group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-file-contract"></i>
                    </div>
                    <span class="text-lg font-display font-bold text-slate-800 tracking-tight">
                        Resume<span class="text-indigo-600">AI</span>
                    </span>
                </NuxtLink>

                <nav class="hidden md:flex items-center gap-8">
                    <NuxtLink to="/builder" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors">Dashboard</NuxtLink>
                    <NuxtLink to="/builder" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors">Builder</NuxtLink>
                    <NuxtLink to="/ats-checker" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors">ATS Check</NuxtLink>
                    <NuxtLink to="/templates" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors">Templates</NuxtLink>
                    <NuxtLink to="/profile" class="text-sm font-medium text-indigo-600 bg-indigo-50 px-3 py-1 rounded-md">Profile</NuxtLink>
                </nav>

                <div class="flex items-center gap-4">
                    <div class="hidden md:block text-right">
                        <p class="text-xs font-bold text-slate-900">{{ currentUser?.name || 'Guest' }}</p>
                        <p class="text-[10px] text-slate-500">{{ currentUser?.plan?.name || 'Free Plan' }}</p>
                    </div>
                    <div class="relative group">
                        <img :src="currentUser?.avatar || 'https://ui-avatars.com/api/?name=User&background=6366f1&color=fff&size=128'" class="w-9 h-9 rounded-full border-2 border-white shadow-sm hover:border-indigo-200 transition-all cursor-pointer">
                        <!-- Dropdown -->
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-slate-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                            <div class="p-2">
                                <button @click="handleLogout" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg flex items-center gap-2">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                    Logout
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow container mx-auto px-6 py-10 max-w-5xl">
            <div class="grid lg:grid-cols-12 gap-8">
                
                <!-- Left Sidebar -->
                <div class="lg:col-span-3 space-y-6">
                    
                    <!-- Profile Overview Card -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 text-center animate-slide-up">
                        <div class="relative w-24 h-24 mx-auto mb-4 group cursor-pointer">
                            <img :src="user.avatar" class="w-full h-full rounded-full border-4 border-slate-50 shadow-inner" id="avatar-preview">
                            <div class="absolute inset-0 bg-slate-900/50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white backdrop-blur-[2px]">
                                <i class="fa-solid fa-camera"></i>
                            </div>
                            <input type="file" class="absolute inset-0 opacity-0 cursor-pointer" @change="previewAvatar">
                        </div>
                        <h2 class="text-lg font-bold text-slate-900">{{ user.name }}</h2>
                        <p class="text-xs text-slate-500 mb-4">{{ user.email }}</p>
                        
                        <div class="inline-block px-3 py-1 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-full mb-4">{{ user.plan?.name || 'Free Plan' }}</div>
                        
                        <button class="w-full py-2 bg-slate-900 text-white text-xs font-bold rounded-lg hover:bg-slate-800 transition-colors">
                            Upgrade to Premium
                        </button>
                    </div>

                    <!-- Side Navigation -->
                    <nav class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden animate-slide-up" style="animation-delay: 0.1s;">
                        <button 
                            v-for="tab in tabs" 
                            :key="tab.id"
                            @click="activeTab = tab.id"
                            :class="[
                                'w-full text-left px-5 py-3.5 text-sm font-medium flex items-center gap-3 border-l-4 transition-all',
                                activeTab === tab.id 
                                    ? 'border-indigo-600 bg-indigo-50/50 text-indigo-700' 
                                    : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                            ]"
                        >
                            <i :class="tab.icon + ' w-5'"></i> {{ tab.label }}
                        </button>
                    </nav>

                    <!-- Account Stats -->
                    <div class="bg-slate-900 text-white p-6 rounded-2xl shadow-lg animate-slide-up" style="animation-delay: 0.2s;">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">Account Stats</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-slate-300">Resumes Created</span>
                                <span class="font-bold">{{ stats.resumesCreated }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-slate-300">ATS Scans</span>
                                <span class="font-bold">{{ stats.atsScans }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-slate-300">Member Since</span>
                                <span class="font-bold text-xs">{{ stats.memberSince }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Content Area -->
                <div class="lg:col-span-9">
                    
                    <!-- Personal Info Tab -->
                    <div v-show="activeTab === 'personal'" class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 h-full">
                        <div class="mb-8 border-b border-slate-100 pb-6">
                            <h2 class="text-xl font-bold text-slate-900">Personal Information</h2>
                            <p class="text-sm text-slate-500">Update your personal details and contact information.</p>
                        </div>

                        <form @submit.prevent="handleSave" class="space-y-6 max-w-2xl">
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Full Name</label>
                                    <input v-model="formData.name" type="text" class="form-input w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Job Title</label>
                                    <input v-model="formData.jobTitle" type="text" class="form-input w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Email Address</label>
                                <input v-model="formData.email" type="email" class="form-input w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                            </div>

                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Phone Number</label>
                                    <input v-model="formData.phone" type="tel" class="form-input w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Location</label>
                                    <input v-model="formData.location" type="text" class="form-input w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                                </div>
                            </div>

                            <div class="pt-4">
                                <button type="submit" :disabled="saving" :class="['px-6 py-2.5 text-white text-sm font-bold rounded-xl shadow-lg transition-all transform active:scale-95 flex items-center gap-2', saveButtonClass]">
                                    <i v-if="saving" class="fa-solid fa-circle-notch fa-spin"></i>
                                    <i v-else-if="saved" class="fa-solid fa-check"></i>
                                    <span>{{ saveButtonText }}</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Security Tab -->
                    <div v-show="activeTab === 'security'" class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 h-full">
                        <div class="mb-8 border-b border-slate-100 pb-6">
                            <h2 class="text-xl font-bold text-slate-900">Account Security</h2>
                            <p class="text-sm text-slate-500">Manage your password and security settings.</p>
                        </div>

                        <form @submit.prevent="handleSave" class="space-y-6 max-w-xl">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Current Password</label>
                                <input v-model="securityData.currentPassword" type="password" placeholder="••••••••" class="form-input w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:outline-none focus:border-indigo-500">
                            </div>
                            
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">New Password</label>
                                    <input v-model="securityData.newPassword" type="password" placeholder="New password" class="form-input w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:outline-none focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Confirm Password</label>
                                    <input v-model="securityData.confirmPassword" type="password" placeholder="Confirm password" class="form-input w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:outline-none focus:border-indigo-500">
                                </div>
                            </div>

                            <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 flex items-start gap-3">
                                <i class="fa-solid fa-circle-info text-blue-500 mt-0.5"></i>
                                <div class="text-xs text-blue-800">
                                    <p class="font-bold mb-1">Password Requirements</p>
                                    <ul class="list-disc pl-4 space-y-1">
                                        <li>Minimum 8 characters long</li>
                                        <li>At least one uppercase character</li>
                                        <li>At least one number or symbol</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="pt-4 flex items-center justify-between">
                                <button type="submit" :disabled="saving" :class="['px-6 py-2.5 text-white text-sm font-bold rounded-xl shadow-lg transition-all transform active:scale-95', saveButtonClass]">
                                    <i v-if="saving" class="fa-solid fa-circle-notch fa-spin mr-2"></i>
                                    <i v-else-if="saved" class="fa-solid fa-check mr-2"></i>
                                    {{ saving ? 'Updating...' : saved ? 'Updated!' : 'Update Password' }}
                                </button>
                                <span class="text-xs text-slate-400">Last updated: 3 months ago</span>
                            </div>
                        </form>

                        <!-- Danger Zone -->
                        <div class="mt-12 pt-8 border-t border-slate-100">
                            <h3 class="text-red-600 font-bold mb-4">Danger Zone</h3>
                            <div class="flex items-center justify-between p-4 bg-red-50 rounded-xl border border-red-100">
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900">Delete Account</h4>
                                    <p class="text-xs text-slate-500">Permanently remove your account and all data.</p>
                                </div>
                                <button class="px-4 py-2 bg-white border border-red-200 text-red-600 text-xs font-bold rounded-lg hover:bg-red-50 transition-colors">Delete</button>
                            </div>
                        </div>
                    </div>

                    <!-- Preferences Tab -->
                    <div v-show="activeTab === 'preferences'" class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 h-full">
                        <div class="mb-8 border-b border-slate-100 pb-6">
                            <h2 class="text-xl font-bold text-slate-900">Preferences</h2>
                            <p class="text-sm text-slate-500">Customize your experience and notification settings.</p>
                        </div>

                        <div class="space-y-8">
                            <!-- Notifications -->
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 mb-4 uppercase tracking-wide">Email Notifications</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-slate-800">Job Alerts</p>
                                            <p class="text-xs text-slate-500">Get notified when new jobs match your resume.</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input v-model="preferences.jobAlerts" type="checkbox" class="sr-only peer">
                                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        </label>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-slate-800">Product Updates</p>
                                            <p class="text-xs text-slate-500">Receive news about new features and templates.</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input v-model="preferences.productUpdates" type="checkbox" class="sr-only peer">
                                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="h-px bg-slate-100 w-full"></div>

                            <!-- Resume Defaults -->
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 mb-4 uppercase tracking-wide">Resume Defaults</h3>
                                <div class="grid md:grid-cols-2 gap-6 max-w-2xl">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Default Narrative Tone</label>
                                        <select v-model="preferences.narrativeTone" class="form-input w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:outline-none focus:border-indigo-500">
                                            <option>Professional</option>
                                            <option>Confident</option>
                                            <option>Technical</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Date Format</label>
                                        <select v-model="preferences.dateFormat" class="form-input w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:outline-none focus:border-indigo-500">
                                            <option>MM/YYYY</option>
                                            <option>Month Year</option>
                                            <option>YYYY-MM-DD</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="pt-4">
                                <button @click="handleSave" :disabled="saving" :class="['px-6 py-2.5 text-white text-sm font-bold rounded-xl shadow-lg transition-colors', saveButtonClass]">
                                    {{ saving ? 'Saving...' : saved ? 'Saved!' : 'Save Preferences' }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Subscription Tab -->
                    <div v-show="activeTab === 'subscription'" class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 h-full">
                        <div class="mb-8 border-b border-slate-100 pb-6">
                            <h2 class="text-xl font-bold text-slate-900">Subscription & Billing</h2>
                            <p class="text-sm text-slate-500">Manage your plan and payment methods.</p>
                        </div>

                        <!-- Current Plan -->
                        <div class="bg-indigo-50 rounded-2xl p-6 border border-indigo-100 flex flex-col md:flex-row items-center justify-between mb-8">
                            <div>
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-bold text-indigo-900">{{ subscription.plan }}</h3>
                                    <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold uppercase rounded-full">{{ subscription.status }}</span>
                                </div>
                                <p class="text-sm text-indigo-700 mb-1">Your next billing date is <span class="font-bold">{{ subscription.nextBilling }}</span></p>
                                <p class="text-xs text-indigo-500">{{ subscription.paymentMethod }}</p>
                            </div>
                            <div class="mt-4 md:mt-0 flex gap-3">
                                <button class="px-4 py-2 bg-white text-indigo-600 text-xs font-bold rounded-lg border border-indigo-200 hover:bg-indigo-50 transition-colors">Manage Billing</button>
                                <button class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg shadow-md hover:bg-indigo-500 transition-colors">Upgrade Plan</button>
                            </div>
                        </div>

                        <!-- Billing History -->
                        <div>
                            <h4 class="text-sm font-bold text-slate-900 mb-4">Billing History</h4>
                            <div class="border border-slate-200 rounded-xl overflow-hidden">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-slate-50 text-slate-500 border-b border-slate-200">
                                        <tr>
                                            <th class="px-6 py-3 font-medium">Date</th>
                                            <th class="px-6 py-3 font-medium">Amount</th>
                                            <th class="px-6 py-3 font-medium">Status</th>
                                            <th class="px-6 py-3 font-medium text-right">Invoice</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <tr v-for="invoice in billingHistory" :key="invoice.id" class="hover:bg-slate-50 transition-colors">
                                            <td class="px-6 py-4 text-slate-700">{{ invoice.date }}</td>
                                            <td class="px-6 py-4 text-slate-700">{{ invoice.amount }}</td>
                                            <td class="px-6 py-4">
                                                <span class="text-green-600 font-bold text-xs bg-green-50 px-2 py-1 rounded-full">{{ invoice.status }}</span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <a href="#" class="text-indigo-600 hover:underline">Download</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-slate-950 text-slate-400 py-8 border-t border-slate-800 text-sm mt-auto">
            <div class="container mx-auto px-6 text-center">
                <p>&copy; 2023 ResumeAI. All rights reserved.</p>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
// TODO: Implement auth composable or store


const currentUser = computed(() => authStore.currentUser)

const activeTab = ref('personal')
const saving = ref(false)
const saved = ref(false)

// User data - will be populated from currentUser
const user = computed(() => currentUser.value || {
    name: 'Loading...',
    email: '',
    plan: 'Free Plan',
    avatar: 'https://ui-avatars.com/api/?name=User&background=6366f1&color=fff&size=128'
})

const formData = ref({
    name: '',
    jobTitle: '',
    email: '',
    phone: '',
    location: ''
})

const securityData = ref({
    currentPassword: '',
    newPassword: '',
    confirmPassword: ''
})

const preferences = ref({
    jobAlerts: true,
    productUpdates: false,
    narrativeTone: 'Professional',
    dateFormat: 'MM/YYYY'
})

const subscription = ref({
    plan: 'Pro Plan',
    status: 'Active',
    nextBilling: 'Nov 24, 2023',
    paymentMethod: 'Visa ending in •••• 4242'
})

const billingHistory = ref([
    { id: 1, date: 'Oct 24, 2023', amount: '$19.00', status: 'Paid' },
    { id: 2, date: 'Sep 24, 2023', amount: '$19.00', status: 'Paid' }
])

const stats = ref({
    resumesCreated: 12,
    atsScans: 48,
    memberSince: 'Oct 2023'
})

const tabs = [
    { id: 'personal', label: 'Personal Info', icon: 'fa-regular fa-user' },
    { id: 'security', label: 'Security', icon: 'fa-solid fa-shield-halved' },
    { id: 'preferences', label: 'Preferences', icon: 'fa-solid fa-sliders' },
    { id: 'subscription', label: 'Subscription', icon: 'fa-regular fa-credit-card' }
]

const saveButtonText = computed(() => {
    if (saving.value) return 'Saving...'
    if (saved.value) return 'Saved!'
    return 'Save Changes'
})

const saveButtonClass = computed(() => {
    if (saving.value) return 'bg-slate-900 opacity-75 cursor-not-allowed'
    if (saved.value) return 'bg-green-600'
    return activeTab.value === 'security' ? 'bg-slate-900 hover:bg-slate-800' : 'bg-indigo-600 hover:bg-indigo-500'
})

const previewAvatar = (event) => {
    const file = event.target.files[0]
    if (file) {
        const reader = new FileReader()
        reader.onload = (e) => {
            if (currentUser.value) {
                currentUser.value.avatar = e.target.result
            }
        }
        reader.readAsDataURL(file)
    }
}

const handleSave = async () => {
    saving.value = true
    saved.value = false

    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000))

    saving.value = false
    saved.value = true

    // Reset saved state after 2 seconds
    setTimeout(() => {
        saved.value = false
    }, 2000)
}

const handleLogout = async () => {
  await authStore.logout()
}

// Load user on mount
onMounted(async () => {
  await authStore.fetchUser()
  
  // Populate form with user data
  if (currentUser.value) {
    formData.value = {
      name: currentUser.value.name || '',
      jobTitle: currentUser.value.job_title || '',
      email: currentUser.value.email || '',
      phone: currentUser.value.phone || '',
      location: currentUser.value.location || ''
    }
  }
})
</script>

<style scoped>
.glass-header {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid rgba(226, 232, 240, 0.8);
}

.form-input {
    transition: all 0.2s ease;
}

.form-input:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
}

@keyframes slideUp {
    0% {
        transform: translateY(10px);
        opacity: 0;
    }
    100% {
        transform: translateY(0);
        opacity: 1;
    }
}

.animate-slide-up {
    animation: slideUp 0.5s ease-out forwards;
}
</style>
