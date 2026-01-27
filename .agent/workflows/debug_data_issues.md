---
description: Steps to debug data issues by tracing from frontend to backend
---

1. **Identify the Missing Data in Frontend**:
   - Locate the widget or logic in the frontend (e.g., Flutter/Dart files) where data is expected but not appearing.
   - Check the model definition (e.g., `User.fromJson`) to see if the field is being parsed.

2. **Trace the API Call**:
   - Find the `ApiService` method being called.
   - Identify the specific API endpoint (e.g., `/guard-change-requests/users`).

3. **Inspect the Backend Implementation**:
   - Locate the corresponding route in `routes/api.php` to find the Controller and Method.
   - Open the Controller file (e.g., `app/Http/Controllers/Api/...`).
   - examining the `select()` or `with()` clauses in the Eloquent query.
   - **Crucial Step**: Verify if the required column (e.g., `department`) is actually included in the selected columns.

4. **Verify Database Structure (Optional)**:
   - If the column is selected but still null, check `database/migrations` or `seeders` to ensure the column exists and has data.

5. **Fix and Verification**:
   - Add the missing field to the Controller's query `select` list.
   - Retry the operation in the frontend.
