# CLAUDE.md — イベント申込フォーム管理システム

## プロジェクト概要

管理者がイベントと試合枠を自由に作成・管理できる**マルチイベント対応の申込フォームシステム**。
管理者がイベントごとに申込フォームを発行し、利用者はログイン不要で申込できる。
**Laravel 13** + さくらのレンタルサーバー（PHP 8.3 / MySQL）上で動作する。

---

## 技術スタック

| 項目 | 内容 |
|------|------|
| フレームワーク | Laravel 13 |
| PHP | 8.3 以上 |
| データベース | MySQL 5.7 以上 |
| フロントエンド | Blade テンプレート / Tailwind CSS v4（デジタル庁デザインシステム準拠） / Vanilla JS |
| 管理者認証 | Laravel Auth（admin ガード） |
| メール送信 | Laravel Mail + さくらのSMTP |
| デプロイ先 | さくらのレンタルサーバー（スタンダード以上） |

---

## システム全体像

```
【管理者】
  ├─ イベントを作成（名称・日程・説明・公開状態）
  ├─ 試合枠を設定（日付・時刻・定員）
  ├─ 申込フォームのURLを発行・共有
  └─ 申込状況の確認・CSV出力

【利用者】
  ├─ 発行されたURLにアクセス（ログイン不要）
  ├─ チーム情報・希望枠を入力・確認・送信
  └─ 確認メールを受信
```

---

## アクセス権限の設計

| 対象 | 認証 | アクセス可能ページ |
|------|------|-----------------|
| **一般利用者** | 不要（ログインなし） | 各イベントの申込フォーム・確認・完了画面 |
| **管理者** | メール＋パスワードでログイン | 管理画面全体（イベント管理・申込管理・CSV等） |

---

## ディレクトリ構成

```
project-root/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── EntryController.php             ← 申込フォーム（利用者向け）
│   │   │   └── Admin/
│   │   │       ├── AuthController.php          ← 管理者ログイン・ログアウト
│   │   │       ├── DashboardController.php     ← ダッシュボード
│   │   │       ├── EventController.php         ← イベントCRUD
│   │   │       ├── SlotController.php          ← 試合枠CRUD
│   │   │       ├── EntryController.php         ← 申込一覧・詳細・ステータス変更
│   │   │       └── ExportController.php        ← CSV出力
│   │   └── Requests/
│   │       ├── EntryRequest.php                ← 申込バリデーション
│   │       ├── Admin/
│   │       │   ├── EventRequest.php            ← イベント作成・編集バリデーション
│   │       │   └── SlotRequest.php             ← 試合枠バリデーション
│   ├── Models/
│   │   ├── Event.php                           ← イベントモデル
│   │   ├── Slot.php                            ← 試合枠モデル
│   │   ├── Entry.php                           ← 申込モデル
│   │   └── AdminUser.php                       ← 管理者モデル（Authenticatable）
│   └── Mail/
│       ├── EntryConfirmation.php               ← 申込者への確認メール
│       └── EntryNotification.php               ← 管理者への通知メール（任意）
│
├── database/
│   ├── migrations/
│   │   ├── xxxx_create_admin_users_table.php
│   │   ├── xxxx_create_guest_users_table.php
│   │   ├── xxxx_create_guest_email_verifications_table.php
│   │   ├── xxxx_create_guest_event_owners_table.php
│   │   ├── xxxx_create_events_table.php
│   │   ├── xxxx_create_slots_table.php
│   │   ├── xxxx_create_entries_table.php
│   │   └── xxxx_create_entry_members_table.php
│   └── seeders/
│       ├── AdminUserSeeder.php                 ← 初期管理者アカウント
│       └── SampleEventSeeder.php               ← 開発用サンプルデータ（任意）
│
├── resources/views/
│   ├── layouts/
│   │   ├── entry.blade.php                     ← 利用者向けレイアウト
│   │   └── admin.blade.php                     ← 管理者向けレイアウト
│   ├── entry/
│   │   ├── index.blade.php                     ← 申込フォーム（入力画面）
│   │   ├── confirm.blade.php                   ← 確認画面
│   │   └── complete.blade.php                  ← 完了画面
│   └── admin/
│       ├── login.blade.php
│       ├── dashboard.blade.php
│       ├── events/
│       │   ├── index.blade.php                 ← イベント一覧
│       │   ├── create.blade.php                ← イベント作成
│       │   ├── edit.blade.php                  ← イベント編集
│       │   └── show.blade.php                  ← イベント詳細（枠一覧・申込状況）
│       ├── slots/
│       │   ├── create.blade.php                ← 試合枠追加
│       │   └── edit.blade.php                  ← 試合枠編集
│       └── entries/
│           ├── index.blade.php                 ← 申込一覧
│           └── show.blade.php                  ← 申込詳細
│
├── routes/
│   ├── web.php                                 ← 利用者向けルート
│   └── admin.php                               ← 管理者向けルート
│
└── config/
    └── auth.php                                ← adminガードを追記
```

---

## データベース設計

### admin_users テーブル（管理者）

```php
Schema::create('admin_users', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100);
    $table->string('email', 255)->unique();
    $table->string('password');
    $table->boolean('is_active')->default(true);
    $table->timestamp('last_login')->nullable();
    $table->rememberToken();
    $table->timestamps();
});
```

### events テーブル（イベント）

