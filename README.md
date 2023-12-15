# How To Run:

### Docker deployment
1. Install [Docker](https://docs.docker.com/engine/install/ubuntu/) on your machine.
2. Clone the project to your machine.
3. Copy ```.env.example``` to ```.env ``` file
4. Use command ``` docker-compose up -d --build ``` to build the project
5. Use command ``` docker-compose exec app ./artisan migrate --seed ``` to migrate database and add dummy data
6. Use command ``` docker-compose exec app ./artisan migrate:fresh --seed ``` to refresh database and add dummy data
7. The project should be working now on ```http://localhost:9000```
8. You will find postman collection and environment in ``` /postman ``` directory
9. To login use these credentials email: ``` test@example.com ``` password: ``` 123456 ```
10. You can run PHPUnit test ``` docker-compose exec app ./artisan test ``` but remember to refresh migration after because currently it runs the tests on same database

