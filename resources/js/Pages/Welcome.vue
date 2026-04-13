<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import { ref, onMounted, computed } from 'vue'

const props = defineProps({
    blogPosts: {
        type: Array,
        default: () => []
    },
    industries: {
        type: Array,
        default: () => []
    },
    partners: {
        type: Array,
        default: () => []
    }
})

// Icon mapping for industries (Heroicons paths)
const industryIcons = {
    'aviation': 'M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z',
    'manufacturing': 'M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z',
    'services': 'M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z',
    'technology': 'M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 01.9-2.7L5.737 5.1a3.375 3.375 0 012.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 01.9 2.7m0 0a3 3 0 01-3 3m0 3h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008zm-3 6h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008z',
    'healthcare': 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
    'construction': 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z',
    'education': 'M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5',
    'retail': 'M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.75-5.925V5.1',
    'energy': 'M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z',
    'oil': 'M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.288 3 .486m-3-.486l2.484 2.484a.75.75 0 01-1.061 1.061M18.75 4.97l-2.484 2.484a.75.75 0 001.061-1.061m0 0l2.484-2.484M18.75 4.97v2.25m0 2.25v-2.25m0 2.25l2.484 2.484M12 20.25l-2.484-2.484a.75.75 0 011.061-1.061M12 20.25v-2.25m0 2.25v2.25m0-2.25l-2.484-2.484M5.25 4.97l2.484 2.484a.75.75 0 01-1.061 1.061L4.189 4.97',
    'transportation': 'M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9H4.5a2.25 2.25 0 01-2.25-2.25V6.75A2.25 2.25 0 014.5 4.5h15A2.25 2.25 0 0121.75 6.75v9.75a2.25 2.25 0 01-2.25 2.25h-6m-9 0V9.375m0 0a2.25 2.25 0 002.25 2.25h.096a2.25 2.25 0 001.591-.659l2.122-2.121a2.25 2.25 0 011.591-.659H18.75M8.25 18.75V9.375m0 0a2.25 2.25 0 012.25-2.25h.096a2.25 2.25 0 011.591.659l2.122 2.121a2.25 2.25 0 001.591.659H18.75',
    'logistics': 'M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9H4.5a2.25 2.25 0 01-2.25-2.25V6.75A2.25 2.25 0 014.5 4.5h15A2.25 2.25 0 0121.75 6.75v9.75a2.25 2.25 0 01-2.25 2.25h-6m-9 0V9.375m0 0a2.25 2.25 0 002.25 2.25h.096a2.25 2.25 0 001.591-.659l2.122-2.121a2.25 2.25 0 011.591-.659H18.75M8.25 18.75V9.375m0 0a2.25 2.25 0 012.25-2.25h.096a2.25 2.25 0 011.591.659l2.122 2.121a2.25 2.25 0 001.591.659H18.75',
    'default': 'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z'
}

const getIndustryIcon = (name) => {
    const key = (name || '').toLowerCase()
    if (key.includes('aviation') || key.includes('aerospace')) return industryIcons.aviation
    if (key.includes('manufacturing')) return industryIcons.manufacturing
    if (key.includes('service')) return industryIcons.services
    if (key.includes('technology') || key.includes('tech')) return industryIcons.technology
    if (key.includes('health') || key.includes('medical')) return industryIcons.healthcare
    if (key.includes('construction')) return industryIcons.construction
    if (key.includes('education')) return industryIcons.education
    if (key.includes('retail')) return industryIcons.retail
    if (key.includes('energy')) return industryIcons.energy
    if (key.includes('oil') || key.includes('gas')) return industryIcons.oil
    if (key.includes('transport')) return industryIcons.transportation
    if (key.includes('logistics')) return industryIcons.logistics
    return industryIcons.default
}

// Industries from DB only
const displayIndustries = computed(() => props.industries || [])

