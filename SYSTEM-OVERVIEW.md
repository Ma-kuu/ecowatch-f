# EcoWatch System - Master Documentation

## Overview
**EcoWatch** is a web-based environmental violation reporting and tracking system built with Laravel 12 for Local Government Units (LGUs) in Davao del Norte, Philippines. The system enables citizens to report environmental violations, track their resolution, and engage with their community through a public feed system.

---

## System Architecture

**Technology Stack:**
- Backend: Laravel 12 (PHP 8.2+)
- Database: MySQL
- Frontend: Blade Templates + Bootstrap 5
- JavaScript: Vanilla JS + Chart.js for analytics
- CSS: Bootstrap 5 + Custom SCSS
- Build Tools: Vite
- Additional: Vue.js 3 (for future enhancements)

**User Roles:**
1. **Admin** - Full system control, report validation, user management
2. **LGU Staff** - Manage assigned reports, post announcements
3. **Registered Users** - Submit reports, track status, confirm resolution
4. **Anonymous/Public** - Submit anonymous reports, view public feed, upvote reports

---

## Controllers & Features

### Public Controllers

#### `PublicFeedController`
Displays public environmental violation feed with filtering, upvoting, and flagging capabilities.
- **Tables:** reports, violation_types, lgus, barangays, report_upvotes, report_flags
- **Features:** Pagination, filtering by type/status/LGU, upvote system, report flagging

#### `AnonymousReportController`
Handles anonymous report submissions (no login required).
- **Tables:** reports, photos, report_validity
- **Features:** Anonymous submission, admin verification required before public display

#### `ReportStatusController`
Public API for checking report status by report ID.
- **Tables:** reports, lgus, barangays
- **Features:** Public status lookup, no authentication required

### Authentication Controllers

#### `Auth\LoginController`
Manages user authentication and login.
- **Tables:** users
- **Features:** Multi-role login (admin/LGU/user), remember me, session management

#### `Auth\RegisterController`
Handles new user registration.
- **Tables:** users
- **Features:** Email validation, password hashing, default role assignment

### User Controllers

#### `DashboardController`
Provides role-specific dashboards with statistics and report management.
- **Tables:** reports, violation_types, lgus, users, notifications
- **Features:**
  - User Dashboard: Personal reports with filters
  - Admin Dashboard: All reports, category statistics, status breakdown
  - LGU Dashboard: Assigned reports, monthly trends, resolution analytics

#### `ReportController`
Manages authenticated user report submissions and updates.
- **Tables:** reports, photos, violation_types, barangays, lgus
- **Features:** Create/update reports, photo upload, GPS-based LGU auto-assignment

#### `UserController`
Handles user confirmation/rejection of report resolutions.
- **Tables:** reports, notifications
- **Features:** Confirm fixed reports, reject and request re-work, notify LGU staff

#### `NotificationController`
Manages user notifications (read, unread, mark all as read).
- **Tables:** notifications
- **Features:** Real-time updates, mark as read, bulk operations

#### `UserSettingsController`
User profile and password management.
- **Tables:** users
- **Features:** Update profile, change password, email updates

### LGU Staff Controllers

#### `LguSettingsController`
LGU staff profile and password management.
- **Tables:** users, lgus
- **Features:** Update LGU staff profile, password changes

### Admin Controllers

#### `AdminController`
Full administrative control panel.
- **Tables:** users, reports, lgus, violation_types, public_announcements
- **Features:**
  - User management (create, edit, toggle status, delete)
  - Report validation and verification
  - Report priority adjustment
  - Report deletion
  - System-wide announcements
  - Analytics and reporting

---

## Blade Views & Pages

### Public Pages

#### `index.blade.php`
Homepage with hero section and call-to-action for reporting.
- **Features:** Hero banner, quick stats, violation type overview

#### `about.blade.php`
About page explaining the EcoWatch system mission and features.
- **Features:** System information, contact details

#### `feed.blade.php`
Public feed displaying verified environmental violation reports.
- **Features:**
  - Card-based report display with photos
  - Filter by violation type, status, LGU
  - Upvote system (authenticated & IP-based for guests)
  - Flag inappropriate reports
  - Pagination with custom styling
  - View count tracking