```php
Schema::create('events', function (Blueprint $table) {
    $table->id();
    $table->string('title', 200);                       // イベント名
    $table->text('description')->nullable();            // イベント説明（フォーム上部に表示）
    $table->string('slug', 100)->unique();              // URL用スラッグ（例: summer-game-2025）
    $table->date('start_date');                         // 開催開始日
    $table->date('end_date');                           // 開催終了日
    $table->unsignedTinyInteger('member_count')->default(5); // チーム人数（1〜10）
    $table->string('contact_email', 255)->nullable();   // 問合せ先メール
    $table->text('notes')->nullable();                  // フォーム下部の注意事項
    $table->enum('status', ['draft', 'open', 'closed'])->default('draft');
    // draft:非公開, open:受付中, closed:受付終了
    $table->foreignId('created_by')
          ->nullable()
          ->constrained('admin_users')
          ->nullOnDelete();                             // 作成した管理者（ゲスト作成時はNULL）
    $table->timestamps();
});
```

### slots テーブル（試合枠）

```php
Schema::create('slots', function (Blueprint $table) {
    $table->id();
    $table->foreignId('event_id')->constrained()->cascadeOnDelete();
    $table->date('game_date');
    $table->unsignedTinyInteger('game_no');             // 枠番号（表示用）
    $table->time('start_time');
    $table->time('end_time');
    $table->unsignedInteger('capacity')->default(10);   // 定員
    $table->boolean('is_active')->default(true);        // 個別の公開フラグ
    $table->timestamps();
    $table->unique(['event_id', 'game_date', 'game_no']);
});
```

### entries テーブル（申込データ）

```php
Schema::create('entries', function (Blueprint $table) {
    $table->id();
    $table->foreignId('event_id')->constrained()->restrictOnDelete();
    $table->foreignId('slot_id')->constrained()->restrictOnDelete();
    $table->foreignId('guest_user_id')->nullable()->constrained()->nullOnDelete();
    $table->string('entry_no', 20)->unique();           // 受付番号（自動生成）
    $table->string('edit_token', 64)->unique()->nullable(); // 編集URL用トークン
    $table->string('rep_name', 100);                    // 代表者氏名
    $table->unsignedTinyInteger('rep_age');             // 代表者年齢
    $table->string('email', 255);
    $table->enum('status', ['confirmed', 'cancelled'])->default('confirmed');
    $table->timestamps();
});
```

### entry_members テーブル（メンバー明細）

```php
Schema::create('entry_members', function (Blueprint $table) {
    $table->id();
    $table->foreignId('entry_id')->constrained()->cascadeOnDelete();
    $table->unsignedTinyInteger('sort_order');          // 並び順（1〜member_count）
    $table->string('name', 100);
    $table->unsignedTinyInteger('grade');               // 学年 1〜6
    $table->timestamps();
    $table->unique(['entry_id', 'sort_order']);
});
```

### モデルのリレーション

```
AdminUser   1 ── * Event
GuestUser   1 ── * Event（ゲスト作成分）
GuestUser   1 ── * Entry
Event       1 ── * Slot
Event       1 ── * Entry
Slot        1 ── * Entry
Entry       1 ── * EntryMember
```

---

## ルーティング設計

### routes/web.php（利用者向け）

```php
// イベントのslugでフォームを識別
Route::get('/entry/{slug}',          [EntryController::class, 'index'])  ->name('entry.index');
Route::post('/entry/{slug}/confirm', [EntryController::class, 'confirm'])->name('entry.confirm');
Route::post('/entry/{slug}/submit',  [EntryController::class, 'submit']) ->name('entry.submit');
Route::get('/entry/{slug}/complete', [EntryController::class, 'complete'])->name('entry.complete');
```

**利用者向けフォームURL例：**
```
https://yourdomain.com/entry/summer-game-2025
https://yourdomain.com/entry/winter-cup-2025
```

### routes/admin.php（管理者向け）

```php
// ログイン（認証不要）
Route::get( '/admin/login',  [Admin\AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login',  [Admin\AuthController::class, 'login']);
Route::post('/admin/logout', [Admin\AuthController::class, 'logout'])->name('admin.logout');

Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {

    // ダッシュボード
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // イベント管理
    Route::resource('events', Admin\EventController::class);
    // GET    /admin/events           → index   イベント一覧
    // GET    /admin/events/create    → create  イベント作成フォーム
    // POST   /admin/events           → store   イベント保存
    // GET    /admin/events/{event}   → show    イベント詳細（枠・申込状況）
    // GET    /admin/events/{event}/edit → edit イベント編集フォーム
    // PATCH  /admin/events/{event}   → update  イベント更新
    // DELETE /admin/events/{event}   → destroy イベント削除

    // 試合枠管理（イベント配下）
    Route::post(  '/events/{event}/slots',         [Admin\SlotController::class, 'store'])  ->name('slots.store');
    Route::get(   '/events/{event}/slots/create',  [Admin\SlotController::class, 'create']) ->name('slots.create');
    Route::get(   '/events/{event}/slots/{slot}/edit', [Admin\SlotController::class, 'edit'])   ->name('slots.edit');
    Route::patch( '/events/{event}/slots/{slot}',  [Admin\SlotController::class, 'update']) ->name('slots.update');
    Route::delete('/events/{event}/slots/{slot}',  [Admin\SlotController::class, 'destroy'])->name('slots.destroy');
    // 一括登録（CSV取込 or フォームで複数行入力）
    Route::post(  '/events/{event}/slots/bulk',    [Admin\SlotController::class, 'bulk'])   ->name('slots.bulk');

    // 申込管理
    Route::get(  '/events/{event}/entries',          [Admin\EntryController::class, 'index'])       ->name('entries.index');
    Route::get(  '/events/{event}/entries/{entry}',  [Admin\EntryController::class, 'show'])        ->name('entries.show');
    Route::patch('/events/{event}/entries/{entry}',  [Admin\EntryController::class, 'updateStatus'])->name('entries.updateStatus');

    // CSV出力
    Route::get('/events/{event}/export', [Admin\ExportController::class, 'download'])->name('export');
});
```

