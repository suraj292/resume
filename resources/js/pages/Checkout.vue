<template>
  <div class="min-h-screen flex flex-col bg-gray-50">
    <!-- Header -->
    <header class="glass-header sticky top-0 z-50">
      <div class="container mx-auto px-6 h-16 flex items-center justify-between">
        <!-- Logo -->
        <router-link to="/" class="flex items-center gap-2.5 group">
          <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-600 to-blue-600 flex items-center justify-center text-white text-sm shadow-md">
            <i class="fa-solid fa-file-contract"></i>
          </div>
          <span class="text-lg font-display font-bold text-slate-800 tracking-tight">
            Resume<span class="text-indigo-600">AI</span>
          </span>
        </router-link>

        <!-- Secure Indicator -->
        <div class="flex items-center gap-2 text-green-700 bg-green-50 px-3 py-1.5 rounded-full border border-green-100 text-xs font-bold">
          <i class="fa-solid fa-lock"></i>
          <span>SSL Secure Checkout</span>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-6 py-12 max-w-6xl">
      <!-- Hero Title -->
      <div class="text-center mb-12 animate-fade-in">
        <h1 class="text-3xl font-display font-bold text-slate-900 mb-2">Complete Your Purchase</h1>
        <p class="text-slate-500">Unlock premium features and land your dream job faster.</p>
      </div>

      <div class="grid lg:grid-cols-12 gap-8 lg:gap-12">
        <!-- Left Column: Payment Details -->
        <div class="lg:col-span-7 space-y-8 animate-slide-up">
          <!-- Plan Details -->
          <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 relative overflow-hidden group">
            <div class="absolute top-0 right-0 bg-indigo-600 text-white text-[10px] font-bold px-3 py-1 rounded-bl-xl">SELECTED</div>
            
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
              <div>
                <h3 class="text-lg font-bold text-slate-900 mb-1">
                  {{ selectedPlan.name }} Plan <span class="text-slate-400 font-normal mx-2">|</span> {{ billingCycle }}
                </h3>
                <p class="text-sm text-slate-500">{{ selectedPlan.description }}</p>
              </div>
              <router-link to="/pricing" class="text-sm font-bold text-indigo-600 hover:text-indigo-700 hover:underline">
                Change Plan
              </router-link>
            </div>
          </div>

          <!-- User Details -->
          <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
            <h3 class="font-bold text-slate-900 mb-6 flex items-center gap-2">
              <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center text-xs">1</span>
              Billing Information
            </h3>
            
            <form class="space-y-6" @submit.prevent>
              <div class="grid md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Full Name</label>
                  <input 
                    type="text" 
                    v-model="user.name" 
                    class="form-input w-full px-4 py-3 rounded-xl border border-slate-200 text-slate-900 text-sm focus:outline-none focus:border-indigo-500 bg-slate-50" 
                    readonly
                  >
                </div>
                <div>
                  <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Email Address</label>
                  <input 
                    type="email" 
                    v-model="user.email" 
                    class="form-input w-full px-4 py-3 rounded-xl border border-slate-200 text-slate-900 text-sm focus:outline-none focus:border-indigo-500 bg-slate-50" 
                    readonly
                  >
                </div>
              </div>
              
              <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">
                  GST Number <span class="text-slate-400 font-normal lowercase">(optional)</span>
                </label>
                <input 
                  type="text" 
                  v-model="gstNumber"
                  placeholder="Enter GST Number for business invoice" 
                  class="form-input w-full px-4 py-3 rounded-xl border border-slate-200 text-slate-900 text-sm focus:outline-none focus:border-indigo-500 placeholder-slate-400"
                >
              </div>
            </form>
          </div>

          <!-- Payment Method -->
          <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 payment-card cursor-pointer group">
            <h3 class="font-bold text-slate-900 mb-6 flex items-center gap-2">
              <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center text-xs">2</span>
              Payment Method
            </h3>

            <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-6 text-center">
              <div class="flex justify-center items-center gap-4 mb-4 grayscale opacity-70 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-300">
                <i class="fa-brands fa-google-pay text-2xl"></i>
                <i class="fa-brands fa-cc-visa text-2xl"></i>
                <i class="fa-brands fa-cc-mastercard text-2xl"></i>
                <i class="fa-solid fa-building-columns text-xl"></i>
              </div>
              <h4 class="font-bold text-indigo-900 mb-1">Pay Securely with Razorpay</h4>
              <p class="text-xs text-indigo-600 mb-4">Supports UPI, Credit/Debit Cards, Net Banking, and Wallets</p>
              
              <button 
                @click="handlePayment" 
                :disabled="processing"
                class="w-full sm:w-auto px-8 py-3 bg-indigo-600 text-white font-bold rounded-xl shadow-lg hover:bg-indigo-700 hover:shadow-indigo-500/30 transition-all transform active:scale-95 flex items-center justify-center gap-2 mx-auto disabled:opacity-75 disabled:cursor-not-allowed"
              >
                <i v-if="!processing" class="fa-solid fa-lock text-xs"></i>
                <i v-else class="fa-solid fa-circle-notch fa-spin"></i>
                {{ processing ? 'Processing...' : 'Proceed to Pay' }}
              </button>
            </div>
            
            <p class="text-center text-[10px] text-slate-400 mt-4">
              <i class="fa-solid fa-shield-halved text-green-500 mr-1"></i> Your payment information is encrypted and secure.
            </p>
          </div>
        </div>

        <!-- Right Column: Order Summary -->
        <div class="lg:col-span-5 space-y-6 animate-slide-up" style="animation-delay: 0.2s;">
          <div class="bg-white p-8 rounded-2xl shadow-lg border border-slate-100 sticky top-24">
            <h3 class="font-bold text-slate-900 text-lg mb-6 border-b border-slate-100 pb-4">Order Summary</h3>
            
            <div class="space-y-4 mb-6">
              <div class="flex justify-between text-sm text-slate-600">
                <span>{{ selectedPlan.name }} Plan ({{ billingCycle }})</span>
                <span class="font-medium text-slate-900">{{ currencySymbol }} {{ planPrice }}</span>
              </div>
              <div class="flex justify-between text-sm text-slate-600">
                <span>GST (18%)</span>
                <span class="font-medium text-slate-900">{{ currencySymbol }} {{ gstAmount }}</span>
              </div>
              
              <!-- Discount Row -->
              <div v-if="discountApplied" class="flex justify-between text-sm text-green-600 font-bold coupon-success">
                <span>Coupon Discount ({{ discountPercentage }}%)</span>
                <span>-{{ currencySymbol}} {{ discountAmount }}</span>
              </div>
            </div>

            <!-- Coupon Code -->
            <div class="mb-6">
              <div class="relative">
                <input 
                  type="text" 
                  v-model="couponCode"
                  placeholder="Have a coupon code?" 
                  class="form-input w-full pl-4 pr-24 py-2.5 rounded-lg border border-slate-200 text-sm focus:outline-none focus:border-indigo-500 uppercase"
                  :class="{ 'border-green-500': couponValid, 'border-red-500': couponError }"
                >
                <button 
                  @click="applyCoupon" 
                  class="absolute right-1.5 top-1.5 bottom-1.5 px-4 bg-slate-900 text-white text-xs font-bold rounded-md hover:bg-slate-800 transition-colors"
                >
                  Apply
                </button>
              </div>
              <p v-if="couponMessage" class="text-xs mt-2" :class="couponValid ? 'text-green-600 font-bold' : 'text-red-500'">
                {{ couponMessage }}
              </p>
            </div>

            <div class="border-t border-slate-100 pt-4 mb-6">
              <div class="flex justify-between items-end">
                <span class="text-slate-500 font-medium">Total Payable</span>
                <div class="text-right">
                  <span class="block text-3xl font-display font-bold text-slate-900 transition-all" :class="{ 'text-green-600 scale-110': priceAnimating }">
                    {{ currencySymbol }} {{ totalPrice }}
                  </span>
                  <span class="text-[10px] text-slate-400">Includes all taxes</span>
                </div>
              </div>
            </div>

            <!-- Trust Badges -->
            <div class="grid grid-cols-2 gap-3">
              <div class="flex items-center gap-2 bg-slate-50 p-2 rounded-lg border border-slate-100">
                <i class="fa-solid fa-award text-indigo-500"></i>
                <div class="text-[10px] leading-tight text-slate-600">
                  <span class="font-bold block text-slate-800">Money Back</span>
                  7-day guarantee
                </div>
              </div>
              <div class="flex items-center gap-2 bg-slate-50 p-2 rounded-lg border border-slate-100">
                <i class="fa-solid fa-headset text-indigo-500"></i>
                <div class="text-[10px] leading-tight text-slate-600">
                  <span class="font-bold block text-slate-800">Support</span>
                  24/7 Assistance
                </div>
              </div>
            </div>

            <!-- Footer Note -->
            <p class="text-[10px] text-slate-400 text-center mt-6 leading-relaxed">
              By proceeding, you agree to our 
              <a href="#" class="underline hover:text-slate-600">Terms of Service</a> and 
              <a href="#" class="underline hover:text-slate-600">Privacy Policy</a>. 
              Subscriptions auto-renew but can be cancelled anytime.
            </p>
          </div>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-950 text-slate-400 py-8 border-t border-slate-800 text-sm mt-auto">
      <div class="container mx-auto px-6 text-center flex flex-col md:flex-row justify-between items-center gap-4">
        <p>&copy; 2023 ResumeAI. All rights reserved.</p>
        <div class="flex gap-4 text-xl opacity-50">
          <i class="fa-brands fa-cc-visa"></i>
          <i class="fa-brands fa-cc-mastercard"></i>
          <i class="fa-brands fa-cc-amex"></i>
          <i class="fa-solid fa-building-columns"></i>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