#### `report-form.blade.php`
Entry point for authenticated report submission.
- **Features:** Role check, redirect to login or report form

#### `report-anon.blade.php`
Anonymous report submission form.
- **Features:**
  - Location selection (LGU/Barangay dropdowns)
  - Violation type selection
  - Description and exact location fields
  - Photo upload (multiple files)
  - GPS coordinate capture via map
  - No authentication required

#### `report-show.blade.php`
View and edit existing report details.
- **Features:**
  - Report information display
  - Photo gallery
  - Status timeline
  - Edit capability (if pending)
  - Map location display

#### `report-status.blade.php`
Public status lookup interface.
- **Features:** Search by report ID, display current status and progress

#### `login.blade.php`
User authentication login form.
- **Features:** Email/password login, remember me, registration link

#### `register.blade.php`
New user registration form.
- **Features:** Name, email, password validation, LGU selection

### Dashboard Pages

#### `auth/user-dashboard.blade.php`
Personal dashboard for registered users.
- **Features:**
  - Overview statistics (total, pending, in-progress, resolved)
  - Reports table with filters
  - Status badges
  - Quick actions (view, edit, delete)
  - Date range filtering

#### `auth/admin-dashboard.blade.php`
Administrative control panel.
- **Features:**
  - System-wide statistics
  - Category breakdown (doughnut chart)
  - Status distribution (bar chart)
  - Monthly trends (line chart)
  - All reports table with validation controls
  - Priority assignment
  - Bulk operations

#### `auth/lgu-dashboard.blade.php`
LGU staff workspace.
- **Features:**
  - Assigned reports overview
  - Monthly resolution trends
  - Category statistics
  - Mark reports as in-progress/fixed
  - Upload proof photos
  - LGU announcements section

#### `auth/lgu-announcements.blade.php`
LGU announcement management interface.
- **Features:** Create, edit, pin, delete announcements for local residents

#### `auth/admin-announcements.blade.php`
System-wide announcement management.
- **Features:** Admin-level announcements visible to all users

### Settings Pages

#### `settings/user.blade.php`
User profile and password settings.
- **Features:** Update name, email, password change

#### `settings/lgu.blade.php`
LGU staff settings.
- **Features:** LGU staff profile updates, password management

#### `settings/admin-settings.blade.php`
Admin user management panel.
- **Features:**
  - User list with role filters
  - Create new users (admin/LGU/user)
  - Edit user details
  - Toggle active/inactive status
  - Delete users
  - Assign LGU to staff

### Notifications

#### `notifications/index.blade.php`
User notification center.
- **Features:**
  - Unread/read notifications
  - Mark as read
  - Mark all as read
  - Navigate to related reports

### Components

#### `components/stat-card.blade.php`
Reusable statistics card component.
- **Usage:** Dashboard metrics display

#### `components/dashboard-filters.blade.php`
Reusable filter component for dashboards.
- **Features:** Status, type, date range, priority, LGU, barangay filters

#### `components/map-lightbox.blade.php`
Map display in lightbox modal.
- **Features:** Display report location on interactive map

#### `components/lgu/announcements-section.blade.php`
LGU announcements display section.
- **Features:** Show pinned and recent announcements

#### Chart Components
- `components/charts/bar.blade.php` - Bar chart for status distribution
- `components/charts/line.blade.php` - Line chart for trends
- `components/charts/doughnut.blade.php` - Doughnut chart for category breakdown

#### Modal Components
- `components/modals/validate-report.blade.php` - Admin report validation modal
- `components/modals/view-report.blade.php` - View report details modal
- `components/modals/view-report-user.blade.php` - User-specific report view
- `components/modals/update-report.blade.php` - Update report status modal
- `components/modals/report-success.blade.php` - Success message after submission

### Layouts

#### `layouts/app.blade.php`
Main application layout with navigation and footer.
- **Features:** Responsive navbar, role-based menu items, notification bell

#### `layouts/dashboard.blade.php`
Dashboard-specific layout with sidebar.
- **Features:** Sidebar navigation, breadcrumbs, role-based access

---

## JavaScript Files

