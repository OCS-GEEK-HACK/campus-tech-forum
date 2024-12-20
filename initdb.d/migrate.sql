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
