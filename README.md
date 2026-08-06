# contact-form

## 概要

旧教材のLaravel演習講座の『Todoアプリ(初級編)』を、新教材のLaravel Sailの環境で作成しました。

## 作成の流れ

### 1. 要件定義

＊基本情報

- アプリ名：Todoアプリ
- 利用者：個人ユーザー（ログイン機能追加）
- 目的：ユーザーがTodoリストを管理する(一覧表示（日付順）・作成・更新・削除)

＊入力内容

- content

### 2. 整理

＊CRUD

-

### 3. バリデーション・テスト要件書き出し

＊バリデーション

- content required,string,max:20

＊テスト要件

1.

### 4. 環境構築

#### 1. Laravelプロジェクトの作成

Dockerが起動していることを確認

```bash
# ホームディレクトリに移動
cd ~

# Laravelプロジェクトを作成
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    -e COMPOSER_CACHE_DIR=/tmp/composer_cache \
    laravelsail/php82-composer:latest \
    composer create-project laravel/laravel:^10.0 contact-form
```

#### 2. Laravel Sailのインストール

```bash
# プロジェクトディレクトリに移動
cd contact-form

# Laravel Sailをインストール
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    -e COMPOSER_CACHE_DIR=/tmp/composer_cache \
    laravelsail/php82-composer:latest \
    composer require laravel/sail --dev

# Sailの設定ファイルをパブリッシュ（MySQLを選択）
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    -e COMPOSER_CACHE_DIR=/tmp/composer_cache \
    laravelsail/php82-composer:latest \
    php artisan sail:install --with=mysql
```

#### 3. エイリアス設定

```bash
echo "alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'" >> ~/.bashrc
exec $SHELL
```

※↓このコードでもエイリアス設定できるが、 Laravelプロジェクトのルートディレクトリ（一番上の階層）にいる時しか動かないため注意が必要

```bash
alias sail="./vendor/bin/sail"
```

#### 4. フロントエンドのセットアップ

1. Sailの起動

    ```bash
    sail up -d
    ```

2. NPM依存パッケージのインストール

    ```bash
    sail npm install
    ```

3. Tailwind CSSのインストール

    ```bash
    sail npm install -D tailwindcss@^3.4.0 postcss autoprefixer
    ```

4. 設定ファイルの生成

    ```bash
    sail npx tailwindcss init -p
    ```

5. Tailwind CSSのテンプレートパス設定
   `tailwind.config.js`を開き、`content`プロパティを以下のように設定

    ```php
    /** @type {import('tailwindcss').Config} */
    export default {
    content: [
     "./resources/**/*.blade.php",
     "./resources/**/*.js",
     "./resources/**/*.vue",
    ],
    theme: {
     extend: {},
    },
    plugins: [],
    }
    ```

6. CSSファイルにTailwindディレクティブを追加
   `resources/css/app.css`の中身を以下の3行に置き換える

    ```php
    @tailwind base;
    @tailwind components;
    @tailwind utilities;
    ```

7. Vite開発サーバーの起動
   新しいターミナルを開いて実行する。
   ※このコマンドは開発中、常に実行したままにする。
    ```bash
    sail npm run dev
    ```

#### 5. envファイルとphpMyAdminの設定

1.  .envファイルの確認
    `.env`ファイルを開き、データベース接続情報が以下と一致していることを確認する

```php
 DB_CONNECTION=mysql
 DB_HOST=mysql
 DB_PORT=3306
 DB_DATABASE=laravel
 DB_USERNAME=sail
 DB_PASSWORD=password
```

2.  `compose.yaml`を開き、`mysql`サービスの後に以下の設定を追加して保存する

    ```php
     phpmyadmin:
         image: 'phpmyadmin:latest'
         ports:
             - '${FORWARD_PHPMYADMIN_PORT:-8080}:80'
         environment:
             PMA_HOST: mysql
             PMA_USER: '${DB_USERNAME}'
             PMA_PASSWORD: '${DB_PASSWORD}'
         networks:
             - sail
         depends_on:
             - mysql
    ```

#### 5. Sailの起動

1. Sailの再起動

    ```bash
    sail down
    sail up -d
    ```

2. アプリケーションキーの生成
    ```bash
    sail artisan key:generate
    ```

#### 6. 動作確認

1. Laravelの動作確認
   ブラウザで`http://localhost`にアクセスする。
   Laravelのウェルカムページが表示されることを確認

2. phpMyAdminの動作確認
   ブラウザで`http://localhost:8080`にアクセスする。
   phpMyAdminが表示されることを確認

3. マイグレーションの実行
    ```bash
    sail artisan migrate
    ```
    phpMyAdminで`users`テーブルが作られていることを確認する

### 5. 提供アセットの配置

旧教材の提供ファイルを新教材環境で使用するため、以下の手順で修正した。

1.  Bladeファイルを`@vite`ディレクティブ使用のコードに書き換え
    `app.blade.php`

        ```html
        <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
        <link rel="stylesheet" href="{{ asset('css/common.css') }}" />
        ```

        上記の二行を以下のように修正

        ```html
        @vite(['css/sanitize.css', 'css/common.css'])
        ```

2.  `resources/css`の中に提供のCSSファイルを設置
3.  `vite.config.js`にCSSファイルを登録する

```php
plugins: [
laravel({
   input: [
       'resources/css/app.css',
       'resources/css/common.css', //←追加
       'resources/css/index.css', //←追加
       'resources/css/sanitize.css', //←追加
       'resources/js/app.js'],
```

### 6. Git/GitHub準備とIssue登録

### 7. マイグレーションの作成

### 8. モデル作成

### 9. CRUD機能の実装

### 10. テスト
