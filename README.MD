## Локальный разворот.
1. git clone git@github.com:Korts/cmtt-test.git
2. cd cmtt
3. cp .env.example .env
4. composer i
5. docker-compose up -d

В зависимости от ОС должен быть настроен файл hosts (адрес cmtt-loc ссылается на Ip докера)

Документация доступна по адресу http://cmtt-loc:8000/docs

<img width="529" alt="swagger-cmtt" src="https://user-images.githubusercontent.com/16737207/127052995-06eff366-b179-4ec1-aa06-ce2a1d7f2aca.PNG">

Сваггер прямо там позволяет покликать эндпоинты без помощи postman`а и curl

<img width="511" alt="swagger-cmtt2" src="https://user-images.githubusercontent.com/16737207/127053036-9e6b107c-09ae-46d4-a29c-264f04c7a7c2.PNG">
