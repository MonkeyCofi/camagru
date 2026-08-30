build:
	docker compose --env-file ./database/.env build

down:
	docker compose down

up:
	docker compose --env-file ./database/.env up -d

all:
	docker container rm --force $(qlist_containers)
	make down; make build; make up

re:
	make down; make build; make up