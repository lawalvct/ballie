# Support Center Feature - Complete Implementation Plan

## Overview

A comprehensive support ticket system enabling tenants (companies) to request help from super admin, with efficient ticket management, real-time notifications, and knowledge base integration.

---

## 🎯 Feature Objectives

1. **For Tenants:**

    - Easy ticket submission
    - Track ticket progress
    - Quick access to solutions
    - Rate support quality

2. **For Super Admin:**
    - Centralized ticket management
    - Efficient response system
    - Performance metrics
    - Knowledge base management

---

## 📊 Database Schema

### 1. **support_categories** table

```php
- id (bigint, primary key)
- name (string) // "Technical Support", "Billing", "Feature Request", "Bug Report", "Account Management"
- slug (string, unique)
- description (text, nullable)
- icon (string, nullable) // Icon class or SVG
- color (string, nullable) // Badge color
- is_active (boolean, default: true)
- sort_order (integer, default: 0)
- created_at, updated_at
```

### 2. **support_tickets** table

```php
- id (bigint, primary key)
- ticket_number (string, unique) // Format: TKT-20250001
- tenant_id (bigint, foreign key → tenants)
- user_id (bigint, foreign key → users) // Who created the ticket
- category_id (bigint, foreign key → support_categories)
- subject (string)
- description (longtext)
- priority (enum: low, medium, high, urgent)
- status (enum: new, open, in_progress, waiting_customer, resolved, closed)
- assigned_to (bigint, nullable, foreign key → super_admins) // For team assignments
- first_response_at (timestamp, nullable) // SLA tracking
- resolved_at (timestamp, nullable)
- closed_at (timestamp, nullable)
- satisfaction_rating (tinyint, nullable, 1-5)
- satisfaction_comment (text, nullable)
- metadata (json, nullable) // Additional context (app version, browser, etc.)
- created_at, updated_at
- deleted_at (soft delete)

Indexes:
- tenant_id
- user_id
- status
- priority
- ticket_number
- created_at
```

### 3. **support_ticket_replies** table

```php
- id (bigint, primary key)
- ticket_id (bigint, foreign key → support_tickets)
- user_id (bigint, nullable, foreign key → users) // Tenant user
- admin_id (bigint, nullable, foreign key → super_admins) // Super admin
- message (longtext)
- is_internal_note (boolean, default: false) // Only visible to admins
- is_automated (boolean, default: false) // Auto-generated messages
- created_at, updated_at

Indexes:
- ticket_id
- created_at
```

### 4. **support_ticket_attachments** table

```php
- id (bigint, primary key)
- ticket_id (bigint, foreign key → support_tickets)
- reply_id (bigint, nullable, foreign key → support_ticket_replies)
- filename (string)
- original_name (string)
- file_path (string)
- file_size (bigint) // in bytes
- mime_type (string)
- uploaded_by_type (string) // "tenant" or "admin"
- uploaded_by_id (bigint)
- created_at, updated_at
```

### 5. **knowledge_base_articles** table

```php
- id (bigint, primary key)
- category_id (bigint, foreign key → support_categories)
- title (string)
- slug (string, unique)
- content (longtext)
- excerpt (text, nullable)
- featured_image (string, nullable)
- is_published (boolean, default: false)
- is_featured (boolean, default: false)
- view_count (integer, default: 0)
- helpful_count (integer, default: 0)
- not_helpful_count (integer, default: 0)
- sort_order (integer, default: 0)
- meta_title (string, nullable)
- meta_description (text, nullable)
- author_id (bigint, foreign key → super_admins)
- published_at (timestamp, nullable)
- created_at, updated_at
- deleted_at (soft delete)

Indexes:
- category_id
- slug
- is_published
- is_featured
```

### 6. **support_ticket_status_history** table

```php
- id (bigint, primary key)
- ticket_id (bigint, foreign key → support_tickets)
- old_status (string)
- new_status (string)
- changed_by_type (string) // "tenant" or "admin"
- changed_by_id (bigint)
- notes (text, nullable)
- created_at
```

