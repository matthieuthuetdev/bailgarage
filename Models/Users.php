    <?php
    class Users
    {
        private PDO $connection;
        public function __construct()
        {
            $this->connection = Database::getInstance();
        }
        public function searchUserByEmail($_email)
        {
            $request = "SELECT users.id FROM users WHERE email = :email";
            $rq = $this->connection->prepare($request);
            $rq->bindValue(":email", $_email, PDO::PARAM_STR);
            $rq->execute();
            $userId = $rq->fetch(PDO::FETCH_ASSOC);
            return $userId;
        }

        public function create($_firstName, $_name, $_email)
        {
            $result = $this->searchUserByEmail($_email);
            if (empty($result)) {
                $request = "INSERT INTO users (name,firstName,email,password,roleId) VALUE (:name, :firstName, :email, :password,2)";
                $rq = $this->connection->prepare($request);
                $rq->bindValue(":name", $_name, PDO::PARAM_STR);
                $rq->bindValue(":firstName", $_firstName, PDO::PARAM_STR);
                $rq->bindValue(":email", $_email, PDO::PARAM_STR);
                $password = $_name . $_firstName . mt_rand(0, 1000);
                $rq->bindValue(":password", password_hash($password, PASSWORD_ARGON2I), PDO::PARAM_STR);

                $rq->execute();
                $result = $this->searchUserByEmail($_email);
                $result["password"] = $password;
                return $result;
            } else {
                return false;
            }
        }
        public function read($_userId)
        {
            $request = "SELECT * FROM users WHERE id = :userId";
            $rq = $this->connection->prepare($request);
            $rq->bindValue(":userId", $_userId);
            $rq->execute();
            $result = $rq->fetch(PDO::FETCH_ASSOC);
            return $result;
        }
        public function signIn(string $_email, string $_password): array
        {
            $request = "SELECT users.id,users.name, users.firstName, users.email, users.password, roles.name AS roleName FROM users INNER JOIN roles ON users.roleId = roles.id WHERE users.email= :email";
            $rq = $this->connection->prepare($request);
            $rq->bindValue(":email", $_email, PDO::PARAM_STR);
            $rq->execute();
            $result = $rq->fetch(PDO::FETCH_ASSOC);
            if (!empty($result)) {
                $succes = password_verify($_password, $result["password"]);
                if ($succes) {
                    return $result;
                } else {
                    return [];
                }
            } else {
                return [];
            }
        }
        public function update($_id, $_firstName, $_name, $_email, $_password = null)
        {
            $request = "UPDATE users SET firstName = :firstName, name = :name, email = :email" . ($_password !== null ? ", password = :password" : "") . " WHERE id = :id";
            $rq = $this->connection->prepare($request);

            $rq->bindValue(":id", $_id, PDO::PARAM_INT);
            $rq->bindValue(":firstName", $_firstName, PDO::PARAM_STR);
            $rq->bindValue(":name", $_name, PDO::PARAM_STR);
            $rq->bindValue(":email", $_email, PDO::PARAM_STR);

            if ($_password !== null) {
                $rq->bindValue(":password", password_hash($_password, PASSWORD_ARGON2I), PDO::PARAM_STR);
            }

            return $rq->execute();
        }
        public function activeResetPasswordToken($_id, $_token)
        {
            $request = "UPDATE users SET resetToken = :token WHERE users.id = :id";
            $rq = $this->connection->prepare($request);
            $rq->bindValue(":id", $_id);
            $rq->bindValue(":token", $_token);
            return $rq->execute();
        }
        public function disableResetPasswordToken($_id)
        {
            $request = "UPDATE users SET resetToken = null where id = :id";
            $rq = $this->connection->prepare($request);
            $rq->bindValue(":id", $_id);
            return $rq->execute();
        }
        public function searchUserByResetPasswordToken($_tresetToken)
        {
            $request = "SELECT users.id FROM users WHERE resetToken = :resetToken";
            $rq = $this->connection->prepare($request);
            $rq->bindValue(":resetToken", $_tresetToken, PDO::PARAM_STR);
            $rq->execute();
            $userId = $rq->fetch(PDO::FETCH_ASSOC);
            return $userId;
        }
        public function resetPassword($_id, $_password)
        {
            $request = "UPDATE users SET password = :password WHERE users.id = :id";
            $rq = $this->connection->prepare($request);
            $rq->bindValue(":id", $_id);
            $rq->bindValue(":password", $_password);
            return $rq->execute();
        }
    }
