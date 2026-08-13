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

- Todoの表示 TodoController@index
- Todoの追加 TodoController@store
- Todoの更新 TodoController@update
- Todoの削除 TodoController@destroy

### 3. バリデーション・テスト要件書き出し

＊バリデーション

- content required,string,max:20

＊テスト要件<br>
【タスクCRUD機能のテスト】<br>

1. ユーザーはタスク一覧を取得できる
2. ユーザーはタスクを作成できる
3. ユーザーはタスクを更新できる
4. ユーザーはタスクを削除できる
5. タスク内容が空だとバリデーションエラーになる
6. タスク内容は25文字まで入力できる
7. タスク内容が26文字以上だとバリデーションエラーになる

【ログイン機能のテスト】<br>

1. ログイン画面を表示できる
2. 正しい認証情報でログインできる
3. 間違ったパスワードではログインできない
4. 存在しないメールアドレスではログインできない
5. メールアドレスが空だとバリデーションエラーになる
6. パスワードが空だとバリデーションエラーになる
7. ログアウトできる
8. 認証済ユーザーはログインページにアクセスするとリダイレクトされる

【ユーザー登録機能のテスト】<br>

1. 登録画面を表示できる
2. 新規ユーザーを登録できる
3. 名前が空だとバリデーションエラーになる
4. メールアドレスが空だとバリデーションエラーになる
5. 無効なメールアドレスの形式だとバリデーションエラーになる
6. 既に登録済のメールアドレスだとバリデーションエラーになる
7. パスワードが8文字未満だとバリデーションエラーになる
8. パスワード確認が一致しないとバリデーションエラーになる

【未認証リダイレクトテスト】<br>

1. 未認証ユーザーはタスク一覧にアクセスするとログインページにリダイレクトされる

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
cd Todo

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

#### 6. Sailの起動

1. Sailの再起動

    ```bash
    sail down
    sail up -d
    ```

2. アプリケーションキーの生成
    ```bash
    sail artisan key:generate
    ```

#### 7. 動作確認

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

---

### 4. 環境構築 (`git clone`から環境構築する場合)

#### 1. git cloneを実行

Dockerが起動していることを確認<br>

```bash
# ホームディレクトリに移動
cd ~

# git cloneを実行
git clone https://github.com/yuna-genma/Todo.git
```

#### 2. セットアップ

```bash
# プロジェクトディレクトリに移動
cd Todo

# Composerパッケージをインストール
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    -e COMPOSER_CACHE_DIR=/tmp/composer_cache \
    laravelsail/php82-composer:latest \
    composer install

# 環境設定ファイルをコピー
cp .env.example .env

# Sailを起動
./vendor/bin/sail up -d
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
    `.env`ファイルを開き、データベース接続情報が以下と一致していることを確認する<br>
    一致しない場合は以下の情報に書き換える

```php
 DB_CONNECTION=mysql
 DB_HOST=mysql
 DB_PORT=3306
 DB_DATABASE=laravel
 DB_USERNAME=sail
 DB_PASSWORD=password
```

2.  `compose.yaml`を開き、`mysql`サービスの後に以下の設定と一致するか確認する。<br>
    一致しない場合は以下の情報を追加して保存する。

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

1.  Bladeファイルを`@vite`ディレクティブ使用のコードに書き換え<br>
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
export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/common.css',
                'resources/css/index.css',
                'resources/css/sanitize.css',
                'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
```

### 6. Git/GitHub準備とIssue登録

### 7. マイグレーションの作成

### 8. モデル作成

### 9. CRUD機能の実装

### 10. テスト

#### テスト環境のセットアップ

1. phpunit.xml の設定
   ファイル：`phpunit.xml`
   修正箇所<br>
   `<php>`セクション内で以下の2点を修正する。
    1. `DB_CONNECTION`行を追加
    2. `DB_DATABASE`の値を変更
       修正後の`<php>`セクション全体
    ```php
    <php>
    <env name="APP_ENV" value="testing"/>
    <env name="BCRYPT_ROUNDS" value="4"/>
    <env name="CACHE_DRIVER" value="array"/>
    <env name="DB_CONNECTION" value="sqlite"/> <!-- ← 追加 -->
    <env name="DB_DATABASE" value=":memory:"/> <!-- ← 値を変更 -->
    <env name="MAIL_MAILER" value="array"/>
    <env name="PULSE_ENABLED" value="false"/>
    <env name="QUEUE_CONNECTION" value="sync"/>
    <env name="SESSION_DRIVER" value="array"/>
    <env name="TELESCOPE_ENABLED" value="false"/>
    </php>
    ```
2. ファクトリの作成
3. デフォルトのテストファイルを削除
    ```bash
    # デフォルトのExampleTestを削除
    rm tests/Feature/ExampleTest.php
    rm tests/Unit/ExampleTest.php
    # tests/Unitディレクトリを維持するため、.gitkeepを作成
    touch tests/Unit/.gitkeep
    ```
