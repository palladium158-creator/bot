<?php
namespace PalladiumBot;

final class Bot
{
    public function __construct(private Database $db, private Telegram $tg, private Config $config) {}

    public function handle(array $u): void
    {
        if (isset($u['message'])) $this->message($u['message']);
        if (isset($u['callback_query'])) $this->callback($u['callback_query']);
    }

    private function text(string $key): string { return $this->db->one('SELECT value FROM bot_texts WHERE key=?',[$key])['value'] ?? $key; }
    private function isAdmin(int $id): bool { return $id === $this->config->ownerId() || (bool)$this->db->one('SELECT tg_id FROM admins WHERE tg_id=? AND active=1',[$id]); }
    private function upsertUser(array $from): int
    {
        $now = date('c');
        $this->db->run('INSERT INTO users(tg_id,username,first_name,last_name,started_at,last_seen) VALUES(?,?,?,?,?,?) ON CONFLICT(tg_id) DO UPDATE SET username=excluded.username, first_name=excluded.first_name, last_name=excluded.last_name, last_seen=excluded.last_seen',[$from['id'],$from['username']??'', $from['first_name']??'', $from['last_name']??'', $now, $now]);
        return (int)$this->db->one('SELECT id FROM users WHERE tg_id=?',[$from['id']])['id'];
    }

    private function message(array $m): void
    {
        $from = $m['from'] ?? []; if (!$from) return; $uid = $this->upsertUser($from); $chat = $m['chat']['id']; $txt = trim($m['text'] ?? '');
        if ($txt === '/start') { $this->tg->sendMessage($chat, $this->text('welcome')."\n\n".$this->text('brand'), Keyboards::main()); return; }
        if ($txt === '/admin' && $this->isAdmin((int)$from['id'])) { $this->tg->sendMessage($chat, '🛡 پنل مدیریت پالادیوم', Keyboards::admin()); return; }
        if ($txt === 'پیکربندی' && $from['id'] == $this->config->ownerId() && in_array($m['chat']['type'] ?? '', ['group','supergroup'], true)) { $this->configureGroup($m); return; }
        $state = $this->db->one('SELECT state,state_data FROM users WHERE id=?',[$uid]);
        match ($state['state'] ?? '') {
            'ticket_wait' => $this->createTicket($uid, $from, $m),
            'challenge_wait' => $this->createChallengeEntry($uid, $m),
            'match_name','match_city','match_orientation','match_position','match_self','match_partner','match_show' => $this->matchWizard($uid, $chat, $txt, $state),
            default => $this->tg->sendMessage($chat, 'لطفاً از دکمه‌های منظم زیر استفاده کنید 👇', Keyboards::main()),
        };
    }

    private function callback(array $c): void
    {
        $from=$c['from']; $chat=$c['message']['chat']['id']; $data=$c['data']; $uid=$this->upsertUser($from);
        if ($data==='home') { $this->tg->sendMessage($chat,$this->text('welcome'),Keyboards::main()); return; }
        if ($data==='svc:ticket') { $this->db->run('UPDATE users SET state=? WHERE id=?',['ticket_wait',$uid]); $this->tg->sendMessage($chat,'🎫 پیام، عکس یا فایل خود را برای ثبت تیکت ارسال کنید.',Keyboards::back()); return; }
        if ($data==='svc:challenge') { $cat=$this->db->one("SELECT custom_text FROM categories WHERE type='challenge' AND enabled=1 ORDER BY sort_order LIMIT 1"); $this->db->run('UPDATE users SET state=? WHERE id=?',['challenge_wait',$uid]); $this->tg->sendMessage($chat,'🏆 '.($cat['custom_text']??'اثر خود را ارسال کنید.'),Keyboards::back()); return; }
        if ($data==='svc:match') { $this->db->run('UPDATE users SET state=?, state_data=? WHERE id=?',['match_name','{}',$uid]); $this->tg->sendMessage($chat,'💞 فرم رل‌یابی شروع شد. نام نمایشی خود را بنویسید:',Keyboards::back()); return; }
        if (str_starts_with($data,'admin:') && $this->isAdmin((int)$from['id'])) $this->adminAction($chat,$data);
    }