### `public/js/location-dropdowns.js`
Dynamic LGU and Barangay dropdown population.
- **Features:**
  - Fetches LGUs from API
  - Populates barangay dropdown based on selected LGU
  - Handles AJAX requests
  - Error handling

### `public/js/report-map.js`
Interactive map for report location selection.
- **Features:**
  - Map initialization with default coordinates
  - Click to set report location
  - Reverse geocoding for address lookup
  - Latitude/longitude capture
  - Marker placement

### `public/js/modal-helper.js`
Modal popup utilities and event handlers.
- **Features:**
  - Bootstrap modal initialization
  - Dynamic content loading
  - Form submission handling

### `public/js/map-helper.js`
General map utility functions.
- **Features:** Map initialization, marker management, geocoding helpers

### `public/js/map-lightbox.js`
Lightbox functionality for map display.
- **Features:** Open map in fullscreen lightbox, display report location

### `public/js/table-filter.js`
Dashboard table filtering and sorting.
- **Features:**
  - Client-side filtering
  - Column sorting
  - Search functionality
  - Pagination helpers

---

## Database Tables

### Core Tables

#### `users`
System users (admin, LGU staff, registered users).
- **Key Fields:** id, name, email, password, role (admin/lgu/user), lgu_id, is_active
- **Relationships:** Reports, Notifications, Announcements, Upvotes

#### `reports`
Environmental violation reports.
- **Key Fields:**
  - id, report_id (e.g., RPT-001)
  - user_id (nullable for anonymous)
  - violation_type_id
  - status (pending, in-review, in-progress, awaiting-confirmation, resolved, rejected)
  - assigned_lgu_id
  - barangay_id
  - exact_location, latitude, longitude
  - description, remarks
  - is_public, is_anonymous
  - manual_priority (low, medium, high, urgent)
  - upvotes_count, views_count
  - confirmed_by_user, confirmed_by_lgu
  - timestamps
- **Relationships:** User, ViolationType, Barangay, AssignedLgu, Photos, Updates, Validity, Upvotes, Flags

#### `lgus`
Local Government Units (cities/municipalities).
- **Key Fields:** id, name, code, province, latitude, longitude, coverage_radius_km
- **Relationships:** Users, Barangays, Reports, Announcements

#### `barangays`
Barangays under each LGU.
- **Key Fields:** id, lgu_id, name, code
- **Relationships:** LGU, Reports

#### `violation_types`
Categories of environmental violations.
- **Key Fields:** id, name, slug, description, icon, color, severity (low, medium, high)
- **Relationships:** Reports
- **Examples:** Illegal Dumping, Water Pollution, Air Pollution, Deforestation, etc.

#### `photos`
Report evidence photos.
- **Key Fields:** id, report_id, file_path, file_name, uploaded_by, is_proof_of_fix
- **Relationships:** Report, Uploader

### Supporting Tables

#### `notifications`
User notifications for report updates.
- **Key Fields:** id, user_id, report_id, type, title, message, read_at
- **Types:**
  - report_fixed (LGU marked as fixed)
  - report_confirmed (User confirmed resolution)
  - report_rejected (User rejected fix)
  - new_report (Admin notification)
  - report_flagged (Admin notification)
  - announcement (New announcement)

#### `report_validity`
Tracks report verification status.
- **Key Fields:** id, report_id, status (pending, verified, disputed), reviewed_by, disputed_by, notes
- **Purpose:** Admin verification of anonymous reports

#### `report_updates`
Timeline of report progress updates.
- **Key Fields:** id, report_id, created_by, update_type (status_change, progress_note, admin_note), title, description
- **Purpose:** Audit trail and progress tracking

#### `report_upvotes`
Community upvote tracking.
- **Key Fields:** id, report_id, user_id (nullable), ip_address
- **Purpose:** Track community engagement, prevent duplicate upvotes

#### `report_flags`
Inappropriate report flagging.
- **Key Fields:** id, report_id, user_id, reason, resolved
- **Purpose:** Community moderation, admin review queue

#### `public_announcements`
LGU and admin announcements.
- **Key Fields:** id, lgu_id (nullable for admin), created_by, title, content, type (info, warning, success), is_pinned, expires_at
- **Relationships:** LGU, Creator

