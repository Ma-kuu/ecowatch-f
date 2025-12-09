# EcoWatch-F Database Relationships Guide

## Overview
This document explains each table's purpose and its relationships with other tables in plain text format.

---

## Core Tables

### 1. USERS
**Purpose**: Manages user accounts and authentication for the system.

**Columns**:
- `id` - Primary key
- `name` - User's full name
- `email` - Unique email address for login
- `password` - Encrypted password
- `phone` - Contact number
- `role` - User type: `user`, `admin`, or `lgu`
- `lgu_id` - Foreign key to LGUs table (nullable)
- `profile_photo` - Path to profile image
- `is_active` - Account status flag

**Relationships**:
- **Belongs to**: `lgus` (Many users → One LGU) - LGU staff members are linked to their office
- **Has many**: `reports` (One user → Many reports) - Users can create multiple reports
- **Has many**: `photos` (One user → Many photos) - Users can upload multiple photos
- **Has many**: `notifications` (One user → Many notifications) - Users receive multiple notifications
- **Has many**: `report_upvotes` (One user → Many upvotes) - Users can upvote multiple reports
- **Has many**: `report_updates` (One user → Many updates) - Users can create multiple updates
- **Has many**: `report_flags` (One user → Many flags) - Users can flag multiple reports
- **Has many**: `announcement_reactions` (One user → Many reactions) - Users can react to multiple announcements

**Business Logic**:
- Regular users (`role='user'`) have `lgu_id` as NULL
- LGU staff (`role='lgu'`) must have a valid `lgu_id`
- Admins (`role='admin'`) have `lgu_id` as NULL
- Soft authentication allows anonymous reporting (reports without `user_id`)

---

### 2. LGUS (Local Government Units)
**Purpose**: Represents municipal/city government offices that handle environmental reports.

**Columns**:
- `id` - Primary key
- `name` - Official LGU name (e.g., "Davao del Norte")
- `code` - Unique identifier code (e.g., "DDN")
- `province` - Province name
- `region` - Region name
- `contact_email` - Official email
- `contact_phone` - Official phone
- `address` - Office address
- `latitude`, `longitude` - Geographic coordinates
- `coverage_radius_km` - Service area radius (default 10km)
- `is_active` - Operational status

**Relationships**:
- **Has many**: `users` (One LGU → Many users) - LGU employs multiple staff members
- **Has many**: `barangays` (One LGU → Many barangays) - LGU governs multiple barangays
- **Has many**: `reports` (One LGU → Many reports) - LGU is assigned multiple reports
- **Has many**: `public_announcements` (One LGU → Many announcements) - LGU publishes multiple announcements

**Business Logic**:
- Auto-assignment algorithm uses `latitude`, `longitude`, and `coverage_radius_km` to assign reports to nearest LGU
- Each LGU can have multiple staff accounts with `role='lgu'`
- Deactivating an LGU (`is_active=false`) prevents new report assignments

---

### 3. BARANGAYS
**Purpose**: Represents barangays (villages/districts) within each LGU for precise location tracking.

**Columns**:
- `id` - Primary key
- `lgu_id` - Foreign key to LGUs (required)
- `name` - Barangay name
- `code` - Barangay code
- `population` - Population count
- `area_sqkm` - Area in square kilometers
- `captain_name` - Barangay captain's name
- `contact_number` - Contact number
- `is_active` - Status flag

**Relationships**:
- **Belongs to**: `lgus` (Many barangays → One LGU) - Each barangay is under one LGU
- **Has many**: `reports` (One barangay → Many reports) - Multiple reports can be from same barangay

**Business Logic**:
- Used for filtering and grouping reports by location
- Helps LGUs prioritize reports from specific barangays
- `lgu_id` cascade deletes - if LGU is deleted, all its barangays are deleted

---

### 4. VIOLATION_TYPES
**Purpose**: Categorizes types of environmental violations for classification and filtering.

**Columns**:
- `id` - Primary key
- `name` - Violation type name (e.g., "Illegal Dumping")
- `slug` - URL-friendly identifier
- `description` - Detailed description
- `icon` - Bootstrap icon class (e.g., "bi-trash")
- `color` - Color code for UI (e.g., "danger", "warning")
- `severity` - Impact level: `low`, `medium`, `high`, `critical`
- `is_active` - Status flag

**Relationships**:
- **Has many**: `reports` (One violation type → Many reports) - Multiple reports can have same violation type

