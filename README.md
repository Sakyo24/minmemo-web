# Todoアプリ

## アプリ概要
Todアプリ<br>
ユーザー側：Flutter×Laravel　　管理者側：Vue.js×Laravel

### 仕様
- 認証機能
- Todo　一覧・新規登録・詳細・編集・削除

### 技術スタック
- Flutter2
- PHP8.1
- Laravel9
- JavaScript
- Vue.js3
- Vuex4
- Vue Router4
- MySQL5.7

## 環境構築

### プロジェクトのインストール

```
git clone https://github.com/Migisan/task-management-app.git
cd todo_app/larave_vue
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
COMPOSER_PROJECT_NAME=task-todo_app

PHP_VERSION=8.1

MYSQL_VERSION=latest
MYSQL_DATABASE=todo_db
MYSQL_USER=todo_user
MYSQL_PASSWORD=todo_pass
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
# npm run dev
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
DB_DATABASE=todo_db
DB_USERNAME=todo_user
DB_PASSWORD=todo_pass

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

# 作業コンテナにアクセス
make workspace
```

### ブラウザ確認

```
# プロジェクト
http://localhost

# phpMyAdmin
http://localhost:8081
サーバー：mysql
ユーザー名：todo_user
パスワード：todo_pass

# MailHog
http://localhost:8025
```

### Flutter環境構築
```
cd /todo_app/flutter_src
flutter pub get
```
flutter doctorにより、ローカル環境で実行可能か確認する。
エラーが出ていた場合、ログに従い、修正等を行う。

```
flutter pub get
```
上記コマンド実行後、エミュレーターを起動し、デバッグを開始する。

### Git運用ルール
- masterブランチから、ブランチを切る。
- ブランチ名の命名規則は、issue-1　※数字の部分は、各タスク毎に合わせる。
- タスク完了後は、masterブランチへプルリクを作成する。
- タスク作業者ではない者が、プルリクを確認する。

## アーキテクチャ

ドメイン駆動開発を取り入れてみよう。
理由としては、仕様変更に柔軟に対応できるシステムにしたいため。

- コントローラー
- アプリケーションサービス
- ドメインサービス
- リポジトリ
- エンティティ
- バリューオブジェクト

### コントローラー

ルーティングからパラメーターを受け取り、ユースケース層のアプリケーションサービスを呼んで、JSON レスポンスを返すだけの責務を担う。

### アプリケーションサービス

ドメインサービスやリポジトリ、エンティティ、バリューオブジェクトを駆使して、ユースケースを実装する。

また、ユースケースでトランザクションを張る。

### ドメインサービス

複数のドメインオブジェクトを使って、重要な処理や複雑な計算を行うためのサービス。
上記のような複雑なドメインルールを記述する。
必ず実装するサービスではない。

### リポジトリ

データリソースの I/O を担う。
DB やファイルなどの特定のデータリソースに依存しないために実装する。
よって、EloquentModel クラスを直接呼び出せるのは、リポジトリだけ。

### エンティティ

値が可変でライフサイクルを持つドメインクラス。
ドメインルールを記述する。
例：人間

### バリューオブジェクト

値が不変のドメインクラス。
ドメインルールを記述する。
例：人間が持つ属性である体重、身長など

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

### テストの実行(フロントエンド)

```
// 全テスト
$ npm run test
// 個別テスト
$ npm run test resources/js/tests/components/ExampleComponent.test.js
```

## 本番

### URL
