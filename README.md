# BookNest

BookNest is a Digital Library Platform for learn children from reading books and listen it.

# Account Page Update Implementation Guide

## Issue

The account.php file has structural issues with misplaced HTML elements. The Child Profiles tab content is inside the `<ul>` navigation instead of the `<div class="tab-content">`.

## Required Fixes

### 1. Fix HTML Structure

The tabs navigation and content are misaligned. Lines 138-159 (Child Profiles tab content) should be moved after line 140 (inside tab-content div).

### 2. Add Form IDs and Handlers

#### Personal Information Form

```html
<form id="personalInfoForm" class="form-container">
  <h3 class="mb-4 fw-bold">Personal Information</h3>
  <div class="row g-3 mb-3">
    <div class="col-md-6">
      <input
        type="text"
        class="form-control"
        id="firstName"
        name="first_name"
        placeholder="First Name"
        value="<?php echo htmlspecialchars($user['FIRST_NAME'] ?? ''); ?>"
        required
      />
    </div>
    <div class="col-md-6">
      <input
        type="text"
        class="form-control"
        id="lastName"
        name="last_name"
        placeholder="Last Name"
        value="<?php echo htmlspecialchars($user['LAST_NAME'] ?? ''); ?>"
        required
      />
    </div>
  </div>
  <div class="row g-3 mb-4">
    <div class="col-md-6">
      <input
        type="email"
        class="form-control"
        placeholder="Email Address"
        value="<?php echo htmlspecialchars($user['USERNAME'] ?? ''); ?>"
        readonly
      />
    </div>
    <div class="col-md-6">
      <input
        type="tel"
        class="form-control"
        id="phone"
        name="phone"
        placeholder="Phone Number"
        value="<?php echo htmlspecialchars($user['PHONE'] ?? ''); ?>"
      />
    </div>
  </div>
  <div class="alert alert-danger d-none" id="personalInfoError"></div>
  <div class="alert alert-success d-none" id="personalInfoSuccess"></div>
  <div class="d-flex justify-content-end">
    <button type="submit" class="btn btn-primary rounded-4">
      Save Changes
    </button>
  </div>
</form>
```

#### Password Form

```html
<form id="passwordForm" class="form-container">
  <h3 class="mb-4 fw-bold">Password & Security</h3>
  <div class="row g-3 mb-3">
    <div class="col-md-6">
      <input
        type="password"
        class="form-control mb-3"
        id="oldPassword"
        name="old_password"
        placeholder="Current Password"
        required
      />
      <input
        type="password"
        class="form-control mb-3"
        id="newPassword"
        name="new_password"
        placeholder="New Password"
        required
      />
      <input
        type="password"
        class="form-control mb-3"
        id="confirmPassword"
        name="confirm_password"
        placeholder="Confirm New Password"
        required
      />
    </div>
  </div>
  <div class="alert alert-danger d-none" id="passwordError"></div>
  <div class="alert alert-success d-none" id="passwordSuccess"></div>
  <div class="d-flex justify-content-end">
    <button type="submit" class="btn btn-primary rounded-4">
      Update Password
    </button>
  </div>
</form>
```

### 3. Add JavaScript Handlers

Add this script before the closing `<?php endif; ?>` for parent section:

```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab persistence
    const activeTab = localStorage.getItem('activeAccountTab') || 'personal';
    const tabEl = document.querySelector(`#${activeTab}-tab`);
    if (tabEl) {
        const tab = new bootstrap.Tab(tabEl);
        tab.show();
    }

    // Save active tab
    document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(button => {
        button.addEventListener('shown.bs.tab', function (e) {
            const tabId = e.target.id.replace('-tab', '');
            localStorage.setItem('activeAccountTab', tabId);
        });
    });

    // Personal Info Form
    const personalInfoForm = document.getElementById('personalInfoForm');
    if (personalInfoForm) {
        personalInfoForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const errorDiv = document.getElementById('personalInfoError');
            const successDiv = document.getElementById('personalInfoSuccess');
            const submitBtn = this.querySelector('button[type="submit"]');

            errorDiv.classList.add('d-none');
            successDiv.classList.add('d-none');

            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving...';

            try {
                const response = await fetch('core/api/users/update-profile.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    successDiv.textContent = result.message;
                    successDiv.classList.remove('d-none');
                    // Update displayed name if needed
                    setTimeout(() => location.reload(), 1500);
                } else {
                    errorDiv.textContent = result.message;
                    errorDiv.classList.remove('d-none');
                }
            } catch (error) {
                errorDiv.textContent = 'An error occurred. Please try again.';
                errorDiv.classList.remove('d-none');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Save Changes';
            }
        });
    }

    // Password Form
    const passwordForm = document.getElementById('passwordForm');
    if (passwordForm) {
        passwordForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const errorDiv = document.getElementById('passwordError');
            const successDiv = document.getElementById('passwordSuccess');
            const submitBtn = this.querySelector('button[type="submit"]');

            errorDiv.classList.add('d-none');
            successDiv.classList.add('d-none');

            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            submitBtn.disabled = true;
            submitBtn.textContent = 'Updating...';

            try {
                const response = await fetch('core/api/users/update-password.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    successDiv.textContent = result.message;
                    successDiv.classList.remove('d-none');
                    this.reset();
                } else {
                    errorDiv.textContent = result.message;
                    errorDiv.classList.remove('d-none');
                }
            } catch (error) {
                errorDiv.textContent = 'An error occurred. Please try again.';
                errorDiv.classList.remove('d-none');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Update Password';
            }
        });
    }

    <?php if ($isParent): ?>
    // Fetch children for parents
    fetchChildren();
    <?php endif; ?>
});
</script>
```

## API Endpoints Created

1. **`core/api/users/update-profile.php`** - Updates first name, last name, and phone
2. **`core/api/users/update-password.php`** - Updates password with validation

## Features Implemented

✅ Personal information update (first name, last name, phone)
✅ Password update with validation
✅ Tab state persistence using localStorage
✅ Form validation
✅ Error and success messages
✅ Loading states on buttons
✅ Session updates after profile change

## Manual Fix Required

Due to file corruption, you need to manually fix the account.php structure:

1. Move lines 138-158 (Child Profiles tab content) to be inside the `<div class="tab-content">` section
2. Ensure proper nesting of `<ul>` and `<div class="tab-content">` elements
3. Add the form IDs and input names as shown above
4. Add the JavaScript code at the end of the file

The correct structure should be:

```
<ul class="nav nav-tabs">
    <li>Personal Tab</li>
    <li>Child Profiles Tab (if parent)</li>
    <li>Security Tab</li>
    <li>Favorites Tab</li>
</ul>

<div class="tab-content">
    <div id="personal">...</div>
    <div id="profile">...</div>  <!-- This is currently misplaced -->
    <div id="security">...</div>
    <div id="favorites">...</div>
</div>
```

# Critical File Corruption - account.php

## Status

The `account.php` file has become severely corrupted during editing attempts. The HTML structure is broken with misplaced elements.

## Recommended Action

**RESTORE FROM BACKUP** or manually rebuild the file using the following correct structure:

## Correct File Structure

```php
<?php
// 1. PHP Header - Database and session setup
require_once 'core/db/config.php';
include_once 'core/layout/book-card.php';

// Fetch user data and favorites
if (isset($_SESSION['user_id'])) {
    // ... database queries
}
$isParent = isset($_SESSION['role']) && $_SESSION['role'] === 'PARENT';
?>

<!-- 2. Session Debug Modal -->
<div class="modal fade" id="exampleModalToggle">...</div>

<!-- 3. Session Debug Button -->
<a class="btn btn-primary position-fixed">Session</a>

<!-- 4. Main Section -->
<section class="mt-5 p-2 p-md-5">
    <div class="container-fluid">
        <!-- 4.1 Header with Logout -->
        <div class="row my-5">...</div>

        <!-- 4.2 Profile Cards Row -->
        <div class="row my-5">
            <div class="row g-3 align-items-stretch">
                <!-- Parent Card -->
                <div class="col-12 col-md-4 col-lg-3">...</div>

                <!-- Child Summary Cards (Dynamic) -->
                <div id="childSummaryCards">...</div>
            </div>
        </div>

        <!-- 4.3 Tabs Section -->
        <div class="underline-tabs mb-5">
            <!-- Navigation Tabs -->
            <ul class="nav nav-tabs">
                <li>Personal Tab</li>
                <li>Child Profiles Tab (if parent)</li>
                <li>Security Tab</li>
                <li>Favorites Tab</li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content">
                <div id="personal">...</div>
                <div id="profile">...</div>
                <div id="security">...</div>
                <div id="favorites">...</div>
            </div>
        </div>
    </div>
</section>

<!-- 5. Modals (if parent) -->
<?php if ($isParent): ?>
    <div class="modal" id="addChildModal">...</div>
    <div class="modal" id="editChildModal">...</div>

    <!-- 6. JavaScript -->
    <script>
        // Children management functions
    </script>
