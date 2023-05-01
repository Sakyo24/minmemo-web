# コンテナ起動
up:
	docker-compose -f ./laradock/docker-compose.yml up -d nginx mysql phpmyadmin mailhog
# コンテナ停止
stop:
	docker-compose -f ./laradock/docker-compose.yml stop
# コンテナ削除(コンテナ/ネットワーク)
down:
	docker-compose -f ./laradock/docker-compose.yml down
# コンテナ削除(コンテナ/ネットワーク/ボリューム)
down-with-volumes:
	docker-compose -f ./laradock/docker-compose.yml down --volumes
# コンテナ確認
ps:
	docker-compose -f ./laradock/docker-compose.yml ps
# workspaceコンテナにアクセス
workspace:
	docker-compose -f ./laradock/docker-compose.yml exec --user=laradock workspace bash
# フロントコンパイル
dev:
	docker-compose -f ./laradock/docker-compose.yml exec --user=laradock workspace npm run dev
# フロントビルド
build:
	docker-compose -f ./laradock/docker-compose.yml exec --user=laradock workspace npm run build
# mysqlコンテナにアクセス
mysql:
	docker-compose -f ./laradock/docker-compose.yml exec mysql bash
# テスト実行
test:
	docker-compose -f ./laradock/docker-compose.yml exec --user=laradock workspace php artisan test
