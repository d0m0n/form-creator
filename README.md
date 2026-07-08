# イベント申込フォーム管理システム

管理者がイベントと時間枠を作成・管理し、利用者がログイン不要で申込できるマルチイベント対応の申込フォームシステム。

- **フレームワーク**: Laravel 13 / PHP 8.3
- **DB**: MySQL 8.x
- **フロントエンド**: Blade / Tailwind CSS v4
- **ローカル開発**: Laravel Sail（Docker）

---

## ローカル開発環境のセットアップ

```bash
# 1. 依存パッケージのインストール
composer install

# 2. 環境ファイルを作成
cp .env.example .env

# 3. コンテナを起動
./vendor/bin/sail up -d

# 4. アプリケーションキーを生成
./vendor/bin/sail artisan key:generate

# 5. マイグレーション＋初期データ投入
./vendor/bin/sail artisan migrate --seed

# 6. ストレージのシンボリックリンク作成
./vendor/bin/sail artisan storage:link

# 7. フロントエンドのビルド（開発用ウォッチ）
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

ブラウザで `http://localhost` にアクセス。管理画面は `http://localhost/admin/login`。

初期管理者アカウントは `.env` の `ADMIN_INITIAL_EMAIL` / `ADMIN_INITIAL_PASSWORD` で設定（デフォルト: `admin@example.com` / `changeme`）。

---

## メール設定

### ローカル開発（Mailpit）

Sail のデフォルト設定では送信メールは **Mailpit** にキャプチャされます。実際には外部へ送信されません。

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

Mailpit の受信ボックスは `http://localhost:8025` で確認できます。

---

### 本番（さくらのレンタルサーバー）

さくらのコントロールパネル「メール」→「メールアカウント一覧」で SMTP 情報を確認し、`.env` を以下のように設定します。

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com        # 例: sv***.sakura.ne.jp
MAIL_PORT=587
MAIL_SCHEME=tls
MAIL_USERNAME=info@yourdomain.com    # 送信に使うメールアドレス
MAIL_PASSWORD=your_mail_password     # メールアカウントのパスワード
MAIL_FROM_ADDRESS=info@yourdomain.com
MAIL_FROM_NAME="イベント申込システム"
```

| 項目 | 説明 |
|------|------|
| `MAIL_HOST` | さくらの SMTP サーバー（コントロールパネルで確認） |
| `MAIL_PORT` | `587`（TLS）または `465`（SSL） |
| `MAIL_SCHEME` | `tls`（587番ポート）または `ssl`（465番ポート） |
| `MAIL_USERNAME` | 送信元メールアドレス（＝ SMTP 認証ユーザー名） |
| `MAIL_PASSWORD` | メールアカウントのパスワード |
| `MAIL_FROM_ADDRESS` | メールの From アドレス（`MAIL_USERNAME` と一致させる） |

設定変更後は必ずキャッシュをクリアしてください。

```bash
php artisan config:clear
```

---

### Gmail SMTP（テスト・開発用）

Gmail を一時的な送信元として使う場合は**アプリパスワード**が必要です。

**アプリパスワードの発行手順：**
1. Google アカウント →「セキュリティ」
2. 「2段階認証プロセス」を有効化
3. 「アプリパスワード」→ アプリ名を入力して生成（16桁）

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_SCHEME=tls
MAIL_USERNAME=your-address@gmail.com
MAIL_PASSWORD=xxxx-xxxx-xxxx-xxxx   # 発行したアプリパスワード（スペースなし）
MAIL_FROM_ADDRESS=your-address@gmail.com
MAIL_FROM_NAME="イベント申込システム"
```

> **注意**: Gmail SMTP は1日あたりの送信上限（500通）があります。本番運用にはさくらの SMTP を使用してください。

---

### 送信テスト

設定後、以下のコマンドで疎通確認ができます。

```bash
./vendor/bin/sail artisan tinker --execute="
Mail::raw('テスト送信', fn(\$m) => \$m->to('確認用メールアドレス')->subject('送信テスト'));
echo 'sent';
"
```

エラーが出ずに `sent` と表示されれば設定完了です。

---

## さくらのレンタルサーバーへのデプロイ

### ディレクトリ構成

```
~/
├── laravel/          ← Laravelプロジェクト本体（ドキュメントルート外）
└── www/              ← ドキュメントルート
    ├── index.php     ← パスを修正したエントリーポイント
    └── .htaccess
```

### www/index.php の修正

```php
require __DIR__ . '/../laravel/vendor/autoload.php';
$app = require_once __DIR__ . '/../laravel/bootstrap/app.php';
```

### デプロイコマンド（SSH接続後）

```bash
cd ~/laravel
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 本番 .env の追加設定

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
APP_TIMEZONE=Asia/Tokyo

SESSION_LIFETIME=60

ADMIN_INITIAL_EMAIL=admin@yourdomain.com
ADMIN_INITIAL_PASSWORD=＜初回ログイン後すぐ変更すること＞
```

---

## 主要コマンド

```bash
# コンテナ起動・停止
./vendor/bin/sail up -d
./vendor/bin/sail down

# マイグレーション
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan migrate:fresh --seed   # DBリセット

# キャッシュクリア
./vendor/bin/sail artisan optimize:clear

# ルート確認
./vendor/bin/sail artisan route:list
```
