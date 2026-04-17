import { defineConfig } from 'vitepress'

export default defineConfig({
  title: 'Ballie Documentation',
  description: 'Complete guide to using Ballie — the all-in-one business management platform for Nigerian businesses',
  lang: 'en-US',
  cleanUrls: true,
  lastUpdated: true,
  ignoreDeadLinks: true,

  sitemap: {
    hostname: 'https://doc.ballie.co'
  },

  head: [
    ['link', { rel: 'icon', href: '/favicon.ico' }],
    ['meta', { name: 'theme-color', content: '#2b6399' }],
    ['meta', { property: 'og:type', content: 'website' }],
    ['meta', { property: 'og:site_name', content: 'Ballie Documentation' }],
    ['meta', { property: 'og:image', content: 'https://doc.ballie.co/og-image.png' }],
  ],

  themeConfig: {
    logo: '/logo.svg',
    siteTitle: 'Ballie Docs',

    nav: [
      { text: 'Getting Started', link: '/getting-started/' },
      {
        text: 'Modules',
        items: [
          { text: 'Accounting', link: '/accounting/' },
          { text: 'Inventory', link: '/inventory/' },
          { text: 'CRM', link: '/crm/' },
          { text: 'POS', link: '/pos/' },
          { text: 'Payroll & HR', link: '/payroll/' },
          { text: 'Banking', link: '/banking/' },
          { text: 'Projects', link: '/projects/' },
          { text: 'E-Commerce', link: '/ecommerce/' },
          { text: 'Procurement', link: '/procurement/' },
        ]
      },
      { text: 'Reports', link: '/reports/' },
      { text: 'Settings', link: '/settings/' },
      { text: 'Mobile App', link: '/mobile-app/' },
      { text: 'FAQ', link: '/faq' },
    ],

    sidebar: {
      '/getting-started/': [
        {
          text: 'Getting Started',
          items: [
            { text: 'What is Ballie?', link: '/getting-started/' },
            { text: 'Registration', link: '/getting-started/registration' },
            { text: 'Onboarding Wizard', link: '/getting-started/onboarding' },
            { text: 'Navigating the Dashboard', link: '/getting-started/navigation' },
          ]
        }
      ],
      '/accounting/': [
        {
          text: 'Accounting',
          items: [
            { text: 'Overview', link: '/accounting/' },
            { text: 'Chart of Accounts', link: '/accounting/chart-of-accounts' },
            { text: 'Invoices', link: '/accounting/invoices' },
            { text: 'Quotations & Proforma', link: '/accounting/quotations' },
            { text: 'Vouchers', link: '/accounting/vouchers' },
            { text: 'Expenses', link: '/accounting/expenses' },
            { text: 'Payments', link: '/accounting/payments' },
            { text: 'Prepaid Expenses', link: '/accounting/prepaid-expenses' },
          ]
        }
      ],
      '/inventory/': [
        {
          text: 'Inventory',
          items: [
            { text: 'Overview', link: '/inventory/' },
            { text: 'Products', link: '/inventory/products' },
            { text: 'Categories & Units', link: '/inventory/categories' },
            { text: 'Stock Management', link: '/inventory/stock-management' },
            { text: 'Physical Stock Audit', link: '/inventory/physical-stock' },
          ]
        }
      ],
      '/crm/': [
        {
          text: 'CRM',
          items: [
            { text: 'Overview', link: '/crm/' },
            { text: 'Customers', link: '/crm/customers' },
            { text: 'Vendors', link: '/crm/vendors' },
            { text: 'CRM Reports', link: '/crm/reports' },
          ]
        }
      ],
      '/pos/': [
        {
          text: 'Point of Sale',
          items: [
            { text: 'Overview', link: '/pos/' },
            { text: 'Cash Register', link: '/pos/cash-register' },
            { text: 'Making Sales', link: '/pos/making-sales' },
          ]
        }
      ],
      '/payroll/': [
        {
          text: 'Payroll & HR',
          items: [
            { text: 'Overview', link: '/payroll/' },
            { text: 'Employees', link: '/payroll/employees' },
            { text: 'Departments & Positions', link: '/payroll/departments-positions' },
            { text: 'Salary Components', link: '/payroll/salary-components' },
            { text: 'Attendance & Shifts', link: '/payroll/attendance' },
            { text: 'Leave Management', link: '/payroll/leave-management' },
            { text: 'Loans & Advances', link: '/payroll/loans-advances' },
            { text: 'Running Payroll', link: '/payroll/payroll-processing' },
            { text: 'Statutory Compliance', link: '/payroll/statutory' },
          ]
        }
      ],
      '/banking/': [
        {
          text: 'Banking',
          items: [
            { text: 'Overview', link: '/banking/' },
            { text: 'Bank Accounts', link: '/banking/bank-accounts' },
            { text: 'Reconciliation', link: '/banking/reconciliation' },
          ]
        }
      ],
      '/projects/': [
        {
          text: 'Projects',
          items: [
            { text: 'Overview', link: '/projects/' },
            { text: 'Managing Projects', link: '/projects/managing-projects' },
            { text: 'Project Expenses', link: '/projects/project-expenses' },
          ]
        }
      ],
      '/ecommerce/': [
        {
          text: 'E-Commerce',
          items: [
            { text: 'Overview', link: '/ecommerce/' },
            { text: 'Store Setup', link: '/ecommerce/store-setup' },
            { text: 'Orders', link: '/ecommerce/orders' },
            { text: 'Coupons', link: '/ecommerce/coupons' },
            { text: 'Shipping', link: '/ecommerce/shipping' },
          ]
        }
      ],
      '/procurement/': [
        {
          text: 'Procurement',
          items: [
            { text: 'Overview', link: '/procurement/' },
            { text: 'Purchase Orders', link: '/procurement/purchase-orders' },
          ]
        }
      ],
      '/reports/': [
        {
          text: 'Reports',
          items: [
            { text: 'Overview', link: '/reports/' },
            { text: 'Financial Reports', link: '/reports/financial-reports' },
            { text: 'Sales Reports', link: '/reports/sales-reports' },
            { text: 'Purchase Reports', link: '/reports/purchase-reports' },
            { text: 'Inventory Reports', link: '/reports/inventory-reports' },
          ]
        }
      ],
      '/settings/': [
        {
          text: 'Settings',
          items: [
            { text: 'Overview', link: '/settings/' },
            { text: 'Company Settings', link: '/settings/company-settings' },
            { text: 'Users & Roles', link: '/settings/users-roles' },
            { text: 'Modules', link: '/settings/modules' },
            { text: 'Subscription & Billing', link: '/settings/subscription' },
          ]
        }
      ],
      '/mobile-app/': [
        {
          text: 'Mobile App',
          items: [
            { text: 'Overview', link: '/mobile-app/' },
            { text: 'Installation', link: '/mobile-app/installation' },
            { text: 'Mobile Features', link: '/mobile-app/features' },
          ]
        }
      ],
      '/audit-trail/': [
        {
          text: 'Audit Trail',
          items: [
            { text: 'Overview', link: '/audit-trail/' },
            { text: 'Viewing Audits', link: '/audit-trail/viewing-audits' },
          ]
        }
      ],
      '/support/': [
        {
          text: 'Support',
          items: [
            { text: 'Overview', link: '/support/' },
            { text: 'Support Tickets', link: '/support/tickets' },
          ]
        }
      ],
    },

    search: {
      provider: 'local',
      options: {
        detailedView: true,
      }
    },

    socialLinks: [
      { icon: 'twitter', link: 'https://twitter.com/ballieapp' },
    ],

    footer: {
      message: 'Ballie — The All-in-One Business Management Platform',
      copyright: 'Copyright © 2024-present Ballie Technologies'
    },

    editLink: {
      pattern: 'https://github.com/your-org/ballie-docs/edit/main/docs/:path',
      text: 'Edit this page'
    },
  },

  // Auto-generate llms-full.txt at build time
  async buildEnd(siteConfig) {
    const fs = await import('fs')
    const path = await import('path')

    const docsDir = siteConfig.srcDir
    const outDir = siteConfig.outDir

    // Collect all .md files recursively
    function collectMdFiles(dir: string, base: string = ''): string[] {
      const entries = fs.readdirSync(dir, { withFileTypes: true })
      let files: string[] = []
      for (const entry of entries) {
        const rel = base ? `${base}/${entry.name}` : entry.name
        if (entry.isDirectory() && !entry.name.startsWith('.')) {
          files = files.concat(collectMdFiles(path.join(dir, entry.name), rel))
        } else if (entry.isFile() && entry.name.endsWith('.md')) {
          files.push(rel)
        }
      }
      return files
    }

    const pages = collectMdFiles(docsDir)

    let fullContent = '# Ballie Documentation — Full Content\n\n'
    fullContent += '> This file contains the complete documentation for Ballie, a multi-tenant business management platform.\n'
    fullContent += '> Generated automatically from doc.ballie.co\n\n'

    for (const page of pages) {
      const content = fs.readFileSync(path.join(docsDir, page), 'utf-8')
      const cleanContent = content
        .replace(/^---[\s\S]*?---\n/m, '') // Remove frontmatter
        .replace(/```[\s\S]*?```/g, '')     // Remove code blocks
        .trim()

      if (cleanContent) {
        fullContent += `\n---\nSource: ${page}\n\n${cleanContent}\n`
      }
    }

    fs.writeFileSync(path.join(outDir, 'llms-full.txt'), fullContent)
    console.log('✅ Generated llms-full.txt')
  },

  // Inject JSON-LD structured data per page
  transformHead({ pageData }) {
    const jsonLd = {
      '@context': 'https://schema.org',
      '@type': 'TechArticle',
      'headline': pageData.title,
      'description': pageData.description || pageData.frontmatter?.description || '',
      'url': `https://doc.ballie.co/${pageData.relativePath.replace(/\.md$/, '')}`,
      'publisher': {
        '@type': 'Organization',
        'name': 'Ballie Technologies',
        'url': 'https://ballie.co'
      }
    }

    return [
      ['script', { type: 'application/ld+json' }, JSON.stringify(jsonLd)]
    ]
  }
})
