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
Untuk menguji endpoint API, kamu bisa menggunakan [Postman](https://www.postman.com/). File koleksi tersedia di repo ini.

🔗 **Download Postman Collection:**
[📥 Klik di sini untuk mengunduh](./PlanEase.postman_collection.json)

Atau import manual ke Postman:

1. Buka Postman
2. Klik `Import`
3. Pilih file `PlanEase.postman_collection.json`
4. Mulai testing endpoint!
