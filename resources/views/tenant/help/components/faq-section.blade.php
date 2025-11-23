'faq-section': {
    template: `
        <section id="faq" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
            <div class="space-y-4">
                <div v-for="(faq, idx) in faqs" :key="idx" class="border-b pb-4">
                    <h3 class="font-semibold text-lg mb-2" v-text="faq.question"></h3>
                    <p class="text-gray-700" v-text="faq.answer"></p>
                </div>
            </div>
        </section>
    `,
    data() {
        return {
            faqs: [
                { question: 'How do I create an invoice?', answer: 'Go to Accounting → Invoices → Create New. Select a customer, add products, and save or post the invoice.' },
                { question: "What's the difference between Draft and Posted?", answer: 'Draft documents can be edited or deleted. Posted documents are finalized and affect your accounts and inventory.' },
                { question: 'How do I add team members?', answer: 'Navigate to Admin → Users → Add New User. Assign them a role to control their permissions.' },
                { question: 'Can I customize my invoice template?', answer: 'Yes, go to Settings → Company to upload your logo and customize business information that appears on invoices.' },
                { question: 'How do I track inventory?', answer: 'Enable "Maintain Stock" when creating products. Stock automatically updates when you post sales or purchase invoices.' }
            ]
        }
    }
},