const activeFeature = ref(0)
const activeTestimonial = ref(0)
const isScrolled = ref(false)
const mobileMenuOpen = ref(false)

const page = usePage()
const contactSuccess = computed(() => page.props.flash?.contact_success)

const contactForm = useForm({
    name: '',
    email: '',
    phone: '',
    message: '',
})

const submitContact = () => {
    contactForm.post(route('contact.store'), {
        preserveScroll: true,
        onSuccess: () => contactForm.reset(),
    })
}

const scrollToContact = () => {
    document.getElementById('contact')?.scrollIntoView({ behavior: 'smooth' })
    mobileMenuOpen.value = false
}

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

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-slate-50 to-brand-bg">
        <!-- Navigation -->
        <nav :class="['bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-200/50 sticky top-0 z-50 transition-all duration-300', isScrolled ? 'bg-white/95 shadow-md' : '']">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16 sm:h-20">
                    <div class="flex items-center space-x-2 sm:space-x-3 min-w-0">
                        <img src="/logos/lo.png" class="h-16 sm:h-20 w-auto flex-shrink-0" alt="QSMCore Logo" />
                        <div class="min-w-0">
                            <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-brand-navy to-brand-blue bg-clip-text text-transparent truncate">
                                QSMCore
                            </h1>
                            <p class="text-xs text-gray-500 hidden sm:block">Quality & Safety Management</p>
                        </div>
                    </div>
                    <!-- Desktop nav -->
                    <div class="hidden lg:flex items-center space-x-4 xl:space-x-6">
                        <Link href="/" class="text-gray-700 hover:text-brand-blue font-medium transition-colors whitespace-nowrap">
                            Home
                        </Link>
                        <Link 
                            :href="route('blog.index')" 
                            class="text-gray-700 hover:text-brand-blue font-medium transition-colors whitespace-nowrap"
                        >
                            Blog
                        </Link>
                        <Link
                            :href="route('careers.index')"
                            class="text-gray-700 hover:text-brand-blue font-medium transition-colors whitespace-nowrap"
                        >
                            Careers
                        </Link>
                        <button
                            @click="scrollToContact"
                            class="text-gray-700 hover:text-brand-blue font-medium transition-colors whitespace-nowrap"
                        >
                            Contact
                        </button>
                        <Link
                            :href="route('login')"
                            class="text-gray-700 hover:text-brand-blue font-medium transition-colors whitespace-nowrap"
                        >
                            Admin Portal
                        </Link>
                        <Link 
                            :href="route('companies.login')" 
                            class="bg-gradient-to-r from-brand-navy to-brand-blue text-white px-4 xl:px-6 py-2 xl:py-2.5 rounded-lg hover:from-brand-blue hover:to-brand-sky font-semibold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5 whitespace-nowrap text-sm xl:text-base"
                        >
                            Company Portal
                        </Link>
                    </div>
                    <!-- Mobile menu button -->
                    <button
                        type="button"
                        class="lg:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-sky"
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
                        <Link href="/" class="block py-2 px-3 text-gray-700 hover:text-brand-blue hover:bg-slate-50 rounded-lg font-medium" @click="mobileMenuOpen = false">Home</Link>
                        <Link :href="route('blog.index')" class="block py-2 px-3 text-gray-700 hover:text-brand-blue hover:bg-slate-50 rounded-lg font-medium" @click="mobileMenuOpen = false">Blog</Link>
                        <Link :href="route('careers.index')" class="block py-2 px-3 text-gray-700 hover:text-brand-blue hover:bg-slate-50 rounded-lg font-medium" @click="mobileMenuOpen = false">Careers</Link>
                        <button @click="scrollToContact" class="block w-full text-left py-2 px-3 text-gray-700 hover:text-brand-blue hover:bg-slate-50 rounded-lg font-medium">Contact</button>
                        <Link :href="route('login')" class="block py-2 px-3 text-gray-700 hover:text-brand-blue hover:bg-slate-50 rounded-lg font-medium" @click="mobileMenuOpen = false">Admin Portal</Link>
                        <Link :href="route('companies.login')" class="block py-3 px-3 bg-gradient-to-r from-brand-navy to-brand-blue text-white rounded-lg font-semibold text-center hover:from-brand-blue hover:to-brand-sky" @click="mobileMenuOpen = false">Company Portal</Link>
                    </div>
                </Transition>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative overflow-hidden">
            <div class="absolute inset-0 bg-grid-pattern opacity-5"></div>
            <div class="absolute top-[-6rem] right-[-6rem] w-[28rem] h-[28rem] bg-brand-sky rounded-full mix-blend-multiply filter blur-3xl opacity-15 animate-pulse"></div>
            <div class="absolute bottom-[-8rem] left-[-8rem] w-[32rem] h-[32rem] bg-brand-blue rounded-full mix-blend-multiply filter blur-3xl opacity-15 animate-pulse delay-1000"></div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-20 lg:py-24">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
                    <!-- Copy -->
                    <div class="lg:col-span-6">
                        <div class="inline-flex items-center gap-2 rounded-full bg-white/70 backdrop-blur px-3 py-1.5 border border-brand-border shadow-sm">
                            <span class="inline-flex h-2 w-2 rounded-full bg-brand-sky"></span>
                            <span class="text-xs sm:text-sm font-semibold text-brand-navy">
                                From Reporting to Insight — Safety & Quality in one platform
                            </span>
                        </div>

                        <h1 class="mt-5 text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-brand-text">
                            Streamline your
                            <span class="block bg-gradient-to-r from-brand-navy via-brand-blue to-brand-sky bg-clip-text text-transparent">
                                Quality & Safety Management
                            </span>
                        </h1>

                        <p class="mt-5 text-base sm:text-lg text-gray-600 leading-relaxed max-w-xl">
                            QSMCore centralizes reporting, risk management, audits, and compliance monitoring—built for operational teams in aviation and other high‑risk industries.
                        </p>

                        <div class="mt-7 flex flex-col sm:flex-row gap-3 sm:gap-4">
                            <Link
                                :href="route('companies.login')"
                                class="group inline-flex items-center justify-center px-6 py-3.5 bg-gradient-to-r from-brand-navy to-brand-blue text-white rounded-xl font-bold hover:from-brand-blue hover:to-brand-sky transition-all duration-300 shadow-xl hover:shadow-brand-sky/30 transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-brand-sky focus:ring-offset-2"
                            >
                                Company Portal
                                <svg class="ml-2 w-5 h-5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </Link>
                            <Link
                                :href="route('login')"
                                class="inline-flex items-center justify-center px-6 py-3.5 bg-white/80 backdrop-blur text-brand-navy border border-brand-border rounded-xl font-bold hover:bg-white transition-all duration-300 shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-brand-sky focus:ring-offset-2"
                            >
                                Admin Portal
                            </Link>
                        </div>

                        <div class="mt-8 grid grid-cols-3 gap-4 max-w-xl">
                            <div class="rounded-xl bg-white/70 backdrop-blur border border-brand-border px-4 py-3">
                                <div class="text-xl font-extrabold text-brand-navy">100+</div>
                                <div class="text-xs text-gray-600">Companies</div>
                            </div>
                            <div class="rounded-xl bg-white/70 backdrop-blur border border-brand-border px-4 py-3">
                                <div class="text-xl font-extrabold text-brand-navy">50+</div>
                                <div class="text-xs text-gray-600">Industries</div>
                            </div>
                            <div class="rounded-xl bg-white/70 backdrop-blur border border-brand-border px-4 py-3">
                                <div class="text-xl font-extrabold text-brand-navy">99.9%</div>
                                <div class="text-xs text-gray-600">Uptime</div>
                            </div>
                        </div>
                    </div>

                    <!-- Visual -->
                    <div class="lg:col-span-6">
                        <div class="relative mx-auto max-w-xl">
                            <div class="absolute inset-0 -z-10 bg-gradient-to-tr from-brand-sky/20 via-brand-blue/10 to-brand-navy/10 blur-2xl rounded-[2.5rem]"></div>

                            <div class="rounded-[2rem] bg-white/80 backdrop-blur border border-brand-border shadow-2xl overflow-hidden">
                                <div class="flex items-center justify-between px-5 py-4 border-b border-brand-border">
                                    <div class="flex items-center gap-2">
                                        <span class="h-2.5 w-2.5 rounded-full bg-red-400/80"></span>
                                        <span class="h-2.5 w-2.5 rounded-full bg-yellow-400/80"></span>
                                        <span class="h-2.5 w-2.5 rounded-full bg-green-400/80"></span>
                                    </div>
                                    <div class="text-xs font-semibold text-gray-600">Reports Overview</div>
                                    <div class="h-6 w-6 rounded-lg bg-brand-bg border border-brand-border"></div>
                                </div>

                                <div class="grid grid-cols-12 gap-0">
                                    <div class="col-span-4 bg-brand-navy text-white p-5">
                                        <div class="text-xs text-white/70 font-semibold">Dashboard</div>
                                        <div class="mt-4 space-y-2.5">
                                            <div class="h-8 rounded-lg bg-white/10 border border-white/10"></div>
                                            <div class="h-8 rounded-lg bg-white/10 border border-white/10"></div>
                                            <div class="h-8 rounded-lg bg-white/15 border border-white/15"></div>
                                            <div class="h-8 rounded-lg bg-white/10 border border-white/10"></div>
                                        </div>
                                        <div class="mt-6 rounded-xl bg-white/10 border border-white/10 p-3">
                                            <div class="text-[10px] text-white/70">Compliance</div>
                                            <div class="mt-2 h-2 rounded-full bg-white/20">
                                                <div class="h-2 rounded-full bg-brand-sky" style="width: 78%"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-span-8 p-5">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="rounded-xl border border-brand-border bg-white p-4">
                                                <div class="text-xs font-semibold text-gray-600">Last 12 months</div>
                                                <div class="mt-3 h-20 rounded-lg bg-gradient-to-tr from-brand-bg to-white border border-brand-border"></div>
                                                <div class="mt-3 flex items-end gap-1.5 h-10">
                                                    <div class="w-2.5 rounded bg-brand-sky/60" style="height: 45%"></div>
                                                    <div class="w-2.5 rounded bg-brand-sky/70" style="height: 65%"></div>
                                                    <div class="w-2.5 rounded bg-brand-sky/80" style="height: 55%"></div>
                                                    <div class="w-2.5 rounded bg-brand-sky/90" style="height: 78%"></div>
                                                    <div class="w-2.5 rounded bg-brand-blue/80" style="height: 70%"></div>
                                                    <div class="w-2.5 rounded bg-brand-navy/80" style="height: 82%"></div>
                                                </div>
                                            </div>
                                            <div class="rounded-xl border border-brand-border bg-white p-4">
                                                <div class="text-xs font-semibold text-gray-600">Performance</div>
                                                <div class="mt-3 grid grid-cols-3 gap-2 items-end h-28">
                                                    <div class="rounded-lg bg-brand-blue/80" style="height: 55%"></div>
                                                    <div class="rounded-lg bg-brand-sky/80" style="height: 72%"></div>
                                                    <div class="rounded-lg bg-brand-navy/80" style="height: 62%"></div>
                                                    <div class="rounded-lg bg-brand-sky/70" style="height: 45%"></div>
                                                    <div class="rounded-lg bg-brand-blue/70" style="height: 68%"></div>
                                                    <div class="rounded-lg bg-brand-navy/70" style="height: 52%"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4 grid grid-cols-3 gap-4">
                                            <div class="rounded-xl border border-brand-border bg-white p-4">
                                                <div class="text-[11px] text-gray-600 font-semibold">Incidents</div>
                                                <div class="mt-2 text-2xl font-extrabold text-brand-navy">94%</div>
                                                <div class="mt-2 h-2 rounded-full bg-brand-bg">
                                                    <div class="h-2 rounded-full bg-brand-sky" style="width: 94%"></div>
                                                </div>
                                            </div>
                                            <div class="rounded-xl border border-brand-border bg-white p-4">
                                                <div class="text-[11px] text-gray-600 font-semibold">Avg response</div>
                                                <div class="mt-2 text-2xl font-extrabold text-brand-navy">2.3h</div>
                                                <div class="mt-2 text-xs text-gray-500">Real‑time alerts</div>
                                            </div>
                                            <div class="rounded-xl border border-brand-border bg-white p-4">
                                                <div class="text-[11px] text-gray-600 font-semibold">Compliance</div>
                                                <div class="mt-2 text-2xl font-extrabold text-brand-navy">99.8%</div>
                                                <div class="mt-2 text-xs text-gray-500">Audit trail ready</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Industries Section -->
        <section class="py-16 sm:py-24 bg-white/80 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 sm:mb-16">
                    <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-3 sm:mb-4 px-2">
                        Trusted Across
                        <span class="bg-gradient-to-r from-brand-navy to-brand-blue bg-clip-text text-transparent">
                            Multiple Industries
                        </span>
                    </h2>
                    <p class="text-base sm:text-xl text-gray-600 max-w-2xl mx-auto px-2">
                        From aviation and energy to logistics and manufacturing—QSMCore adapts to your industry
                    </p>
                </div>
                <div class="flex flex-wrap justify-center gap-4 sm:gap-6">
                    <div 
                        v-for="(industry, index) in displayIndustries" 
                        :key="index"
                        class="group relative bg-white rounded-2xl p-5 sm:p-6 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden w-48 sm:w-56"
                    >
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-slate-100 to-slate-50 rounded-bl-full opacity-60 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative">
                            <div class="bg-gradient-to-br from-brand-navy to-brand-blue text-white rounded-xl w-14 h-14 sm:w-16 sm:h-16 flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 transition-transform">
                                <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIndustryIcon(industry.name)" />
                                </svg>
                            </div>
                            <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-1">{{ industry.name }}</h3>
                            <p v-if="industry.description" class="text-sm text-gray-600 line-clamp-2">{{ industry.description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- About QSMCore Section -->
        <section class="py-16 sm:py-24 bg-gradient-to-br from-slate-50 via-slate-50 to-brand-bg">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-10">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                        What is <span class="bg-gradient-to-r from-brand-navy to-brand-blue bg-clip-text text-transparent">QSMCore</span>?
                    </h2>
                </div>
                <div class="bg-white/90 backdrop-blur-sm rounded-2xl p-6 sm:p-10 shadow-xl border border-gray-100 space-y-6 text-gray-700 leading-relaxed">
                    <p class="text-base sm:text-lg">
                        QSMCore is a digital platform designed to simplify and strengthen Safety and Quality Management Systems (SMS & QMS) across multiple industries.
                    </p>
                    <p class="text-base sm:text-lg">
                        Built with operational environments in mind, QSMCore provides structured reporting tools, risk management features, audit tracking, and performance dashboards that help organizations monitor safety events, manage quality processes, and ensure regulatory compliance.
                    </p>
                    <p class="text-base sm:text-lg">
                        From aviation operations and maintenance to energy and logistics sectors, QSMCore centralizes critical reporting and transforms operational data into actionable insights. By streamlining incident reporting, investigation workflows, corrective actions, and performance monitoring, the platform helps organizations build a proactive safety culture and maintain high operational standards.
                    </p>
                    <p class="text-base sm:text-lg">
                        With an intuitive interface and industry-aligned reporting templates, QSMCore empowers teams to report, analyze, and improve continuously.
                    </p>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-16 sm:py-24 bg-white/50 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 sm:mb-20">
                    <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-3 sm:mb-4 px-2">
                        Powerful Features for
                        <span class="bg-gradient-to-r from-brand-navy to-brand-blue bg-clip-text text-transparent">
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
        <section class="py-16 sm:py-24 bg-gradient-to-br from-gray-50 to-brand-bg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 sm:mb-20">
                    <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-3 sm:mb-4 px-2">
                        How It
                        <span class="bg-gradient-to-r from-brand-navy to-brand-blue bg-clip-text text-transparent">
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
                            <div class="absolute -top-6 -left-6 w-16 h-16 bg-gradient-to-br from-brand-navy to-brand-blue rounded-full flex items-center justify-center text-white font-bold text-xl shadow-lg">
                                {{ step.step }}
                            </div>
                            <div class="mt-4 mb-6">
                                <div class="bg-gradient-to-br from-slate-100 to-slate-50 rounded-xl w-16 h-16 flex items-center justify-center mb-6">
                                    <svg class="w-8 h-8 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="step.icon" />
                                    </svg>
                                </div>
                                <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2 sm:mb-3">{{ step.title }}</h3>
                                <p class="text-gray-600 leading-relaxed">
                                    {{ step.description }}
                                </p>
                            </div>
                        </div>
                        <div v-if="index < howItWorks.length - 1" class="hidden md:block absolute top-1/2 -right-4 w-8 h-0.5 bg-gradient-to-r from-brand-sky to-brand-blue transform -translate-y-1/2 z-0">
                            <div class="absolute right-0 top-1/2 transform -translate-y-1/2 w-3 h-3 bg-brand-blue rounded-full"></div>
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
                            <span class="bg-gradient-to-r from-brand-navy to-brand-blue bg-clip-text text-transparent">
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
                        <div class="bg-gradient-to-br from-brand-navy to-brand-blue rounded-2xl sm:rounded-3xl p-6 sm:p-8 shadow-2xl">
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
        <section class="py-16 sm:py-24 bg-gradient-to-br from-slate-50 via-brand-bg to-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 sm:mb-20">
                    <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-3 sm:mb-4 px-2">
                        Trusted by
                        <span class="bg-gradient-to-r from-brand-navy to-brand-blue bg-clip-text text-transparent">
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
                                        <div class="w-16 h-16 bg-gradient-to-br from-brand-navy to-brand-blue rounded-full flex items-center justify-center text-white text-2xl font-bold">
                                            {{ testimonial.name.charAt(0) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 text-lg">{{ testimonial.name }}</div>
                                            <div class="text-gray-600">{{ testimonial.role }}</div>
                                            <div class="text-brand-blue font-semibold">{{ testimonial.company }}</div>
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
                            :class="['w-3 h-3 rounded-full transition-all duration-300', activeTestimonial === index ? 'bg-brand-blue w-8' : 'bg-gray-300']"
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
                            <div v-else class="h-48 bg-gradient-to-br from-slate-100 to-slate-50 flex items-center justify-center">
                            <svg class="w-16 h-16 text-brand-sky" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center text-sm text-gray-500 mb-3">
                                <span>{{ new Date(post.published_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) }}</span>
                                <span class="mx-2">•</span>
                                <span>{{ post.user?.name || 'Admin' }}</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-brand-blue transition-colors line-clamp-2">
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
                                class="inline-flex items-center text-brand-blue font-semibold hover:text-brand-navy group-hover:translate-x-1 transition-transform"
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
        <section class="py-12 sm:py-20 bg-gradient-to-r from-brand-navy to-brand-blue text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-8 text-center">
                    <div class="py-4">
                        <div class="text-2xl sm:text-4xl md:text-5xl font-bold mb-1 sm:mb-2">100+</div>
                        <div class="text-white/80 text-sm sm:text-base">Companies</div>
                    </div>
                    <div class="py-4">
                        <div class="text-2xl sm:text-4xl md:text-5xl font-bold mb-1 sm:mb-2">50+</div>
                        <div class="text-white/80 text-sm sm:text-base">Industries</div>
                    </div>
                    <div class="py-4">
                        <div class="text-2xl sm:text-4xl md:text-5xl font-bold mb-1 sm:mb-2">1000+</div>
                        <div class="text-white/80 text-sm sm:text-base">Active Users</div>
                    </div>
                    <div class="py-4">
                        <div class="text-2xl sm:text-4xl md:text-5xl font-bold mb-1 sm:mb-2">99.9%</div>
                        <div class="text-white/80 text-sm sm:text-base">Uptime</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Partners Section -->
        <section v-if="partners && partners.length > 0" class="py-16 sm:py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 sm:mb-16">
                    <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-3 sm:mb-4">
                        Our
                        <span class="bg-gradient-to-r from-brand-navy to-brand-blue bg-clip-text text-transparent">
                            Partners
                        </span>
                    </h2>
                    <p class="text-base sm:text-xl text-gray-600 max-w-2xl mx-auto">
                        Trusted by leading organizations across industries
                    </p>
                </div>

                <!-- Partners Grid -->
                <div class="flex flex-wrap justify-center gap-6 sm:gap-8 mb-16">
                    <div
                        v-for="partner in partners"
                        :key="partner.id"
                        class="group flex flex-col items-center justify-center bg-white border border-gray-100 rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 p-5 sm:p-6 w-40 sm:w-48"
                    >
                        <a v-if="partner.website" :href="partner.website" target="_blank" rel="noopener" class="flex flex-col items-center gap-3 w-full">
                            <img v-if="partner.logo_url" :src="partner.logo_url" :alt="partner.name" class="h-14 w-full object-contain" />
                            <div v-else class="h-14 w-full flex items-center justify-center bg-gradient-to-br from-brand-navy to-brand-blue rounded-xl">
                                <span class="text-white font-bold text-lg">{{ partner.name.charAt(0) }}</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-700 group-hover:text-brand-blue transition-colors text-center leading-tight">{{ partner.name }}</span>
                        </a>
                        <div v-else class="flex flex-col items-center gap-3 w-full">
                            <img v-if="partner.logo_url" :src="partner.logo_url" :alt="partner.name" class="h-14 w-full object-contain" />
                            <div v-else class="h-14 w-full flex items-center justify-center bg-gradient-to-br from-brand-navy to-brand-blue rounded-xl">
                                <span class="text-white font-bold text-lg">{{ partner.name.charAt(0) }}</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-700 text-center leading-tight">{{ partner.name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scrolling Logo Marquee Bar -->
            <div class="bg-gray-50 border-y border-gray-100 py-6 overflow-hidden">
                <div class="marquee-track flex items-center gap-12">
                    <!-- Render twice for seamless loop -->
                    <template v-for="n in 2" :key="n">
                        <div
                            v-for="partner in partners"
                            :key="`${n}-${partner.id}`"
                            class="flex-shrink-0 flex items-center gap-3 px-2"
                        >
                            <img v-if="partner.logo_url" :src="partner.logo_url" :alt="partner.name" class="h-10 w-auto max-w-[120px] object-contain grayscale hover:grayscale-0 transition-all duration-300 opacity-60 hover:opacity-100" />
                            <span v-else class="text-sm font-semibold text-gray-400 whitespace-nowrap">{{ partner.name }}</span>
                        </div>
                    </template>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="py-16 sm:py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <span class="inline-block bg-blue-100 text-blue-700 text-sm font-semibold px-4 py-1.5 rounded-full mb-4">Get In Touch</span>
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Add Your Inquiry</h2>
                    <p class="text-gray-500 max-w-xl mx-auto">Have a question or want to learn more? Fill in the form and we'll get back to you.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
                    <!-- Contact Info -->
                    <div class="space-y-8">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Email</h4>
                                <a href="mailto:support@qsm.com" class="text-blue-600 hover:underline">support@qsm.com</a>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Response Time</h4>
                                <p class="text-gray-500">We typically respond within 24 hours on business days.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Secure & Confidential</h4>
                                <p class="text-gray-500">Your information is safe with us and never shared with third parties.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Form -->
                    <div class="bg-gray-50 rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100">
                        <!-- Success message -->
                        <div v-if="contactSuccess" class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="font-medium">Your inquiry was sent successfully! We'll be in touch soon.</span>
                        </div>

                        <form @submit.prevent="submitContact" class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                                <input
                                    v-model="contactForm.name"
                                    type="text"
                                    placeholder="Your full name"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white text-gray-900"
                                    :class="{ 'border-red-400': contactForm.errors.name }"
                                />
                                <p v-if="contactForm.errors.name" class="text-red-500 text-xs mt-1">{{ contactForm.errors.name }}</p>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                                    <input
                                        v-model="contactForm.email"
                                        type="email"
                                        placeholder="your@email.com"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white text-gray-900"
                                        :class="{ 'border-red-400': contactForm.errors.email }"
                                    />
                                    <p v-if="contactForm.errors.email" class="text-red-500 text-xs mt-1">{{ contactForm.errors.email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Mobile Number</label>
                                    <input
                                        v-model="contactForm.phone"
                                        type="tel"
                                        placeholder="+1 234 567 8900"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white text-gray-900"
                                    />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Your Request <span class="text-red-500">*</span></label>
                                <textarea
                                    v-model="contactForm.message"
                                    rows="5"
                                    placeholder="Tell us how we can help you..."
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white text-gray-900 resize-none"
                                    :class="{ 'border-red-400': contactForm.errors.message }"
                                ></textarea>
                                <p v-if="contactForm.errors.message" class="text-red-500 text-xs mt-1">{{ contactForm.errors.message }}</p>
                            </div>
                            <button
                                type="submit"
                                :disabled="contactForm.processing"
                                class="w-full bg-gradient-to-r from-brand-navy to-brand-blue text-white font-semibold py-3 px-6 rounded-xl hover:from-brand-blue hover:to-brand-sky transition-all duration-300 shadow-lg hover:shadow-xl disabled:opacity-60 disabled:cursor-not-allowed"
                            >
                                <span v-if="contactForm.processing">Sending...</span>
                                <span v-else>Send Inquiry</span>
                            </button>
                        </form>
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
                                <img src="/logos/lo.png" class="h-6 sm:h-8 w-auto" alt="QSMCore Logo" />
                            </div>
                            <h3 class="text-lg sm:text-xl font-bold">QSMCore</h3>
                        </div>
                        <p class="text-gray-400 text-sm sm:text-base">
                            From Reporting to Insight – One Platform for Safety & Quality across industries. Integrated SMS & QMS for aviation, energy, logistics, and more.
                        </p>
                    </div>
                    <div>
                        <h4 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4">Quick Links</h4>
                        <ul class="space-y-1.5 sm:space-y-2 text-gray-400 text-sm sm:text-base">
                            <li><Link href="/" class="hover:text-white transition-colors">Home</Link></li>
                            <li><Link :href="route('blog.index')" class="hover:text-white transition-colors">Blog</Link></li>
                            <li><Link :href="route('careers.index')" class="hover:text-white transition-colors">Careers</Link></li>
                            <li><button @click="scrollToContact" class="hover:text-white transition-colors">Contact</button></li>
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

/* Infinite scrolling marquee */
@keyframes marquee {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

.marquee-track {
    display: flex;
    width: max-content;
    animation: marquee 30s linear infinite;
}

.marquee-track:hover {
    animation-play-state: paused;
}
</style>
