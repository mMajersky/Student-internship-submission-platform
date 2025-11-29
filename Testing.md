# Manual Testing Guide - Student Internship Submission Platform

## Overview

This document contains manual test cases for the Student Internship Submission Platform, organized by user roles. Tests include step-by-step instructions with acceptance criteria to ensure functionality works as expected.

## Table of Contents

- [Garant Manual Tests](#garant-manual-tests)
- [Student Manual Tests](#student-manual-tests)
- [Company Manual Tests](#company-manual-tests)
- [Admin Manual Tests](#admin-manual-tests)

---

## Garant Manual Tests

### GT-01: Login as Garant

**Preconditions:**
- Garant account exists in the system
- User is not logged in

**Steps:**
1. Navigate to the application landing page
2. Click on the "Login" link/button
3. Enter valid Garant email and password
4. Click "Login" button

**Acceptance Criteria:**
- User is redirected to Garant dashboard
- User role is correctly identified as "GARANT"
- Dashboard displays Garant-specific menu options
- JWT token is stored securely
- Navigation reflects Garant permissions

### GT-02: View All Internships Management

**Preconditions:**
- Logged in as Garant
- There are internships in statuses: 'vytvorená', 'potvrdená', 'schválená', 'zamietnutá', 'obhájená', 'neobhájená', 'ukončená', 'prebieha', 'zrušená'

**Steps:**
1. Navigate to Garant dashboard
2. Click on "Internships" tab
3. View the complete internships list

**Acceptance Criteria:**
- List displays all internships with details including:
  - Student name (full name)
  - Company name
  - Academic year
  - Semester (ZS/LS)
  - Start and end dates
  - Current status with colored badges: 'vytvorená'(grey), 'potvrdená'(blue), 'schválená'(green), 'zamietnutá'(red), 'obhájená'(primary), 'neobhájená'(red), 'ukončená'(primary), 'prebieha'(yellow), 'zrušená'(red)
- Table columns: Student, Company, Year, Semester, Start, End, Status, Actions
- Each internship entry has action buttons:
  - Comment button (chat bubble icon)
  - Edit button (pencil icon)
  - Documents button (folder icon)
  - Delete button (trash icon)

### GT-03: Confirm Internship and Notify Company

**Preconditions:**
- Logged in as Garant
- There is at least one internship with "vytvorená" status

**Steps:**
1. View pending internship requests (GT-02)
2. Click "Edit" button (pencil icon) on a specific internship
3. In the edit form:
   - Change status to "potvrdená" (confirmed)
   - Optionally add a comment explaining the confirmation
   - Click "Save Changes"

**Acceptance Criteria:**
- Edit form loads with current internship data
- Status dropdown shows available status values
- When status changed to "potvrdená", success message displays
- Email notification with action buttons (confirm/reject) sent to company
- Internal notification created for company
- When status changes to "potvrdená", internship is ready for company review

### GT-04: Set Internship Status to Declined

**Preconditions:**
- Logged in as Garant
- There is at least one internship where status needs to be changed to declined

**Steps:**
1. View internships list (GT-02)
2. Click "Edit" button (pencil icon) on a specific internship
3. In the edit form:
   - Change status to "zamietnutá" (rejected)
   - Optionally add a comment explaining the rejection reason
   - Click "Save Changes"

**Acceptance Criteria:**
- Edit form loads with current internship data
- Status dropdown allows changing to "zamietnutá"
- When status changed to "zamietnutá", success message displays
- Email notification sent to student (if email enabled)
- Internal notification created for student
- Internship status shows as rejected/declined in the list

### GT-05: Add Comment to Internship

**Preconditions:**
- Logged in as Garant
- Internship exists (any status)

**Steps:**
1. Open internship details view
2. Click "Add Comment" button
3. Enter comment text explaining decision or requesting changes
4. Click "Save Comment"

**Acceptance Criteria:**
- Comment is saved to internship record
- Comment includes timestamp and Garant identifier
- Comment is visible to student in their internship view
- Comment appears in internship comments history

### GT-06: Update Internship Details

**Preconditions:**
- Logged in as Garant
- Internship exists

**Steps:**
1. Open internship details view
2. Click "Edit" button
3. Modify allowed fields (dates, status, etc.)
4. Save changes

**Acceptance Criteria:**
- Changed fields are updated in database
- Status change triggers appropriate notifications
- Changes are reflected in all relevant views

### GT-07: Delete Internship

**Preconditions:**
- Logged in as Garant
- Internship exists
- Internship has appropriate status for deletion

**Steps:**
1. Open internship details view
2. Click "Delete" button
3. Confirm deletion in modal dialog

**Acceptance Criteria:**
- Internship is permanently removed from database
- Associated documents are handled appropriately
- Success message displays
- Internship no longer appears in any lists

### GT-08: Create New Internship

**Preconditions:**
- Logged in as Garant

**Steps:**
1. Navigate to internship management
2. Click "Create Internship" button
3. Fill in required fields:
   - Student selection
   - Company selection
   - Start and end dates
   - Status (default "Created")
4. Submit form

**Acceptance Criteria:**
- New internship record is created
- Default status is "Created"
- All required validations pass
- Internship appears in appropriate lists
- Notifications are sent as per business rules

### GT-09: Garant Download PDF Agreement

**Preconditions:**
- Logged in as Garant
- Internship exists with uploaded PDF agreement

**Steps:**
1. Navigate to Garant dashboard
2. Click "Documents" tab
3. Select an internship from the list
4. Click "View Documents" button (folder-open icon)
5. In the documents view, click "Download" button on a PDF document

**Acceptance Criteria:**
- PDF file downloads successfully to default download location
- Downloaded file matches the uploaded PDF agreement
- File opens correctly in PDF viewer
- File download is logged appropriately

### GT-10: Garant View Document Details

**Preconditions:**
- Logged in as Garant
- Internship exists with documents

**Steps:**
1. Navigate to Garant dashboard
2. Click "Documents" tab
3. Select an internship from the list
4. Click "View Documents" button (folder-open icon)
5. View document details and metadata

**Acceptance Criteria:**
- Documents associated with the internship are displayed
- Document details show filename, upload date, file size
- Documents are only accessible to authorized users
- Document list updates when new documents are uploaded

### GT-11: Edit Landing Page Announcements

**Preconditions:**
- Logged in as Garant 
- Access to Garant Dashboard 

**Steps:**
1. Navigate to the general Dashboard 
2. Click on "Edit Announcement" tab
3. In the announcement editor:
   - Edit the content using the rich text editor (Quill editor)
   - Optionally toggle the "Show on main page" checkbox to control publication
   - Click "Save Changes"

**Acceptance Criteria:**
- Rich text editor loads with current announcement content
- "Show on main page" checkbox controls the published status
- Changes are saved to database and caches are updated
- Published announcements appear on the landing page notification bar
- Unpublished announcements are hidden from public view
- Success message confirms changes were saved

### GT-12: Manage Garants (Admin Function)

**Preconditions:**
- Logged in as Garant 

**Steps:**
1. Access garant management section
2. Click "Create New Garant"
3. Fill in garant details:
   - Name
   - Email
   - Password
4. Submit form

**Acceptance Criteria:**
- New garant account is created
- Password is properly hashed
- New garant can login successfully
- List of existing garants is displayed

---

## Test Execution Guidelines

### Pre-test Setup Requirements
- Ensure database is seeded with demo data
- Confirm email services are working (for notification tests)
- Have multiple user accounts for different roles
- Clear browser cache/storage before testing

### Test Data Requirements
- At least 5 student accounts
- At least 3 company accounts
- At least 2 garant accounts
- Multiple internships in different statuses
- Various document types uploaded

### Regression Testing Checklist
After each update/deployment:
- [ ] All login functionality works
- [ ] Basic CRUD operations function
- [ ] Email notifications are sent
- [ ] File uploads/downloads work
- [ ] Role-based permissions are enforced

### Bug Reporting Format
When reporting issues:
- **Test Case ID:**
- **Steps to Reproduce:**
- **Expected Behavior:**
- **Actual Behavior:**
- **Environment:**
- **Severity:**
- **Screenshots/Logs:** (if applicable)

---


**Legend:**
- **GT-**: Garant Test Case
- **ST-**: Student Test Case
- **CT-**: Company Test Case
- **AT-**: Admin Test Case
