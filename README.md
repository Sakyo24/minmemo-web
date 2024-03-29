# みんメモ

## アプリ概要

Web アプリ,API：Vue.js×Laravel<br>
モバイル：Flutter×Laravel

### 仕様

- 認証機能
- メモ 　一覧・新規登録・詳細・編集・削除
- グループ 　一覧・新規登録・詳細・編集・削除
- カレンダー
- マイページ 　詳細・編集

### 技術スタック

- Flutter3
- PHP8.1
- Laravel9
- JavaScript
- Vue.js3
- Vuex4
- Vue Router4
- MySQL5.7

## モバイル　リポジトリ

- [Flutter×Laravel の環境構築](https://github.com/Sakyo24/minmemo-mobile)

## Git 運用ルール

- master ブランチから、ブランチを切る。
- ブランチ名の命名規則は、issue-1 　※数字の部分は、各タスク毎に合わせる。
- 原則、１つのイシューに対して１つのプルリクとする。
- タスク完了後は、master ブランチへプルリクを作成する。
- タスク作業者ではない者が、プルリクを確認する。

## 本番

（未定）

### URL

https://localhost

## 環境構築

### プロジェクトのインストール

```
cd minmemo-web
git clone https://github.com/laradock/laradock.git
```

### Laradock 設定ファイルの編集

コマンド

```
cd laradock
cp .env.example .env
```

.env ファイル

```
APP_CODE_PATH_HOST=../src/
DATA_PATH_HOST=../.laradock/data
COMPOSER_PROJECT_NAME=minmemo

PHP_VERSION=8.1

MYSQL_VERSION=5.7
MYSQL_DATABASE=minmemo_db
MYSQL_USER=minmemo_user
MYSQL_PASSWORD=minmemo_pass
```

### コンテナ起動

```
docker-compose up -d nginx mysql phpmyadmin mailhog
docker ps --format "table {{.Names}}"
```

### インストール

```
docker-compose exec --user=laradock workspace bash
# composer install
# npm install
# exit
```

### Laravel 設定ファイルの編集

コマンド

```
cd ../src
cp .env.example .env
```

.env ファイル

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=minmemo_db
DB_USERNAME=minmemo_user
DB_PASSWORD=minmemo_pass

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=user
MAIL_PASSWORD=password
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=no-reply@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

### アプリキー生成/テーブル作成/初期データ投入

```
cd ../laradock
docker-compose exec --user=laradock workspace bash
# php artisan key:generate
# php artisan migrate
# php artisan db:seed
# exit
```

## 開発

### コンテナ操作

```
# コンテナ起動
make up

# コンテナ停止
make stop

# コンテナ削除
make down

# workspaceコンテナにアクセス
make workspace
```

※詳細は Makefile 参照

### フロントエンドのコンパイル

workspace コンテナにアクセスして下記コマンドを実行

```
npm run dev
```

### フロントエンドのビルド(本番環境)

下記コマンドを実行

```
npm run build
```

### ブラウザ確認

```
# プロジェクト
http://localhost

# phpMyAdmin
http://localhost:8081
サーバー：mysql
ユーザー名：minmemo_user
パスワード：minmemo_pass

# MailHog
http://localhost:8025
```

## アーキテクチャ

レイヤードアーキテクチャを採用。(クリーンアーキテクチャやドメイン駆動開発は現段階で採用しない。)
理由としては、ある程度品質を担保しつつも高速に開発を進めたいため。

- フォームリクエスト
- コントローラー
- アプリケーションサービス
- リポジトリ

### フォームリクエスト

入力値の単純なバリデーションを行う。

### コントローラー

ルーティングからパラメーターを受け取り、ユースケース層のアプリケーションサービスを呼んで、JSON レスポンスを返すだけの責務を担う。

### アプリケーションサービス

リポジトリなどを駆使して、ユースケースを実装する。

また、ユースケースでトランザクションを張る。

### リポジトリ

データリソースの I/O を担う。
DB やファイルなどの特定のデータリソースに依存しないために実装する。
よって、EloquentModel クラスを直接呼び出せるのは、リポジトリだけ。
戻り値は Eloquent や Eloquent の Collection。

## コード規約

### 命名規則

| 用途               | 命名          | 規則                           |
| ------------------ | ------------- | ------------------------------ |
| 変数名(PHP)        | $sample_value | スネークケース                 |
| 変数名(JavaScript) | sampleValue   | ローワーキャメルケース         |
| メソッド名         | getData       | ローワーキャメルケース         |
| クラス             | SampleClass   | アッパーキャメルケース         |
| モデル             | User          | アッパーキャメルケース(単数形) |
| テーブル           | users         | スネークケース(複数形)         |

## テスト

### テストデータ

ファクトリーの作成

```
$ php artisan make:factory SampleFactory
```

### テストの作成

テストクラスの作成

```
$ php artisan make:test SampleApiTest
```

### テストの実行(バックエンド)

```
// 全テスト
$ php artisan test
or
$ ./vendor/bin/phpunit --testdox

// 個別テスト
$ php artisan test tests/Feature/SampleTest.php
or
$ ./vendor/bin/phpunit tests/Feature/SampleTest.php
```
