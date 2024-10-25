# WebPetDispenser API Documentation

## Endpoints

---

### 1. Login
- **URL**: `api/login.php`
- **Method**: `POST`
- **Description**: Realiza o login de um usuário.
- **Request Body**:
  - `email` (string): Email do usuário.
  - `password` (string): Senha do usuário.
- **Response**:
  - `status`: Indica o resultado da operação.
  - `message`: Mensagem de sucesso ou erro.

---

### 2. Register
- **URL**: `api/register.php`
- **Method**: `POST`
- **Description**: Registra um novo usuário.
- **Request Body**:
  - `name` (string): Nome do usuário.
  - `email` (string): Email do usuário.
  - `password` (string): Senha do usuário.
- **Response**:
  - `status`: Indica o resultado da operação.
  - `message`: Mensagem de sucesso ou erro.

---

### 3. User Information
- **URL**: `api/user.php`
- **Method**: `GET`
- **Description**: Retorna informações do usuário autenticado.
- **Query Parameters**:
  - `token` (string): Token de autenticação do usuário.
- **Response**:
  - `status`: Indica o resultado da operação.
  - `message`: Mensagem de sucesso ou erro.

---

### 4. Device Information
- **URL**: `api/device.php`
- **Method**: `GET` ou `POST`
- **Description**:
  - `GET`: Retorna informações de um dispositivo.
  - `POST`: Cria um novo dispositivo.
- **GET Parameters**:
  - `token` (string): Token de autenticação do dispositivo.
  - `deviceId` (string): ID do dispositivo.
- **POST Body**:
  - `token` (string): Token de autenticação do dispositivo.
  - `name` (string): Nome do dispositivo.
- **Response**:
  - `status`: Indica o resultado da operação.
  - `message`: Mensagem de sucesso ou erro.

---

### 5. Command Queue
- **URL**: `api/command.php`
- **Method**: `POST` ou `GET`
- **Description**:
  - `POST`: Adiciona um comando à fila de um dispositivo.
  - `GET`: Executa o comando mais antigo da fila.
- **POST Body**:
  - `token` (string): Token de autenticação.
  - `deviceId` (string): ID do dispositivo.
  - `command` (string): Comando a ser executado.
  - `info` (string): Informações adicionais.
- **GET Parameters**:
  - `token` (string): Token de autenticação.
- **Response**:
  - `status`: Indica o resultado da operação.
  - `message`: Mensagem de sucesso ou erro.

---

### 6. Scheduling Commands
- **URL**: `api/schedule.php`
- **Method**: `POST`, `GET`, `DELETE`
- **Description**:
  - `POST`: Agenda um comando para execução.
  - `GET`: Executa o comando agendado mais antigo.
  - `DELETE`: Remove um comando agendado.
- **POST Body**:
  - `token` (string): Token de autenticação.
  - `deviceId` (string): ID do dispositivo.
  - `command` (string): Comando a ser executado.
  - `time` (string): Horário para execução.
  - `info` (string): Informações adicionais.
- **GET Parameters**:
  - `token` (string): Token de autenticação.
- **DELETE Body**:
  - `token` (string): Token de autenticação.
  - `deviceId` (string): ID do dispositivo.
  - `scheduleId` (string): ID do agendamento.
- **Response**:
  - `status`: Indica o resultado da operação.
  - `message`: Mensagem de sucesso ou erro.