---

## 処理フロー

### 利用者：申込フロー

```
GET  /entry/{slug}
  → Eventをslugで検索（status=openでなければ「受付終了」画面）
  → 紐づくSlotsと申込数を取得・残席計算
  → entry/index.blade.php 表示

POST /entry/{slug}/confirm
  → EntryRequest でバリデーション
  → セッションに入力値を一時保存
  → entry/confirm.blade.php 表示（確認画面）

POST /entry/{slug}/submit
  → CSRFトークン検証（Laravel自動）
  → DB::transaction() + lockForUpdate() で定員チェック
  → 定員超過 → back()->withErrors() でフォームに戻す
  → Entry::create() で登録
  → entry_no を自動生成・保存
  → Mail::to()->send(new EntryConfirmation()) で確認メール送信
  → redirect()->route('entry.complete', $slug)

GET  /entry/{slug}/complete
  → entry/complete.blade.php 表示
```

### 管理者：イベント作成フロー

```
GET  /admin/events/create
  → イベント作成フォーム表示
  （タイトル・スラッグ・開催日・説明・注意事項・ステータス）

POST /admin/events
  → EventRequest でバリデーション
  → Event::create()（created_by に認証中の管理者IDをセット）
  → redirect()->route('admin.slots.create', $event) でスラッグ作成へ誘導

GET  /admin/events/{event}/slots/create
  → 試合枠追加フォーム表示（単体 or 一括入力）

POST /admin/events/{event}/slots/bulk
  → 複数の日付・時刻・定員を一括保存
  → redirect()->route('admin.events.show', $event)

GET  /admin/events/{event}
  → イベント詳細（枠一覧・各枠の申込数・残席・フォームURL表示）
  → フォームURLをコピーするボタンを設置

PATCH /admin/events/{event}
  → status を draft / open / closed に変更（受付開始・終了操作）
```

---

## 管理画面 機能要件

### ダッシュボード（admin.dashboard）
- 全イベントの一覧と受付状況サマリー（受付中・下書き・終了）
- 直近のイベントの総申込数・残席数
- ログイン中の管理者名・最終ログイン日時

### イベント一覧（admin.events.index）
- イベント名・開催日・ステータス・総申込数・残席数を一覧表示
- ステータスでフィルタリング（全件 / 受付中 / 下書き / 終了）
- 各イベントへの操作リンク（詳細・編集・削除）

### イベント作成・編集（admin.events.create / edit）

作成・編集できる項目：

| 項目 | 内容 |
|------|------|
| タイトル | イベント名（フォーム上部に表示） |
| スラッグ | フォームURL用（例: `summer-game-2025`） |
| 開催開始日 / 終了日 | |
| 説明文 | フォーム上部に表示する概要・参加資格など |
| 注意事項 | フォーム下部に表示 |
| 問合せ先メール | |
| ステータス | draft（下書き）/ open（受付中）/ closed（終了） |

### イベント詳細（admin.events.show）
- フォームURL表示とコピーボタン
- 試合枠一覧（日付・時刻・定員・申込数・残席・公開状態）
- 枠ごとの編集・削除・公開切り替えボタン
- 枠追加ボタン（単体 / 一括）

### 試合枠の一括追加（admin.slots.bulk）
- 複数行入力フォーム（日付・試合番号・開始時刻・終了時刻・定員）
- JavaScriptで行の追加・削除が可能
- 「今回のイベントと同じ枠構成をコピー」ボタン（別イベント作成時の効率化）

### 申込一覧（admin.entries.index）
- 受付番号・代表者氏名・メール・参加枠・申込日時・ステータスを一覧表示
- 日付・試合枠・ステータスでフィルタリング
- 申込詳細（メンバー全員の氏名・学年）を別画面で確認
- ステータスの手動変更（confirmed ↔ cancelled）

### CSV出力（admin.export）
- 対象：全件 or 日付指定 or 試合枠指定
- 出力項目：受付番号・参加日・試合枠・代表者情報・メンバー5名情報・申込日時・ステータス
- ファイル名：`{slug}_entries_YYYYMMDD.csv`
- 文字コード：**UTF-8 BOM付き**（Excelで文字化けしないよう）

---

## セキュリティ要件

### 利用者フォーム側
- **CSRF保護**: `@csrf` ディレクティブで自動保護
- **バリデーション**: `EntryRequest` による入力検証
- **XSS対策**: Blade の `{{ }}` による自動エスケープ
- **SQLインジェクション対策**: Eloquent ORM 使用（生SQLは禁止）
- **存在しないslug・非公開イベント**: 404または「受付終了」画面を表示

### 管理者認証側
- **パスワードハッシュ**: `Hash::make()` (bcrypt) で保存
- **ログイン検証**: `Auth::guard('admin')->attempt()`
- **セッション固定化対策**: `session()->regenerate()`
- **認証ガード分離**: `admin` ガードで利用者セッションと完全に独立
- **ログイン失敗対策**: `throttle:5,1` ミドルウェア（5回失敗で1分ロック）
- **セッションタイムアウト**: `.env` の `SESSION_LIFETIME=60`（分）

