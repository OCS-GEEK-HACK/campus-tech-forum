# .envファイルを手動で作成する必要があります

build:
	docker compose build
	docker compose run --rm express npm i

up:
	docker compose up -d

down:
	docker compose down

db-access:
	docker exec -it db bash

psql-access:
	docker exec -it db bash -c "psql -U johndoe -d mydb"

init-data:
	echo "データベースに初期データを挿入するには、initdb.d/migrate.sqlを編集してください"

help:
	@echo "利用可能なコマンド:"
	@echo "  make build        - 初回構築（イメージ作成＆npm install）"
	@echo "  make up           - コンテナを起動"
	@echo "  make down         - コンテナを停止"
	@echo "  make db-access    - DBコンテナにアクセス"
	@echo "  make psql-access  - PostgreSQLにアクセス"
	@echo "  make init-data    - 初期データの挿入ガイドを表示"
