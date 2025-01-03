ALTER DATABASE mydb SET timezone TO 'Asia/Tokyo';
CREATE EXTENSION IF NOT EXISTS pgcrypto;

CREATE TABLE users (
    id SERIAL PRIMARY KEY,            -- id: 主キー
    name VARCHAR(255) NOT NULL,       -- name: 名前
    displayName VARCHAR(255),         -- displayName: 表示名
    email VARCHAR(255) UNIQUE NOT NULL, -- email: メアド (一意制約)
    password VARCHAR(255) NOT NULL,    -- password: ハッシュ化したパスワード
    bio TEXT,                         -- bio: 自己紹介や説明
    image TEXT,                       -- image: 画像のパス・base64
    github_url VARCHAR(255),             --link:Githubのリンク
    x_url VARCHAR(255),             --link:Xのリンク
    portfolie_url VARCHAR(255)        --link:Githubのリンク
);

INSERT INTO users (name, displayName, bio, image, email, password, github_url, x_url, portfolie_url)
VALUES
('山田太郎', 'Taro Yamada', '日本のエンジニア。プログラミングが得意です。', 'https://irasutoya.jp/wp-content/uploads/2021/01/kawa-pikach-no-irasuto-t-mei.png', 'taro.yamada@example.com', crypt('password123', gen_salt('bf')),'https://github.com' ,'https://x.com' ,''),
('鈴木花子', 'Hanako Suzuki', 'ウェブデザイナー。美しいデザインを作成します。', '', 'hanako.suzuki@example.com', crypt('mypassword', gen_salt('bf')), '' ,'' ,''),
('佐藤一郎', 'Ichiro Sato', 'システムアーキテクト。技術的な問題を解決するのが得意です。', 'path/to/image3.jpg', 'ichiro.sato@example.com', crypt('securepass', gen_salt('bf')), '' ,'' ,'');

CREATE TABLE events (
    id SERIAL PRIMARY KEY, -- 自動で増加するID
    user_id INT NOT NULL, -- ユーザーID (usersテーブルと紐付け)
    title VARCHAR(255) NOT NULL, -- イベントのタイトル
    tags TEXT[] NOT NULL, -- タグの配列（PostgreSQLの配列型を利用）
    event_date TIMESTAMP NOT NULL, -- 日時を表すTIMESTAMP型（DATEでも良いが、時間も考慮できる）
    location VARCHAR(255) NOT NULL, -- 場所を表すカラム
    description TEXT, -- イベントの詳細な説明
    created_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP, -- 作成日時（タイムゾーン付き）
    updated_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP, -- 更新日時（タイムゾーン付き）

    -- 外部キーの設定 (usersテーブルのidを参照)
    CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE event_comments (
    id SERIAL PRIMARY KEY, -- 自動で増加するID
    event_id INT NOT NULL, -- どのイベントへのコメントか（eventsテーブルと紐付け）
    user_id INT NOT NULL, -- コメントしたユーザーのID（usersテーブルと紐付け）
    content TEXT NOT NULL, -- コメントの内容
    created_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP, -- 作成日時
    updated_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP, -- 更新日時
    -- 外部キーの設定
    CONSTRAINT fk_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE event_participants (
    id SERIAL PRIMARY KEY,               -- 主キー
    event_id INT NOT NULL,               -- イベントID (eventsテーブルと紐付け)
    user_id INT NOT NULL,                -- ユーザーID (usersテーブルと紐付け)
    created_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP, -- 参加日時

    -- 外部キーの設定
    CONSTRAINT fk_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,

    -- ユニーク制約: 同じユーザーが同じイベントに重複して参加できないようにする
    CONSTRAINT unique_event_user UNIQUE (event_id, user_id)
);

-- user_id 1 (Yamada) が作成したイベントを挿入
-- 今日から2日後の14:00に設定する
INSERT INTO events (user_id, title, tags, event_date, location, description) 
VALUES 
(1, 
 'PostgreSQLハンズオンセミナー', 
 ARRAY['セミナー', 'PostgreSQL', 'ハンズオン'], 
 (CURRENT_DATE + INTERVAL '2 days')::timestamp + INTERVAL '14 hours', 
 '渋谷カンファレンスルーム', 
 'PostgreSQLの基礎から応用まで学べるハンズオン形式のセミナーです。'
);


CREATE TABLE ideas (
    id SERIAL PRIMARY KEY, -- 自動で増加するID
    user_id INT NOT NULL, -- ユーザーID (usersテーブルと紐付け)
    title VARCHAR(255) NOT NULL, -- イベントのタイトル
    tags TEXT[] NOT NULL, -- タグの配列（PostgreSQLの配列型を利用）
    description TEXT, -- イベントの詳細な説明
    created_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP, -- 作成日時（タイムゾーン付き）
    updated_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP, -- 更新日時（タイムゾーン付き）
    -- 外部キーの設定 (usersテーブルのidを参照)
    CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE idea_comments (
    id SERIAL PRIMARY KEY, -- 自動で増加するID
    idea_id INT NOT NULL, -- どのイベントへのコメントか（eventsテーブルと紐付け）
    user_id INT NOT NULL, -- コメントしたユーザーのID（usersテーブルと紐付け）
    content TEXT NOT NULL, -- コメントの内容
    created_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP, -- 作成日時
    updated_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP, -- 更新日時
    -- 外部キーの設定
    CONSTRAINT fk_idea FOREIGN KEY (idea_id) REFERENCES ideas(id) ON DELETE CASCADE,
    CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- user_id 1 (Yamada) が作成したイベントを挿入
-- 今日から2日後の14:00に設定する
INSERT INTO ideas (user_id, title, tags, description) 
VALUES 
(1, 
 'オーシャン掲示板', 
 ARRAY['PHP'], 
 '人とつながれる掲示板をPHPで作りたい'
);
