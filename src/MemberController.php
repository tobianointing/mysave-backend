<?php

class MemberController
{
    public function __construct(private MemberGateway $gateway)
    {
    }

    public function processRequest(string $method,  int $id): void
    {
        if ($id) {

            $this->processResourceRequest($method,  $id);
        } else {

            $this->processCollectionRequest($method, $id);
        }
    }

    private function processResourceRequest(string $method, int $id): void
    {
        $member = $this->gateway->get($id);

        if (!$member) {
            http_response_code(404);
            echo json_encode(["message" => "member not found"]);
            return;
        }

        switch ($method) {
            case "GET":
                echo json_encode($member);
                break;

            case "PATCH":
                $data = (array) json_decode(file_get_contents("php://input"), true);

                $rows = $this->gateway->update($member, $data);

                echo json_encode([
                    "message" => "Accout updated successfully",
                    "rows" => $rows,
                    "status" => 1
                ]);
                break;

            case "DELETE":
                $rows = $this->gateway->delete($id);

                echo json_encode([
                    "message" => "member $id deleted",
                    "rows" => $rows
                ]);
                break;

            default:
                http_response_code(405);
                header("Allow: GET, PATCH, DELETE");
        }
    }

    private function processCollectionRequest(string $method, int $id): void
    {
        switch ($method) {
            case "GET":
                echo json_encode($this->gateway->getAll($id));
                break;

            default:
                http_response_code(405);
                header("Allow: GET, POST");
        }
    }

  
}
