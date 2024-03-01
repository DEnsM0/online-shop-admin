# Online Shop Admin
![App_view][App_view]
## 1. About The Project
This is a small CRUD (Create, Read, Update, Delete) web application I created to learn about databases and how to interact with them. For the implementation of the web application, I used PHP with Bootstrap for styling and the jQuery library for creating interactive pages. I've built my CRUD web application as an admin interface for an online shop, allowing the user (administrator) to access the database of a hypothetical online shop and perform operations like searching/displaying, adding, modifying, and deleting data. Additionally, I've written Java code to populate the database with randomly generated data. One notable feature of my PHP implementation is that the resulting API is almost universal except for a few peculiarities. The application consists of 4 pages: main - search, add, details, edit. All of them implement the CRUD system. The application was deployed on the free hosting service 000webhost as well as locally using Docker images.

## 2. Built With

* [![Bootstrap][Bootstrap]][Bootstrap-url]
* [![Docker][Docker]][Docker-url]
* [![Java][Java]][Java-url]
* [![jQuery][jQuery]][jQuery-url]
* [![MySQL][MySQL]][MySQL-url]
* [![PHP][PHP]][PHP-url]

## 3. Features

- **CRUD functionality**
- **Admin interface for managing an online shop's database**
- **Bootstrap for styling**
- **jQuery for interactivity**
- **Easy deployment with Docker**

## 4. Getting Started
### Online Deployment
The application is available online at the following link: [myonlineshopadmin.000webhostapp.com][Online-host]
### Local Deployment with Docker
**Installation:**

1. Ensure Docker is installed on your local machine. For Docker installation instructions, refer to [Docker Documentation][Docker Documentation]. On Windows, you may need to install WSL2 for running bash scripts. [WSL2 Installation Guide][WSL2 Installation Guide]

2. Once Docker is installed, clone the repository using `git clone`:
    ```
    git clone https://github.com/DEnsM0/online-shop-admin.git
    ```
3. Navigate to the project directory in your terminal.

4. Run the following command to start the Docker containers: 
    ```
    docker-compose up -d
    ```

5. Once all Docker containers are up and running, the application will be accessible at `localhost`.

>**Note:** It may take a few seconds for the database to become functional after the containers are started.

To shut down the containers and preserve changes in the local database, use `./shutdown.sh` instead of `docker-compose down`.



## 5. Usage

- The main page (index.php) features a search functionality across all tables and attributes. Searches are conducted using the standard LIKE principle. This means that the value must exactly match the entered search to appear in the search results. Leaving the search field blank will display all data in the table.
- Clicking on a row in the search result table will redirect the user to the details page, where more detailed information about the data retrieved from the search is displayed. From there, users can either edit or delete the data.
- It's also possible to add new data to any table in the database via the add page (accessible through the "Add" link in the navbar or the "Add New" button on the main page).

#### Online Version:

>**Note:** The online version of the web application is limited due to the constraints of the free hosting service, which does not support certain features such as stored procedures.

#### Docker Version:

>**Note:** All further commands and files are relative to the project root.

- **Database Reset:** To reset the database and fill it with new data, delete `db/data/backup.sql` before starting the Docker containers. Then run `docker-compose up -d`.
- **Table Creation:** After starting the Docker containers, execute the following steps:
    1. Access the bash inside the database image by running:
        ```
        docker exec -it online-shop-admin-db-1 bash`
        ```
    2. Run the script to create tables and other necessary operations using the command:
        ```
        mysql -u php_docker -ppassword php_docker < /create-script.sql`
        ```
    3. Exit the bash inside the database image by typing `exit`.

- **Data Population:** You can manually populate the database through the web application or use the data generator:
    ```
    java -jar java/java.jar
    ```


## 6. Contributing

Any contributions you make are **greatly appreciated**. If you plan to make significant changes, kindly initiate a discussion by creating an issue beforehand.

Feel free to explore and address these issues, which are conveniently listed in the "Issues" tab for your convenience.

[Bootstrap]: https://img.shields.io/badge/bootstrap-%238511FA.svg?style=for-the-badge&logo=bootstrap&logoColor=white
[Bootstrap-url]: https://getbootstrap.com/
[Docker]: https://img.shields.io/badge/docker-%230db7ed.svg?style=for-the-badge&logo=docker&logoColor=white
[Docker-url]: https://www.docker.com/
[Java]: https://img.shields.io/badge/Java-ED8B00?style=for-the-badge&logo=openjdk&logoColor=white
[Java-url]: https://www.java.com/en/
[jQuery]: https://img.shields.io/badge/jquery-%230769AD.svg?style=for-the-badge&logo=jquery&logoColor=white
[jQuery-url]: https://jquery.com/
[MySQL]: https://img.shields.io/badge/mysql-%2300f.svg?style=for-the-badge&logo=mysql&logoColor=white
[MySQL-url]: https://www.mysql.com/
[PHP]: https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white
[PHP-url]: https://www.php.net/

[App_view]: https://imgur.com/a/xOckuXt
[Online-host]: https://myonlineshopadmin.000webhostapp.com/
[Docker Documentation]: https://docs.docker.com/desktop/
[WSL2 Installation Guide]: https://docs.microsoft.com/en-us/windows/wsl/install