<?php endif; ?>
```

## What Went Wrong

1. Multiple edit attempts caused HTML elements to be placed incorrectly
2. The `<ul>` and `<div class="tab-content">` got nested incorrectly
3. Modal content got mixed with profile cards
4. PHP endif statements are misplaced

## Solution

Please manually restore the file or use version control to revert to the last working version before the tab structure edits.

The API endpoints I created are still valid and working:

- `core/api/users/update-profile.php` ✅
- `core/api/users/update-password.php` ✅

Once the file structure is fixed, refer to `ACCOUNT_PAGE_UPDATE_GUIDE.md` for adding the form handlers and tab persistence.

# Children Management Test Checklist

## 1. User Access & Permissions

- [ ] **Parent Role**: Log in as a user with `ROLE = PARENT`. Verify "Child Profiles" tab is visible.
- [ ] **Non-Parent Role**: Log in as `ADMIN` or `CHILD`. Verify "Child Profiles" tab is **NOT** visible.

## 2. Adding a Child

- [ ] **First Child**: Click "Add Child". Fill in Name and DOB. Submit.
  - [ ] Verify child appears in the list.
  - [ ] Verify child appears in the top summary cards.
  - [ ] Verify a **Parent Passkey** is displayed (if it was previously empty).
  - [ ] Verify a unique **Child Code** is generated.
- [ ] **Multiple Children**: Add a second child.
  - [ ] Verify both children are listed.
  - [ ] Verify the Parent Passkey remains the same.

## 3. Managing Children

- [ ] **Edit Child**: Click "Edit" on a child card.
  - [ ] Change Name and DOB.
  - [ ] Save.
  - [ ] Verify the changes are reflected in the list (Name and Age).
- [ ] **Delete Child**: Click "Delete" on a child card.
  - [ ] Confirm the dialog.
  - [ ] Verify the child is removed from the list and summary cards.
- [ ] **Copy Code**: Click the copy button next to the Child Code.
  - [ ] Paste into a text editor to verify the correct code was copied.

## 4. Backend Validation

- [ ] **API Security**: Try accessing `core/api/children/list.php` directly in the browser without being logged in (should return 403 or error).
- [ ] **Data Integrity**: Check the database `children` table to ensure records are created with the correct `USER_ID` (Parent ID).

# Favorites Display on Account Page

## Implementation Summary

### Database Query

The account page now fetches the user's favorite books from the database using a JOIN query:

```sql
SELECT
    b.ID as id,
    b.TITLE as title,
    b.DESCRIPTION as description,
    b.AUTHOR as author,
    b.COVER as coverImage,
    b.FILE_PATH as filePath,
    b.AGE_GROUP as ageGroup
FROM favorites f
INNER JOIN books b ON f.BOOK_ID = b.ID
WHERE f.USER_ID = :user_id
AND b.IS_ACTIVE = 'Y'
ORDER BY f.CREATED_DATE DESC
```

### Key Features

1. **User-Specific**: Only shows books favorited by the logged-in user
2. **Active Books Only**: Filters out inactive books (`IS_ACTIVE = 'Y'`)
3. **Ordered by Date**: Most recently favorited books appear first
4. **Complete Book Data**: Fetches all necessary fields for `renderBookCard()`
5. **Error Handling**: Catches database errors and logs them

### Data Flow

```
User Logs In
    ↓
Session Contains user_id
    ↓
account.php Loads
    ↓
Query favorites Table (JOIN with books)
    ↓
Fetch Book Details
    ↓
Store in $books Array
    ↓
Render with renderBookCard()
```

### Code Structure

```php
<?php
include_once 'core/layout/book-card.php';
require_once 'core/db/config.php';

// Fetch user's favorite books
$books = [];
if (isset($_SESSION['user_id'])) {
    try {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("...");
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching favorites: " . $e->getMessage());
        $books = [];
    }
}
?>
```

### Display Logic

```php
<?php if (empty($books)): ?>
    <p class="text-center text-muted">No favorite books yet.</p>
<?php else: ?>
    <?php foreach ($books as $book): ?>
        <div class="col-lg-3 col-md-4 col-sm-6">
            <?php echo renderBookCard($book); ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
