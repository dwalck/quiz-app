# Wersja Node LTS
FROM node:24-alpine

# Ustaw katalog roboczy
WORKDIR /app

# Kopiuj package.json i package-lock.json (szybsze buildy)
COPY package*.json ./

# Instalacja zależności (opcjonalnie w buildzie — przydatne w prod)
RUN npm install

# Kopiowanie reszty projektu
COPY . .

# Dodaj ścieżkę do lokalnych binarek (np. next)

# Expose port
EXPOSE 3000

# Wylistuj zawartość (debug)
RUN ls -al

# Komenda startowa (tryb development)
CMD [ "sh", "-c", "npm install && npm run dev" ]
