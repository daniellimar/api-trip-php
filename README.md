## Requisitos

- PHP >= 8.2
- Composer
- Laravel >= 10
- Banco de Dados (Postgres)

---

## Configuração do Ambiente

## Instalação Backend

```bash
git clone https://github.com/daniellimar/api-trip-php.git
cd api-trip-php
docker-compose up -d
```

### Serviço disponível na Porta: 8080

```bash
http://127.0.0.1:8080/
```

## Instalação Frontend

```bash
git clone https://github.com/daniellimar/app-travel-vue.git
cd app-travel-vue
cp .env.example .env
docker-compose up -d
```

### Serviço disponível na Porta: 8089

```bash
http://127.0.0.1:8089/
```

---