**Business Logic**:
- Predefined categories seeded during installation
- Cannot be deleted if reports exist (RESTRICT constraint)
- Used for analytics and filtering
- `severity` helps prioritize urgent violations

**Default Types**:
1. Illegal Dumping
2. Water Pollution
3. Air Pollution
4. Deforestation
5. Noise Pollution
6. Soil Contamination
7. Wildlife Violations
8. Industrial Violations

---

### 5. REPORTS (Main Table)
**Purpose**: Core table storing all environmental violation reports submitted by users or anonymously.

**Columns**:
- `id` - Primary key
- `report_id` - Human-readable ID (e.g., "RPT-001")
- `user_id` - Foreign key to users (nullable for anonymous)
- `reporter_name`, `reporter_email`, `reporter_phone` - Anonymous reporter info
- `anonymous_token` - Unique token for anonymous tracking
- `is_anonymous` - Flag for anonymous reports
- `violation_type_id` - Foreign key to violation_types
- `title` - Report title
- `description` - Detailed description
- `location_address` - Full address
- `purok_sitio` - Sub-barangay location
- `barangay_id` - Foreign key to barangays (nullable)
- `latitude`, `longitude` - GPS coordinates
- `status` - Current status (pending, in-review, in-progress, awaiting-confirmation, resolved, rejected)
- `assigned_lgu_id` - Foreign key to LGUs (nullable)
- `assigned_at` - Assignment timestamp
- `lgu_confirmed` - LGU marked as fixed
- `user_confirmed` - User confirmed resolution
- `admin_override` - Admin bypass flag
- `lgu_confirmed_at`, `user_confirmed_at` - Confirmation timestamps
- `user_feedback` - User's feedback on resolution
- `is_public` - Visibility flag
- `priority` - Urgency level (low, medium, high, urgent)
- `upvotes_count` - Community engagement metric
- `views_count` - View counter
- `resolved_at` - Resolution timestamp
- `deleted_at` - Soft delete timestamp

**Relationships**:
- **Belongs to**: `users` (Many reports → One user) - Reporter (nullable for anonymous)
- **Belongs to**: `violation_types` (Many reports → One violation type) - Categorization
- **Belongs to**: `barangays` (Many reports → One barangay) - Location (nullable)
- **Belongs to**: `lgus` (Many reports → One LGU) - Assignment (nullable)
- **Has many**: `photos` (One report → Many photos) - Evidence images
- **Has many**: `notifications` (One report → Many notifications) - Status updates
- **Has one**: `report_validity` (One report → One validity record) - Verification status
- **Has many**: `report_updates` (One report → Many updates) - Activity timeline
- **Has many**: `report_upvotes` (One report → Many upvotes) - Community votes
- **Has many**: `report_flags` (One report → Many flags) - Content moderation

**Business Logic**:
- **Anonymous Reporting**: If `is_anonymous=true`, `user_id` is NULL and `anonymous_token` is generated
- **Auto-Assignment**: System calculates nearest LGU using Haversine formula on coordinates
- **Three-Way Resolution**: Report is fully resolved when both `lgu_confirmed=true` AND `user_confirmed=true`, OR `admin_override=true`
- **Status Flow**: pending → in-review → in-progress → awaiting-confirmation → resolved
- **Soft Deletes**: Uses `deleted_at` to preserve data integrity

---

## Supporting Tables

### 6. PHOTOS
**Purpose**: Stores evidence images for reports (before/after photos).

**Columns**:
- `id` - Primary key
- `report_id` - Foreign key to reports
- `file_path` - Storage path
- `file_name` - Original filename
- `file_size` - File size in bytes
- `mime_type` - File type (e.g., "image/jpeg")
- `is_primary` - Main display photo flag
- `uploaded_by` - Foreign key to users (nullable)
- `created_at` - Upload timestamp

**Relationships**:
- **Belongs to**: `reports` (Many photos → One report) - Each photo belongs to one report
- **Belongs to**: `users` (Many photos → One user) - Uploader (nullable for anonymous)

**Business Logic**:
- One photo per report should have `is_primary=true` for display
- LGU can upload resolution proof photos (`is_primary=false`)
- Cascade deletes when report is deleted
- Anonymous uploads have `uploaded_by=NULL`

---

### 7. NOTIFICATIONS
**Purpose**: User notification system for report updates and system alerts.

**Columns**:
- `id` - Primary key
- `user_id` - Foreign key to users
- `report_id` - Foreign key to reports (nullable)
- `type` - Notification category (e.g., "report_assigned", "status_changed")
- `title` - Notification title
- `message` - Notification message
- `data` - JSON payload for additional context
- `read_at` - Read timestamp (nullable)
- `created_at` - Creation timestamp

