# Backend Requirements

docker

---

## Setup
### 1. You must have running ChromaDB and Ollama services running (locally or externally), and configure their URLs/Ports in [ChatbotServices.php](../php/ChatbotServices.php) as needed.  The [server](../server/) code can help you with this.

### 2. The [server](../server/) folder has a [docker-compose.yml](../server/docker-compose.yml) file to get those services running locally if you do not have an external server running them.

### 3. There is also a sample terraform implementation for a small arm64 EC2 instance that will be OK for dev and test purposes.  Suggest upgrading the instance type (e.g. c7g.4xlarge or c7g.8xlarge) if you want to do demos with it.

### 4. Future enhancement suggestions
#### 1. Backup proxy configuration in a [persistent database](https://nginxproxymanager.com/setup/#using-mysql-mariadb-database)
