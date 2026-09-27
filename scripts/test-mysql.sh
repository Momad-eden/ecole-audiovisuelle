#!/usr/bin/env bash
# Lance la suite de tests sur MySQL 8.4 dans un conteneur Docker jetable
# (port 33306, base emsi_test). Ne touche jamais à la base de développement.
set -euo pipefail

cd "$(dirname "$0")/.."

NAME="emsi-test-mysql-$$"
cleanup() { docker rm -f "$NAME" >/dev/null 2>&1 || true; }
trap cleanup EXIT

docker run -d --rm --name "$NAME" \
    -e MYSQL_ROOT_PASSWORD=emsi-test \
    -e MYSQL_DATABASE=emsi_test \
    -p 127.0.0.1:33306:3306 \
    mysql:8.4 >/dev/null

echo "Attente de MySQL…"
for _ in $(seq 1 60); do
    if docker exec "$NAME" mysql -uroot -pemsi-test -e 'SELECT 1' emsi_test >/dev/null 2>&1; then
        break
    fi
    sleep 1
done

php artisan config:clear >/dev/null
status=0
./vendor/bin/phpunit --configuration=phpunit.mysql.xml "$@" || status=$?

# Preuve que la suite a bien tourné sur MySQL : tables créées par les migrations.
tables=$(docker exec "$NAME" mysql -N -uroot -pemsi-test -e 'SHOW TABLES' emsi_test 2>/dev/null | wc -l)
echo "Tables présentes dans la base MySQL de test : ${tables}"

exit "$status"