### 7. **support_response_templates** table

```php
- id (bigint, primary key)
- name (string)
- subject (string, nullable) // For email templates
- content (longtext)
- category_id (bigint, nullable, foreign key → support_categories)
- is_active (boolean, default: true)
- usage_count (integer, default: 0)
- created_by (bigint, foreign key → super_admins)
- created_at, updated_at
```

---

## 🔄 Ticket Status Workflow

```
NEW → OPEN → IN_PROGRESS → WAITING_CUSTOMER → RESOLVED → CLOSED
   ↓                            ↓
   └─────────────────→ CLOSED ←─┘
```

**Status Definitions:**

-   **NEW**: Just submitted, awaiting admin review
-   **OPEN**: Acknowledged by admin, queued for action
-   **IN_PROGRESS**: Admin actively working on issue
-   **WAITING_CUSTOMER**: Awaiting response from tenant
-   **RESOLVED**: Issue fixed, awaiting confirmation
-   **CLOSED**: Ticket completed and archived

---

## 🎨 Tenant Side Implementation

### Routes (routes/tenant.php)

```php
Route::prefix('support')->name('support.')->group(function () {
    // Ticket Management
    Route::get('/', [SupportController::class, 'index'])->name('index');
    Route::get('/create', [SupportController::class, 'create'])->name('create');
    Route::post('/tickets', [SupportController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [SupportController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/reply', [SupportController::class, 'reply'])->name('tickets.reply');
    Route::post('/tickets/{ticket}/close', [SupportController::class, 'close'])->name('tickets.close');
    Route::post('/tickets/{ticket}/reopen', [SupportController::class, 'reopen'])->name('tickets.reopen');
    Route::post('/tickets/{ticket}/rate', [SupportController::class, 'rate'])->name('tickets.rate');

    // File Attachments
    Route::post('/tickets/{ticket}/upload', [SupportController::class, 'uploadAttachment'])->name('tickets.upload');
    Route::get('/attachments/{attachment}/download', [SupportController::class, 'downloadAttachment'])->name('attachments.download');

    // Knowledge Base
    Route::get('/kb', [SupportController::class, 'knowledgeBase'])->name('kb.index');
    Route::get('/kb/{category}', [SupportController::class, 'knowledgeBaseCategory'])->name('kb.category');
    Route::get('/kb/{category}/{article}', [SupportController::class, 'knowledgeBaseArticle'])->name('kb.article');
    Route::post('/kb/{article}/helpful', [SupportController::class, 'markHelpful'])->name('kb.helpful');

    // Search
    Route::get('/search', [SupportController::class, 'search'])->name('search');
});
```

### Views Structure

```
resources/views/tenant/support/
├── index.blade.php              // Ticket list
├── create.blade.php             // New ticket form
├── show.blade.php               // Ticket detail with replies
├── components/
│   ├── ticket-card.blade.php
│   ├── ticket-status-badge.blade.php
│   ├── ticket-priority-badge.blade.php
│   ├── reply-item.blade.php
│   └── file-attachment.blade.php
├── knowledge-base/
│   ├── index.blade.php          // KB home
│   ├── category.blade.php       // Articles by category
│   └── article.blade.php        // Full article view
└── partials/
    ├── ticket-filters.blade.php
    └── quick-links.blade.php
```

### Controller: SupportController.php

```php
Key Methods:
- index(): List user's tickets with filters
- create(): Show ticket creation form
- store(): Create new ticket with validation
- show(): Display ticket with all replies
- reply(): Add reply to ticket
- close(): Mark ticket as closed
- reopen(): Reopen closed ticket
- rate(): Submit satisfaction rating
- uploadAttachment(): Handle file uploads
- downloadAttachment(): Serve attachment file
- knowledgeBase(): Show KB articles
- search(): Search tickets and KB
```

### UI Components

#### 1. Ticket List Page