**Relationships**:
- **Belongs to**: `users` (Many notifications → One user) - Recipient
- **Belongs to**: `reports` (Many notifications → One report) - Related report (nullable for system notifications)

**Business Logic**:
- `read_at=NULL` means unread notification
- System notifications have `report_id=NULL`
- Cascade deletes when user or report is deleted
- Used for real-time updates via polling or WebSockets

**Notification Types**:
- `report_assigned` - LGU assigned to report
- `status_changed` - Report status updated
- `awaiting_confirmation` - LGU marked as fixed
- `resolved` - Report fully resolved
- `rejected` - Report rejected
- `comment_added` - New comment on report

---

### 8. REPORT_VALIDITY
**Purpose**: Tracks report verification and dispute resolution process.

**Columns**:
- `id` - Primary key
- `report_id` - Foreign key to reports (unique)
- `status` - Verification status (pending, valid, invalid, disputed, under-review)
- `reviewed_by` - Foreign key to users (admin/LGU who reviewed)
- `reviewed_at` - Review timestamp
- `review_notes` - Admin's notes
- `is_disputed` - Dispute flag
- `disputed_by` - Foreign key to users (reporter who disputed)
- `disputed_at` - Dispute timestamp
- `dispute_reason` - User's dispute explanation

**Relationships**:
- **Belongs to**: `reports` (One validity → One report) - One-to-one relationship
- **Belongs to**: `users` (Many validities → One user) - Reviewer (nullable)
- **Belongs to**: `users` (Many validities → One user) - Disputer (nullable)

**Business Logic**:
- Created automatically when report is submitted with `status='pending'`
- Admin/LGU reviews and marks as `valid` or `invalid`
- If marked `invalid`, user can dispute by setting `is_disputed=true`
- Disputed reports change status to `disputed` and require re-review
- Final decision changes status to `valid` or `invalid`

**Workflow**:
1. Report submitted → `status='pending'`
2. Admin reviews → `status='valid'` or `status='invalid'`
3. User disputes invalid → `is_disputed=true`, `status='disputed'`
4. Admin re-reviews → `status='under-review'`
5. Final decision → `status='valid'` or `status='invalid'`

---

### 9. REPORT_UPDATES
**Purpose**: Unified timeline/activity log for all report-related actions.

**Columns**:
- `id` - Primary key
- `report_id` - Foreign key to reports
- `user_id` - Foreign key to users (nullable)
- `update_type` - Type of update (status_change, assignment, progress, resolution, note, verification)
- `title` - Update title
- `description` - Detailed description
- `old_status`, `new_status` - For status changes
- `progress_percentage` - 0-100 for progress updates
- `photo_path` - Attached photo (nullable)
- `created_at`, `updated_at` - Timestamps

**Relationships**:
- **Belongs to**: `reports` (Many updates → One report) - Activity log for one report
- **Belongs to**: `users` (Many updates → One user) - Creator (nullable for system updates)

**Business Logic**:
- Replaces old `status_history`, `report_actions`, and `comments` tables
- System-generated updates have `user_id=NULL`
- Provides complete audit trail
- Used to display timeline on report details page

**Update Types**:
- `status_change` - Status changed (e.g., pending → in-review)
- `assignment` - LGU assigned to report
- `progress` - LGU provides progress update with percentage
- `resolution` - LGU marks as fixed
- `note` - Admin/LGU adds note
- `verification` - Admin verifies validity

---

### 10. REPORT_UPVOTES
**Purpose**: Community voting system to prioritize important reports.

**Columns**:
- `id` - Primary key
- `report_id` - Foreign key to reports
- `user_id` - Foreign key to users (nullable)
- `ip_address` - IP address for anonymous votes
- `created_at` - Vote timestamp

**Relationships**:
- **Belongs to**: `reports` (Many upvotes → One report) - Votes for one report
- **Belongs to**: `users` (Many upvotes → One user) - Voter (nullable for anonymous)

**Business Logic**:
- Authenticated users: Vote tracked by `user_id`
- Anonymous users: Vote tracked by `ip_address`
- Unique constraint prevents duplicate votes: `(report_id, user_id)` OR `(report_id, ip_address)`
- `upvotes_count` in reports table is incremented/decremented
- Higher upvotes = higher priority for LGU attention

---

### 11. REPORT_FLAGS
**Purpose**: Content moderation system for reporting inappropriate or fake reports.

