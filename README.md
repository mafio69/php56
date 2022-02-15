*Tested on ubuntu 20.04

**For the application to work, it is necessary to run dockerfile or docker-compose**
**To run on the system, docker and docker-compose must be installed**

---
# Note: All commands are executed in the directory where docker-compose.yml is located
### [host] your computer your system
### [docker] system installed in a docker, in a container
___
___
### Linux

1.[install docker ubuntu](https://docs.docker.com/engine/install/ubuntu/)  
2.[install docker-compose ubuntu](https://docs.docker.com/compose/install)

### WIN

1.[install docker win 10](https://docs.docker.com/docker-for-windows/install/)

**NOTE `<EXAMPLE>` To be replaced with the appropriate values**
**`exit 0` or `exit status 0` In linux it means `[OK]` any other number is an error**

## RUN APPLICATION

---
### ROAD MAP:

### First run  

Build `./.env` the file from `./.env_example`  [host]  
###Sometimes it doesn't work for commands on the host add 'sudo'  (`sudo docker ....`) in front  
###Paste your application in the `/main`directory, server nginx will look for `index.php` in the `/main/public` directory  
* terminal in host `docker-compose down` [host]  
* terminal in host `docker-compose up --build` . [host]     
 **It works!**  
- list container `docker ps`  (take the container id) [host]  
- insert the id of the container you want to use `docker exec -it <CONTAINER_ID> bash` [host]  
- terminal in container  `cd /main` [docker]  
- terminal in container next `composer install -o`[docker]  
- terminal in container end   `exit` out container terminal [docker]  

### Next starts

`docker-compose up --build` or faster `docker-compose up` [host]

##**NOTE**

---

- in case of port conflicts, database name ... it is possible to change the value in the `./.env`  file in the
  .env_example**_ file

### Links according to the .env_example file:
NOTE: RUN `docker ps` The list will include the port, e.g. 8070:8080 the first is the host port, e.g. http://localhost:8070   
used in the browser should display a running application  
app: http://localhost:<WEB_PORT_LOCAL>  
database: localhost:<DATABASE_PORT_LOCAL> user: <DATABASE_USER>(test)   
password:<DATABASE_PASSWORD>(1234) database:<DATABASE_NAME>(test)


### Links according to the .env_example file:

NOTE: RUN `docker ps` The list will include the port, e.g. 8070:8080 the first is the host port, e.g. http://localhost:8070
used in the browser should display a running application
app: http://localhost:<WEB_PORT_LOCAL>
database: localhost:<DATABASE_PORT_LOCAL> user: <DATABASE_USER>(test)
password:<DATABASE_PASSWORD>(1234) database:<DATABASE_NAME>(test)
___
## WARNING

### All discovered passwords are examples, all addresses also apply to the local network, after deploying locally, they should be changed.

[mafio69](mailto:mf1969@gmail.com?subject=[GitHub]%20Docker%20Repo)

