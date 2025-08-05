#!/bin/bash
# save as run_travel_requests.sh
# Torna executável com: chmod +x run_travel_requests.sh

BASE_URL="http://localhost:8000/api/travel-requests"
HEADERS="-H \"Accept: application/json\""
JSON_CONTENT="-H \"Content-Type: application/json\""

{
  echo "=== Criar novo pedido (POST)"
  curl -X POST ${BASE_URL} \
    ${JSON_CONTENT} ${HEADERS} \
    -d '{
      "applicant_name": "João Silva",
      "destination": "Rio de Janeiro",
      "start_date": "2025-09-10",
      "end_date": "2025-09-15",
      "status": "solicitado"
    }'
  echo -e "\n"

  echo "=== Listar todos os pedidos (GET)"
  curl -X GET ${BASE_URL} ${HEADERS}
  echo -e "\n"

  echo "=== Consultar pedido específico por ID (GET)"
  ID="INSIRA_ID_AQUI"
  curl -X GET ${BASE_URL}/${ID} ${HEADERS}
  echo -e "\n"

  echo "=== Atualização completa (PUT)"
  curl -X PUT ${BASE_URL}/${ID} ${JSON_CONTENT} ${HEADERS} \
    -d '{
      "applicant_name": "Maria Oliveira",
      "destination": "São Paulo",
      "start_date": "2025-10-01",
      "end_date": "2025-10-05",
      "status": "aprovado"
    }'
  echo -e "\n"

  echo "=== Atualização parcial (PATCH)"
  curl -X PATCH ${BASE_URL}/${ID} ${JSON_CONTENT} ${HEADERS} \
    -d '{"status": "cancelado"}'
  echo -e "\n"

  echo "=== Excluir pedido (DELETE)"
  curl -X DELETE ${BASE_URL}/${ID} ${HEADERS}
  echo -e "\n"
} 2>&1 | tee travel_requests_test.log

echo "Script finalizado. Veja o arquivo travel_requests_test.log para o log completo."