### System Tables
- `cache` - Laravel cache storage
- `cache_locks` - Cache locking mechanism
- `jobs` - Queue jobs
- `job_batches` - Batch job tracking
- `failed_jobs` - Failed queue jobs
- `password_reset_tokens` - Password reset functionality
- `sessions` - User sessions

---

## Key Features & Workflows

### 1. Report Submission Flow
```
User/Anonymous → Report Form → Validation
  ↓
Photo Upload → GPS Location Capture
  ↓
Auto-assign to Nearest LGU (based on coordinates + coverage radius)
  ↓
Generate Sequential Report ID (RPT-001, RPT-002...)
  ↓
Create Report Validity Record (pending)
  ↓
Notify Admins (new report)
  ↓
Status: pending (awaiting admin verification)
```

### 2. Report Lifecycle (Status Transitions)
```
pending → in-review (admin validates)
  ↓
in-progress (LGU working on issue)
  ↓
awaiting-confirmation (LGU uploads proof, marks fixed)
  ↓
resolved (user confirms + LGU confirmed)

OR

rejected (doesn't meet requirements/invalid)
```

### 3. Public Feed Visibility
- Only reports with status: in-review, in-progress, resolved, awaiting-confirmation
- Anonymous reports must be admin-verified first
- Users can mark reports as private (is_public = false)
- Community can upvote and flag reports

### 4. Resolution Workflow
```
LGU receives assigned report
  ↓
Marks as "in-progress" (optional)
  ↓
Uploads proof photo + remarks
  ↓
Marks as "fixed" → Status: awaiting-confirmation
  ↓
User receives notification
  ↓
User confirms → Status: resolved (confirmed_by_user = true)
OR
User rejects → Status: in-progress (LGU notified to re-address)
```

### 5. Notification System
- **New Report:** Admins notified
- **Report Fixed:** User notified
- **User Confirmed/Rejected:** LGU notified
- **Report Flagged:** Admins notified (multiple flags)
- **New Announcement:** Relevant users notified

### 6. Auto-Assignment Logic
```
Report submitted with GPS coordinates
  ↓
Calculate distance to all LGUs
  ↓
Find nearest LGU within coverage_radius_km
  ↓
Assign report to LGU
  ↓
If no LGU in range, assign to closest LGU
```

### 7. Upvote System
- Authenticated users: 1 upvote per report (tracked by user_id)
- Anonymous users: 1 upvote per IP (tracked by ip_address)
- Toggle functionality (can remove upvote)
- Upvote count displayed on public feed

### 8. Flag System
- Authenticated users can flag inappropriate reports
- Reasons: spam, inappropriate, misleading, other
- Admins receive notifications for flagged reports
- Admin can review and resolve flags

---

## External Libraries & Frameworks

### Backend (PHP/Composer)
1. **Laravel Framework 12** - Full-stack web framework
2. **Laravel UI 4.6** - Authentication scaffolding
3. **Laravel Tinker 2.10** - REPL for Laravel
4. **Faker** - Test data generation
5. **Pest PHP** - Testing framework
6. **Laravel Pint** - Code style fixer
7. **Laravel Sail** - Docker development environment
8. **Laravel Boost** - Performance optimization
9. **Laravel Pail** - Log viewer

### Frontend (NPM/Node)
1. **Bootstrap 5.2.3** - CSS framework for responsive design
2. **@popperjs/core 2.11** - Tooltip and popover positioning
3. **Chart.js** (via CDN) - Data visualization and analytics charts
4. **Axios 1.11** - HTTP client for AJAX requests
5. **Vue.js 3.2** - Progressive JavaScript framework (for future enhancements)
6. **Vite 7.0** - Frontend build tool
7. **Tailwind CSS 4.0** - Utility-first CSS framework
8. **SASS 1.56** - CSS preprocessor
9. **Concurrently 9.0** - Run multiple npm scripts simultaneously

### Third-Party Services
1. **Leaflet.js** (via CDN) - Interactive map implementation
2. **OpenStreetMap** - Map tile provider
3. **Font Awesome** (via CDN) - Icon library

