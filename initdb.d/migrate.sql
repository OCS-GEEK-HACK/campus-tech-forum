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
    link VARCHAR(255)                 --link:GithubやXなどのリンク
);

INSERT INTO users (name, displayName, bio, image, email, password, link)
VALUES
('山田太郎', 'Taro Yamada', '日本のエンジニア。プログラミングが得意です。', 'path/to/image1.jpg', 'taro.yamada@example.com', crypt('password123', gen_salt('bf')),'github.com'),
('鈴木花子', 'Hanako Suzuki', 'ウェブデザイナー。美しいデザインを作成します。', 'path/to/image2.jpg', 'hanako.suzuki@example.com', crypt('mypassword', gen_salt('bf')), 'github.com'),
('佐藤一郎', 'Ichiro Sato', 'システムアーキテクト。技術的な問題を解決するのが得意です。', 'path/to/image3.jpg', 'ichiro.sato@example.com', crypt('securepass', gen_salt('bf')), 'github.com');

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
