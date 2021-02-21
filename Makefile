up:
	docker-compose up -d
build:
	docker-compose up -d --build
down:
	docker-compose down
restart:
	docker-compose down && docker-compose up -d
destroy:
	docker-compose down --rmi all  --volumes
destroy-volumes:
	docker-compose down --volumes
ps:
	docker-compose ps
app:
	docker-compose exec app bash
db:
	docker-compose exec db bash