-   **Filter by:** Status, Priority, Category, Date range
-   **Sort by:** Latest, Oldest, Priority, Status
-   **Display:** Ticket number, Subject, Status, Priority, Category, Last updated
-   **Actions:** View, Reply count badge
-   **Empty state:** Helpful message with "Create Ticket" button

#### 2. Create Ticket Form

-   **Fields:**
    -   Category (dropdown)
    -   Priority (radio buttons with descriptions)
    -   Subject (text input)
    -   Description (rich text editor)
    -   Attachments (drag & drop, max 5 files, 10MB each)
    -   System info (auto-collected: browser, OS, app version)
-   **Validation:**
    -   Category required
    -   Subject required (min 10 chars)
    -   Description required (min 20 chars)
    -   File types: jpg, png, pdf, txt, log, zip

#### 3. Ticket Detail Page

-   **Header:** Ticket number, Status, Priority, Created date
-   **Timeline:** All replies with timestamps
-   **Reply Box:** Rich text editor for responses
-   **Sidebar:**
    -   Ticket information
    -   Category
    -   Created by
    -   Assigned to (if visible)
    -   Status history
    -   Attachments list
-   **Actions:**
    -   Add Reply
    -   Upload Attachment
    -   Close Ticket (if resolved)
    -   Reopen Ticket (if closed and within 30 days)
    -   Rate Support (after resolution)

#### 4. Knowledge Base

-   **Home:** Featured articles, popular categories
-   **Category Page:** All articles in category
-   **Article Page:**
    -   Full content with TOC
    -   Related articles
    -   Was this helpful? (Yes/No buttons)
    -   Still need help? (Create ticket button)
    -   Last updated date

---

## 🛠️ Super Admin Side Implementation

### Routes (routes/super-admin.php)

```php
Route::prefix('support')->name('support.')->middleware(['auth:super_admin'])->group(function () {
    // Dashboard & Tickets
    Route::get('/', [SuperAdminSupportController::class, 'index'])->name('index');
    Route::get('/tickets/{ticket}', [SuperAdminSupportController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/reply', [SuperAdminSupportController::class, 'reply'])->name('tickets.reply');
    Route::post('/tickets/{ticket}/internal-note', [SuperAdminSupportController::class, 'internalNote'])->name('tickets.internal-note');
    Route::patch('/tickets/{ticket}/status', [SuperAdminSupportController::class, 'updateStatus'])->name('tickets.update-status');
    Route::patch('/tickets/{ticket}/priority', [SuperAdminSupportController::class, 'updatePriority'])->name('tickets.update-priority');
    Route::patch('/tickets/{ticket}/assign', [SuperAdminSupportController::class, 'assign'])->name('tickets.assign');
    Route::delete('/tickets/{ticket}', [SuperAdminSupportController::class, 'destroy'])->name('tickets.destroy');

    // Bulk Actions
    Route::post('/tickets/bulk/status', [SuperAdminSupportController::class, 'bulkUpdateStatus'])->name('tickets.bulk-status');
    Route::post('/tickets/bulk/assign', [SuperAdminSupportController::class, 'bulkAssign'])->name('tickets.bulk-assign');
    Route::delete('/tickets/bulk/delete', [SuperAdminSupportController::class, 'bulkDelete'])->name('tickets.bulk-delete');

    // Response Templates
    Route::get('/templates', [ResponseTemplateController::class, 'index'])->name('templates.index');
    Route::get('/templates/create', [ResponseTemplateController::class, 'create'])->name('templates.create');
    Route::post('/templates', [ResponseTemplateController::class, 'store'])->name('templates.store');
    Route::get('/templates/{template}/edit', [ResponseTemplateController::class, 'edit'])->name('templates.edit');
    Route::put('/templates/{template}', [ResponseTemplateController::class, 'update'])->name('templates.update');
    Route::delete('/templates/{template}', [ResponseTemplateController::class, 'destroy'])->name('templates.destroy');

    // Categories
    Route::resource('categories', SupportCategoryController::class);
    Route::post('/categories/reorder', [SupportCategoryController::class, 'reorder'])->name('categories.reorder');

    // Knowledge Base Management
    Route::prefix('kb')->name('kb.')->group(function () {
        Route::get('/', [KnowledgeBaseController::class, 'index'])->name('index');
        Route::get('/create', [KnowledgeBaseController::class, 'create'])->name('create');
        Route::post('/', [KnowledgeBaseController::class, 'store'])->name('store');
        Route::get('/{article}/edit', [KnowledgeBaseController::class, 'edit'])->name('edit');
        Route::put('/{article}', [KnowledgeBaseController::class, 'update'])->name('update');
        Route::delete('/{article}', [KnowledgeBaseController::class, 'destroy'])->name('destroy');
        Route::post('/{article}/publish', [KnowledgeBaseController::class, 'publish'])->name('publish');
        Route::post('/{article}/unpublish', [KnowledgeBaseController::class, 'unpublish'])->name('unpublish');
    });

    // Reports & Analytics
    Route::get('/reports', [SuperAdminSupportController::class, 'reports'])->name('reports');
    Route::get('/reports/export', [SuperAdminSupportController::class, 'exportReport'])->name('reports.export');

    // Settings
    Route::get('/settings', [SuperAdminSupportController::class, 'settings'])->name('settings');
    Route::put('/settings', [SuperAdminSupportController::class, 'updateSettings'])->name('settings.update');
});
```