**Columns**:
- `id` - Primary key
- `report_id` - Foreign key to reports
- `user_id` - Foreign key to users
- `reason` - Flag reason (spam, inappropriate, duplicate, false_information, other)
- `description` - Detailed explanation
- `status` - Flag status (pending, reviewed, dismissed, actioned)
- `reviewed_by` - Foreign key to users (admin who reviewed)
- `reviewed_at` - Review timestamp
- `review_notes` - Admin's notes

**Relationships**:
- **Belongs to**: `reports` (Many flags → One report) - Flags for one report
- **Belongs to**: `users` (Many flags → One user) - Flagger
- **Belongs to**: `users` (Many flags → One user) - Reviewer (nullable)

**Business Logic**:
- Users can flag suspicious or inappropriate reports
- Admin reviews flags and takes action
- Multiple flags on same report trigger admin review
- Actions: dismiss flag, hide report, delete report, ban user
- Prevents abuse and maintains content quality

**Flag Reasons**:
- `spam` - Spam or advertising
- `inappropriate` - Offensive content
- `duplicate` - Duplicate report
- `false_information` - Fake or misleading report
- `other` - Other reason (requires description)

---

### 12. PUBLIC_ANNOUNCEMENTS
**Purpose**: LGU and admin public communications system.

**Columns**:
- `id` - Primary key
- `lgu_id` - Foreign key to LGUs (nullable for system-wide)
- `created_by` - Foreign key to users (admin/LGU staff)
- `title` - Announcement title
- `content` - Announcement content
- `type` - Announcement type (info, warning, urgent, success)
- `is_pinned` - Priority display flag
- `expires_at` - Auto-hide timestamp (nullable)
- `reactions_count` - Engagement metric
- `created_at`, `updated_at` - Timestamps

**Relationships**:
- **Belongs to**: `lgus` (Many announcements → One LGU) - LGU-specific (nullable for system-wide)
- **Belongs to**: `users` (Many announcements → One user) - Creator (nullable for system)
- **Has many**: `announcement_reactions` (One announcement → Many reactions) - User engagement

**Business Logic**:
- LGU-specific: `lgu_id` is set, visible to users in that LGU
- System-wide: `lgu_id=NULL`, visible to all users
- Pinned announcements (`is_pinned=true`) appear at top
- Expired announcements (`expires_at < NOW()`) are auto-hidden
- Used for maintenance notices, events, updates

**Announcement Types**:
- `info` - General information (blue)
- `warning` - Important notice (yellow)
- `urgent` - Critical alert (red)
- `success` - Good news (green)

---

### 13. ANNOUNCEMENT_REACTIONS
**Purpose**: User engagement with announcements (like, love, helpful, celebrate).

**Columns**:
- `id` - Primary key
- `announcement_id` - Foreign key to public_announcements
- `user_id` - Foreign key to users
- `reaction_type` - Reaction type (like, love, helpful, celebrate)
- `created_at` - Reaction timestamp

**Relationships**:
- **Belongs to**: `public_announcements` (Many reactions → One announcement) - Reactions for one announcement
- **Belongs to**: `users` (Many reactions → One user) - User who reacted

**Business Logic**:
- Users can react to announcements
- One user can have multiple reaction types per announcement
- `reactions_count` in announcements table is updated
- Provides engagement metrics for LGUs

**Reaction Types**:
- `like` - 👍 General approval
- `love` - ❤️ Strong positive reaction
- `helpful` - 💡 Found it useful
- `celebrate` - 🎉 Celebratory reaction

---

## Relationship Summary by Type

### One-to-Many (1:N) Relationships
| Parent Table | Child Table | Description |
|-------------|-------------|-------------|
| `users` | `reports` | User creates multiple reports |
| `users` | `photos` | User uploads multiple photos |
| `users` | `notifications` | User receives multiple notifications |
| `users` | `report_upvotes` | User votes on multiple reports |
| `users` | `report_updates` | User creates multiple updates |
| `users` | `report_flags` | User flags multiple reports |
| `users` | `announcement_reactions` | User reacts to multiple announcements |
| `lgus` | `users` | LGU employs multiple staff |
| `lgus` | `barangays` | LGU governs multiple barangays |
| `lgus` | `reports` | LGU is assigned multiple reports |
| `lgus` | `public_announcements` | LGU publishes multiple announcements |
| `barangays` | `reports` | Barangay has multiple reports |
| `violation_types` | `reports` | Violation type categorizes multiple reports |
| `reports` | `photos` | Report has multiple photos |
| `reports` | `notifications` | Report triggers multiple notifications |
| `reports` | `report_updates` | Report has multiple updates |
| `reports` | `report_upvotes` | Report receives multiple upvotes |
| `reports` | `report_flags` | Report receives multiple flags |
| `public_announcements` | `announcement_reactions` | Announcement receives multiple reactions |

