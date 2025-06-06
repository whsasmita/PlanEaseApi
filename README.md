<h1 align="center">PlanEase API</h1>

<h2>How to clone and running api?</h2>
## Follow these steps to run this project locally:

1. **Open your terminal.**
2. **Clone the repository:**
   ```bash
   https://github.com/whsasmita/PlanEaseApi.git
3. Navigate to the project folder:
    ```bash
    cd PlanEaseApi
    ```
4. Install Laravel dependencies:
    ```bash
    composer install
    ```
5. Copy the configuration file:
    ```bash
    cp .env.example .env
    ```
6. Generate the application key:
    ```bash
    php artisan key:generate
    ```
7. Create a symbolic link for storage:
    ```bash
    php artisan storage:link
    ```
8. Edit the `.env` file to configure your database:
    ```env
    DB_DATABASE=planeaseapi
    DB_USERNAME=root
    DB_PASSWORD=
    ```
9. Run the database migrations:
    ```bash
    php artisan migrate
    ```
10. Seed the database with initial data:
    ```bash
    php artisan db:seed
    ```
11. Start the Laravel local server:
    ```bash
    php artisan serve
    ```
12. BaseURL for this api:
    ```
    http://127.0.0.1:8000/
    ```

---
<h2>Postman Collection</h2> 
To test the API, import the provided Postman collection file into [Postman](https://www.postman.com/). The file is included in this repository.

🔗 **Download Postman Collection:**
[Tap here for start download](./PlanEase.postman_collection.json)

Or manual import to Postman:

1. Open Postman
2. Click `Import`
3. Select file `PlanEase.postman_collection.json`
4. Ready to testing endpoint!