    private function createTicket(int $uid, array $from, array $m): void
    {
        $cat=$this->db->one("SELECT id,admin_ids FROM categories WHERE type='ticket' AND enabled=1 ORDER BY sort_order LIMIT 1");
        $now=date('c'); $this->db->run('INSERT INTO tickets(user_id,category_id,subject,created_at,updated_at) VALUES(?,?,?,?,?)',[$uid,$cat['id']??null,'تیکت جدید',$now,$now]);
        $tid=(int)$this->db->pdo->lastInsertId(); $content=$m['text']??($m['caption']??'رسانه/فایل'); $file=$m['photo'][array_key_last($m['photo']??[])] ['file_id'] ?? ($m['document']['file_id']??'');
        $this->db->run('INSERT INTO ticket_messages(ticket_id,sender_type,sender_tg_id,message_type,content,file_id,created_at) VALUES(?,?,?,?,?,?,?)',[$tid,'user',$from['id'],'message',$content,$file,$now]);
        $info="🎫 تیکت کل #$tid\n👤 نام: ".htmlspecialchars(($from['first_name']??'').' '.($from['last_name']??''))."\n🆔 آیدی: @".htmlspecialchars($from['username']??'ندارد')."\n🔢 آیدی عددی: {$from['id']}\n💬 پیام: ".htmlspecialchars($content);
        foreach ($this->targetAdmins($cat['admin_ids']??'[]') as $admin) $this->tg->sendMessage($admin,$info,['inline_keyboard'=>[[['text'=>'↪️ ارجاع','callback_data'=>"ticket:assign:$tid"],[ 'text'=>'📝 یادداشت','callback_data'=>"ticket:note:$tid"]]]]);
        $this->db->run('UPDATE users SET state=NULL,state_data=NULL WHERE id=?',[$uid]); $this->tg->sendMessage($m['chat']['id'],$this->text('ticket_created'),Keyboards::main());
    }
    private function targetAdmins(string $json): array { $ids=json_decode($json,true)?:[]; return $ids ?: [$this->config->ownerId()]; }
    private function createChallengeEntry(int $uid, array $m): void { $file=$m['photo'][array_key_last($m['photo']??[])] ['file_id'] ?? ''; $this->db->run('INSERT INTO challenge_entries(challenge_id,user_id,content,file_id,created_at) VALUES(?,?,?,?,?)',[1,$uid,$m['text']??($m['caption']??''),$file,date('c')]); $this->db->run('UPDATE users SET state=NULL WHERE id=?',[$uid]); $this->tg->sendMessage($m['chat']['id'],$this->text('challenge_received'),Keyboards::main()); }
    private function matchWizard(int $uid,int $chat,string $txt,array $state): void { $data=json_decode($state['state_data']?:'{}',true)?:[]; $map=['match_name'=>['name','شهر خود را بنویسید:','match_city'],'match_city'=>['city','گرایش خود را بنویسید (گی، لز، بای، استریت و...):','match_orientation'],'match_orientation'=>['orientation','پوزیشن خود را بنویسید:','match_position'],'match_position'=>['position','خصوصیات اخلاقی خودتان را بنویسید:','match_self'],'match_self'=>['self_traits','خصوصیات پارتنر دلخواه را بنویسید:','match_partner'],'match_partner'=>['partner_traits','آیا آیدی شما در فرم نمایش داده شود؟ بله/خیر','match_show']]; if (($state['state']??'')==='match_show') { $data['show_username']=str_contains($txt,'بله')?1:0; $this->db->run('INSERT INTO match_forms(user_id,name,city,orientation,position,self_traits,partner_traits,show_username,created_at,updated_at) VALUES(?,?,?,?,?,?,?,?,?,?)',[$uid,$data['name']??'',$data['city']??'',$data['orientation']??'',$data['position']??'',$data['self_traits']??'',$data['partner_traits']??'', $data['show_username'],date('c'),date('c')]); $this->db->run('UPDATE users SET state=NULL,state_data=NULL WHERE id=?',[$uid]); $this->tg->sendMessage($chat,$this->text('match_received'),Keyboards::main()); return; } [$field,$nextText,$nextState]=$map[$state['state']]; $data[$field]=$txt; $this->db->run('UPDATE users SET state=?,state_data=? WHERE id=?',[$nextState,json_encode($data,JSON_UNESCAPED_UNICODE),$uid]); $this->tg->sendMessage($chat,$nextText,Keyboards::back()); }
    private function adminAction(int $chat,string $data): void { $stats=$this->db->one('SELECT (SELECT COUNT(*) FROM users) users,(SELECT COUNT(*) FROM tickets) tickets,(SELECT COUNT(*) FROM challenge_entries) challenges,(SELECT COUNT(*) FROM match_forms) matches'); $this->tg->sendMessage($chat,"📊 داشبورد\nکاربران: {$stats['users']}\nتیکت‌ها: {$stats['tickets']}\nآثار چالش: {$stats['challenges']}\nفرم‌های رل‌یابی: {$stats['matches']}\n\nبخش‌های پیشرفته از همین پنل و با متن‌های قابل ویرایش توسعه‌پذیر هستند.",Keyboards::admin()); }
    private function configureGroup(array $m): void { $admins=$this->tg->call('getChatAdministrators',['chat_id'=>$m['chat']['id']]); foreach (($admins['result']??[]) as $a) if (!($a['user']['is_bot']??false)) $this->db->run('INSERT OR IGNORE INTO admins(tg_id,name,created_at) VALUES(?,?,?)',[$a['user']['id'],$a['user']['first_name']??'',date('c')]); $this->tg->sendMessage($m['chat']['id'],'✅ ادمین‌های گروه به مدیران ربات اضافه شدند.'); }
}
