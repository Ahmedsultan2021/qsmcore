<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'

const props = defineProps({
    blogPosts: {
        type: Array,
        default: () => []
    }
})

const activeFeature = ref(0)
const activeTestimonial = ref(0)
const isScrolled = ref(false)
const mobileMenuOpen = ref(false)

const features = [
    {
        icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
        title: 'Multi-Industry Support',
        description: 'Manage multiple industries with organized sectors and companies. Scale your operations seamlessly across different business units.',
        color: 'from-blue-600 to-indigo-600',
        bgColor: 'from-blue-100 to-indigo-100'
    },
    {
        icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        title: 'Incident Management',
        description: 'Track and manage incidents with comprehensive reporting, real-time updates, and automated corrective action workflows.',
        color: 'from-green-600 to-emerald-600',
        bgColor: 'from-green-100 to-emerald-100'
    },
    {
        icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
        title: 'Role-Based Access',
        description: 'Flexible permissions system with granular role management. Control access and ensure security across all organizational levels.',
        color: 'from-purple-600 to-pink-600',
        bgColor: 'from-purple-100 to-pink-100'
    },
    {
        icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        title: 'Analytics & Reporting',
        description: 'Comprehensive dashboards with real-time analytics, customizable reports, and data-driven insights for informed decision-making.',
        color: 'from-orange-600 to-red-600',
        bgColor: 'from-orange-100 to-red-100'
    },
    {
        icon: 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
        title: 'Compliance Management',
        description: 'Stay compliant with industry standards and regulations. Automated compliance tracking and audit trail management.',
        color: 'from-teal-600 to-cyan-600',
        bgColor: 'from-teal-100 to-cyan-100'
    },
    {
        icon: 'M13 10V3L4 14h7v7l9-11h-7z',
        title: 'Real-Time Notifications',
        description: 'Instant alerts and notifications keep your team informed. Customizable notification preferences for critical updates.',
        color: 'from-yellow-600 to-amber-600',
        bgColor: 'from-yellow-100 to-amber-100'
    }
]

const testimonials = [
    {
        name: 'Sarah Johnson',
        role: 'Safety Director',
        company: 'TechCorp Industries',
        content: 'QSMCore has transformed how we manage safety incidents. The real-time tracking and automated workflows have reduced our response time by 60%.',
        avatar: '👩‍💼'
    },
    {
        name: 'Michael Chen',
        role: 'Quality Manager',
        company: 'Global Manufacturing Co.',
        content: 'The multi-industry support and comprehensive reporting features make QSMCore indispensable for our operations across 15 different sectors.',
        avatar: '👨‍💼'
    },
    {
        name: 'Emily Rodriguez',
        role: 'Compliance Officer',
        company: 'Healthcare Solutions Inc.',
        content: 'Compliance management has never been easier. The audit trail and automated compliance tracking save us countless hours every month.',
        avatar: '👩‍⚕️'
    }
]

const howItWorks = [
    {
        step: '01',
        title: 'Sign Up & Configure',
        description: 'Set up your organization, define sectors, and configure your quality and safety parameters.',
        icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
    },
    {
        step: '02',
        title: 'Track Incidents',
        description: 'Log incidents in real-time, assign responsibilities, and track progress through automated workflows.',
        icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'
    },
    {
        step: '03',
        title: 'Generate Reports',
        description: 'Create comprehensive reports, analyze trends, and make data-driven decisions with powerful analytics.',
        icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'
    }
]

const faqs = [
    {
        question: 'What industries does QSMCore support?',
        answer: 'QSMCore supports a wide range of industries including manufacturing, healthcare, construction, energy, transportation, and more. Our flexible system can be customized to meet the specific needs of any industry.'
    },
    {
        question: 'How secure is my data?',
        answer: 'We take data security seriously. QSMCore uses enterprise-grade encryption, regular security audits, role-based access controls, and complies with major data protection regulations including GDPR and SOC 2.'
    },
    {
        question: 'Can I integrate QSMCore with existing systems?',
        answer: 'Yes! QSMCore offers robust API integrations and can connect with most enterprise systems including ERP, HRIS, and other business management platforms.'
    },
    {
        question: 'What kind of support do you provide?',
        answer: 'We offer 24/7 customer support, comprehensive documentation, training resources, and dedicated account managers for enterprise clients.'
    }
]

