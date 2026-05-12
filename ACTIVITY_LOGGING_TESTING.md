# Activity Logging - Testing Guide

## Quick Testing Steps

### 1. Testing via Web Admin Interface

#### Test Package Module:
```
1. Login to admin panel as admin user
2. Go to Admin → Packages
3. Click "Create New Package"
4. Fill in details and save
5. Go to Admin → Activity Logs
6. Verify entry appears: "Membuat package baru: [package name]"
7. Edit the package and save
8. Verify update log appears: "Mengupdate package: [package name]"
9. Delete the package
10. Verify delete log appears: "Menghapus package: [package name]"
```

#### Test Kelas Module:
```
1. Go to Admin → Kelas
2. Create, update, and delete a kelas
3. Verify activity logs show all three operations
```

#### Test Role Management:
```
1. Go to Admin → Roles
2. Create, update, and delete a role
3. Verify activity logs track all changes
```

#### Test User Management:
```
1. Go to Admin → Users
2. Create a new user
3. Verify log: "Membuat user baru: [username]"
4. Edit user details
5. Verify log: "Mengupdate user: [username]"
6. Delete user
7. Verify log: "Menghapus user: [username]"
```

### 2. Testing via API (Using Postman/curl)

#### Test Package API:
```bash
# Create Package
POST /api/admin/packages
Content-Type: application/json
Authorization: Bearer {token}

{
  "nama_package": "Test Package",
  "jumlah_kredit": 100,
  "harga": 50000,
  "masa_berlaku": 30,
  "status_package": "active"
}

# Then check Activity Logs via:
GET /api/admin/activity-logs
```

#### Test Kelas API:
```bash
POST /api/admin/kelas
{
  "nama_kelas": "Test Kelas",
  "deskripsi": "Test Description",
  "kapasitas": 20
}
```

#### Test User API:
```bash
# Update User
PUT /api/admin/users/{id}
{
  "nama": "Updated Name"
}

# Deactivate User
POST /api/admin/users/{id}/deactivate
```

### 3. Database Verification

Check the activity_log table directly:
```sql
-- View recent activity logs
SELECT * FROM activity_log 
ORDER BY tanggal_log DESC 
LIMIT 10;

-- Count activities by module
SELECT modul, COUNT(*) as count 
FROM activity_log 
GROUP BY modul;

-- View specific user activities
SELECT * FROM activity_log 
WHERE id_user = 1 
ORDER BY tanggal_log DESC;
```

### 4. Verify Log Details

Each log entry should contain:
- ✅ `id_log` - Auto-incremented ID
- ✅ `id_user` - User who performed action (should not be NULL for authenticated operations)
- ✅ `modul` - Module name (package, kelas, role, user, etc.)
- ✅ `aktivitas` - Action type (create, update, delete, etc.)
- ✅ `keterangan` - Detailed description (including resource name)
- ✅ `tanggal_log` - Timestamp of operation

### 5. Edge Cases to Test

#### Test unauthenticated API calls:
```
- Admin activity log should show id_user = 0 for unauthenticated API calls
- Or should fail with 401 Unauthorized (depends on middleware)
```

#### Test batch operations:
```
- Create multiple resources in quick succession
- Verify each has separate log entry
- Check timestamps are sequential
```

#### Test concurrent updates:
```
- Have two admin users update same resource
- Verify both users' activities are logged
```

### 6. Activity Log Filtering Tests

In the Activity Logs page, verify you can:
- ✅ See all logs for admin users
- ✅ Filter by date (if filtering implemented)
- ✅ See username of who performed action
- ✅ See module name
- ✅ See action type (create, update, delete)
- ✅ See detailed description
- ✅ See timestamp

## Expected Results

### Successful Implementation Indicators:
- ✅ Every create operation logged
- ✅ Every update operation logged
- ✅ Every delete operation logged
- ✅ User context captured (id and name visible)
- ✅ Timestamps accurate
- ✅ Descriptions meaningful and descriptive
- ✅ Both Web and API operations logged
- ✅ Activity logs queryable and filterable

### Common Issues to Check:

1. **No logs appearing:**
   - Check if user is authenticated
   - Verify ActivityLogService is properly injected
   - Check database table exists and is accessible

2. **Logs with user_id = 0:**
   - Expected for unauthenticated API calls
   - Check if authentication middleware is applied

3. **Missing activity types:**
   - Verify service method has logging call
   - Check controller method calls service method
   - Ensure logging is after successful operation

4. **Duplicate logs:**
   - Check if both controller and service are logging (only service should log for API)
   - Verify service is being called once per operation

## Manual Inspection

To verify implementation without full testing:

1. Check all Web Admin controller files have ActivityLogService injected
2. Check all Service files have ActivityLogService injected
3. Check create/update/delete methods have $this->activityLog->log() calls
4. Check log calls include proper parameters (user_id, module, activity, description)
5. Verify no syntax errors in modified files

## Success Criteria

The implementation is successful when:
1. ✅ All CRUD operations are logged to database
2. ✅ Activity logs visible in admin panel
3. ✅ Each log has user context
4. ✅ Each log has module context
5. ✅ Each log has operation context
6. ✅ Each log has detailed description
7. ✅ Timestamps are accurate
8. ✅ Both Web and API operations logged

---

**Last Updated:** 2026-05-12
**Implementation Status:** ✅ Complete
