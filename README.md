## Installation

### Prérequis
- Docker & Docker Compose
- WSL2 (si Windows)
- Make

### Installation from scratch
```bash
# Cloner le repo
git clone 
cd chart-playlister

# Installer le projet
make install

# Lancer la stack
make dev
```

### Accès
- API : https://localhost:8443
- Front : http://localhost:5173
- DB : localhost:5432