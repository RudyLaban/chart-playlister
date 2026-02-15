# 🎵 Chart Playlister

Application web qui génère des playlists Spotify à partir de classements musicaux (charts) Billboard.

## Stack technique

- **Back-end** : Symfony 8 + FrankenPHP + PostgreSQL 16
- **Front-end** : Vue 3 + TypeScript + Vite
- **Containerisation** : Docker Compose custom

## Prérequis

- Docker & Docker Compose
- WSL2 (si Windows)
- Make

## Installation
```bash
# Cloner le repo
git clone <url>
cd chart-playlister

# Build et installation
make build
make install

# Lancer la stack
make dev
```

## Accès

- **API** : https://localhost:8443
- **Front** : http://localhost:5173
- **DB** : localhost:5432 (user: `chart_playlister`, password: `chart_playlister`)

## Commandes utiles
```bash
make help           # Liste toutes les commandes
make dev            # Lance la stack
make stop           # Arrête la stack
make destroy        # Reset complet (supprime volumes)
make logs-api       # Logs API
make logs-front     # Logs Front
make shell-api      # Shell dans le container API
make shell-front    # Shell dans le container Front
make db-reset       # Reset DB (drop + create + migrate)
```

## Développement

### Structure du projet
```
chart-playlister/
├── api/            # Symfony 8 (API JSON)
├── front/          # Vue 3 + Vite (SPA)
├── docker/         # Dockerfiles
└── docker-compose.yml
```

### Workflow Git

- `main` : Production (protégée)
- `develop` : Intégration (branche de travail)
- `feature/*` : Nouvelles fonctionnalités

## Roadmap

- [x] Phase 0 : Fondations & environnement dev
- [ ] Phase 1 : Modèle de données & API
- [ ] Phase 2 : Scraping Billboard
- [ ] Phase 3 : Intégration Spotify
- [ ] Phase 4 : Front-end Vue
- [ ] Phase 5 : CI/CD & Déploiement