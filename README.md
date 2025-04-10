
## Steps to Run the Application

### 1. **Download and Extract the ZIP File**

First, you will receive a ZIP file of the application. Extract the file to a location of your choice.



### 2. **Install Project Dependencies**

Open a terminal or command prompt and navigate to the directory where you extracted the ZIP file.

Run the following command to install the PHP dependencies using **Composer**:


composer install


This will download all the necessary dependencies to run the Laravel backend.


### 3. Configure Environment Variables

Inside the root directory of the project, you will find a file named .env.example. Copy this file to a new file called .env:



cp .env.example .env



Then, open the .env file and configure the following variables:


Example configuration for MySQL:


### 3.1 Database Variables  (copy paste)

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pledgepool
DB_USERNAME=root
DB_PASSWORD=


### 3.2 Swagger variables (copy paste)

L5_SWAGGER_USE_ABSOLUTE_PATH=true
L5_SWAGGER_BASE_PATH=/api
L5_SWAGGER_GENERATE_ALWAYS=true
L5_SWAGGER_CONST_HOST=http://localhost:8000  
L5_SWAGGER_UI_DISPLAY=true
L5_SWAGGER_UI_DOCS_PATH=docs
L5_SWAGGER_USE_ABSOLUTE_PATH=true
L5_SWAGGER_UI_ASSETS_PATH=vendor/swagger-api/swagger-ui/dist/
L5_FORMAT_TO_USE_FOR_DOCS=json
L5_SWAGGER_GENERATE_ALWAYS=false
L5_SWAGGER_GENERATE_YAML_COPY=false
L5_SWAGGER_UI_DARK_MODE=false
L5_SWAGGER_UI_DOC_EXPANSION=none
L5_SWAGGER_UI_FILTERS=true
L5_SWAGGER_UI_PERSIST_AUTHORIZATION=false



### 4. Generate the Application Key


Laravel needs a unique application key. Generate this key by running:

php artisan key:generate


### 5. Run Database Migrations

Next, you need to run the migrations to create the necessary tables in the database:

php artisan migrate


 ### 6. Install Swagger (if not already installed)
To generate API documentation, ensure that the L5-Swagger package is installed. If it's not installed yet, run the following command:



composer require "darkaonline/l5-swagger"


php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider"



 ### 7. Generate the API Documentation with Swagger


Run the following command to generate the Swagger documentation for your API:


php artisan l5-swagger:generate


This will create the documentation in JSON format inside the public/docs folder.



 ### 8. Start the Development Server

To start the Laravel development server, run:


php artisan serve


This will start the application on your local server at port 8000 (by default). Access the application through http://localhost:8000


Running Tests
To run unit tests, Laravel uses PHPUnit. You can execute the tests with the following command:


php artisan test



### 9.Main API Endpoints


Here is a summary of the main API endpoints available:

User Registration: POST /api/register

User Login: POST /api/login

Get Investment Account: GET /api/investment-account

Top-up Investment Account: POST /api/investment-account/top-up

Withdraw Funds from Investment Account: POST /api/investment-account/withdraw

Create Crowdfunding Campaign: POST /api/campaigns

Close Campaign: POST /api/campaigns/{id}/close

Invest in Campaign: POST /api/campaigns/{id}/invest

List Campaigns: GET /api/campaigns

Cancel Investment: DELETE /api/investments/{id}



### 10.Documentation

You can find the full API documentation at: 

http://127.0.0.1:8000/docs  (JUST JSON FILE)

http://127.0.0.1:8000/api/documentation   (UI)
