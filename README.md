## アプリケーション名

coachtech 勤怠管理アプリ

## 環境構築

1⃣ Dockerビルド

⓵ アプリケーションを作成するために、開発環境を GitHub からクローンします。

```
コマンドライン上

git clone git@github.com:HIDE52/hide-kintaiapp.git
mv hide-fremaapp kintaiapp
```

⓶ 開発環境を構築します。

```
コマンドライン上

cd kintaiapp
docker-compose up -d --build
code .
```

※画像処理(GD)等の必要な環境が自動でインストールされます

⓷「Docker Desktop 」の確認を行い、「kintaiapp」コンテナが作成されているか確認を行います。

2⃣ Laravelの初期設定

⓵ Dockerコンテナ内に移動します。

```
コマンドライン上

docker-compose exec php bash
```

⓶ 必要なパッケージをインストールします。

```
PHPコンテナ上

composer install
```

⓷ 設定ファイル（.env）を作成し、データベースの接続先を書き換えます。

```
PHPコンテナ上

cp .env.example .env
```

⓸ .env ファイルを開き、以下の設定を確認・編集します。

（.env.example に最適化済みの設定が入っているため、基本的にはそのままで動作します）

1. メールサーバー（MailHog）との接続設定の編集を行います。

```
.envファイル

MAIL_FROM_ADDRESS=
※MAIL_FROM_ADDRESS=　の後に、ご自身のメールアドレスを設定して下さい。
```

⓹ セキュリティに必要な「鍵」を作ります。

```

PHPコンテナ上

php artisan key:generate

```

⓺ 画像を表示させるために実行します。

```

PHPコンテナ上

php artisan storage:link

```

⓻ 本アプリは商品画像の加工にGDライブラリを使用します。Dockerfileにて自動インストールする設定にしているため、手動でのインストール作業は不要です。

3⃣ データベースの構築

⓵ データベースにテーブルを作成します。

```

PHPコンテナ上

php artisan migrate

```

⓶ 初期データ（テストデータ）を登録します。

```

PHPコンテナ上

php artisan db:seed

```

初期アカウント情報
動作確認用に以下のテストユーザーが登録されます。

```

・メールアドレス: test@example.com
・パスワード: password

```

4⃣ テスト環境の構築

本アプリケーションでは PHPUnit を使用して自動テストを実施しています。

⓵ テスト用データベースの作成

1. MySQコンテナ内に移動します。

```

コマンドライン上

docker-compose exec mysql bash

```

2. rootユーザ（管理者）でログインします。

```

MySQL上コンテナ上します

mysql -u root -p
パスワードは'root'を入力します。

```

3. MySQLログイン後、demo_testの作成を行います。

```

MySQLコンテナ上

CREATE DATABASE demo_test;
SHOW DATABASES;
exit

```

⓶ テスト環境の設定

1. PHPコンテナへ移動します。

```
コマンドライン上

docker-compose exec php bash
```

2. テスト用の設定ファイル作成をします。

```

PHPコンテナ上

cp .env.testing.example .env.testing
```

3. テスト用のアプリケーションキーを作成し、キャッシュをクリアします。

```

PHPコンテナ上

php artisan key:generate --env=testing
php artisan config:clear
```

4. マイグレーションコマンドを実行して、テスト用のテーブルを作成します。

```

PHPコンテナ上

php artisan migrate --env=testing
```

⓷ テストの実施

1. テスト用のデータベースでテストのコマンドを実施します。

```
PHPコンテナ上

php artisan test tests
```


5⃣ メール認証機能の設定

本アプリケーションではセキュリティ向上のため、メール認証を導入しています。開発環境ではメールテスト用サーバー（MailHog）を使用して、実際に届くメールを確認できます。

⓵ MailHog 管理画面の確認

以下の URL にアクセスし、MailHog の受信トレイが表示されるか確認してください。<br/>URL：http://localhost:8025/

⓶ 動作確認の手順

1. アプリの新規登録画面（/register）でユーザー登録を行います。

2. 登録後、自動的に「メール認証誘導画面」が表示されます。

3. MailHog の画面に戻り、届いたメールを開きます。

4. メール内の 「Verify Email Address」 をクリックします。

5. プロフィール設定画面へ遷移すれば、すべての設定が正常です。

## 使用技術(実行環境)

- PHP 8.1.34
- Laravel 8.83.8
- MySQL 8.0.26
- Nginx 1.21.1

## URL

商品一覧トップ画面：http://localhost/<br/>
ユーザー登録：http://localhost/register<br/>
ログイン：http://localhost/login<br/>
phpMyAdmin (DB確認ツール)：http://localhost:8080/<br/>
メール確認 (Mailhog):http://localhost:8025/<br/>
MailHogの管理画面（受信トレイ）：http://localhost:8025/

## ER図

![ER図](ER.drawio.png)
