dkup:
		docker compose up -d
dkreboot:
		docker compose up down && docker compose up -d
dkupprod:
		docker compose -f docker-compose-production.yml up -d
dkrebootprod:
		docker compose -f docker-compose-production.yml up down && docker compose -f docker-compose-production.yml up -d
shell:
		docker compose exec realtor-app bash
shellprod:
		docker compose -f docker-compose-production.yml exec realtor-app bash
shellstag:
		docker compose -f docker-compose-staging.yml exec realtor-app bash
nginx:
		docker compose exec nginx /bin/sh
test:
		docker compose exec realtor-app sh -c 'php artisan test'