// Data
const selectedPlan = ref({
  name: 'Pro',
  description: 'Unlimited resumes, AI optimization, PDF downloads',
  monthly_price: 499,
  yearly_price: 4999
})

const billingCycle = ref('Monthly')
const gstNumber = ref('')
const couponCode = ref('')
const couponMessage = ref('')
const couponValid = ref(false)
const couponError = ref(false)
const discountApplied = ref(false)
const discountPercentage = ref(0)
const processing = ref(false)
const priceAnimating = ref(false)

// Computed
const user = computed(() => authStore.currentUser ||  { name: 'Guest', email: '' })
const currencySymbol = computed(() => authStore.user?.currency === 'INR' ? '₹' : '$')

const planPrice = computed(() => {
  return billingCycle.value === 'Monthly' 
    ? selectedPlan.value.monthly_price 
    : selectedPlan.value.yearly_price
})

const gstAmount = computed(() => {
  return (planPrice.value * 0.18).toFixed(2)
})

const subtotal = computed(() => {
  return parseFloat(planPrice.value) + parseFloat(gstAmount.value)
})

const discountAmount = computed(() => {
  if (!discountApplied.value) return 0
  return (subtotal.value * (discountPercentage.value / 100)).toFixed(2)
})

const totalPrice = computed(() => {
  return (subtotal.value - parseFloat(discountAmount.value)).toFixed(2)
})

