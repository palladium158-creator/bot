CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTOINCREMENT, tg_id INTEGER UNIQUE, username TEXT, first_name TEXT, last_name TEXT, phone TEXT, state TEXT, state_data TEXT, started_at TEXT, last_seen TEXT, blocked INTEGER DEFAULT 0);
CREATE TABLE IF NOT EXISTS admins (tg_id INTEGER PRIMARY KEY, name TEXT, role TEXT DEFAULT 'admin', permissions TEXT DEFAULT '{}', active INTEGER DEFAULT 1, warning_count INTEGER DEFAULT 0, dossier TEXT DEFAULT '', created_at TEXT);
CREATE TABLE IF NOT EXISTS categories (id INTEGER PRIMARY KEY AUTOINCREMENT, type TEXT, title TEXT, description TEXT, sort_order INTEGER DEFAULT 100, enabled INTEGER DEFAULT 1, admin_ids TEXT DEFAULT '[]', custom_text TEXT DEFAULT '');
CREATE TABLE IF NOT EXISTS tickets (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER, category_id INTEGER, status TEXT DEFAULT 'open', assigned_admin INTEGER, subject TEXT, created_at TEXT, updated_at TEXT);
CREATE TABLE IF NOT EXISTS ticket_messages (id INTEGER PRIMARY KEY AUTOINCREMENT, ticket_id INTEGER, sender_type TEXT, sender_tg_id INTEGER, message_type TEXT, content TEXT, file_id TEXT, created_at TEXT);
CREATE TABLE IF NOT EXISTS private_notes (id INTEGER PRIMARY KEY AUTOINCREMENT, ticket_id INTEGER, admin_tg_id INTEGER, visibility TEXT, note TEXT, created_at TEXT);
CREATE TABLE IF NOT EXISTS challenges (id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT, body TEXT, media_file_id TEXT, channel_id TEXT, starts_at TEXT, ends_at TEXT, status TEXT DEFAULT 'draft', template TEXT, created_at TEXT);
CREATE TABLE IF NOT EXISTS challenge_entries (id INTEGER PRIMARY KEY AUTOINCREMENT, challenge_id INTEGER, user_id INTEGER, content TEXT, file_id TEXT, status TEXT DEFAULT 'pending', reject_reason TEXT, channel_message_id INTEGER, likes INTEGER DEFAULT 0, dislikes INTEGER DEFAULT 0, created_at TEXT);
CREATE TABLE IF NOT EXISTS match_forms (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER, name TEXT, city TEXT, orientation TEXT, position TEXT, self_traits TEXT, partner_traits TEXT, show_username INTEGER DEFAULT 0, status TEXT DEFAULT 'pending', reject_reason TEXT, channel_id TEXT, channel_message_id INTEGER, created_at TEXT, updated_at TEXT);
CREATE TABLE IF NOT EXISTS forced_channels (id INTEGER PRIMARY KEY AUTOINCREMENT, chat_id TEXT, title TEXT, services TEXT DEFAULT '[]', enabled INTEGER DEFAULT 1, joined_count INTEGER DEFAULT 0, left_count INTEGER DEFAULT 0, stayed_count INTEGER DEFAULT 0);
CREATE TABLE IF NOT EXISTS broadcasts (id INTEGER PRIMARY KEY AUTOINCREMENT, sender_tg_id INTEGER, target_type TEXT, target_ids TEXT, content TEXT, pin INTEGER DEFAULT 0, created_at TEXT);
CREATE TABLE IF NOT EXISTS bot_texts (key TEXT PRIMARY KEY, value TEXT);
CREATE TABLE IF NOT EXISTS canned_replies (id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT, body TEXT, enabled INTEGER DEFAULT 1);
CREATE TABLE IF NOT EXISTS settings (key TEXT PRIMARY KEY, value TEXT);
INSERT OR IGNORE INTO bot_texts(key,value) VALUES
('welcome','✨ به ربات چندکاره پالادیوم خوش آمدید. لطفاً یکی از بخش‌ها را انتخاب کنید.'),
('brand','🇮🇷 طراحی شده توسط تیم پالادیوم؛ کاملاً بومی، ایرانی و فارسی.'),
('ticket_created','✅ پیام شما ثبت شد و به مدیر مربوطه ارسال گردید.'),
('challenge_received','✅ اثر شما دریافت شد؛ پس از بررسی مدیران در چالش قرار می‌گیرد.'),
('match_received','✅ فرم رل‌یابی شما ثبت شد و در انتظار تأیید است.');
INSERT OR IGNORE INTO categories(type,title,description,sort_order,custom_text) VALUES
('ticket','پشتیبانی','ارسال تیکت برای پشتیبانی',10,''),('challenge','چالش عکس','عکس خود را برای شرکت در چالش بفرستید.',20,'اکنون می‌توانید در چالش شرکت کنید؛ عکس یا متن خود را ارسال کنید.'),('match','رل‌یابی','ثبت فرم پارتنریابی',30,'فرم زیر را دقیق تکمیل کنید.');
