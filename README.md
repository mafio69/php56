*Tested on ubuntu 20.04

**For the application to work, it is necessary to run dockerfile or docker-compose**
**To run on the system, docker and docker-compose must be installed**

---

# Note: All commands are executed in the directory where docker-compose.yml is located

#### Linux

1.[install docker ubuntu](https://docs.docker.com/compose/install)
2.[install docker-compose ubuntu](https://docs.docker.com/compose/install)

#### WIN

1.[install docker win 10](https://docs.docker.com/docker-for-windows/install/)

**NOTE `<EXAMPLE>` To be replaced with the appropriate values**
**`exit 0` or `exit status 0` In linux it means `[OK]` any other number is an error**

## RUN APPLICATION

---
### ROAD MAP:

### First run

Build `./.env` the file from `./.env_example`

- terminal in container  `cd /main`
- terminal in container next `composer install -o`
- terminal in container end   `exit` out container terminal

* terminal in host `docker-compose down`
* terminal in host `docker-compose up --build` . **It works!**

### Next starts

`docker-compose up --build` or faster `docker-compose up`

##**NOTE**

---

- in case of port conflicts, database name ... it is possible to change the value in the `./.env`  file in the
  .env_example**_ file
- we do not change `./.env` file ! This file is common to all environments and users. Placed in the repository
  according to the Symfony documentation
### Links according to the .env_example file:
NOTE: RUN `docker ps` The list will include the port, e.g. 8070:8080 the first is the host port, e.g. http://localhost:8070   
used in the browser should display a running application  
app: http://localhost:<WEB_PORT>  
database: localhost:<DATABASE_PORT_LOCAL> user: <DATABASE_USER>(test)   
password:<DATABASE_PASSWORD>(1234) database:<DATABASE_NAME>(ccfound)


### Links according to the .env_example file:

NOTE: RUN `docker ps` The list will include the port, e.g. 8070:8080 the first is the host port, e.g. http://localhost:8070
used in the browser should display a running application
app: http://localhost:<WEB_PORT>
database: localhost:<DATABASE_PORT_LOCAL> user: <DATABASE_USER>(test)
password:<DATABASE_PASSWORD>(1234) database:<DATABASE_NAME>(test)

## WARNING

### All discovered passwords are examples, all addresses also apply to the local network, after deploying locally, they should be changed.

[mafio69](mailto:mf1969@gmail.com?subject=[GitHub]%20Docker%20Repo)

