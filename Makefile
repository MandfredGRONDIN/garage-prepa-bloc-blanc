# Fichiers Docker Compose
BASE_COMPOSE = docker-compose.yml
DEV_COMPOSE = docker-compose.dev.yml
PROD_COMPOSE = docker-compose.prod.yml

# Commandes docker-compose de base
DC = docker-compose -f $(BASE_COMPOSE)

# Lancer en mode développement (logs attachés)
dev:
	$(DC) -f $(DEV_COMPOSE) up --build

# Lancer en mode développement détaché (background)
dev-detach:
	$(DC) -f $(DEV_COMPOSE) up -d --build

# Lancer en mode production (détaché)
prod:
	$(DC) -f $(PROD_COMPOSE) up -d --build

# Arrêter tous les containers
stop:
	$(DC) down

# Afficher les logs (tous services)
logs:
	$(DC) logs -f

# Redémarrer les services en mode détaché (build inclus)
restart:
	$(DC) down
	$(DC) up -d --build

# Nettoyer : supprimer containers, volumes, réseaux inutilisés
clean:
	docker system prune -f

.PHONY: dev dev-detach prod stop logs restart clean
