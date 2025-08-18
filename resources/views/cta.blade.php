<!-- CTA Section -->
<section class="gradient-bg text-white py-20 relative overflow-hidden">
    <div class="absolute inset-0 bg-black opacity-20"></div>

    <!-- Floating background elements -->
    <div class="absolute top-10 right-10 w-32 h-32 bg-brand-gold opacity-10 rounded-full floating-animation"></div>
    <div class="absolute bottom-10 left-10 w-24 h-24 bg-brand-teal opacity-15 rounded-full floating-animation" style="animation-delay: -3s;"></div>

    <div class="relative max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl md:text-4xl font-bold mb-6">
            Ready to Join Our <span class="text-brand-gold">Success Story?</span>
        </h2>
        <p class="text-xl text-gray-200 mb-8 max-w-2xl mx-auto">
            Become part of the thousands of Nigerian businesses already growing with Ballie.
            Experience the perfect combination of <strong class="text-brand-gold">availability and affordability</strong>.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
            <a href="{{ route('register') }}" class="bg-brand-gold text-gray-900 px-8 py-4 rounded-lg hover:bg-yellow-400 font-semibold text-lg transition-all transform hover:scale-105">
                Start Your Free Trial
            </a>
            <a href="{{ route('contact') }}" class="border-2 border-brand-gold text-brand-gold px-8 py-4 rounded-lg hover:bg-brand-gold hover:text-gray-900 font-semibold text-lg transition-all">
                Schedule a Demo
            </a>
        </div>

        <div class="text-gray-300 text-sm">
            30-day free trial • No credit card required • Setup in minutes • Cancel anytime
        </div>

        <!-- Trust indicators -->
        <div class="mt-12 grid grid-cols-2 md:grid-cols-4 gap-8 opacity-70">
            <div class="text-center">
                <div class="text-2xl font-bold text-brand-gold counter" data-target="8500">8500</div>
                <div class="text-gray-300 text-sm">Happy Customers</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-brand-gold counter" data-target="99.9">99.9</div>
                <div class="text-gray-300 text-sm">% Uptime</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-brand-gold">24/7</div>
                <div class="text-gray-300 text-sm">Support</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-brand-gold counter" data-target="5">15</div>
                <div class="text-gray-300 text-sm">Years Experience</div>
            </div>
        </div>
    </div>
</section>

<style>
    :root {
        --color-gold: #d1b05e;
        --color-blue: #2b6399;
        --color-dark-purple: #3c2c64;
        --color-teal: #69a2a4;
        --color-purple: #85729d;
        --color-light-blue: #7b87b8;
        --color-deep-purple: #4a3570;
        --color-lavender: #a48cb4;
        --color-violet: #614c80;
        --color-green: #249484;
    }

    .bg-brand-blue { background-color: var(--color-blue); }
    .bg-brand-gold { background-color: var(--color-gold); }
    .bg-brand-purple { background-color: var(--color-dark-purple); }
    .bg-brand-teal { background-color: var(--color-teal); }
    .bg-brand-green { background-color: var(--color-green); }
    .bg-brand-light-blue { background-color: var(--color-light-blue); }
    .bg-brand-deep-purple { background-color: var(--color-deep-purple); }
    .bg-brand-lavender { background-color: var(--color-lavender); }

    .text-brand-gold { color: var(--color-gold); }
    .text-brand-blue { color: var(--color-blue); }
    .text-brand-teal { color: var(--color-teal); }
    .text-brand-purple { color: var(--color-purple); }
    .text-brand-green { color: var(--color-green); }
    .text-brand-light-blue { color: var(--color-light-blue); }

    .gradient-bg {
        background: linear-gradient(135deg, var(--color-blue) 0%, var(--color-dark-purple) 50%, var(--color-deep-purple) 100%);
    }

    .slide-in-left {
        animation: slideInLeft 1s ease-out;
    }

    .slide-in-right {
        animation: slideInRight 1s ease-out;
    }

    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .floating-animation {
        animation: floating 6s ease-in-out infinite;
    }

    @keyframes floating {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-20px);
        }
    }

    .pulse-animation {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
    }

    .timeline-item {
        display: flex;
        align-items: flex-start;
        position: relative;
    }

    .timeline-item:not(:last-child)::after {
        content: '';
        position: absolute;
        left: 32px;
        top: 64px;
        width: 2px;
        height: calc(100% + 48px);
        background: linear-gradient(to bottom, var(--color-teal), var(--color-purple));
        opacity: 0.3;
    }

    .timeline-dot {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 14px;
        flex-shrink: 0;
        position: relative;
        z-index: 1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .team-card {
        transition: all 0.3s ease;
    }

    .team-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .team-avatar {
        background: linear-gradient(135deg, var(--color-blue), var(--color-teal));
        transition: all 0.3s ease;
    }

    .team-card:hover .team-avatar {
        transform: scale(1.05);
    }

    .value-card {
        transition: all 0.3s ease;
    }

    .value-card:hover {
        transform: translateY(-5px);
    }

    .value-icon {
        transition: all 0.3s ease;
    }

    .value-card:hover .value-icon {
        transform: scale(1.1);
    }

    .award-card {
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }

    .award-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border-color: var(--color-gold);
    }

    /* Counter animation */
    .counter {
        transition: all 0.3s ease;
    }

    /* Intersection Observer animations */
    .fade-in {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.6s ease;
    }

    .fade-in.visible {
        opacity: 1;
        transform: translateY(0);
    }
</style>
<script>

       // Add click tracking for analytics (placeholder)
        document.querySelectorAll('a[href*="register"], a[href*="contact"]').forEach(link => {
            link.addEventListener('click', function() {
                // Analytics tracking code would go here
                console.log('CTA clicked:', this.href);
            });
        });
</script>

