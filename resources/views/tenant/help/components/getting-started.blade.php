'getting-started': {
    template: `
        <section id="getting-started" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Getting Started</h2>
            <div class="space-y-4">
                <div v-for="(step, idx) in steps" :key="idx" class="border-l-4 border-green-500 pl-4">
                    <h3 class="font-semibold text-lg mb-2" v-text="(idx + 1) + '. ' + step.title"></h3>
                    <p class="text-gray-700" v-text="step.description"></p>
                </div>
            </div>
        </section>
    `,
    data() {
        return {
            steps: [
                { title: 'Complete Your Profile', description: 'Navigate to Settings → Company to update your business information, logo, and preferences.' },
                { title: 'Set Up Your Chart of Accounts', description: 'Go to Accounting → Chart of Accounts to customize your accounting structure.' },
                { title: 'Add Products & Services', description: 'Visit Inventory → Products to add your products and services.' },
                { title: 'Add Customers & Vendors', description: 'Use CRM to add your customers and vendors for easy invoicing.' }
            ]
        }
    }
},
