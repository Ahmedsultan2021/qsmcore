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
        icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        title: 'Incident & Occurrence Reporting',
        description: 'Capture, investigate, and close safety events in minutes. Automated workflows route reports to the right people and track corrective actions to closure.',
        color: 'from-blue-600 to-indigo-600',
        bgColor: 'from-blue-100 to-indigo-100'
    },
    {
        icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        title: 'Risk Assessment & Hazard Register',
        description: 'Identify hazards, evaluate risk using a configurable matrix, and track mitigations. Maintain a live hazard register across all departments.',
        color: 'from-orange-600 to-red-600',
        bgColor: 'from-orange-100 to-red-100'
    },
    {
        icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        title: 'Audit Management',
        description: 'Plan and execute internal and external audits. Record findings, assign corrective actions, and track closure — all in one structured workflow.',
        color: 'from-green-600 to-emerald-600',
        bgColor: 'from-green-100 to-emerald-100'
    },
    {
        icon: 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
        title: 'CAPA Management',
        description: 'Drive root-cause analysis and manage corrective & preventive actions from any source — audits, incidents, complaints, or risk reviews.',
        color: 'from-purple-600 to-pink-600',
        bgColor: 'from-purple-100 to-pink-100'
    },
    {
        icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        title: 'Compliance & Regulation Tracking',
        description: 'Map activities to ICAO, EASA, FAA, and ISO requirements. Automated audit trails and compliance dashboards keep you always audit-ready.',
        color: 'from-teal-600 to-cyan-600',
        bgColor: 'from-teal-100 to-cyan-100'
    },
    {
        icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        title: 'Analytics & Safety Performance Indicators',
        description: 'Real-time dashboards show SPIs and KPIs across all departments. Spot trends before they become incidents with data-driven safety intelligence.',
        color: 'from-yellow-600 to-amber-600',
        bgColor: 'from-yellow-100 to-amber-100'
    }
]