---

## 競合・定員超過防止

```php
// EntryController::submit()
DB::transaction(function () use ($validated, $event) {
    $slot = Slot::where('id', $validated['slot_id'])
                ->where('event_id', $event->id)
                ->lockForUpdate()
                ->firstOrFail();

    $count = Entry::where('slot_id', $slot->id)
                  ->where('status', 'confirmed')
                  ->count();

    if ($count >= $slot->capacity) {
        throw new \Exception('この時間枠は満員です。');
    }

    $entry = Entry::create([
        ...$validated,
        'event_id' => $event->id,
        'entry_no' => $this->generateEntryNo($event),
    ]);
});
```

---

## バリデーション仕様

### EntryRequest（申込フォーム）

```php
public function rules(): array
{
    return [
        'slot_id'            => ['required', 'exists:slots,id'],
        'rep_name'           => ['required', 'string', 'max:100'],
        'rep_age'            => ['required', 'integer', 'min:1', 'max:99'],
        'email'              => ['required', 'email', 'max:255', 'confirmed'],
        'email_confirmation' => ['required'],
        'member*.name'       => ['required', 'string', 'max:100'],
        'member*.grade'      => ['required', 'integer', 'min:1', 'max:6'],
    ];
}
```

### EventRequest（イベント作成・編集）

```php
public function rules(): array
{
    return [
        'title'           => ['required', 'string', 'max:200'],
        'slug'            => ['required', 'string', 'max:100', 'alpha_dash',
                              Rule::unique('events')->ignore($this->event)],
        'start_date'      => ['required', 'date'],
        'end_date'        => ['required', 'date', 'gte:start_date'],
        'description'     => ['nullable', 'string'],
        'notes'           => ['nullable', 'string'],
        'contact_email'   => ['nullable', 'email', 'max:255'],
        'status'          => ['required', Rule::in(['draft', 'open', 'closed'])],
    ];
}
```

---

## メール仕様

### 申込者への確認メール（EntryConfirmation）

```php
class EntryConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Entry $entry) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '【受付完了】' . $this->entry->event->title .
                     ' 受付番号：' . $this->entry->entry_no
        );
    }
}
```

**メール本文に含める内容：**
- 受付番号
- イベント名
- 代表者氏名
- 参加日・試合番号・時間帯
- メンバー一覧（氏名・学年）
- 問合せ先メール（`event->contact_email`）

---

## 認証設計（Guardの分離）

### config/auth.php

```php
'guards' => [
    'web' => [
        'driver'   => 'session',
        'provider' => 'users',
    ],
    'admin' => [
        'driver'   => 'session',
        'provider' => 'admin_users',
    ],
],
'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model'  => App\Models\User::class,
    ],
    'admin_users' => [
        'driver' => 'eloquent',
        'model'  => App\Models\AdminUser::class,
    ],
],
```

---

## さくらのレンタルサーバー デプロイ手順

### ディレクトリ構成

```
~/
├── laravel/                    ← Laravelプロジェクト本体（非公開）
└── www/                        ← ドキュメントルート（public_html相当）
    ├── index.php               ← パスを修正したエントリーポイント
    └── .htaccess
```

### www/index.php の修正

```php
require __DIR__ . '/../laravel/vendor/autoload.php';
$app = require_once __DIR__ . '/../laravel/bootstrap/app.php';
```

### .env 設定

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=mysqlXXX.db.sakura.ne.jp
DB_PORT=3306
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=info@yourdomain.com
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=info@yourdomain.com
MAIL_FROM_NAME="イベント申込システム"

SESSION_LIFETIME=60
APP_TIMEZONE=Asia/Tokyo

ADMIN_INITIAL_PASSWORD=changeme   # AdminUserSeederで使用・デプロイ後に変更必須
```

### デプロイコマンド（SSH接続後）

```bash
cd ~/laravel
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 実装上の注意事項

1. **受付番号の生成**: `{slug上3文字大文字}-{Ymd}-{ゼロ埋め6桁ID}` 形式（例: `SUM-20250808-000001`）。`Entry::created` イベントで自動付与
2. **タイムゾーン**: `.env` の `APP_TIMEZONE=Asia/Tokyo` と `config/app.php` で統一
3. **二重送信防止**: PRGパターン（POST後にリダイレクト）。完了後にセッションをクリア
4. **モバイル対応**: デジタル庁デザインシステムのレイアウト仕様に従いレスポンシブ対応。スマートフォン申込を最優先で考慮
5. **初期管理者**: `AdminUserSeeder` で1件作成。パスワードは `.env` の `ADMIN_INITIAL_PASSWORD` から読み込む
6. **slugのユニーク制約**: 編集時は `Rule::unique('events')->ignore($this->event)` を使用
7. **イベント削除**: `status = closed` への変更のみ許可し、物理削除は申込データがある場合に禁止
8. **枠の一括登録**: `SlotController::bulk()` で `upsert()` を使い冪等に投入可能にする
9. **admin.phpの読み込み**: `bootstrap/app.php` の `withRouting()` の `then:` コールバックで `require base_path('routes/admin.php')` を追記する
10. **フォームURL共有**: `route('entry.index', $event->slug)` で生成し、管理画面のイベント詳細ページにコピーボタンを設置する

---

## デザインシステム準拠方針

