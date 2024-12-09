# Project Title

Short description about the project goes here.

## Prerequisites

- Docker and Docker Compose should be installed on your system.

## Installation

1. **Clone the repository**:


2. **Set up Docker environment**:
- Use Docker Compose to start the required services.
  ```
  docker-compose up -d
  ```

3. **Access the Docker container**:
- Once the services are up, you can enter the Docker container.
  ```
  docker-compose exec {php-container} bash
  ```

## Running the Project

1. **Install Composer dependencies**:
  ```
  composer install
  ```

2. **Run database migrations**:
  ```
  bin/console d:m:m
  ```

3. **Import product data**:
- You need to run the Symfony command to import product data.
  ```
  bin/console import:products
  ```

## Troubleshooting

- If you encounter any issues, check the logs in `docker-compose logs` for error messages.
- Make sure that the `.env` file is correctly set up with the necessary configurations.
- Contact support or refer to the documentation for more help.