### Views Structure

```
resources/views/super-admin/support/
├── index.blade.php              // Dashboard with stats
├── show.blade.php               // Ticket detail (admin view)
├── reports.blade.php            // Analytics & metrics
├── settings.blade.php           // Support settings
├── templates/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── categories/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── knowledge-base/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
└── components/
    ├── ticket-card-admin.blade.php
    ├── stats-widget.blade.php
    ├── quick-filters.blade.php
    └── response-template-selector.blade.php
```

### Controller: SuperAdminSupportController.php

```php
Key Methods:
- index(): Dashboard with ticket list and statistics
- show(): Ticket detail with admin actions
- reply(): Post admin reply to ticket
- internalNote(): Add internal note (not visible to tenant)
- updateStatus(): Change ticket status
- updatePriority(): Change ticket priority
- assign(): Assign ticket to admin (if team)
- bulkUpdateStatus(): Update multiple tickets
- reports(): Analytics dashboard
- settings(): Support configuration
```

### Dashboard Components

#### 1. Statistics Widgets (Top of Page)

```
┌──────────────┬──────────────┬──────────────┬──────────────┐
│ New Tickets  │ In Progress  │ Avg Response │ Satisfaction │
│      12      │      25      │   2.5 hrs    │    4.8/5.0   │
└──────────────┴──────────────┴──────────────┴──────────────┘
```

#### 2. Ticket List (Main Area)

-   **Filters:**
    -   Status (All, New, Open, In Progress, Waiting Customer, Resolved, Closed)
    -   Priority (All, Low, Medium, High, Urgent)
    -   Category (All categories)
    -   Tenant (Search by company name)
    -   Date range
    -   Assigned to (Me, Unassigned, Others)
-   **Columns:**
    -   Ticket #
    -   Tenant (Company name)
    -   Subject
    -   Category
    -   Priority
    -   Status
    -   Last Reply
    -   Created
    -   Actions (View, Quick Reply)
-   **Bulk Actions:**
    -   Change Status
    -   Assign To
    -   Delete

#### 3. Ticket Detail (Admin View)

-   **Header:**
    -   Ticket number with copy button
    -   Status dropdown (change inline)
    -   Priority dropdown (change inline)
    -   Assign to dropdown (if multiple admins)
-   **Tenant Info Sidebar:**
    -   Company name (link to tenant dashboard)
    -   User name and email
    -   Account status
    -   Subscription plan
    -   Total tickets (all time)