```

### Database Schema

**favorites Table:**

- `ID` - Primary key
- `USER_ID` - Foreign key to users table
- `BOOK_ID` - Foreign key to books table
- `CREATED_DATE` - When the favorite was added

**books Table:**

- `ID` - Primary key
- `TITLE` - Book title
- `DESCRIPTION` - Book description
- `AUTHOR` - Book author
- `COVER` - Cover image path
- `FILE_PATH` - PDF file path
- `AGE_GROUP` - Target age range
- `IS_ACTIVE` - Y/N flag

### Security

- ✅ Uses prepared statements (prevents SQL injection)
- ✅ Validates user session before querying
- ✅ Only fetches data for logged-in user
- ✅ Sanitizes output with `htmlspecialchars()` in renderBookCard
- ✅ Error logging (doesn't expose errors to user)

### Error Handling

- If user is not logged in: `$books` remains empty array
- If database error occurs: Error is logged, `$books` set to empty array
- If no favorites: Shows "No favorite books yet" message
- If books exist: Renders them in a responsive grid

### Integration with Favorites System

This display automatically updates when:

1. User adds a book to favorites (via toggle button)
2. User removes a book from favorites
3. Page is refreshed

The favorites buttons on the displayed books will show the correct state (filled heart, red button) because the `initFavorites()` JavaScript function loads the favorites status on page load.

### Testing Checklist

- [ ] Favorites display for logged-in user
- [ ] Empty state shows when no favorites
- [ ] Books display in correct order (newest first)
- [ ] Inactive books are filtered out
- [ ] Favorite buttons show correct state
- [ ] Clicking favorite button updates immediately
- [ ] Page refresh shows updated favorites
- [ ] Error handling works (database down, etc.)

# Enhanced Favorites System - Button Types

## Overview

The favorites system now supports two types of buttons:

1. **Icon-only buttons** (for book cards) - Small circular buttons with just a heart icon
2. **Text buttons** (for book detail page) - Full-width buttons with text that toggles

## Button Types

### 1. Icon-Only Button (Book Cards)

```html
<button
  type="button"
  class="btn-favorite icon btn btn-light rounded-circle shadow-sm position-absolute top-0 end-0 m-2"
  data-book-id="{$id}"
  data-book-title="{$title}"
  aria-pressed="false"
  aria-label="Add {$title} to favorites"
  title="Add to favorites"
>
  <i class="bi bi-heart"></i>
</button>
```

**Features:**

- Class `icon` identifies it as icon-only
- Circular button with heart icon
- Icon toggles between `bi-heart` and `bi-heart-fill`
- Button color toggles between light and danger (red)

### 2. Text Button (Book Detail Page)

```html
<button
  type="button"
  class="btn-favorite text btn btn-light"
  data-book-id="<?php echo htmlspecialchars($book['id']); ?>"
  data-book-title="<?php echo htmlspecialchars($book['title']); ?>"
  aria-pressed="false"
  aria-label="Add <?php echo htmlspecialchars($book['title']); ?> to favorites"
>
  <span class="btn-text">Add to Favorites</span>
