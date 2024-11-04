## WebPetDispenser API Documentation

---

### 1. Login
- **URL**: `api/login.php`
- **Method**: `POST`
- **Description**: Realiza o login de um usuário.
- **Request Body**:
  - `email` (string): Email do usuário.
  - `password` (string): Senha do usuário.
- **Response**:
  - `name` (string): Nome do usuário.
  - `token` (string): Token de autenticação gerado.
  - `admin` (boolean): Status de administrador do usuário.
  - **Em caso de erro**:
    - `status`: "error"
    - `message`: "Invalid password" ou "User not found"

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
  - Em caso de sucesso, retornará o mesmo que o endpoint de login.
  - **Em caso de erro**:
    - `status`: "error"
    - `message`: "User already exists" ou "Error creating user"

---

### 3. User Information
- **URL**: `api/user.php`
- **Method**: `GET`
- **Description**: Retorna informações do usuário autenticado.
- **Query Parameters**:
  - `token` (string): Token de autenticação do usuário.
- **Response**:
  - `status`: "success"
  - `user`: Detalhes do usuário como `id`, `name`, `email`, e `admin`
  - `devices`: Lista de dispositivos do usuário
  - **Em caso de erro**:
    - `status`: "error"
    - `message`: "Invalid Token" ou "No devices found with the provided user"

---

### 4. Device Information
- **URL**: `api/device.php`
- **Method**: `GET` ou `POST`
- **Description**:
  - `GET`: Retorna informações de um dispositivo.
  - `POST`: Cria um novo dispositivo.
- **GET Parameters**:
  - `token` (string): Token de autenticação do dispositivo.
- **POST Body**:
  - `token` (string): Token de autenticação do dispositivo.
  - `name` (string): Nome do dispositivo.
- **Response**:
  - `status`: "success" ou "error"
  - `message`: Mensagem sobre o sucesso ou erro da operação
  - `deviceToken` (string): Token único do dispositivo criado (em caso de sucesso na criação)
  - **Erros comuns**: "Invalid user token", "Failed to create device", "Error creating device", "No device found"

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
  - `status`: "success" ou "error"
  - `message`: "Command added to queue" ou "Command executed"
  - `command`: Detalhes do comando executado (somente em GET)
  - **Erros comuns**: "Invalid user token", "Unauthorized access to the device", "Failed to add command to queue", "No unexecuted commands found"

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
  - `id` (string): ID do agendamento.
- **DELETE Body**:
  - `token` (string): Token de autenticação.
  - `deviceId` (string): ID do dispositivo.
  - `scheduleId` (string): ID do agendamento.
- **Response**:
  - `status`: "success" ou "error"
  - `message`: "Schedule created successfully", "Command executed successfully", "Schedule deleted successfully"
  - `command`: Detalhes do comando executado (somente em GET)
  - **Erros comuns**: "Invalid user token", "User does not own this device", "Error creating schedule", "No scheduled commands found", "Error deleting schedule or schedule not found" 