onMounted(() => {
    const handleScroll = () => {
        isScrolled.value = window.scrollY > 50
    }
    window.addEventListener('scroll', handleScroll)
    
    // Auto-rotate testimonials
    setInterval(() => {
        activeTestimonial.value = (activeTestimonial.value + 1) % testimonials.length
    }, 5000)
    
    return () => window.removeEventListener('scroll', handleScroll)
})
</script>

<template>
    <Head title="QSMCore - Quality & Safety Management" />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
        <!-- Navigation -->
        <nav :class="['bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-200/50 sticky top-0 z-50 transition-all duration-300', isScrolled ? 'bg-white/95 shadow-md' : '']">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16 sm:h-20">
                    <div class="flex items-center space-x-2 sm:space-x-3 min-w-0">
                        <div class="bg-gradient-to-br from-blue-600 to-indigo-600 p-1.5 sm:p-2 rounded-lg sm:rounded-xl shadow-lg flex-shrink-0">
                            <img src="/logos/logo-width.png" class="h-8 sm:h-10 w-auto" alt="QSMCore Logo" />
                        </div>
                        <div class="min-w-0">
                            <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent truncate">
                                QSMCore
                            </h1>
                            <p class="text-xs text-gray-500 hidden sm:block">Quality & Safety Management</p>
                        </div>
                    </div>
                    <!-- Desktop nav -->
                    <div class="hidden lg:flex items-center space-x-4 xl:space-x-6">
                        <Link href="/" class="text-gray-700 hover:text-blue-600 font-medium transition-colors whitespace-nowrap">
                            Home
                        </Link>
                        <Link 
                            :href="route('blog.index')" 
                            class="text-gray-700 hover:text-blue-600 font-medium transition-colors whitespace-nowrap"
                        >
                            Blog
                        </Link>
                        <Link 
                            :href="route('login')" 
                            class="text-gray-700 hover:text-blue-600 font-medium transition-colors whitespace-nowrap"
                        >
                            Admin Portal
                        </Link>
                        <Link 
                            :href="route('companies.login')" 
                            class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 xl:px-6 py-2 xl:py-2.5 rounded-lg hover:from-blue-700 hover:to-indigo-700 font-semibold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5 whitespace-nowrap text-sm xl:text-base"
                        >
                            Company Portal
                        </Link>
                    </div>
                    <!-- Mobile menu button -->
                    <button
                        type="button"
                        class="lg:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        aria-label="Toggle menu"
                        @click="mobileMenuOpen = !mobileMenuOpen"
                    >
                        <svg v-if="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <!-- Mobile menu -->
                <Transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 -translate-y-2"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 -translate-y-2"
                >
                    <div v-show="mobileMenuOpen" class="lg:hidden border-t border-gray-200 py-4 space-y-1">
                        <Link href="/" class="block py-2 px-3 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg font-medium" @click="mobileMenuOpen = false">Home</Link>
                        <Link :href="route('blog.index')" class="block py-2 px-3 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg font-medium" @click="mobileMenuOpen = false">Blog</Link>
                        <Link :href="route('login')" class="block py-2 px-3 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg font-medium" @click="mobileMenuOpen = false">Admin Portal</Link>
                        <Link :href="route('companies.login')" class="block py-3 px-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg font-semibold text-center hover:from-blue-700 hover:to-indigo-700" @click="mobileMenuOpen = false">Company Portal</Link>
                    </div>
                </Transition>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative overflow-hidden">
            <div class="absolute inset-0 bg-grid-pattern opacity-5"></div>
            <div class="absolute top-0 right-0 w-96 h-96 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-indigo-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse delay-1000"></div>
            
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 lg:py-32">
                <div class="text-center">
                    <div class="inline-block mb-4 sm:mb-6">
                        <span class="px-3 py-1.5 sm:px-4 sm:py-2 bg-blue-100 text-blue-800 rounded-full text-xs sm:text-sm font-semibold">
                            Enterprise Quality Management
                        </span>
                    </div>
                    <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold mb-4 sm:mb-6 leading-tight">
                        <span class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent">
                            Quality & Safety
                        </span>
                        <br>
                        <span class="text-gray-900">Management System</span>
                    </h1>
                    <p class="text-base sm:text-xl md:text-2xl text-gray-600 mb-3 sm:mb-4 max-w-3xl mx-auto leading-relaxed px-1">
                        Streamline your organization's quality and safety processes with our comprehensive digital platform
                    </p>
                    <p class="text-sm sm:text-lg text-gray-500 mb-8 sm:mb-12 max-w-2xl mx-auto px-1">
                        Track incidents, manage corrective actions, and ensure compliance across multiple industries with ease
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center items-center px-2">
                        <Link
                            :href="route('login')"
                            class="w-full sm:w-auto group relative px-6 sm:px-8 py-3 sm:py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-bold text-base sm:text-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-2xl hover:shadow-blue-500/50 transform hover:-translate-y-1 overflow-hidden text-center"
                        >
                            <span class="relative z-10 flex items-center justify-center">
                                Admin Portal
                                <svg class="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </span>
                            <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity"></div>
                        </Link>
                        <Link
                            :href="route('companies.login')"
                            class="w-full sm:w-auto group px-6 sm:px-8 py-3 sm:py-4 bg-white text-blue-600 border-2 border-blue-600 rounded-xl font-bold text-base sm:text-lg hover:bg-blue-50 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-1 text-center"
                        >
                            <span class="flex items-center justify-center">
                                Company Portal
                                <svg class="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </span>
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-16 sm:py-24 bg-white/50 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 sm:mb-20">
                    <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-3 sm:mb-4 px-2">
                        Powerful Features for
                        <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                            Modern Organizations
                        </span>
                    </h2>
                    <p class="text-base sm:text-xl text-gray-600 max-w-2xl mx-auto px-2">
                        Everything you need to manage quality and safety across your entire organization
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    <div 
                        v-for="(feature, index) in features" 
                        :key="index"
                        class="group relative bg-white rounded-2xl p-6 sm:p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100"
                    >
                        <div :class="['absolute top-0 right-0 w-32 h-32 bg-gradient-to-br', feature.bgColor, 'rounded-bl-full opacity-50 group-hover:opacity-100 transition-opacity']"></div>
                        <div class="relative">
                            <div :class="['bg-gradient-to-br', feature.color, 'text-white rounded-2xl w-16 h-16 flex items-center justify-center text-2xl font-bold mb-6 shadow-lg']">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="feature.icon" />
                                </svg>
                            </div>
                            <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2 sm:mb-3">{{ feature.title }}</h3>
                            <p class="text-gray-600 leading-relaxed">
                                {{ feature.description }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section class="py-16 sm:py-24 bg-gradient-to-br from-gray-50 to-blue-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 sm:mb-20">
                    <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-3 sm:mb-4 px-2">
                        How It
                        <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                            Works
                        </span>
                    </h2>
                    <p class="text-base sm:text-xl text-gray-600 max-w-2xl mx-auto px-2">
                        Get started with QSMCore in three simple steps
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8 relative">
                    <div 
                        v-for="(step, index) in howItWorks" 
                        :key="index"
                        class="relative"
                    >
                        <div class="relative bg-white rounded-2xl p-6 sm:p-8 shadow-xl border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                            <div class="absolute -top-6 -left-6 w-16 h-16 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-xl shadow-lg">
                                {{ step.step }}
                            </div>
                            <div class="mt-4 mb-6">
                                <div class="bg-gradient-to-br from-blue-100 to-indigo-100 rounded-xl w-16 h-16 flex items-center justify-center mb-6">
                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="step.icon" />
                                    </svg>
                                </div>
                                <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2 sm:mb-3">{{ step.title }}</h3>
                                <p class="text-gray-600 leading-relaxed">
                                    {{ step.description }}
                                </p>
                            </div>
                        </div>
                        <div v-if="index < howItWorks.length - 1" class="hidden md:block absolute top-1/2 -right-4 w-8 h-0.5 bg-gradient-to-r from-blue-400 to-indigo-400 transform -translate-y-1/2 z-0">
                            <div class="absolute right-0 top-1/2 transform -translate-y-1/2 w-3 h-3 bg-indigo-400 rounded-full"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Benefits Section -->
        <section class="py-16 sm:py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 sm:gap-12 items-center">
                    <div>
                        <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-4 sm:mb-6 px-1">
                            Why Choose
                            <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                                QSMCore?
                            </span>
                        </h2>
                        <p class="text-base sm:text-xl text-gray-600 mb-6 sm:mb-8 px-1">
                            Experience the difference with our comprehensive quality and safety management platform
                        </p>
                        <div class="space-y-4 sm:space-y-6">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-1 sm:mb-2">Reduce Response Time</h3>
                                    <p class="text-gray-600">Automated workflows and real-time notifications help you respond to incidents 60% faster.</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-1 sm:mb-2">Improve Compliance</h3>
                                    <p class="text-gray-600">Stay ahead of regulations with automated compliance tracking and comprehensive audit trails.</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-1 sm:mb-2">Data-Driven Decisions</h3>
                                    <p class="text-gray-600">Powerful analytics and reporting tools provide insights to make informed decisions.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="relative order-first lg:order-none">
                        <div class="bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl sm:rounded-3xl p-6 sm:p-8 shadow-2xl">
                            <div class="bg-white/10 backdrop-blur-sm rounded-xl sm:rounded-2xl p-4 sm:p-6 mb-4 sm:mb-6">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-white/80 text-sm">Incidents Resolved</span>
                                    <span class="text-white font-bold text-2xl">94%</span>
                                </div>
                                <div class="w-full bg-white/20 rounded-full h-3 mb-6">
                                    <div class="bg-white h-3 rounded-full" style="width: 94%"></div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                                        <div class="text-white/60 text-xs mb-1">Avg Response</div>
                                        <div class="text-white font-bold text-xl">2.3 hrs</div>
                                    </div>
                                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                                        <div class="text-white/60 text-xs mb-1">Compliance</div>
                                        <div class="text-white font-bold text-xl">99.8%</div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white/10 backdrop-blur-sm rounded-xl sm:rounded-2xl p-4 sm:p-6">
                                <h4 class="text-white font-bold text-lg mb-4">Key Metrics</h4>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-white/80">Active Users</span>
                                        <span class="text-white font-semibold">1,247</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-white/80">Companies</span>
                                        <span class="text-white font-semibold">156</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-white/80">Industries</span>
                                        <span class="text-white font-semibold">23</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-yellow-400 rounded-full mix-blend-multiply filter blur-2xl opacity-30 animate-pulse"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="py-16 sm:py-24 bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 sm:mb-20">
                    <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-3 sm:mb-4 px-2">
                        Trusted by
                        <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                            Industry Leaders
                        </span>
                    </h2>
                    <p class="text-base sm:text-xl text-gray-600 max-w-2xl mx-auto px-2">
                        See what our customers have to say about QSMCore
                    </p>
                </div>
                
                <div class="relative max-w-4xl mx-auto px-2 sm:px-4">
                    <div class="overflow-hidden rounded-2xl sm:rounded-3xl">
                        <div 
                            class="flex transition-transform duration-500 ease-in-out"
                            :style="{ transform: `translateX(-${activeTestimonial * 100}%)` }"
                        >
                            <div 
                                v-for="(testimonial, index) in testimonials" 
                                :key="index"
                                class="min-w-full px-4 sm:px-8"
                            >
                                <div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 md:p-12 shadow-2xl">
                                    <div class="text-5xl sm:text-6xl mb-4 sm:mb-6">{{ testimonial.avatar }}</div>
                                    <p class="text-base sm:text-xl md:text-2xl text-gray-700 mb-6 sm:mb-8 leading-relaxed italic">
                                        "{{ testimonial.content }}"
                                    </p>
                                    <div class="flex items-center space-x-4">
                                        <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                                            {{ testimonial.name.charAt(0) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 text-lg">{{ testimonial.name }}</div>
                                            <div class="text-gray-600">{{ testimonial.role }}</div>
                                            <div class="text-blue-600 font-semibold">{{ testimonial.company }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-center space-x-2 mt-8">
                        <button
                            v-for="(testimonial, index) in testimonials"
                            :key="index"
                            @click="activeTestimonial = index"
                            :class="['w-3 h-3 rounded-full transition-all duration-300', activeTestimonial === index ? 'bg-blue-600 w-8' : 'bg-gray-300']"
                        ></button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Blog Section -->
        <section v-if="blogPosts && blogPosts.length > 0" class="py-16 sm:py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 sm:mb-20">
                    <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-3 sm:mb-4 px-2">
                        Latest from
                        <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                            QSMCore Blog
                        </span>
                    </h2>
                    <p class="text-base sm:text-xl text-gray-600 max-w-2xl mx-auto px-2">
                        Stay updated with the latest insights, news, and best practices in quality and safety management
                    </p>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    <div
                        v-for="post in blogPosts"
                        :key="post.id"
                        class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden"
                    >
                        <div v-if="post.featured_image" class="relative h-48 overflow-hidden">
                            <img
                                :src="post.featured_image"
                                :alt="post.title"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                            />
                        </div>
                        <div v-else class="h-48 bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                            <svg class="w-16 h-16 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center text-sm text-gray-500 mb-3">
                                <span>{{ new Date(post.published_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) }}</span>
                                <span class="mx-2">•</span>
                                <span>{{ post.user?.name || 'Admin' }}</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors line-clamp-2">
                                {{ post.title }}
                            </h3>
                            <p v-if="post.excerpt" class="text-gray-600 mb-4 line-clamp-3">
                                {{ post.excerpt }}
                            </p>
                            <p v-else class="text-gray-600 mb-4 line-clamp-3">
                                {{ post.content.substring(0, 150) }}{{ post.content.length > 150 ? '...' : '' }}
                            </p>
                            <Link
                                href="#"
                                class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-700 group-hover:translate-x-1 transition-transform"
                            >
                                Read More
                                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-12 sm:py-20 bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-8 text-center">
                    <div class="py-4">
                        <div class="text-2xl sm:text-4xl md:text-5xl font-bold mb-1 sm:mb-2">100+</div>
                        <div class="text-blue-100 text-sm sm:text-base">Companies</div>
                    </div>
                    <div class="py-4">
                        <div class="text-2xl sm:text-4xl md:text-5xl font-bold mb-1 sm:mb-2">50+</div>
                        <div class="text-blue-100 text-sm sm:text-base">Industries</div>
                    </div>
                    <div class="py-4">
                        <div class="text-2xl sm:text-4xl md:text-5xl font-bold mb-1 sm:mb-2">1000+</div>
                        <div class="text-blue-100 text-sm sm:text-base">Active Users</div>
                    </div>
                    <div class="py-4">
                        <div class="text-2xl sm:text-4xl md:text-5xl font-bold mb-1 sm:mb-2">99.9%</div>
                        <div class="text-blue-100 text-sm sm:text-base">Uptime</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-10 sm:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 sm:gap-8 mb-6 sm:mb-8">
                    <div class="sm:col-span-2 md:col-span-1">
                        <div class="flex items-center space-x-2 sm:space-x-3 mb-3 sm:mb-4">
                            <div class="bg-gradient-to-br from-blue-600 to-indigo-600 p-1.5 sm:p-2 rounded-lg flex-shrink-0">
                                <img src="/logos/logo-width.png" class="h-6 sm:h-8 w-auto" alt="QSMCore Logo" />
                            </div>
                            <h3 class="text-lg sm:text-xl font-bold">QSMCore</h3>
                        </div>
                        <p class="text-gray-400 text-sm sm:text-base">
                            Quality & Safety Management System - Empowering organizations to achieve excellence in quality and safety standards.
                        </p>
                    </div>
                    <div>
                        <h4 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4">Quick Links</h4>
                        <ul class="space-y-1.5 sm:space-y-2 text-gray-400 text-sm sm:text-base">
                            <li><Link href="/" class="hover:text-white transition-colors">Home</Link></li>
                            <li><Link :href="route('blog.index')" class="hover:text-white transition-colors">Blog</Link></li>
                            <li><Link :href="route('login')" class="hover:text-white transition-colors">Admin Portal</Link></li>
                            <li><Link :href="route('companies.login')" class="hover:text-white transition-colors">Company Portal</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4">Contact</h4>
                        <ul class="space-y-1.5 sm:space-y-2 text-gray-400 text-sm sm:text-base break-words">
                            <li>Email: support@qsm.com</li>
                            <li>Phone: +1 (555) 123-4567</li>
                            <li>Address: 123 Business St, Suite 100</li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-gray-800 pt-6 sm:pt-8 text-center text-gray-400 text-sm sm:text-base">
                    <p>&copy; 2025 QSMCore. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
@keyframes pulse {
    0%, 100% {
        opacity: 0.2;
    }
    50% {
        opacity: 0.3;
    }
}

.delay-1000 {
    animation-delay: 1s;
}

.bg-grid-pattern {
    background-image: 
        linear-gradient(to right, rgba(0, 0, 0, 0.1) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(0, 0, 0, 0.1) 1px, transparent 1px);
    background-size: 50px 50px;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