本システムのフロントエンドは**デジタル庁デザインシステム β版（v2.14.0）**に準拠して実装する。
公式ドキュメント: https://design.digital.go.jp/dads/

---

### 基本方針

- **Tailwind CSS v4** の `@theme` でデザインシステムのトークン（色・スペーシング・角丸・影）を定義し、Tailwind ユーティリティと CSS 変数を同時に生成する
- コンポーネント（ボタン・フォーム・ステッパー等）は `@layer components` に定義し、Blade テンプレートからクラス名で呼び出す
- デジタル庁デザインシステムが定義するスタイルを最優先で採用し、独自スタイルは最小限にとどめる
- WCAG 2.2 / JIS X 8341-3:2016 のアクセシビリティ基準を満たすこと
- フォントは **Noto Sans JP**（Google Fonts）をWebフォントとして読み込む
- すべての画面でレスポンシブ対応必須（スマートフォンファースト）

---

### フォント（タイポグラフィ）

デジタル庁デザインシステムでは、可読性・視認性が高いサンセリフとして **Noto Sans JP**（本文・見出し）と **Noto Sans Mono**（コード）を採用している。
CSS定義は [resources/css/app.css](resources/css/app.css) 参照。

#### 使用するテキストスタイル（主要なもの）

フォントサイズは16 CSS px以上を基本とし、14 CSS px未満の使用は原則として許容されない。

| 用途 | スタイル名 | サイズ | 太さ | 行高 |
|------|-----------|--------|------|------|
| ページタイトル | `Std-28B-150` | 28px | Bold | 150% |
| セクション見出し | `Std-22B-150` | 22px | Bold | 150% |
| 小見出し・ラベル | `Std-18B-160` | 18px | Bold | 160% |
| 本文・入力値 | `Std-16N-170` | 16px | Normal | 170% |
| 補助テキスト | `Std-16N-175` | 16px | Normal | 175% |
| 管理画面テーブル | `Dns-16N-130` | 16px | Normal | 130% |
| ボタンラベル | `Oln-16B-100` | 16px | Bold | 100% |

---

### カラー

プライマリーカラーは背景色とのコントラスト比が4.5:1以上を維持できるカラーを選択する必要がある。テキストと背景色とのコントラスト比は常に4.5:1以上、非テキスト要素は3:1以上を保つ。

本システムでは**ニュートラル（Neutral）キーカラー**を採用する。
CSS変数定義は [resources/css/app.css](resources/css/app.css) 参照。

---

### スペーシング・レイアウト

レイアウトは、ページの要素を画面内にどのように配置するかという設計であり、明確な情報伝達を実現するものである。
スペーシングスケール（4px基準）・コンテンツ幅・角丸・エレベーションのCSS変数は [resources/css/app.css](resources/css/app.css) 参照。

---

### コンポーネント実装仕様

#### ボタン

```html
<!-- プライマリーボタン（確定・送信） -->
<button type="submit" class="btn btn-primary">申し込む</button>

<!-- セカンダリーボタン（戻る・キャンセル） -->
<button type="button" class="btn btn-secondary">戻る</button>
```

CSS定義（`.btn`, `.btn-primary`, `.btn-secondary`）は [resources/css/app.css](resources/css/app.css) 参照。

---

#### テキスト入力・セレクト

```html
<!-- 入力フィールド（必須） -->
<div class="form-group">
  <label class="form-label" for="rep_name">
    代表者氏名
    <span class="badge-required" aria-label="必須">必須</span>
  </label>
  <input
    type="text"
    id="rep_name"
    name="rep_name"
    class="form-input"
    autocomplete="name"
    aria-required="true"
    aria-describedby="rep_name-error"
  >
  @error('rep_name')
    <p id="rep_name-error" class="form-error" role="alert">{{ $message }}</p>
  @enderror
</div>
```

CSS定義（`.form-group`, `.form-label`, `.badge-required`, `.form-input`, `.form-error`）は [resources/css/app.css](resources/css/app.css) 参照。

---

#### ラジオボタン（試合枠選択）

```html
<fieldset class="slot-group">
  <legend class="form-label">
    希望試合枠
    <span class="badge-required" aria-label="必須">必須</span>
  </legend>

  @foreach ($slots as $slot)
    @php $remaining = $slot->capacity - $slot->entry_count; @endphp
    <label class="radio-card {{ $remaining <= 0 ? 'is-disabled' : '' }}">
      <input
        type="radio"
        name="slot_id"
        value="{{ $slot->id }}"
        {{ $remaining <= 0 ? 'disabled' : '' }}
        aria-label="{{ $slot->game_date }} 第{{ $slot->game_no }}試合 {{ $slot->start_time }}〜{{ $slot->end_time }} 残り{{ $remaining }}名"
      >
      <span class="radio-card__label">
        <span class="radio-card__time">{{ $slot->start_time }}〜{{ $slot->end_time }}</span>
        <span class="radio-card__game">第{{ $slot->game_no }}試合</span>
        @if ($remaining <= 0)
          <span class="badge-full">満員</span>
        @else
          <span class="badge-remain">残り{{ $remaining }}名</span>
        @endif
      </span>
    </label>
  @endforeach
</fieldset>
```

---

#### エラーサマリー

バリデーションエラーが複数ある場合、フォーム上部にまとめて表示する。

```html
@if ($errors->any())
  <div class="error-summary" role="alert" aria-labelledby="error-summary-title" tabindex="-1">
    <h2 id="error-summary-title" class="error-summary__title">
      入力内容にエラーがあります
    </h2>
    <ul class="error-summary__list">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif
```