const modules = [
    { name: 'Incident Reporting',   desc: 'Log, investigate, and close safety events',       icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
    { name: 'Risk Assessment',      desc: 'Hazard identification & risk matrix',              icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z' },
    { name: 'Audit Management',     desc: 'Plan, execute, and track internal/external audits', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4' },
    { name: 'CAPA',                 desc: 'Corrective & preventive action workflows',         icon: 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15' },
    { name: 'Document Control',     desc: 'Version-controlled document management',           icon: 'M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2' },
    { name: 'Training Records',     desc: 'Track competency and certifications',              icon: 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z' },
    { name: 'Supplier Evaluation',  desc: 'Score and approve suppliers',                      icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' },
    { name: 'Management Review',    desc: 'Structured review meetings and action outputs',    icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' },
]

const complianceItems = [
    { label: 'ICAO Doc. 9859', sub: 'Safety Management' },
    { label: 'EASA',           sub: 'EU Aviation Safety' },
    { label: 'FAA SMS',        sub: 'Federal Aviation Admin.' },
    { label: 'ISO 9001:2015',  sub: 'Quality Management' },
    { label: 'ISO 45001',      sub: 'Occupational Health & Safety' },
]

const vsRows = [
    { aspect: 'Incident reporting',       qsm: 'Structured forms, auto-routing, real-time status', legacy: 'Email threads, shared spreadsheets' },
    { aspect: 'CAPA tracking',            qsm: 'Root-cause workflows with due-date alerts',          legacy: 'Manual follow-ups, missed deadlines' },
    { aspect: 'Audit management',         qsm: 'Scheduled plans, digital findings, auto closure',   legacy: 'Paper checklists, PDF reports' },
    { aspect: 'Compliance evidence',      qsm: 'Automatic audit trail, always audit-ready',         legacy: 'Manual file gathering before each audit' },
    { aspect: 'Safety performance data',  qsm: 'Live dashboards, SPI/KPI trends',                   legacy: 'Monthly Excel summaries, delayed insight' },
    { aspect: 'Multi-department access',  qsm: 'Role-based access for all teams',                   legacy: 'Siloed files, version confusion' },
]

const testimonials = [
    {
        name: 'Operations Director',
        role: 'Safety Director',
        company: 'Regional Airline',
        content: 'Since moving to QSMCore our incident response time dropped by 60%. Automated workflows mean nothing falls through the cracks — every report reaches the right person instantly.',
    },
    {
        name: 'Quality Manager',
        role: 'Quality Manager',
        company: 'MRO Organization',
        content: "Audit preparation used to take us two weeks. With QSMCore's live compliance dashboard and auto-generated audit trails, we're always ready. Our last external audit had zero major findings.",
    },
    {
        name: 'Compliance Officer',
        role: 'Compliance Officer',
        company: 'Ground Handling Company',
        content: 'We manage CAPA across four departments and 300+ employees. QSMCore gives every manager a clear view of their open actions and due dates. Closure rates went from 65% to 97%.',
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

        <!-- Compliance Strip -->
        <section class="bg-white border-t border-gray-100 py-5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                    <span class="text-xs font-semibold tracking-widest text-gray-400 uppercase shrink-0">Aligned with</span>
                    <div class="w-px h-4 bg-gray-200 hidden sm:block shrink-0"></div>
                    <div class="flex flex-wrap gap-2">
                        <div v-for="item in complianceItems" :key="item.label"
                            class="inline-flex items-center gap-1.5 border border-gray-200 rounded px-3 py-1.5 bg-gray-50 hover:border-brand-blue/40 hover:bg-blue-50/30 transition-colors">
                            <svg class="w-3 h-3 text-brand-blue flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-xs font-semibold text-gray-700">{{ item.label }}</span>
                            <span class="hidden sm:inline text-xs text-gray-400">· {{ item.sub }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Industries Section -->
        <section class="py-20 bg-white border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-12">
                    <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase mb-3">Coverage</p>
                    <h2 class="text-2xl sm:text-3xl font-semibold text-gray-900">Industries We Serve</h2>
                </div>
                <div class="flex flex-wrap gap-3">
                    <div
                        v-for="(industry, index) in displayIndustries"
                        :key="index"
                        class="flex items-center gap-2.5 border border-gray-200 rounded px-4 py-2.5 bg-white hover:border-gray-400 transition-colors"
                    >
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="getIndustryIcon(industry.name)" />
                        </svg>
                        <span class="text-sm font-medium text-gray-700">{{ industry.name }}</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section class="py-20 bg-gray-50 border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-20">
                    <div class="lg:col-span-4">
                        <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase mb-3">Why QSMCore</p>
                        <h2 class="text-2xl sm:text-3xl font-semibold text-gray-900 leading-snug">Built for teams that can't afford errors</h2>
                    </div>
                    <div class="lg:col-span-8 space-y-5 text-gray-600 text-base leading-relaxed">
                        <p>In high-risk industries, a missed incident report or a lapsed audit trail isn't just a compliance failure — it's a safety risk. QSMCore replaces scattered spreadsheets and email threads with a single, structured platform where every safety and quality event is captured, investigated, and closed.</p>
                        <p>Organizations using QSMCore reduce incident response time, pass audits without last-minute scrambles, and give every manager real-time visibility into open CAPAs, risk ratings, and compliance status — across every department and site.</p>
                        <p>From aviation flight operations and MRO to energy, logistics, and ground services, QSMCore adapts to your industry's regulations and scales with your team.</p>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-4 border-t border-gray-200">
                            <div>
                                <div class="text-2xl font-bold text-brand-navy">60%</div>
                                <div class="text-sm text-gray-500 mt-1">Faster incident response with automated routing</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-brand-navy">97%</div>
                                <div class="text-sm text-gray-500 mt-1">Average CAPA closure rate across active clients</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-brand-navy">Zero</div>
                                <div class="text-sm text-gray-500 mt-1">Major findings at client audits using live compliance trails</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-20 bg-white border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-12">
                    <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase mb-3">Capabilities</p>
                    <h2 class="text-2xl sm:text-3xl font-semibold text-gray-900">Platform Features</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-px bg-gray-200">
                    <div
                        v-for="(feature, index) in features"
                        :key="index"
                        class="bg-white p-8 hover:bg-gray-50 transition-colors"
                    >
                        <div class="w-8 h-8 mb-5">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="feature.icon" />
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-gray-900 mb-2">{{ feature.title }}</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">{{ feature.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Modules Section -->
        <section class="py-20 bg-gray-50 border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-12">
                    <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase mb-3">Modules</p>
                    <h2 class="text-2xl sm:text-3xl font-semibold text-gray-900">Everything in one platform</h2>
                    <p class="mt-3 text-sm text-gray-500 max-w-xl">Eight fully integrated modules — no switching between systems, no data silos.</p>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-px bg-gray-200">
                    <div
                        v-for="(mod, index) in modules"
                        :key="index"
                        class="bg-white p-6 hover:bg-gray-50 transition-colors group"
                    >
                        <div class="w-8 h-8 mb-4 text-brand-blue group-hover:text-brand-navy transition-colors">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="mod.icon" />
                            </svg>
                        </div>
                        <div class="text-sm font-semibold text-gray-900 mb-1">{{ mod.name }}</div>
                        <div class="text-xs text-gray-500 leading-relaxed">{{ mod.desc }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section class="py-20 bg-gray-50 border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-12">
                    <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase mb-3">Process</p>
                    <h2 class="text-2xl sm:text-3xl font-semibold text-gray-900">How It Works</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div
                        v-for="(step, index) in howItWorks"
                        :key="index"
                        class="relative"
                    >
                        <div class="text-4xl font-light text-gray-200 mb-4 select-none">{{ step.step }}</div>
                        <h3 class="text-base font-semibold text-gray-900 mb-2">{{ step.title }}</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">{{ step.description }}</p>
                        <div v-if="index < howItWorks.length - 1" class="hidden md:block absolute top-5 right-0 translate-x-1/2 text-gray-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-16 bg-brand-navy border-t border-brand-navy">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-5 divide-x divide-white/10">
                    <div class="px-6 py-4 text-center">
                        <div class="text-3xl sm:text-4xl font-semibold text-white mb-1">100+</div>
                        <div class="text-xs font-medium tracking-widest text-white/50 uppercase">Organizations</div>
                    </div>
                    <div class="px-6 py-4 text-center">
                        <div class="text-3xl sm:text-4xl font-semibold text-white mb-1">10+</div>
                        <div class="text-xs font-medium tracking-widest text-white/50 uppercase">Industries</div>
                    </div>
                    <div class="px-6 py-4 text-center">
                        <div class="text-3xl sm:text-4xl font-semibold text-white mb-1">1,000+</div>
                        <div class="text-xs font-medium tracking-widest text-white/50 uppercase">Active Users</div>
                    </div>
                    <div class="px-6 py-4 text-center">
                        <div class="text-3xl sm:text-4xl font-semibold text-white mb-1">8</div>
                        <div class="text-xs font-medium tracking-widest text-white/50 uppercase">Integrated Modules</div>
                    </div>
                    <div class="px-6 py-4 text-center">
                        <div class="text-3xl sm:text-4xl font-semibold text-white mb-1">99.9%</div>
                        <div class="text-xs font-medium tracking-widest text-white/50 uppercase">Uptime</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- QSMCore vs Manual/Legacy Section -->
        <section class="py-20 bg-white border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-12">
                    <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase mb-3">Comparison</p>
                    <h2 class="text-2xl sm:text-3xl font-semibold text-gray-900">QSMCore vs. Spreadsheets & legacy systems</h2>
                    <p class="mt-3 text-sm text-gray-500">See why organizations move off manual processes.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 pr-8 text-xs font-semibold text-gray-400 uppercase tracking-widest w-1/3">Area</th>
                                <th class="text-left py-3 pr-8 text-xs font-semibold text-brand-navy uppercase tracking-widest w-1/3">
                                    <span class="inline-flex items-center gap-1.5">
                                        <span class="inline-block w-2 h-2 rounded-full bg-brand-sky"></span>
                                        QSMCore
                                    </span>
                                </th>
                                <th class="text-left py-3 text-xs font-semibold text-gray-400 uppercase tracking-widest w-1/3">Spreadsheets / Legacy</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="(row, i) in vsRows" :key="i" class="hover:bg-gray-50 transition-colors">
                                <td class="py-4 pr-8 font-medium text-gray-700">{{ row.aspect }}</td>
                                <td class="py-4 pr-8 text-gray-700">
                                    <span class="inline-flex items-start gap-2">
                                        <svg class="w-4 h-4 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                        {{ row.qsm }}
                                    </span>
                                </td>
                                <td class="py-4 text-gray-400">
                                    <span class="inline-flex items-start gap-2">
                                        <svg class="w-4 h-4 text-gray-300 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        {{ row.legacy }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="py-20 bg-white border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-12">
                    <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase mb-3">Testimonials</p>
                    <h2 class="text-2xl sm:text-3xl font-semibold text-gray-900">What Our Clients Say</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div
                        v-for="(testimonial, index) in testimonials"
                        :key="index"
                        class="border border-gray-200 p-8"
                    >
                        <p class="text-gray-600 text-sm leading-relaxed mb-8">"{{ testimonial.content }}"</p>
                        <div class="border-t border-gray-100 pt-5">
                            <div class="text-sm font-semibold text-gray-900">{{ testimonial.name }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">{{ testimonial.role }}, {{ testimonial.company }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Blog Section -->
        <section v-if="blogPosts && blogPosts.length > 0" class="py-20 bg-gray-50 border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-end justify-between mb-12">
                    <div>
                        <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase mb-3">Insights</p>
                        <h2 class="text-2xl sm:text-3xl font-semibold text-gray-900">Latest Articles</h2>
                    </div>
                    <Link :href="route('blog.index')" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors hidden sm:block">
                        View all →
                    </Link>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div
                        v-for="post in blogPosts"
                        :key="post.id"
                        class="group"
                    >
                        <div v-if="post.featured_image" class="h-44 overflow-hidden bg-gray-100 mb-5">
                            <img :src="post.featured_image" :alt="post.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                        </div>
                        <div v-else class="h-44 bg-gray-100 mb-5 flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="text-xs text-gray-400 mb-2">
                            {{ new Date(post.published_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) }}
                            <span class="mx-1.5">·</span>
                            {{ post.user?.name || 'Admin' }}
                        </div>
                        <h3 class="text-base font-semibold text-gray-900 mb-2 line-clamp-2 group-hover:text-brand-navy transition-colors">{{ post.title }}</h3>
                        <p class="text-sm text-gray-500 line-clamp-3 leading-relaxed">
                            {{ post.excerpt || post.content.substring(0, 130) }}{{ (!post.excerpt && post.content.length > 130) ? '…' : '' }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Partners Section -->
        <section v-if="partners && partners.length > 0" class="py-20 bg-white border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-12">
                    <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase mb-3">Network</p>
                    <h2 class="text-2xl sm:text-3xl font-semibold text-gray-900">Our Companies</h2>
                </div>
                <!-- Grid -->
                <div class="flex flex-wrap gap-4 mb-14">
                    <div
                        v-for="partner in partners"
                        :key="partner.id"
                        class="flex items-center gap-3 border border-gray-200 px-4 py-3 hover:border-gray-400 transition-colors"
                    >
                        <img v-if="partner.logo_url" :src="partner.logo_url" :alt="partner.name" class="h-7 w-auto max-w-[80px] object-contain" />
                        <span class="text-sm font-medium text-gray-700">{{ partner.name }}</span>
                    </div>
                </div>
            </div>
            <!-- Scrolling marquee -->
            <div class="border-t border-gray-100 py-6 overflow-hidden">
                <div class="marquee-track flex items-center gap-16">
                    <template v-for="n in 2" :key="n">
                        <div
                            v-for="partner in partners"
                            :key="`${n}-${partner.id}`"
                            class="flex-shrink-0 flex items-center gap-3 px-2"
                        >
                            <img v-if="partner.logo_url" :src="partner.logo_url" :alt="partner.name" class="h-8 w-auto max-w-[100px] object-contain grayscale opacity-40 hover:opacity-70 hover:grayscale-0 transition-all duration-300" />
                            <span v-else class="text-sm font-medium text-gray-300 whitespace-nowrap">{{ partner.name }}</span>
                        </div>
                    </template>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="py-20 bg-gray-50 border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-20">
                    <!-- Left -->
                    <div class="lg:col-span-4">
                        <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase mb-3">Contact</p>
                        <h2 class="text-2xl sm:text-3xl font-semibold text-gray-900 mb-6">Get in Touch</h2>
                        <p class="text-gray-500 text-sm leading-relaxed mb-10">Have a question or want to learn more? Fill in the form and we'll get back to you within one business day.</p>
                        <div class="space-y-6">
                            <div>
                                <div class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Email</div>
                                <a href="mailto:support@qsm.com" class="text-sm text-gray-700 hover:text-brand-navy transition-colors">support@qsm.com</a>
                            </div>
                            <div>
                                <div class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Response Time</div>
                                <p class="text-sm text-gray-500">Within 24 hours on business days</p>
                            </div>
                        </div>
                    </div>

                    <!-- Form -->
                    <div class="lg:col-span-8">
                        <div v-if="contactSuccess" class="mb-6 flex items-center gap-3 border border-green-200 bg-green-50 text-green-700 px-4 py-3 text-sm">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Your inquiry was submitted. We'll be in touch shortly.
                        </div>
                        <form @submit.prevent="submitContact" class="space-y-5">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">Full Name <span class="text-red-400">*</span></label>
                                    <input v-model="contactForm.name" type="text" placeholder="Your full name"
                                        class="w-full px-4 py-3 border border-gray-300 bg-white text-sm text-gray-900 focus:outline-none focus:border-gray-500 transition-colors"
                                        :class="{ 'border-red-400': contactForm.errors.name }" />
                                    <p v-if="contactForm.errors.name" class="text-red-500 text-xs mt-1">{{ contactForm.errors.name }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">Mobile Number</label>
                                    <input v-model="contactForm.phone" type="tel" placeholder="+1 234 567 8900"
                                        class="w-full px-4 py-3 border border-gray-300 bg-white text-sm text-gray-900 focus:outline-none focus:border-gray-500 transition-colors" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">Email <span class="text-red-400">*</span></label>
                                <input v-model="contactForm.email" type="email" placeholder="your@email.com"
                                    class="w-full px-4 py-3 border border-gray-300 bg-white text-sm text-gray-900 focus:outline-none focus:border-gray-500 transition-colors"
                                    :class="{ 'border-red-400': contactForm.errors.email }" />
                                <p v-if="contactForm.errors.email" class="text-red-500 text-xs mt-1">{{ contactForm.errors.email }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">Message <span class="text-red-400">*</span></label>
                                <textarea v-model="contactForm.message" rows="5" placeholder="Tell us how we can help you..."
                                    class="w-full px-4 py-3 border border-gray-300 bg-white text-sm text-gray-900 focus:outline-none focus:border-gray-500 transition-colors resize-none"
                                    :class="{ 'border-red-400': contactForm.errors.message }"></textarea>
                                <p v-if="contactForm.errors.message" class="text-red-500 text-xs mt-1">{{ contactForm.errors.message }}</p>
                            </div>
                            <button type="submit" :disabled="contactForm.processing"
                                class="px-8 py-3 bg-brand-navy text-white text-sm font-semibold hover:bg-brand-blue transition-colors disabled:opacity-50">
                                <span v-if="contactForm.processing">Sending…</span>
                                <span v-else>Send Inquiry</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12 border-t border-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-10 mb-10">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <img src="/logos/lo.png" class="h-8 w-auto" alt="QSMCore Logo" />
                            <span class="text-base font-semibold tracking-wide">QSMCore</span>
                        </div>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            Integrated SMS & QMS for aviation, energy, logistics, and more.
                        </p>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold uppercase tracking-widest text-gray-500 mb-4">Navigation</h4>
                        <ul class="space-y-2 text-sm text-gray-400">
                            <li><Link href="/" class="hover:text-white transition-colors">Home</Link></li>
                            <li><Link :href="route('blog.index')" class="hover:text-white transition-colors">Blog</Link></li>
                            <li><Link :href="route('careers.index')" class="hover:text-white transition-colors">Careers</Link></li>
                            <li><button @click="scrollToContact" class="hover:text-white transition-colors">Contact</button></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold uppercase tracking-widest text-gray-500 mb-4">Portals</h4>
                        <ul class="space-y-2 text-sm text-gray-400">
                            <li><Link :href="route('login')" class="hover:text-white transition-colors">Admin Portal</Link></li>
                            <li><Link :href="route('companies.login')" class="hover:text-white transition-colors">Company Portal</Link></li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-gray-800 pt-6 text-xs text-gray-600">
                    &copy; {{ new Date().getFullYear() }} QSMCore. All rights reserved.
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