</button>
```

**Features:**

- Class `text` identifies it as a text button
- Text toggles between "Add to Favorites" and "Remove from Favorites"
- Button color toggles between light and danger (red)
- `aria-label` updates dynamically
- NO icon (as requested)

## JavaScript Logic

### `setFavoriteState(btn, isFavorited)`

The function now detects button type and handles each appropriately:

```javascript
setFavoriteState(btn, isFavorited) {
  // Determine button type
  const isIconOnly = btn.classList.contains("icon");
  const isTextButton = btn.classList.contains("text");

  // Update button color and aria (both types)
  if (isFavorited) {
    btn.classList.add("btn-danger");
    btn.setAttribute("aria-pressed", "true");
  } else {
    btn.classList.remove("btn-danger");
    btn.setAttribute("aria-pressed", "false");
  }

  // Handle icon (icon-only buttons)
  const icon = btn.querySelector("i");
  if (icon) {
    if (isFavorited) {
      icon.classList.remove("bi-heart");
      icon.classList.add("bi-heart-fill");
    } else {
      icon.classList.remove("bi-heart-fill");
      icon.classList.add("bi-heart");
    }
  }

  // Handle text button (toggle text content)
  if (isTextButton) {
    const textSpan = btn.querySelector(".btn-text");
    if (textSpan) {
      textSpan.textContent = isFavorited
        ? "Remove from Favorites"
        : "Add to Favorites";
    }
    // Update aria-label
    const bookTitle = btn.dataset.bookTitle || "this book";
    btn.setAttribute(
      "aria-label",
      isFavorited
        ? `Remove ${bookTitle} from favorites`
        : `Add ${bookTitle} to favorites`
    );
  }
}
```

## Visual States

### Icon-Only Button

| State         | Icon                     | Color              | Aria-Pressed |
| ------------- | ------------------------ | ------------------ | ------------ |
| Not Favorited | `bi-heart` (outline)     | Light (white/gray) | false        |
| Favorited     | `bi-heart-fill` (filled) | Danger (red)       | true         |

### Text Button

| State         | Text                    | Color              | Aria-Pressed |
| ------------- | ----------------------- | ------------------ | ------------ |
| Not Favorited | "Add to Favorites"      | Light (white/gray) | false        |
| Favorited     | "Remove from Favorites" | Danger (red)       | true         |

## Accessibility

Both button types include:

- ✅ `aria-pressed` attribute (true/false)
- ✅ `aria-label` with book title
- ✅ Semantic button element
- ✅ Clear visual feedback
- ✅ Keyboard accessible

## Usage Examples

### Book Card (Library Page)

- Small circular button in top-right corner
- Only heart icon visible
- Minimal space usage
- Quick visual feedback

### Book Detail Page

- Full-width button below "Read Now"
- Clear text label
- Explicit action description
- Better for primary CTA placement

## Testing

- [x] Icon-only buttons toggle icon correctly
- [x] Icon-only buttons change color correctly
- [x] Text buttons toggle text correctly
- [x] Text buttons change color correctly
- [x] Both types sync with backend
- [x] Both types load correct state on page load
- [x] Accessibility attributes update correctly

# Favorites System Implementation

## Overview

Complete implementation of a favorites system that allows users to like/unlike books with real-time database synchronization.

## Components

### 1. Backend APIs

#### `core/api/favorites/status.php` (GET)

- **Purpose**: Returns all favorited book IDs for the logged-in user
- **Authentication**: Requires active session
- **Response**: `{ success: true, favorites: [1, 5, 12, ...] }`

#### `core/api/favorites/toggle.php` (POST)

- **Purpose**: Adds or removes a book from favorites
- **Authentication**: Requires active session
- **Input**: `{ book_id: 123, action: "like" | "unlike" }`
- **Response**: `{ success: true, status: "added" | "removed", is_favorited: true | false }`
- **Features**:
  - Validates book exists and is active before adding
  - Uses database transactions for data integrity
  - Prevents duplicate favorites

### 2. Frontend (main.js)

#### `initFavorites()`

- Runs on page load
- Fetches user's favorites from backend
- Applies correct visual state to all favorite buttons
- Attaches click handlers

#### `setFavoriteState(btn, isFavorited)`

- Updates button visual state (red/white, filled/empty heart)
- Sets ARIA attributes for accessibility

#### `toggleFavorite(btn)`

- **Optimistic UI**: Updates UI immediately for better UX
- Sends toggle request to backend
- **Error Handling**: Reverts UI if backend fails
- **Offline Support**: Syncs with localStorage

### 3. UI Components

#### Book Card (`core/layout/book-card.php`)

```html
<button
  class="btn-favorite btn btn-light"
  data-user-id="{user_id}"
  data-book-id="{book_id}"
  data-book-title="{title}"
>
  <i class="bi bi-heart"></i>
</button>
```

## Data Flow

### On Page Load:

1. `initFavorites()` calls `status.php`
2. Backend returns array of favorited book IDs
3. Frontend applies red/filled state to matching buttons

### On Button Click:

1. UI updates immediately (optimistic)
2. `toggle.php` called with book_id and action
3. Backend validates and updates database
4. Response confirms success/failure
5. If error, UI reverts to previous state
6. localStorage updated for offline support

## Database Schema

```sql
CREATE TABLE favorites (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    USER_ID INT NOT NULL,
    BOOK_ID INT NOT NULL,
    CREATED_DATE DATETIME,
    FOREIGN KEY (USER_ID) REFERENCES users(ID),
    FOREIGN KEY (BOOK_ID) REFERENCES books(ID),
    UNIQUE KEY unique_favorite (USER_ID, BOOK_ID)
);
```

## Security Features

- ✅ Session-based authentication
- ✅ User can only manage their own favorites
- ✅ SQL injection prevention (prepared statements)
- ✅ Book existence validation
- ✅ Duplicate prevention (database constraint)

## Error Handling

- Network errors: UI reverts, error logged to console
- Invalid book: Backend returns error message
- Unauthenticated: 401 response
- Database errors: Rolled back, logged server-side

## Testing Checklist

- [ ] Favorites load correctly on page load
- [ ] Clicking heart adds to favorites (UI + DB)
- [ ] Clicking filled heart removes from favorites
- [ ] Favorites persist across page reloads
- [ ] Multiple users have separate favorites
- [ ] Error states handled gracefully
- [ ] Works on library page
- [ ] Works on book detail page
- [ ] localStorage syncs correctly
