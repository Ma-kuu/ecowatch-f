# Database Tables - Plain English Guide

This document explains what each table does in simple terms for anyone to understand.

---

## Core Tables

### users
**Purpose**: Who can use the system.

This stores everyone who can log in - regular citizens who report violations, LGU (Local Government Unit) staff who handle the reports, and admins who manage everything.

**Key Info**:
- Has name, email, password (encrypted)
- Role: 'user' (regular citizen), 'lgu' (government staff), or 'admin'
- Can be linked to an LGU if they're staff
- Can be active or inactive

---

### lgus
**Purpose**: Local Government Units with their coverage areas.

Each city/municipality that handles environmental violations. Includes their location (latitude/longitude) and how far they can respond (coverage radius in kilometers).

**Key Info**:
- Has name, code (like "DVO" for Davao City)
- Contact details (email, phone, address)
- Geographic location for auto-assigning nearby reports
- Coverage radius (default 10km)

---

### violation_types
**Purpose**: Types of environmental violations you can report.

The 8 categories of violations: Illegal Dumping, Water Pollution, Air Pollution, Deforestation, Noise Pollution, Soil Contamination, Wildlife Violations, and Industrial Violations.

**Key Info**:
- Each has a name, icon, color for the UI
- Severity level: low, medium, high, or critical
- Description explaining what counts as this violation

---

### reports
**Purpose**: The main table - every environmental violation reported.

This is the heart of the system. When someone reports a violation, all the details go here.

**Key Info**:
- Reporter info (can be anonymous)
- What type of violation it is
- Where it happened (address + map coordinates)
- Current status (pending, in-review, in-progress, etc.)
- Which LGU is handling it
- Resolution tracking (did LGU fix it? did user confirm?)
- How many upvotes and views it has

**Status Flow**:
1. **pending** - Just submitted
2. **in-review** - LGU is looking at it
3. **in-progress** - LGU is actively working on it
4. **awaiting-confirmation** - LGU says it's fixed, waiting for user to confirm
5. **resolved** - Everyone agrees it's fixed
6. **rejected** - Not a valid report

---

## Supporting Tables

### barangays
**Purpose**: Villages/neighborhoods under each LGU.

More specific location tracking. Each LGU has multiple barangays.

**Key Info**:
- Belongs to one LGU
- Has a name and code
- Reports can be linked to specific barangays

---

### photos
**Purpose**: Evidence pictures attached to reports.

When someone reports a violation, they upload photos as proof. These are stored here.

**Key Info**:
- Linked to a specific report
- File path, size, type
- One photo can be marked as "primary" (main image)
- Tracks who uploaded it

---

### notifications
**Purpose**: Alerts sent to users.

When something happens with your report (LGU responds, status changes, etc.), you get a notification.

**Key Info**:
- Who it's for
- What report it's about
- Title and message
- Whether you've read it yet

---

### report_validity
**Purpose**: Tracks if a report is real or fake.

Admins/LGUs review reports to check if they're valid. Users can dispute if their report is marked invalid.

**Key Info**:
- Status: pending, valid, invalid, disputed, under-review
- Who reviewed it and when
- Notes explaining the decision
- If disputed: who disputed it and why

**Workflow**:
1. Admin marks report as valid/invalid
2. If invalid, user can dispute with a reason
3. Admin must re-review disputed reports

---

### report_updates
**Purpose**: Timeline of everything that happens to a report.

Instead of separate audit tables, everything goes here: status changes, LGU actions, progress updates, notes.

**Key Info**:
- What type of update (status change, assignment, progress, etc.)
- Title and description
- For progress updates: percentage complete (0-100)
- For status changes: old status → new status

**Why this is good**: One clean timeline showing the complete history of a report.

---

### report_upvotes
**Purpose**: Community votes to show which reports matter most.

Both logged-in users and anonymous visitors can upvote reports. This helps prioritize issues that affect more people.

**Key Info**:
- Which report was upvoted
- Who upvoted (if logged in) OR their IP address (if anonymous)
- Prevents duplicates (one vote per person/IP per report)

---

### public_announcements
**Purpose**: News and updates from LGUs that everyone can see.

LGUs and admins can post announcements like cleanup drives, policy updates, reminders about waste segregation, etc.

**Key Info**:
- Title and content
- Type: info, warning, urgent, or success
- Can be from a specific LGU or system-wide
- Can be pinned (shows at top)
- Can expire after a certain date

---

## How It All Works Together

### Submitting a Report
1. User fills out form → Creates **report**
2. Uploads photos → Creates **photos**
3. System finds nearest LGU using lat/lng → Updates **report** with assigned_lgu_id
4. Creates notification → **notifications** for LGU staff
5. Creates timeline entry → **report_updates** saying "Report assigned to [LGU]"
6. Creates validity check → **report_validity** set to "pending"

### Resolution Workflow
1. LGU marks as fixed → **report** lgu_confirmed = true, status = 'awaiting-confirmation'
2. User confirms it's fixed → **report** user_confirmed = true, status = 'resolved'
3. Each action creates entries in **report_updates** (timeline)
4. Notifications sent at each step → **notifications**

### Community Engagement
- Users upvote important reports → **report_upvotes**
- LGUs post announcements → **public_announcements**
- Everyone can see progress → **report_updates** timeline

---

## What We Removed (Simplified)

Before, we had:
- `status_history` table - separate audit trail for status changes
- `report_actions` table - separate log for LGU actions
- `comments` table - public comments on reports

Now we have:
- `report_updates` - ONE table that handles all of the above

**Why?** Simpler, cleaner, easier to understand. One timeline instead of three separate logs.

---

## Key Numbers

- **11 tables total** (down from 14 in original plan)
- **8 violation types** (Illegal Dumping, Water Pollution, etc.)
- **6 report statuses** (pending → in-review → in-progress → awaiting-confirmation → resolved)
- **3 user roles** (user, lgu, admin)
- **3 boolean flags for resolution** (lgu_confirmed, user_confirmed, admin_override)

---

## Security & Privacy

- **Passwords**: Encrypted (hashed)
- **Anonymous reports**: Uses random token, no personal info required
- **Anonymous upvotes**: Tracked by IP address only
- **LGU access**: Only see reports in their coverage area
- **User access**: Only see public reports + their own

---

## For Developers

**Relationships**:
```
User
  → reports (many)
  → notifications (many)
  → upvotes (many)
  ← lgu (one)

LGU
  → users (many - staff members)
  → barangays (many)
  → assignedReports (many)
  → announcements (many)

Report (most complex)
  ← reporter (User)
  ← violationType
  ← barangay
  ← assignedLgu (LGU)
  → photos (many)
  → notifications (many)
  → validity (one)
  → updates (many)
  → upvotes (many)
```

---

## Common Questions

**Q: Can reports be anonymous?**
A: Yes! Set `is_anonymous = true`, use a random `anonymous_token`, and don't require user_id.

**Q: How does auto-assignment work?**
A: Uses Haversine formula to calculate distance from report to each LGU. Assigns to nearest LGU within their coverage radius.

**Q: What if user and LGU disagree on resolution?**
A: Admin can override with `admin_override = true` to force resolve.

**Q: Can anonymous users upvote?**
A: Yes! Tracked by IP address to prevent duplicates.

**Q: Why no comments table?**
A: To keep it simple. Focus is on LGU action, not public discussion. Can add later if needed.

---

## Summary

This is a **clean, simple** database focused on what's actually needed:
- Report violations (with photos)
- Assign to nearest LGU automatically
- Track progress (one timeline)
- Check if reports are valid
- Community voting
- Public announcements

No over-engineering, no unnecessary complexity. Just what works.
