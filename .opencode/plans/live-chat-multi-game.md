# Live Chat Multi-Game Johen - Implementation Plan

## Overview
Implementasi fitur Live Chat Multi-Game pada website Johen Marketplace.

## Role System Decision
- Tambah kolom `is_live_chat_admin` di users table
- `is_admin=true` = Super Admin (existing)
- `is_live_chat_admin=true` = Live Chat Admin (baru)
- Operator = user yang di-assign via `live_chat_operators` table

## Real-time Strategy
- AJAX polling setiap 3 detik
- Poll new messages & unread count

## Operator Creation
- Super admin buat user dari admin panel (email, password, role)
- Lalu assign sebagai operator ke channel

## File yang akan dibuat (37 file)

### Database Migrations (6 file)
1. `database/migrations/2026_09_13_000001_add_live_chat_columns_to_users_table.php`
2. `database/migrations/2026_09_13_000002_create_live_chat_channels_table.php`
3. `database/migrations/2026_09_13_000003_create_live_chat_conversations_table.php`
4. `database/migrations/2026_09_13_000004_create_live_chat_messages_table.php`
5. `database/migrations/2026_09_13_000005_create_live_chat_operators_table.php`
6. `database/migrations/2026_09_13_000006_create_live_chat_operator_schedules_table.php`

### Models (5 file)
7. `app/Models/LiveChatChannel.php`
8. `app/Models/LiveChatConversation.php`
9. `app/Models/LiveChatMessage.php`
10. `app/Models/LiveChatOperator.php`
11. `app/Models/LiveChatOperatorSchedule.php`

### Seeder (1 file)
12. `database/seeders/LiveChatChannelSeeder.php`

### Middleware (1 file)
13. `app/Http/Middleware/LiveChatAdminMiddleware.php`

### Policies (3 file)
14. `app/Policies/LiveChatConversationPolicy.php`
15. `app/Policies/LiveChatMessagePolicy.php`
16. `app/Policies/LiveChatOperatorPolicy.php`

### Controllers (6 file)
17. `app/Http/Controllers/LiveChatController.php`
18. `app/Http/Controllers/Admin/LiveChatDashboardController.php`
19. `app/Http/Controllers/Admin/LiveChatChannelController.php`
20. `app/Http/Controllers/Admin/LiveChatConversationController.php`
21. `app/Http/Controllers/Admin/LiveChatOperatorController.php`
22. `app/Http/Controllers/Admin/LiveChatChatController.php`

### Frontend (3 file)
23. `resources/views/livewire/partials/livechat-popup.blade.php`
24. `public/css/livechat.css`
25. `public/js/livechat.js`

### Admin Views (5 file)
26. `resources/views/admin/live-chat/dashboard.blade.php`
27. `resources/views/admin/live-chat/conversations/index.blade.php`
28. `resources/views/admin/live-chat/conversations/show.blade.php`
29. `resources/views/admin/live-chat/channels/index.blade.php`
30. `resources/views/admin/live-chat/operators/index.blade.php`

## File yang akan dimodifikasi (7 file)
31. `app/Models/User.php` - tambah relationship & method
32. `routes/web.php` - tambah routes live chat
33. `resources/views/layouts/topup.blade.php` - ganti fungsi floating CS button
34. `resources/views/admin/layouts/app.blade.php` - tambah sidebar menu
35. `bootstrap/app.php` - register middleware
36. `app/Providers/AuthServiceProvider.php` - register policies
37. `database/seeders/DatabaseSeeder.php` - tambah LiveChatChannelSeeder

## Database Schema

### users (modifikasi)
```
+ is_live_chat_admin BOOLEAN DEFAULT FALSE
```

### live_chat_channels
```
id, name, slug, description, icon, image, is_active, sort_order, created_at, updated_at
```

### live_chat_conversations
```
id, channel_id (FK), user_id (FK), status (open/pending/closed), 
last_message_at, user_unread_count, admin_unread_count, 
created_at, updated_at
```

### live_chat_messages
```
id, conversation_id (FK), sender_id (FK), sender_type, message_type,
message, media_path, media_name, media_mime, media_size,
reply_to_message_id (FK nullable), read_at, deleted_at, created_at, updated_at
```

### live_chat_operators
```
id, user_id (FK), channel_id (FK), is_active, created_at, updated_at
```

### live_chat_operator_schedules
```
id, operator_id (FK), day_of_week, start_time, end_time, is_active, created_at, updated_at
```

## Phase Execution Plan
1. Database & Models → 2. Authorization → 3. Backend → 4. Frontend User → 5. Admin Panel → 6. Real-time → 7. Testing
