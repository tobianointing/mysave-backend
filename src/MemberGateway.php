<?php

class MemberGateway
{
    private PDO $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function getAll(string $id): array
    {
        $sql = "SELECT *
                FROM users
                WHERE id = :id";


        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        $data = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $data[] = $row;
        }

        return $data;
    }

    public function get(string $id): array | false
    {
        $sql = "SELECT *
                FROM users
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data;
    }


    public function verify_member(string $email, string $phone = null): array | false
    {
        $sql = "SELECT *
                FROM users
                WHERE email = :email OR phone = :phone";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->bindValue(":phone", $phone, PDO::PARAM_STR);

        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data == false) {
            return false;
        }

        return $data;
    }


    public function update(array $current, array $new): int
    {
        $sql = "UPDATE users
                SET first_name = :first_name, last_name = :last_name, 
                email = :email, phone = :phone, address = :address
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":first_name", $new["first_name"] ?? $current["first_name"], PDO::PARAM_STR);
        $stmt->bindValue(":last_name", $new["last_name"] ?? $current["last_name"], PDO::PARAM_INT);
        $stmt->bindValue(":phone", $new["phone"] ?? $current["phone"], PDO::PARAM_STR);
        $stmt->bindValue(":email", $new["email"] ?? $current["email"], PDO::PARAM_INT);
        $stmt->bindValue(":address", $new["address"] ?? $current["address"], PDO::PARAM_INT);


        $stmt->bindValue(":id", $current["id"], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public function delete(string $id): int
    {
        $sql = "DELETE FROM users
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }


    public function credit_member(string $method, array $data, int $id): void
    {
        if ($method == "POST") {

            $amount = (float) $data["amount"];

            $reason = $data["reason"];

            $member = $this->get($id);

            if (!$member) {
                http_response_code(404);
                echo json_encode(["message" => "member not found"]);
                exit;
            }

            $balance = (float) $member["balance"];

            $new_balance =  $balance  + $amount;

            $sql = "UPDATE users
                SET balance = :balance
                WHERE id = :id";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":balance", $new_balance, PDO::PARAM_STR);

            $stmt->bindValue(":id", $id, PDO::PARAM_INT);

            $stmt->execute();

            $rows = $stmt->rowCount();

            $last_id_mem_trxs = $this->insert_into_members_trxs($amount, $id, "credit", ["reason" => $reason]);


            echo json_encode([
                "status" => 1,
                "message" => "account credited successfully",
                "rows" => $rows,
                "trx_id" => $last_id_mem_trxs,
            ]);
        } else {
            http_response_code(405);
            header("Allow: POST");
        }
    }



    public function debit_member(string $method, array $data, int $id): void
    {
        if ($method == "POST") {

            $amount = (float) $data["amount"];

            $password = (float) $data["password"];

            $member = $this->get($id);

            if (!password_verify($password, $member['password'])) {

                echo json_encode([
                    'status' => 0,
                    'message' => 'Incorrect Password',
                ]);
                return;
            }

            if (!$member) {
                http_response_code(404);
                echo json_encode(["status" => 0, "message" => "member not found"]);
                return;
            }

            $balance = $member["balance"];

            if ($balance < $amount) {
                echo json_encode(["status" => 0, "message" => "insufficient funds"]);
                return;
            }

            $new_balance = $balance - (float) $amount;

            $sql = "UPDATE users
                SET balance = :balance
                WHERE id = :id";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":balance", $new_balance ?? $member["balance"], PDO::PARAM_STR);

            $stmt->bindValue(":id", $id, PDO::PARAM_INT);

            $stmt->execute();

            $rows = $stmt->rowCount();

            $last_id_mem_trxs = $this->insert_into_members_trxs($amount, $id, "debit", $data);

            echo json_encode([
                "status" => 1,
                "message" => "Fund withdrawal successfully",
                "rows" => $rows,
                "trx_lstid" => $last_id_mem_trxs
            ]);
        } else {
            http_response_code(405);
            header("Allow: POST");
        }
    }

    private function insert_into_members_trxs(float $amount, int $id, string $ttype, ?array $data = null): int
    {
        $sql = "INSERT INTO transactions (user_id, amount, transaction_type, dest_bank, dest_acct, reason, narration)
                    VALUES (:user_id, :amount, :transaction_type, :dest_bank, :dest_acct, :reason, :narration)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":user_id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":amount", $amount, PDO::PARAM_INT);
        $stmt->bindValue(":transaction_type", $ttype, PDO::PARAM_STR);
        $stmt->bindValue(":dest_bank", $data["dest_bank"] ?? "", PDO::PARAM_INT);
        $stmt->bindValue(":dest_acct", $data["dest_acct"] ?? "", PDO::PARAM_STR);
        $stmt->bindValue(":reason", $data["reason"] ?? "", PDO::PARAM_INT);
        $stmt->bindValue(":narration", $data["narration"] ?? "", PDO::PARAM_STR);

        $stmt->execute();

        return $this->conn->lastInsertId();
    }


    public function get_coop_bal(string $id): array | false
    {
        $sql = "SELECT income_balance
                FROM cooperatives
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data;
    }


    public function history(string $method, int $id): void
    {
        if ($method === "GET") {
            $sql = "SELECT *
                FROM transactions
                WHERE user_id = :id";


            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":id", $id, PDO::PARAM_INT);

            $stmt->execute();

            $data = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                $data[] = $row;
            }

            echo json_encode([
                'status' => 1,
                'results' => $data,
            ]);
    
            return;
        } else {
            http_response_code(405);
            header("Allow: GET");
        }
    }

    public function image_upload(string $method, array $post, array $file): void
    {
        if ($method === "POST") {
            $fileName  =  $file['image']['name'];
            $tempPath  =  $file['image']['tmp_name'];
            $fileSize  =  $file['image']['size'];

            $member_id = $post["member_id"];

            if (empty($fileName)) {
                echo json_encode(["message" => "please select image", "status" => 0]);
                return;
            }

            $upload_path = '../profile_images/';

            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $valid_extensions = array('jpeg', 'jpg', 'png', 'gif');

            $fileName = "member" . rand(000, 999) . '.' . $fileExt;

            if (in_array($fileExt, $valid_extensions)) {
                if ($fileSize < 5000000) {
                    move_uploaded_file($tempPath, $upload_path . $fileName);
                } else {
                    echo json_encode(["message" => "Sorry, your file is too large, please upload 5 MB size", "status" => 0]);
                    return;
                }
            } else {
                echo json_encode(["message" => "Sorry, only JPG, JPEG, PNG & GIF files are allowed", "status" => 0]);
                return;
            }

            $sql = "UPDATE members
                    SET profile_image = :profile_image
                    WHERE id = :id";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":profile_image", $fileName, PDO::PARAM_STR);
            $stmt->bindValue(":id", $member_id, PDO::PARAM_INT);

            $stmt->execute();

            echo json_encode(["message" => "Image Uploaded Successfully", "status" => 1]);
        } else {
            http_response_code(405);
            header("Allow: POST");
        }
    }
}
