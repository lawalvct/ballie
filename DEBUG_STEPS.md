# Debug Attendance Issues

Let me help you troubleshoot step by step:

## Step 1: Check Browser Console for Errors

1. Open your attendance page
2. Press `F12` to open DevTools
3. Click the **Console** tab
4. Refresh the page
5. Look for any **red errors**

**Common errors to look for:**

-   `Alpine is not defined`
-   `attendanceManager is not defined`
-   `Uncaught ReferenceError`
-   `404 errors` for missing files

**Take a screenshot of any errors you see.**

---

## Step 2: Test Alpine.js Loading

In the browser console, type:

```javascript
window.Alpine;
```

**Expected result**: Should show Alpine object
**If undefined**: Alpine.js is not loading

---

## Step 3: Test Manual Entry Button Click

1. Right-click the "Manual Entry" button
2. Click "Inspect Element"
3. In the Elements tab, you should see:

```html
<button @click="showManualEntryModal = true" ...></button>
```

4. In Console, type:

```javascript
document.querySelector('[\\@click="showManualEntryModal = true"]');
```

**Expected**: Should find the button element
**If null**: Button not found

---

## Step 4: Test Alpine Data

In console, type:

```javascript
// Get the div with x-data
const div = document.querySelector('[x-data="attendanceManager()"]');
console.log(div._x_dataStack);
```

**Expected**: Should show array with attendance manager data
**If undefined**: Alpine not initialized properly

---

## Step 5: Manually Test Modal

In console, try:

```javascript
// Find Alpine component and trigger modal
const div = document.querySelector('[x-data="attendanceManager()"]');
if (div) {
    div.__alpine.showManualEntryModal = true;
}
```

**Expected**: Modal should appear
**If error**: Alpine component issue

---

## Step 6: Check Network Requests

1. Go to **Network** tab in DevTools
2. Click "Manual Entry" button
3. Look for any failed requests (red status)
4. Check if `/manual-entry` route is being called

---

## Step 7: Test Clock-In Error

1. Find an employee in the table
2. Click "Clock In" button
3. Check **Network** tab for the request
4. Look at the **Response** - should show clock-in time

---

**Please run these tests and tell me what you find. Send screenshots of any errors!**