---

#### ステッパー（申込フロー進行表示）

```html
<nav class="stepper" aria-label="申込の手順">
  <ol class="stepper__list">
    <li class="stepper__item {{ $step === 1 ? 'is-current' : ($step > 1 ? 'is-done' : '') }}"
        aria-current="{{ $step === 1 ? 'step' : 'false' }}">
      <span class="stepper__num">1</span>
      <span class="stepper__label">入力</span>
    </li>
    <li class="stepper__item {{ $step === 2 ? 'is-current' : ($step > 2 ? 'is-done' : '') }}"
        aria-current="{{ $step === 2 ? 'step' : 'false' }}">
      <span class="stepper__num">2</span>
      <span class="stepper__label">確認</span>
    </li>
    <li class="stepper__item {{ $step === 3 ? 'is-current' : '' }}"
        aria-current="{{ $step === 3 ? 'step' : 'false' }}">
      <span class="stepper__num">3</span>
      <span class="stepper__label">完了</span>
    </li>
  </ol>
</nav>
```

---

### アクセシビリティ要件

テキストと背景色とのコントラスト比は常に4.5:1以上、UIの枠線など非テキスト要素は隣接する背景色とのコントラスト比3:1以上を保つこと。

実装時に守るべき事項：

| 項目 | 要件 |
|------|------|
| コントラスト比（テキスト） | 4.5:1 以上（WCAG AA） |
| コントラスト比（非テキスト） | 3:1 以上 |
| フォーカスリング | `focus-visible` でアウトラインを必ず表示 |
| フォーム必須項目 | `aria-required="true"` を付与 |
| エラーメッセージ | `role="alert"` + `aria-describedby` で入力欄と紐づける |
| エラーサマリー | 表示後に `tabindex="-1"` の要素に `focus()` を当てる |
| ラジオ・チェックボックス | `<fieldset>` と `<legend>` でグループ化 |
| 画像 | 情報を持つ画像には `alt` 属性を必ず付与 |
| スキップリンク | ページ先頭に「本文へスキップ」リンクを設置 |
| ページタイトル | `<title>` に「ページ名 | サービス名」形式で記述 |
| 言語指定 | `<html lang="ja">` を必ず指定 |

---

### ページ別レイアウト方針

#### 利用者向けフォーム（entry/）

```
[スキップリンク]
[ヘッダー: サービス名]
[ステッパー: 入力 → 確認 → 完了]
[メインコンテンツ]
  ├─ イベント概要（event->description）
  ├─ 参加資格・注意事項（event->notes）
  ├─ エラーサマリー（あれば）
  └─ フォーム
[フッター: 問合せ先]
```

- 最大幅: `var(--layout-content-width)` = 760px（フォームはこれに収める）
- 余白: 上下 `var(--space-8)` / 左右 `var(--space-5)`（SP: `var(--space-4)`）

#### 管理画面（admin/）

```
[スキップリンク]
[ヘッダー: システム名 | ログイン中: 管理者名 | ログアウト]
[サイドナビ: イベント一覧 / ダッシュボード]
[メインコンテンツ]
[フッター]
```

- 最大幅: `var(--layout-max-width)` = 1080px
- データテーブルには `Dns`（Dense）テキストスタイルを使用して情報密度を確保

---

### Bladeレイアウトファイル構成

```
resources/views/layouts/
├── entry.blade.php        ← 利用者向け（Noto Sans JP・スペーシング）
└── admin.blade.php        ← 管理者向け（サイドナビ付き・Dense対応）
```

両レイアウトともに `<html lang="ja">` / スキップリンク / フォーカスリング / Noto Sans JPの読み込みを共通で含める。

---

## ローカル開発環境（Laravel Sail）

ローカル開発には **Laravel Sail**（Docker）を使用する。
すべての開発コマンドは `./vendor/bin/sail` 経由で実行する。
ホスト環境に PHP / MySQL を直接インストールする必要はない。

### エイリアス設定（推奨）

毎回 `./vendor/bin/sail` と打つのを省略するため、シェルに以下のエイリアスを設定する。

```bash
# ~/.bashrc または ~/.zshrc に追記
alias sail='[ -f sail ] && sh sail || ./vendor/bin/sail'
```

---

### 主要コマンド一覧

#### コンテナの起動・停止

```bash
# バックグラウンドで起動
./vendor/bin/sail up -d

# 停止
./vendor/bin/sail down

# 停止（ボリュームも削除してDBをリセットしたいとき）
./vendor/bin/sail down -v
```

#### PHP / Artisan

```bash
# artisan コマンド全般
./vendor/bin/sail artisan <command>

# マイグレーション
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan migrate:fresh --seed   # DBをリセットしてシーダー実行

# シーダーのみ実行
./vendor/bin/sail artisan db:seed
./vendor/bin/sail artisan db:seed --class=SlotSeeder
./vendor/bin/sail artisan db:seed --class=AdminUserSeeder

# ルーティング確認
./vendor/bin/sail artisan route:list

# キャッシュクリア（ルート・設定・ビュー）
./vendor/bin/sail artisan optimize:clear

# モデル・コントローラーなどの生成
./vendor/bin/sail artisan make:model Event -m
./vendor/bin/sail artisan make:controller Admin/EventController --resource
./vendor/bin/sail artisan make:request Admin/EventRequest
./vendor/bin/sail artisan make:mail EntryConfirmation --markdown=emails.entry_confirmation
```

