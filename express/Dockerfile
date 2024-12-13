FROM node:20-slim

# 作業ディレクトリを指定
WORKDIR /app

# Node.jsのコードをコピー
COPY ./package.json /app

# 依存関係をインストール
RUN npm install

# ポートの公開
EXPOSE 3000

CMD ["npm", "run", "dev"]
