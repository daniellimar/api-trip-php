## Requisitos

- Docker

---

## Configuração do Ambiente

## Instalação Backend

```bash
git clone https://github.com/daniellimar/api-trip-php.git
cd api-trip-php
docker compose up -d
```

### Importante: Após rodar o comando
```docker compose up -d```, a aplicação já estará totalmente preparada, com banco migrado e dados seedados automaticamente dentro do container.

### Serviço disponível na Porta: 8080

```bash
http://127.0.0.1:8080/
```

---

### Usuários gerados automaticamente (Seeders)

### A aplicação cria automaticamente usuários com as seguintes credenciais para testes:

| Nome     | Email             | Senha     |
|----------|-------------------|-----------|
| Admin    | admin@example.com | admin1234 |
| Usuário1 | user1@example.com | user1234  |
| Usuário2 | user2@example.com | user1234  |

Importante:
Para autenticação via API, utilize o token JWT gerado para cada usuário (exibido no terminal após o seed).

---

## Como executar os testes dentro do container

### Acesse o container do backend:

docker exec -it <container_id_ou_nome> bash

Aplique as migrations no ambiente de teste (usando o arquivo .env.testing):

``php artisan migrate --env=testing --force``

(Opcional) Rode o seeder para popular dados iniciais:

``php artisan db:seed --class=DatabaseSeeder --env=testing``

### Execute os testes com PHPUnit:

``php artisan test --env=testing``

ou

``./vendor/bin/phpunit --configuration phpunit.xml``