#### Composer

```bash
./vendor/bin/sail composer install
./vendor/bin/sail composer require <package>
./vendor/bin/sail composer update
```

#### Node.js / npm（CSS・JS ビルド）

```bash
# 依存パッケージのインストール
./vendor/bin/sail npm install

# 開発用ウォッチ（Viteを使う場合）
./vendor/bin/sail npm run dev

# 本番用ビルド
./vendor/bin/sail npm run build
```

#### テスト

```bash
# 全テスト実行
./vendor/bin/sail artisan test

# 特定クラスのみ
./vendor/bin/sail artisan test --filter=EntryControllerTest

# 並列実行
./vendor/bin/sail artisan test --parallel
```

#### データベース操作

```bash
# MySQLに直接接続
./vendor/bin/sail mysql

# tinker でモデル操作
./vendor/bin/sail artisan tinker
```

#### その他

```bash
# コンテナ内でbashシェルを起動
./vendor/bin/sail bash

# ログの確認
./vendor/bin/sail logs
./vendor/bin/sail logs laravel.test   # アプリコンテナのみ

# コンテナのステータス確認
./vendor/bin/sail ps
```

---

### 初回セットアップ手順

```bash
# 1. リポジトリのクローン後、依存パッケージをインストール
#    （Sailがまだないのでホストのcomposerを使う）
composer install

# 2. .env ファイルを作成
cp .env.example .env

# 3. Sail でコンテナを起動
./vendor/bin/sail up -d

# 4. アプリケーションキーを生成
./vendor/bin/sail artisan key:generate

# 5. マイグレーション＋シーダーを実行
./vendor/bin/sail artisan migrate --seed

# 6. ブラウザで確認
# http://localhost
# 管理画面: http://localhost/admin/login
```

---

### docker-compose.yml の補足

Sailのデフォルト構成に加え、メール確認用に **Mailpit** を含む。

```yaml
# .env に設定するSail関連の環境変数
APP_PORT=80              # http://localhost でアクセス
FORWARD_DB_PORT=3306     # ホストからDBに直接接続する場合のポート
MAIL_PORT=1025           # Mailpit SMTPポート
```

開発中のメール送信は Mailpit（`http://localhost:8025`）でキャプチャして確認する。
本番（さくらサーバー）では `.env` の `MAIL_MAILER=smtp` に切り替える。

---

## 未ログインユーザーの識別と権限管理

ログイン不要のまま「申込・編集・イベント作成」ができる仕組みを、
**UUIDトークン**をキーとした識別で実現する。
パスワード管理が不要で、メールアドレスさえあれば利用できる。

---

### 識別の仕組み（トークンベース）

```
初回アクセス時
  → UUIDトークンを生成
  → DBの guest_users テーブルに保存
  → ブラウザの Cookie にセット（30日間）

以降のアクセス
  → Cookie のトークンで guest_users を照合
  → 本人確認済みの操作（編集・削除）が可能になる
```

メールアドレスを持つ操作（申込・フォーム作成）では、
**メール認証リンク**を送ることでなりすましを防ぐ。

---

### 権限レベルの整理

| 権限 | 識別方法 | できること |
|------|---------|-----------|
| **未識別ゲスト** | なし | 申込フォームの閲覧のみ |
| **識別済みゲスト** | Cookieトークン | 自分の申込の閲覧・編集・キャンセル |
| **メール認証済みゲスト** | トークン＋メール認証 | イベントフォームの作成・管理 |
| **管理者** | ログイン（adminガード） | すべての操作 |

---

### 追加するDBテーブル

#### guest_users（未ログインユーザー識別テーブル）

```php
Schema::create('guest_users', function (Blueprint $table) {
    $table->id();
    $table->uuid('token')->unique();             // Cookie に保存するトークン
    $table->string('email')->nullable();          // メール認証後に保存
    $table->boolean('email_verified')->default(false);
    $table->timestamp('email_verified_at')->nullable();
    $table->timestamp('last_seen_at')->nullable();
    $table->timestamps();
});
```

#### guest_event_owners（ゲストが作成したイベントの所有権）

```php
Schema::create('guest_event_owners', function (Blueprint $table) {
    $table->id();
    $table->foreignId('guest_user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('event_id')->constrained()->cascadeOnDelete();
    $table->timestamps();
    $table->unique(['guest_user_id', 'event_id']);
});
```

#### guest_email_verifications（メール認証トークン）

```php
Schema::create('guest_email_verifications', function (Blueprint $table) {
    $table->id();
    $table->foreignId('guest_user_id')->constrained()->cascadeOnDelete();
    $table->string('email');
    $table->string('verify_token', 64)->unique(); // メールに送る認証トークン
    $table->timestamp('expires_at');              // 有効期限（15分）
    $table->timestamps();
});
```

---

### ① 申込の編集（識別済みゲスト）

申込完了時にエントリーとゲストトークンを紐づけ、
Cookie があれば本人として編集URLにアクセスできる。

#### 申込完了時の処理

```php
// EntryController::submit()
$guestUser = $this->resolveGuestUser($request);  // Cookie からゲストを取得 or 作成

$entry = Entry::create([
    ...$validated,
    'guest_user_id' => $guestUser->id,
    'edit_token'    => Str::random(64),           // 編集URL用トークン
]);

// 確認メールに編集URLを含める
// https://example.com/entry/{slug}/edit/{edit_token}
```

#### 編集URL（メール記載）