// Methods
const applyCoupon = () => {
  const code = couponCode.value.trim().toUpperCase()
  
  // Reset
  couponMessage.value = ''
  couponValid.value = false
  couponError.value = false
  discountApplied.value = false

  if (!code) return

  // Check valid coupons
  if (code === 'SAVE20') {
    couponMessage.value = `Coupon applied successfully! You saved ${currencySymbol.value} ${discountAmount.value}`
    couponValid.value = true
    discountApplied.value = true
    discountPercentage.value = 20

    // Animate price change
    priceAnimating.value = true
    setTimeout(() => {
      priceAnimating.value = false
    }, 300)
  } else {
    couponMessage.value = 'Invalid coupon code. Please try again.'
    couponError.value = true
  }
}

const handlePayment = () => {
  processing.value = true

  // TODO: Integrate with Razorpay
  setTimeout(() => {
    alert('Payment integration coming soon!')
    processing.value = false
  }, 1500)
}

// Lifecycle
onMounted(() => {
  // Get plan details from URL query params
  const urlParams = new URLSearchParams(window.location.search)
  
  if (urlParams.get('plan')) {
    // Fetch plan details based on plan parameter
    // selectedPlan.value = ...
  }
  if (urlParams.get('billing')) {
    billingCycle.value = urlParams.get('billing') === 'yearly' ? 'Yearly' : 'Monthly'
  }
})
</script>

<style scoped>
.glass-header {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid rgba(226, 232, 240, 0.8);
}

/* Form Input Focus Animation */
.form-input {
  transition: all 0.2s ease;
}

.form-input:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
}

/* Payment Card Hover */
.payment-card {
  transition: all 0.3s ease;
}

.payment-card:hover {
  transform: translateY(-2px);
  border-color: #6366f1;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

/* Animations */
.animate-fade-in {
  animation: fadeIn 0.5s ease-out forwards;
}

.animate-slide-up {
  animation: slideUp 0.5s ease-out forwards;
}

@keyframes fadeIn {
  0% { opacity: 0; }
  100% { opacity: 1; }
}

@keyframes slideUp {
  0% { transform: translateY(10px); opacity: 0; }
  100% { transform: translateY(0); opacity: 1; }
}

.coupon-success {
  animation: fadeIn 0.4s ease-out;
}
</style>