### One-to-One (1:1) Relationships
| Table 1 | Table 2 | Description |
|---------|---------|-------------|
| `reports` | `report_validity` | Each report has one validity record |

### Many-to-One (N:1) Relationships
All parent-child relationships above are also Many-to-One from the child's perspective.

---

## Cascade Delete Rules

### CASCADE DELETE (Children deleted when parent is deleted)
- `reports` deleted → `photos`, `notifications`, `report_validity`, `report_updates`, `report_upvotes`, `report_flags` deleted
- `lgus` deleted → `barangays` deleted
- `users` deleted → `notifications`, `report_upvotes`, `report_updates`, `report_flags`, `announcement_reactions` deleted
- `public_announcements` deleted → `announcement_reactions` deleted

### SET NULL (Foreign key set to NULL when parent is deleted)
- `users` deleted → `reports.user_id` set to NULL (preserves anonymous reports)
- `lgus` deleted → `reports.assigned_lgu_id` set to NULL
- `lgus` deleted → `users.lgu_id` set to NULL
- `lgus` deleted → `public_announcements.lgu_id` set to NULL
- `barangays` deleted → `reports.barangay_id` set to NULL

### RESTRICT (Prevents deletion if children exist)
- `violation_types` cannot be deleted if reports exist with that type

---

## Nullable Foreign Keys Explained

| Table | Column | Why Nullable | Use Case |
|-------|--------|--------------|----------|
| `users` | `lgu_id` | Regular users don't belong to LGU | Only LGU staff have this set |
| `reports` | `user_id` | Anonymous reporting | Anonymous reports have NULL user_id |
| `reports` | `barangay_id` | Location might be outside barangays | Rural or unspecified locations |
| `reports` | `assigned_lgu_id` | Unassigned reports | New reports before assignment |
| `photos` | `uploaded_by` | Anonymous uploads | Photos from anonymous reports |
| `notifications` | `report_id` | System notifications | General system alerts |
| `public_announcements` | `lgu_id` | System-wide announcements | Admin announcements for all users |
| `public_announcements` | `created_by` | System-generated | Automated announcements |
| `report_validity` | `reviewed_by` | Pending reviews | Not yet reviewed |
| `report_validity` | `disputed_by` | No dispute | Not disputed |
| `report_updates` | `user_id` | System-generated updates | Automated status changes |
| `report_upvotes` | `user_id` | Anonymous votes | Votes from non-logged-in users |
| `report_flags` | `reviewed_by` | Pending review | Not yet reviewed by admin |

---

## Database Statistics

- **Total Tables**: 17 (13 core + 4 Laravel system tables)
- **Total Relationships**: 25+
- **Foreign Keys**: 25+
- **One-to-Many**: 19 relationships
- **One-to-One**: 1 relationship
- **Unique Constraints**: 8
- **Indexes**: 30+
- **Enum Fields**: 7
- **Boolean Flags**: 12

---

## Key Business Workflows

### Report Submission Workflow
1. User submits report form
2. `reports` record created with `status='pending'`
3. `photos` records created for uploaded images
4. System calculates nearest LGU using coordinates
5. `assigned_lgu_id` set, `assigned_at` timestamp recorded
6. `report_validity` record created with `status='pending'`
7. `notifications` created for assigned LGU staff
8. `report_updates` record created for assignment action

### Resolution Workflow
1. LGU marks report as fixed: `lgu_confirmed=true`, `status='awaiting-confirmation'`
2. `report_updates` record created for resolution
3. `notifications` created for reporter
4. User confirms resolution: `user_confirmed=true`, `status='resolved'`
5. `resolved_at` timestamp recorded
6. OR Admin overrides: `admin_override=true`, `status='resolved'`

### Dispute Workflow
1. Admin marks report as invalid: `report_validity.status='invalid'`
2. User disputes: `is_disputed=true`, `status='disputed'`
3. `report_updates` record created for dispute
4. Admin re-reviews: `status='under-review'`
5. Final decision: `status='valid'` or `status='invalid'`
6. `report_updates` record created for final decision

---

**Last Updated**: December 9, 2024  
**Version**: 1.0  
**Database Schema Version**: 1.5
