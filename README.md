# Buzzvel Test assignment for backend
<p>Project developed for the backend test of the company Buzzvel.</p>

## Requirements
<ul>
    <li>PHP 8.1 or newer</li>
    <li>Composer</li>
    <li>Docker</li>
</ul>

## Installing
Clone the repository

    git clone https://github.com/lukasfkt/buzzvel_test_assignment.git

Switch to the repo folder

    cd buzzvel_test_assignment

Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Generate a new application key

    php artisan key:generate

Generate a new JWT authentication secret key

    php artisan jwt:secret

Run the database migrations (**Set the database connection in .env before migrating**)

    php artisan migrate

Start the local development server

    php artisan serve

You can now access the server at http://localhost:80

## Get Started

To initialize the API just access the project folder and run the command: `./vendor/bin/sail up`. (For this you will need to have docker installed on your machine).

To start using the api, you will first need to register.

* Register End Point: http://your-domain/api/register

> Params to be sent in the body
`name(string)`, `email(string)` and  `password(string)`
Return Value: `User Object or Reponse Status 400`

After registering the user, you will need to login to authenticate.

* Login End Point: http://your-domain/api/login

> Params to be sent in the body
`email(string)` and  `password(string)`
Return Value: `Object with token or Reponse Status 401`

The only public routes are registration and login. For the others, it will be necessary to use the <b>Authorization header </b> with the <b>token</b> returned by the login route.

## Available Endpoints

### Tasks
Base URL: http://your-domain/api/tasks

#### GET METHODS

* List all tasks: `/`

> Return Value: `Array of Task Object`

* List a specific task: `/{id}`

> Parameters that must be sent in the route: 
`id(uuid)`
Return Value: `Task Object or Reponse Status 400`

#### POST METHODS

* Create new Task: `/`

> Params to be sent in the body: 
`title(string)`, `description(string)`, `completed(boolean) - optional` and  `file(image) - optional`
Return Value:  `Task Object or Reponse Status 400`

#### PUT METHODS

* Edit exiting Task: `/{id}`

> Params to be sent in the route: 
`id(uuid)`

> Params to be sent in the body: 
`title(string)`, `description(string)`, `completed(boolean)`
Return Value:  `Reponse Status 200 or Reponse Status 400`

#### DELETE METHODS

* Edit exiting Collaborador: `/{id}`

> Params to be sent in the route: 
`id(uuid)`
Return Value:  `Reponse Status 200 or Reponse Status 400`

## Contact

* Owner: Lucas Tanaka
* Email: lucasfktanaka@gmail.com
