# =============================================================================
# Dockerfile Front — Node 22 + Vite (dev server)
# =============================================================================
# En dev : Vite sert le front avec Hot Module Replacement (HMR)
# En prod : on buildera les assets statiques (pas géré ici, c'est pour plus tard)

FROM node:22-slim

# Répertoire de travail
WORKDIR /app

# Exposer le port Vite
EXPOSE 5173

# Commande par défaut : on la surcharge dans docker-compose
# (npm install + npm run dev)
# Commande par défaut : lancer le dev server Vite
CMD ["npm", "run", "dev", "--", "--host"]