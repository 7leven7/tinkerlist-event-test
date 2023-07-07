## tinkerlist-event-test

Instead of using one Call API 1.0, I decided to use one Call API 3.0 just to get weather information based on event location. This API version provides accurate and relevant weather information for a certain location, ensuring the accuracy and relevance of weather data for a certain location.

I chose to apply the Geocoding function of the API-Ninja platform (https://api-ninjas.com) to get accurate location information such as longitude and latitude. The reason for this choice is that I could not find response with the necessary attributes to display when using only the city name. This additional service allows us to obtain the necessary information for the problem.

I have integrated the Mailgun service to manage email sending functionality.

Authentication is done using the JSON Web Tokens (JWT) mechanism.  By incorporating JWT authentication, the solution ensures that only registered users can access, increasing security and protecting sensitive data.

The solution follows repository design pattern to maintain a clean and structured code base. This pattern separates data entry logic from business logic, modularity, and code reuse. Following this pattern, the codebase is organized and maintainable, facilitating improvements and future updates.

The solution uses Docker for a consistent and scalable development environment. Docker allows you to create an isolated and lightweight container that contains all the necessary dependencies and configurations. By using Docker, the solution ensures that the development environment is consistent across different systems, reducing potential problems related to environment inconsistencies.

In summary, the solution includes one Call API 3.0 integration with the Geocoding functionality of the API-Ninja platform, implementing JWT authentication, following repository design patterns for a clean and maintainable code, and using Docker for a consistent and scalable development environment.


## Setup project localy

1. Start by unzipping the file received via email.
2. Open your terminal and navigate to the unzipped folder.
3. Execute the following commands in sequence:

```
docker compose build
docker compose up -d
```

These commands will build the Docker containers and start them in the background.

4. To continue the setup, we need to access the PHP Docker container. Run the following command:

```
docker exec -it tinklist-php-1 sh
```

This command will open a shell inside the PHP container.

5. Once inside the container, navigate to the working folder by running the following command:

```
cd /code
```

This will take you to the project's working directory.

6. Now, execute the following commands to complete the setup:

```
composer install
php artisan jwt:secret
php artisan migrate --seed
```

With these steps completed, the solution should be set up and ready for use.

Also I wrote few basic unit tests:

```
php artisan test
```

In the email, you will also find a JSON Postman collection


## Routes

1. Register User:
   This route is used to register a new user in the system. It sends a POST request to `http://127.0.0.1:8090/api/register` with the user's name, email, and password. The user will be registered with the provided information.

```
curl --location 'http://127.0.0.1:8090/api/register' \
--form 'name="Branko "' \
--form 'email="branko@branko.com"' \
--form 'password="secret123!"'
```

2. Login User:
   This route is used to authenticate a user and obtain an access token for subsequent API requests. It sends a POST request to `http://127.0.0.1:8090/api/login` with the user's email and password as form data. If the credentials are valid, the API will respond with an access token.

```
curl --location 'http://127.0.0.1:8090/api/login' \
--form 'email="branko@branko.com"' \
--form 'password="secret123!"'
```

3. Create Event:
   This route is used to create a new event in the system. It sends a POST request to `http://127.0.0.1:8090/api/events` with the necessary event data as JSON in the request body. The request should include the title, date and time, location, country code, and invitees' details. The request must also include the `Authorization` header with the access token obtained from the login request.

```
curl --location 'http://127.0.0.1:8090/api/events' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer XXXXXXXXXXX' \
--data-raw '{
  "title": "Summit",
  "date_time": "2023-08-02 14:30:00",
  "location": "Anderlecht",
  "country_code" : "BE",
  "invitees": [
    {
      "name": "Branko Kragovic Invatee",
      "email": "branko@gmail.com"
    }
  ]
}
'
```   
4. Update Event:
   This route is used to update an existing event in the system. It sends a PUT request to `http://127.0.0.1:8090/api/events/{event_id}` where `{event_id}` is the unique identifier of the event to be updated. The request includes the updated event data as JSON in the request body. The `Authorization` header with the access token is required for authentication.

```
curl --location --request PUT 'http://127.0.0.1:8090/api/events/34' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer xxxxxxxxxxxxx' \
--data '{
  "title": "Day Summit Summmmmmit"
}'
```

5. Delete Event:
   This route is used to delete an event from the system. It sends a DELETE request to `http://127.0.0.1:8090/api/events/{event_id}` where `{event_id}` is the unique identifier of the event to be deleted. The `Authorization` header with the access token is required for authentication.

```
curl --location --request DELETE 'http://127.0.0.1:8090/api/events/1' \
--header 'Authorization: Bearer xxxxxxxxxxxxx'
```

6. Get Event Details:
   This route is used to retrieve detailed information about a specific event. It sends a GET request to `http://127.0.0.1:8090/api/events/{event_id}` where `{event_id}` is the unique identifier of the event. The `Authorization` header with the access token is required for authentication.

```
curl --location 'http://127.0.0.1:8090/api/events/34' \
--header 'Authorization: Bearer xxxxxxxxxxxxxx'
```

7. Get Events within Date Range:
   This route is used to retrieve a list of events within a specified date range. It sends a GET request to `http://127.0.0.1:8090/api/events?startDate={start_date}&endDate={end_date}` where `{start_date}` and `{end_date}` are the desired start and end dates for the range. The request returns events that fall within the specified date range. The `Authorization` header with the access token is required for authentication.

```
curl --location 'http://127.0.0.1:8090/api/events?startDate=2023-07-05&endDate=2023-07-11' \
--header 'Authorization: Bearer xxxxxxxxxxxxxx'
```

8. Get Locations of Events within Date Range:
   This route is used to retrieve the locations of events within a specified date range. It sends a GET request to `http://127.0.0.1:8090/api/locations/events?startDate={start_date}&endDate={end_date}` where `{start_date}` and `{end_date}` are the desired start and end dates for the range. The request returns the locations of events that fall within the specified date range. The `Authorization` header with the access token is required for authentication.

```
curl --location 'http://127.0.0.1:8090/api/locations/events?startDate=2023-07-05&endDate=2023-07-11' \
--header 'Authorization: Bearer xxxxxxxxxxxxxxx'
```