### Build Tools
- **Laravel Vite Plugin** - Laravel integration with Vite
- **@vitejs/plugin-vue** - Vue.js support in Vite
- **@tailwindcss/vite** - Tailwind CSS integration

---

## Security Features

1. **CSRF Protection** - Laravel CSRF tokens on all forms
2. **Password Hashing** - Bcrypt password hashing
3. **SQL Injection Prevention** - Eloquent ORM query builder
4. **XSS Prevention** - Blade template escaping
5. **Authentication Middleware** - Route protection
6. **Role-Based Access Control** - Admin/LGU/User roles
7. **File Upload Validation** - File type and size restrictions
8. **IP-Based Rate Limiting** - Upvote spam prevention
9. **Session Management** - Secure session handling

---

## API Endpoints

### Public APIs
- `GET /api/lgus` - Get all LGUs
- `GET /api/lgus/{lguId}/barangays` - Get barangays for specific LGU

### Report APIs
- `POST /report` - Submit authenticated report
- `POST /report-anon` - Submit anonymous report
- `PUT /report/{id}` - Update report
- `POST /feed/reports/{report}/upvote` - Toggle upvote
- `POST /feed/reports/{report}/flag` - Flag report

---

## File Structure

```
ecowatch-f/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AdminController.php
│   │       ├── AnonymousReportController.php
│   │       ├── DashboardController.php
│   │       ├── LguSettingsController.php
│   │       ├── NotificationController.php
│   │       ├── PublicFeedController.php
│   │       ├── ReportController.php
│   │       ├── ReportStatusController.php
│   │       ├── UserController.php
│   │       └── UserSettingsController.php
│   └── Models/
│       ├── Barangay.php
│       ├── Lgu.php
│       ├── Notification.php
│       ├── Photo.php
│       ├── PublicAnnouncement.php
│       ├── Report.php
│       ├── ReportFlag.php
│       ├── ReportUpdate.php
│       ├── ReportUpvote.php
│       ├── ReportValidity.php
│       ├── User.php
│       └── ViolationType.php
├── database/
│   ├── migrations/
│   └── seeders/
├── public/
│   ├── css/
│   ├── js/
│   │   ├── location-dropdowns.js
│   │   ├── map-helper.js
│   │   ├── map-lightbox.js
│   │   ├── modal-helper.js
│   │   ├── report-map.js
│   │   └── table-filter.js
│   └── uploads/
│       └── reports/
├── resources/
│   ├── views/
│   │   ├── auth/
│   │   ├── components/
│   │   ├── layouts/
│   │   ├── settings/
│   │   └── notifications/
│   ├── js/
│   └── css/
├── routes/
│   └── web.php
├── tests/
├── composer.json
├── package.json
└── vite.config.js
```

---

## Performance Optimizations

1. **Eager Loading** - Reduce N+1 queries with Eloquent relationships
2. **Query Caching** - Cache frequently accessed data
3. **Image Optimization** - Resize and compress uploaded photos
4. **Pagination** - Limit database query results
5. **CDN Usage** - Load libraries from CDN
6. **Asset Compilation** - Vite build optimization
7. **Database Indexing** - Indexes on frequently queried columns

---

## Browser Compatibility

- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

---

## Future Enhancements (Vue.js Components)

The system includes Vue.js 3 in package.json for planned future enhancements:
- Real-time notification updates
- Interactive data visualizations
- Single-page application (SPA) conversion
- Advanced filtering components
- Real-time chat/messaging

---

## Development Environment

**Recommended Setup:**
- **Server:** Laragon (Windows) or Laravel Sail (Docker)
- **PHP:** 8.2 or higher
- **MySQL:** 8.0 or higher
- **Node.js:** 18 or higher
- **Composer:** 2.x
- **NPM:** 9.x or higher

---

## Testing

**Testing Framework:** Pest PHP
- Feature tests in `tests/Feature/`
- Unit tests in `tests/Unit/`
- Run tests: `composer test`

---

## Version Control

**Git Status:**
- Main branch: `main`
- Recent changes: Database documentation cleanup, feed improvements, flag system implementation

---

## License

MIT License

---

## Support & Contact

For issues or questions:
1. Check documentation files
2. Review code comments
3. Contact system administrator
