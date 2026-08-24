build:
	docker compose --env-file ./database/.env build

down:
	docker compose down

up:
	docker compose --env-file ./database/.env up