```
https://yourdomain.com/entry/summer-game-2025/edit/a1b2c3d4e5f6...
```

- トークンが一致すれば本人として編集・キャンセルが可能
- Cookie がなくてもメール内のURLから直接編集できる
- 編集可能期間はイベントの `status = open` の間のみ

#### ルーティング

```php
// routes/web.php に追加
Route::get( '/entry/{slug}/edit/{token}',   [EntryController::class, 'edit'])  ->name('entry.edit');
Route::post('/entry/{slug}/edit/{token}',   [EntryController::class, 'update'])->name('entry.update');
Route::post('/entry/{slug}/cancel/{token}', [EntryController::class, 'cancel'])->name('entry.cancel');
```

---

### ② イベントフォームの作成（メール認証済みゲスト）

未ログインユーザーが自分のイベントフォームを作成・管理できる機能。
メールアドレスを認証することでなりすましを防ぐ。

#### フロー

```
フォーム作成画面にアクセス
  → メールアドレスを入力
  → 認証メールが届く（有効期限15分）
  → メール内のリンクをクリック → メール認証完了
  → フォーム作成画面へリダイレクト
  → イベント情報・試合枠を入力して保存
  → 管理用URLをメールで受け取る
```

#### 管理用URLの構造

```
# イベント管理（枠の追加・申込一覧・CSV）
https://yourdomain.com/manage/{event_slug}?token={guest_event_token}

# 申込編集
https://yourdomain.com/entry/{slug}/edit/{edit_token}
```

#### ルーティング

```php
// routes/web.php に追加（ゲストによるイベント管理）
Route::get( '/manage/create',              [GuestEventController::class, 'create'])      ->name('guest.event.create');
Route::post('/manage/verify-email',        [GuestEventController::class, 'verifyEmail']) ->name('guest.event.verifyEmail');
Route::get( '/manage/verify/{token}',      [GuestEventController::class, 'confirm'])     ->name('guest.event.confirm');
Route::post('/manage/store',               [GuestEventController::class, 'store'])       ->name('guest.event.store');
Route::get( '/manage/{slug}',              [GuestEventController::class, 'show'])        ->name('guest.event.show');
Route::get( '/manage/{slug}/edit',         [GuestEventController::class, 'edit'])        ->name('guest.event.edit');
Route::patch('/manage/{slug}',             [GuestEventController::class, 'update'])      ->name('guest.event.update');
Route::get( '/manage/{slug}/entries',      [GuestEventController::class, 'entries'])     ->name('guest.event.entries');
Route::get( '/manage/{slug}/export',       [GuestEventController::class, 'export'])      ->name('guest.event.export');

// メール認証（GuestEmailVerificationController）
Route::post('/guest/email/send',    [GuestEmailVerificationController::class, 'send'])   ->name('guest.email.send');
Route::get( '/guest/email/verify/{token}', [GuestEmailVerificationController::class, 'verify'])->name('guest.email.verify');
```

---

### ③ GuestUserミドルウェア

すべてのリクエストでCookieを確認し、ゲストユーザーを自動識別する。

```php
// app/Http/Middleware/IdentifyGuestUser.php
class IdentifyGuestUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->cookie('guest_token');

        if ($token) {
            $guest = GuestUser::where('token', $token)->first();
            if ($guest) {
                $guest->update(['last_seen_at' => now()]);
                $request->attributes->set('guest_user', $guest);
            }
        }

        $response = $next($request);

        // Cookieが未設定なら新規発行
        if (!$token) {
            $newToken = (string) Str::uuid();
            GuestUser::create(['token' => $newToken]);
            $response->withCookie(cookie('guest_token', $newToken, 60 * 24 * 30)); // 30日
        }

        return $response;
    }
}
```

#### リクエストからゲストを取得するヘルパー

```php
// app/Http/Controllers/EntryController.php など共通で使うメソッド
private function resolveGuestUser(Request $request): GuestUser
{
    return $request->attributes->get('guest_user')
        ?? GuestUser::create(['token' => (string) Str::uuid()]);
}
```

---

### セキュリティ上の注意事項

| リスク | 対策 |
|--------|------|
| Cookie の盗用・なりすまし | HTTPOnly / Secure / SameSite=Strict 属性を付与 |
| 編集トークンの推測 | `Str::random(64)` の十分な長さで総当たりを防ぐ |
| メール認証トークンの使い回し | 認証完了後に即座にDBから削除 |
| 有効期限切れ認証リンクのアクセス | `expires_at` チェックで弾く |
| ゲストによる他人のイベント操作 | `guest_event_owners` テーブルで所有権を必ず照合 |
| Cookie 削除後のアクセス不能 | 編集URLはメールにも必ず記載し、トークン単体でもアクセス可能にする |

---

### ディレクトリ追加（ゲスト機能）

```
app/Http/Controllers/
  ├── GuestEventController.php         ← ゲストによるイベント作成・管理
  └── GuestEmailVerificationController.php ← メール認証処理

app/Http/Middleware/
  └── IdentifyGuestUser.php            ← Cookie識別ミドルウェア

app/Models/
  ├── GuestUser.php
  └── GuestEventOwner.php

resources/views/guest/
  ├── create.blade.php                 ← イベント作成フォーム（メール入力）
  ├── verify_sent.blade.php            ← 認証メール送信完了
  ├── event_form.blade.php             ← イベント情報入力
  └── dashboard.blade.php             ← ゲスト管理画面（申込一覧・枠管理）
```
