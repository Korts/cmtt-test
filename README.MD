1. git clone git@github.com:Korts/cmtt-test.git
2. cd cmtt
3. cp .env.example .env
4. composer i
5. docker-compose up -d

В зависимости от ОС должен быть настроен файл hosts (адрес cmtt-loc ссылается на Ip докера)

Документация доступна по адресу http://cmtt-loc:8000/docs

Сваггер прямо там позволяет покликать эндпоинты без помощи postman`а и curl