-   **Main Content:**
    -   Original ticket description
    -   All replies chronologically
    -   Internal notes (highlighted, tenant can't see)
    -   Attachments
-   **Reply Section:**
    -   Rich text editor
    -   Response template dropdown
    -   Internal note checkbox
    -   Attach files button
    -   Status update on reply
-   **Action Buttons:**
    -   Send Reply
    -   Add Internal Note
    -   Close Ticket
    -   Merge Tickets (optional)
    -   Delete Ticket

#### 4. Response Templates Management

-   **List View:**
    -   Template name
    -   Category
    -   Usage count
    -   Last used
    -   Actions (Edit, Delete, Use)
-   **Create/Edit Form:**
    -   Template name
    -   Category (optional)
    -   Subject (for emails)
    -   Content (rich text)
    -   Variables: {customer_name}, {company_name}, {ticket_number}, {ticket_subject}

#### 5. Knowledge Base Management

-   **List View:**
    -   Article title
    -   Category
    -   Status (Published/Draft)
    -   Views
    -   Helpful votes
    -   Last updated
    -   Actions (Edit, Publish/Unpublish, Delete)
-   **Create/Edit Form:**
    -   Title
    -   Category
    -   Content (rich text with images)
    -   Excerpt
    -   Featured image
    -   Is featured checkbox
    -   Meta title & description (SEO)
    -   Sort order

#### 6. Analytics & Reports

-   **Overview Dashboard:**
    -   Total tickets (all time)
    -   Open tickets
    -   Resolved tickets (this month)
    -   Average resolution time
    -   First response time
    -   Customer satisfaction score
-   **Charts:**
    -   Tickets over time (line chart)
    -   Tickets by category (pie chart)
    -   Tickets by priority (bar chart)
    -   Resolution time trend (line chart)
    -   Tickets by tenant (top 10 bar chart)
-   **Export Options:**
    -   Date range selector
    -   Export to CSV/Excel
    -   PDF report

---

## 🔔 Notification System

### Email Notifications

#### For Tenants:

1. **New Ticket Created** (Confirmation)

    - Subject: "Ticket #{number} Created - {subject}"
    - Include ticket details and expected response time

2. **Admin Reply Received**

    - Subject: "Re: Ticket #{number} - {subject}"
    - Include reply content
    - Link to view full ticket

3. **Status Changed**

    - Subject: "Ticket #{number} Status Updated - {new_status}"
    - Explain status change

4. **Ticket Resolved**

    - Subject: "Ticket #{number} Resolved - Please Confirm"
    - Ask for satisfaction rating
    - Auto-close notice (in 7 days)

5. **Ticket Closed**
    - Subject: "Ticket #{number} Closed - Thank You"
    - Summary of resolution
    - Option to reopen

#### For Super Admin:

1. **New Ticket Created**

    - Subject: "New Support Ticket #{number} from {company}"
    - Include tenant info and ticket preview

2. **Tenant Reply Received**

    - Subject: "Re: Ticket #{number} - New Reply from {company}"
    - Include reply content

3. **Ticket Reopened**

    - Subject: "Ticket #{number} Reopened by {company}"

4. **SLA Breach Warning**
    - Subject: "SLA Alert: Ticket #{number} Requires Attention"
    - For tickets exceeding response time threshold

### In-App Notifications

-   Bell icon with badge count
-   Real-time updates (using Laravel Echo + Pusher/Socket.io)
-   Notification panel with recent alerts
-   Mark as read functionality

---

## ⚙️ Configuration & Settings

### Support Settings (Super Admin)

```php
// config/support.php or database settings table
return [
    'auto_close_resolved_days' => 7,
    'max_attachments_per_ticket' => 5,
    'max_attachment_size' => 10, // MB
    'allowed_attachment_types' => ['jpg', 'jpeg', 'png', 'pdf', 'txt', 'log', 'zip'],
    'enable_satisfaction_rating' => true,
    'enable_knowledge_base' => true,
    'first_response_sla' => 4, // hours
    'resolution_sla' => 48, // hours
    'enable_email_notifications' => true,
    'admin_email' => 'support@ballie.co',
    'enable_auto_assignment' => false, // Round-robin if multiple admins
];
```

---

## 📧 Email Templates

Create Mailable classes:

-   `TicketCreatedMail` (tenant)
-   `TicketRepliedMail` (tenant)
-   `TicketStatusChangedMail` (tenant)
-   `TicketResolvedMail` (tenant)
-   `NewTicketNotificationMail` (admin)
-   `TicketReplyNotificationMail` (admin)

---

## 🔐 Security & Permissions

### Tenant Side:

-   Users can only see tickets they created
-   Admins/Owners can see all company tickets
-   File upload validation and sanitization
-   XSS protection in rich text editor

### Super Admin Side:

-   View all tickets from all tenants
-   Soft delete tickets (keep history)
-   Audit trail for status changes
-   Attachment virus scanning (optional)

---

## 🎨 UI/UX Considerations

### Design Principles:

1. **Simplicity:** Easy ticket creation in 3 steps
2. **Clarity:** Clear status indicators and explanations
3. **Speed:** Quick access to common actions
4. **Feedback:** Instant confirmations and updates
5. **Helpfulness:** Contextual suggestions and KB articles

### Color Coding:

-   **Priority:**

    -   Low: Gray/Blue
    -   Medium: Yellow/Orange
    -   High: Orange/Red
    -   Urgent: Red/Pink (with animation)

-   **Status:**
    -   New: Purple
    -   Open: Blue
    -   In Progress: Yellow
    -   Waiting Customer: Orange
    -   Resolved: Green
    -   Closed: Gray

### Icons:

-   Ticket: 🎫 or ticket icon
-   Reply: 💬 or chat bubble
-   Attachment: 📎 or paperclip
-   Knowledge Base: 📚 or book
-   Search: 🔍 or magnifying glass

---

## 📱 Mobile Responsiveness

-   Responsive tables with horizontal scroll
-   Mobile-friendly forms
-   Touch-optimized file upload
-   Collapsible ticket details
-   Swipe actions for quick operations

---

## 🚀 Implementation Phases

### Phase 1: Core Ticket System (Week 1)

-   [ ] Database migrations
-   [ ] Models and relationships
-   [ ] Basic CRUD for tickets (tenant side)
-   [ ] Ticket list and detail views
-   [ ] Reply functionality
-   [ ] File attachments

### Phase 2: Admin Panel (Week 1-2)

-   [ ] Super admin ticket dashboard
-   [ ] Ticket management (status, priority, assignment)
-   [ ] Admin reply functionality
-   [ ] Internal notes
-   [ ] Bulk actions

### Phase 3: Notifications (Week 2)

-   [ ] Email notification system
-   [ ] In-app notifications
-   [ ] Real-time updates (optional)

### Phase 4: Knowledge Base (Week 2-3)

-   [ ] KB article management
-   [ ] Category system
-   [ ] Article views and search
-   [ ] Helpful voting

### Phase 5: Advanced Features (Week 3)

-   [ ] Response templates
-   [ ] Satisfaction ratings
-   [ ] Analytics and reports
-   [ ] SLA tracking
-   [ ] Auto-assignment

### Phase 6: Polish & Testing (Week 4)

-   [ ] UI/UX refinements
-   [ ] Mobile optimization
-   [ ] Performance optimization
-   [ ] Security audit
-   [ ] End-to-end testing

---

## 📝 Technical Notes

### Performance Optimization:

-   Eager load relationships (tenant, user, category, replies)
-   Index frequently queried columns
-   Cache KB articles
-   Paginate ticket lists (25 per page)
-   Lazy load attachments

### Testing:

-   Unit tests for models
-   Feature tests for controllers
-   Browser tests for critical flows
-   Email testing (Mailtrap)
-   File upload testing

### SEO (Knowledge Base):

-   Friendly URLs (slugs)
-   Meta tags
-   Sitemap generation
-   Schema.org markup

---

## 🎯 Success Metrics

### Track:

1. Average first response time
2. Average resolution time
3. Customer satisfaction score
4. Ticket volume trends
5. Most common categories
6. KB article views and helpfulness
7. Ticket resolution rate
8. Reopened ticket percentage

### Goals:

-   First response: < 4 hours
-   Resolution time: < 48 hours
-   Satisfaction: > 4.5/5.0
-   KB self-service rate: > 30%

---

## 🔄 Future Enhancements

-   [ ] Live chat integration
-   [ ] Video call support
-   [ ] AI-powered suggested solutions
-   [ ] Multi-language support
-   [ ] WhatsApp/Telegram integration
-   [ ] Customer satisfaction surveys
-   [ ] Advanced automation rules
-   [ ] SLA dashboards
-   [ ] Team collaboration features
-   [ ] API for third-party integrations

---

## 📚 Resources Needed

### UI Components:

-   Rich text editor: TinyMCE or Quill
-   File uploader: Dropzone.js
-   Charts: Chart.js or ApexCharts
-   Icons: Heroicons or FontAwesome
-   Date picker: Flatpickr

### Laravel Packages:

-   `spatie/laravel-medialibrary` (file management)
-   `spatie/laravel-activitylog` (audit trail)
-   `beyondcode/laravel-websockets` (real-time, optional)
-   `barryvdh/laravel-dompdf` (PDF reports)
-   `maatwebsite/excel` (Excel exports)

---

## 🎓 Implementation Order

**Start with Tenant Side:**

1. Database migrations (all tables)
2. Models with relationships
3. Tenant routes
4. SupportController (tenant)
5. Ticket list view
6. Create ticket form
7. Ticket detail view
8. Reply functionality
9. File attachments
10. Email notifications (tenant)

**Then Super Admin Side:**

1. Super admin routes
2. SuperAdminSupportController
3. Admin dashboard
4. Ticket management views
5. Response templates
6. Categories management
7. Knowledge base CRUD
8. Analytics/reports
9. Email notifications (admin)
10. Settings page

---

## ✅ Testing Checklist

### Tenant Side:

-   [ ] Create ticket with all fields
-   [ ] Upload various file types
-   [ ] Reply to ticket
-   [ ] View ticket history
-   [ ] Close ticket
-   [ ] Reopen ticket
-   [ ] Rate support
-   [ ] Search tickets
-   [ ] Browse knowledge base
-   [ ] Mark KB article helpful

### Super Admin Side:

-   [ ] View all tickets
-   [ ] Filter by status/priority/category
-   [ ] Reply to ticket
-   [ ] Add internal note
-   [ ] Change ticket status
-   [ ] Change ticket priority
-   [ ] Assign ticket
-   [ ] Bulk update tickets
-   [ ] Create response template
-   [ ] Create KB article
-   [ ] View analytics
-   [ ] Export reports

### Notifications:

-   [ ] Tenant receives new ticket email
-   [ ] Tenant receives reply email
-   [ ] Admin receives new ticket email
-   [ ] Admin receives reply email
-   [ ] In-app notifications work
-   [ ] Email preferences respected

---

## 🎨 Design Mockup References

### Inspiration:

-   Zendesk Support
-   Freshdesk
-   Help Scout
-   Intercom
-   Helpwise

---

## 📞 Support Contact Integration

Add support link to tenant sidebar/header:

```blade
<a href="{{ route('tenant.support.index') }}" class="...">
    <svg>...</svg> Support Center
</a>
```

Add support widget (floating button):

```blade
<div class="fixed bottom-4 right-4 z-50">
    <a href="{{ route('tenant.support.create') }}"
       class="bg-blue-600 text-white rounded-full p-4 shadow-lg hover:bg-blue-700">
        <svg class="w-6 h-6">...</svg>
    </a>
</div>
```

---

## 🏁 Ready to Start!

Begin with **Phase 1: Core Ticket System (Tenant Side)** and follow the implementation order above. After completing the tenant side, use this document to implement the super admin side.

Good luck! 🚀
