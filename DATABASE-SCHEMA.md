# EcoWatch Database Schema

## Overview
This document lists all database tables used in the EcoWatch system. You can use this as a reference to recreate the database structure in your other system.

---

## Tables List

### 1. **users** (Extended)
Main user authentication table with role-based access control.

**Fields:**
- `id` - Primary key
- `name` - varchar(255)
- `email` - varchar(255), unique
- `email_verified_at` - timestamp, nullable
- `password` - varchar(255)
- `phone` - varchar(20), nullable
- `role` - enum('user', 'admin', 'lgu'), default 'user'
- `lgu_id` - foreign key to lgus table, nullable
- `profile_photo` - varchar(255), nullable
- `is_active` - boolean, default true
- `remember_token` - varchar(100), nullable
- `created_at` - timestamp
- `updated_at` - timestamp

**Indexes:**
- email (unique)
- role
- lgu_id
- is_active

---

### 2. **password_reset_tokens**
Stores password reset tokens.

**Fields:**
- `email` - varchar(255), primary key
- `token` - varchar(255)
- `created_at` - timestamp, nullable

---

### 3. **sessions**
Stores user session data.

**Fields:**
- `id` - varchar(255), primary key
- `user_id` - foreign key to users, nullable
- `ip_address` - varchar(45), nullable
- `user_agent` - text, nullable
- `payload` - longText
- `last_activity` - integer

**Indexes:**
- user_id
- last_activity

---

### 4. **lgus** (Local Government Units)
Stores information about local government units.

**Fields:**
- `id` - Primary key
- `name` - varchar(100)
- `code` - varchar(20), unique
- `province` - varchar(100)
- `region` - varchar(50), nullable
- `contact_email` - varchar(100), nullable
- `contact_phone` - varchar(20), nullable
- `address` - text, nullable
- `is_active` - boolean, default true
- `created_at` - timestamp
- `updated_at` - timestamp

**Indexes:**
- code (unique)
- is_active

---

### 5. **barangays**
Stores barangay (village) information under each LGU.

**Fields:**
- `id` - Primary key
- `lgu_id` - foreign key to lgus (cascade delete)
- `name` - varchar(100)
- `code` - varchar(20), nullable
- `population` - integer, nullable
- `area_sqkm` - decimal(10,2), nullable
- `captain_name` - varchar(100), nullable
- `contact_number` - varchar(20), nullable
- `is_active` - boolean, default true
- `created_at` - timestamp
- `updated_at` - timestamp

**Indexes:**
- lgu_id
- is_active
- unique_barangay_per_lgu (lgu_id, name)

---

### 6. **violation_types**
Stores types of environmental violations.

**Fields:**
- `id` - Primary key
- `name` - varchar(50), unique
- `slug` - varchar(50), unique
- `description` - text, nullable
- `icon` - varchar(50), nullable
- `color` - varchar(20), nullable
- `severity` - enum('low', 'medium', 'high', 'critical'), default 'medium'
- `is_active` - boolean, default true
- `created_at` - timestamp
- `updated_at` - timestamp

**Indexes:**
- name (unique)
- slug (unique)
- is_active
- severity

**Example Data:**
- Illegal Dumping
- Water Pollution
- Air Pollution
- Deforestation

---

### 7. **reports** (Main Table)
Core table for environmental violation reports.

**Fields:**

**Identification:**
- `id` - Primary key
- `report_id` - varchar(50), unique (e.g., "RPT-001")

**Reporter Info (nullable for anonymous):**
- `user_id` - foreign key to users, nullable
- `reporter_name` - varchar(100), nullable
- `reporter_email` - varchar(100), nullable
- `reporter_phone` - varchar(20), nullable
- `anonymous_token` - varchar(100), unique, nullable
- `is_anonymous` - boolean, default false

**Report Classification:**
- `violation_type_id` - foreign key to violation_types (restrict delete)
- `title` - varchar(200), nullable
- `description` - text

**Location:**
- `location_address` - varchar(255)
- `barangay_id` - foreign key to barangays, nullable
- `latitude` - decimal(10,8), nullable
- `longitude` - decimal(11,8), nullable

**Status:**
- `status` - enum('pending', 'in-review', 'in-progress', 'fixed', 'verified', 'resolved', 'rejected'), default 'pending'
- `assigned_lgu_id` - foreign key to lgus, nullable
- `assigned_at` - timestamp, nullable

**User Confirmation:**
- `awaiting_user_confirmation` - boolean, default false
- `user_confirmed_resolved` - boolean, default false
- `user_confirmation_date` - timestamp, nullable
- `user_feedback` - text, nullable

**Metadata:**
- `is_public` - boolean, default true
- `priority` - enum('low', 'medium', 'high', 'urgent'), default 'medium'
- `comments_count` - integer, default 0
- `views_count` - integer, default 0
- `resolved_at` - timestamp, nullable
- `created_at` - timestamp
- `updated_at` - timestamp
- `deleted_at` - timestamp, nullable (soft deletes)

**Indexes:**
- report_id (unique)
- user_id
- violation_type_id
- status
- assigned_lgu_id
- barangay_id
- created_at
- is_public
- is_anonymous

---

### 8. **photos**
Stores photos/evidence for reports.

**Fields:**
- `id` - Primary key
- `report_id` - foreign key to reports (cascade delete)
- `file_path` - varchar(255)
- `file_name` - varchar(255)
- `file_size` - integer, nullable
- `mime_type` - varchar(100), nullable
- `is_primary` - boolean, default false
- `uploaded_by` - foreign key to users, nullable
- `created_at` - timestamp

**Indexes:**
- report_id
- is_primary
- uploaded_by

---

### 9. **status_history**
Tracks status changes for reports (audit trail).

**Fields:**
- `id` - Primary key
- `report_id` - foreign key to reports (cascade delete)
- `old_status` - varchar(20)
- `new_status` - varchar(20)
- `changed_by` - foreign key to users (cascade delete)
- `remarks` - text, nullable
- `created_at` - timestamp

**Indexes:**
- report_id
- changed_by
- created_at

---

### 10. **report_actions**
Stores actions taken by admins/LGU on reports.

**Fields:**
- `id` - Primary key
- `report_id` - foreign key to reports (cascade delete)
- `admin_id` - foreign key to users (cascade delete)
- `action_type` - enum('remark', 'assignment', 'resolution', 'verification', 'rejection')
- `description` - text

**Resolution Details:**
- `resolution_photo` - varchar(255), nullable
- `date_fixed` - date, nullable
- `personnel_involved` - varchar(255), nullable
- `created_at` - timestamp
- `updated_at` - timestamp

**Indexes:**
- report_id
- admin_id
- action_type

---

### 11. **comments**
User comments on reports.

**Fields:**
- `id` - Primary key
- `report_id` - foreign key to reports (cascade delete)
- `user_id` - foreign key to users, nullable
- `commenter_name` - varchar(100), nullable
- `comment` - text
- `is_approved` - boolean, default true
- `created_at` - timestamp
- `updated_at` - timestamp
- `deleted_at` - timestamp, nullable (soft deletes)

**Indexes:**
- report_id
- user_id
- is_approved

---

### 12. **notifications**
User notifications system.

**Fields:**
- `id` - Primary key
- `user_id` - foreign key to users (cascade delete)
- `report_id` - foreign key to reports, nullable (cascade delete)
- `type` - varchar(50)
- `title` - varchar(200)
- `message` - text
- `data` - json, nullable
- `read_at` - timestamp, nullable
- `created_at` - timestamp

**Indexes:**
- user_id
- report_id
- read_at
- created_at
- (user_id, read_at) composite

---

### 13. **cache** (Laravel System Table)
Laravel cache storage.

**Fields:**
- `key` - varchar(255), primary
- `value` - mediumText
- `expiration` - integer

---

### 14. **cache_locks** (Laravel System Table)
Laravel cache locks.

**Fields:**
- `key` - varchar(255), primary
- `owner` - varchar(255)
- `expiration` - integer

---

### 15. **jobs** (Laravel System Table)
Queue jobs table.

**Fields:**
- `id` - Primary key
- `queue` - varchar(255)
- `payload` - longText
- `attempts` - tinyint unsigned
- `reserved_at` - integer unsigned, nullable
- `available_at` - integer unsigned
- `created_at` - integer unsigned

**Indexes:**
- queue

---

### 16. **job_batches** (Laravel System Table)
Batch jobs tracking.

**Fields:**
- `id` - varchar(255), primary
- `name` - varchar(255)
- `total_jobs` - integer
- `pending_jobs` - integer
- `failed_jobs` - integer
- `failed_job_ids` - longText
- `options` - mediumText, nullable
- `cancelled_at` - integer, nullable
- `created_at` - integer
- `finished_at` - integer, nullable

---

### 17. **failed_jobs** (Laravel System Table)
Failed queue jobs.

**Fields:**
- `id` - Primary key
- `uuid` - varchar(255), unique
- `connection` - text
- `queue` - text
- `payload` - longText
- `exception` - longText
- `failed_at` - timestamp

**Indexes:**
- uuid (unique)

---

## Relationships Summary

```
users
  ├── has many: reports (as reporter)
  ├── has many: photos (as uploader)
  ├── has many: status_history (as changer)
  ├── has many: report_actions (as admin)
  ├── has many: comments
  ├── has many: notifications
  └── belongs to: lgu

lgus
  ├── has many: users
  ├── has many: barangays
  └── has many: reports (as assigned_lgu)

barangays
  ├── belongs to: lgu
  └── has many: reports

violation_types
  └── has many: reports

reports (MAIN)
  ├── belongs to: user (reporter)
  ├── belongs to: violation_type
  ├── belongs to: barangay
  ├── belongs to: lgu (assigned_lgu)
  ├── has many: photos
  ├── has many: status_history
  ├── has many: report_actions
  ├── has many: comments
  └── has many: notifications
```

---

## Key Features

1. **Anonymous Reporting**: Reports can be submitted anonymously using `anonymous_token`
2. **Role-Based Access**: Users can be 'user', 'admin', or 'lgu'
3. **Soft Deletes**: Reports and comments support soft deletes
4. **Status Tracking**: Full audit trail of status changes via `status_history`
5. **Geolocation**: Latitude/longitude support for mapping
6. **LGU Assignment**: Reports can be assigned to specific LGUs
7. **User Confirmation**: Users can confirm when issues are resolved
8. **Photo Evidence**: Multiple photos per report with primary photo flag
9. **Notifications**: Real-time notification system
10. **Comments System**: Public/moderated comments on reports

---

## Notes for Migration

- All foreign keys use appropriate cascade/set null rules
- Indexes are optimized for common queries
- Enum fields should match application logic
- Timestamps use Laravel conventions (created_at, updated_at)
- Some tables use `useCurrent()` for created_at instead of nullable
